<?php

namespace App\Http\Controllers;

use App\Models\InternalNotification;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageReaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateMessageController extends Controller
{
    public function index(Request $request, ?User $user = null): View
    {
        $currentUser = $request->user();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        abort_if($user?->is($currentUser), 404);

        $users = User::query()
            ->where('id', '!=', $currentUser->id)
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('position', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->get();

        $messages = collect();
        if ($user) {
            $messages = $this->conversationQuery($currentUser, $user)
                ->visibleTo($currentUser)
                ->with(['sender', 'recipient', 'replyTo' => fn ($query) => $query->visibleTo($currentUser)->with('sender'), 'reactions.user'])
                ->oldest()
                ->get();

            PrivateMessage::where('sender_id', $user->id)
                ->where('recipient_id', $currentUser->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('private-messages.index', compact('users', 'user', 'messages', 'filters'));
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'Vous ne pouvez pas vous envoyer un message.');

        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:4000', 'required_without:attachment'],
            'reply_to_id' => ['nullable', 'integer', 'exists:private_messages,id'],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt'],
        ]);

        if (! empty($data['reply_to_id'])) {
            abort_unless($this->conversationQuery($request->user(), $user)->whereKey($data['reply_to_id'])->exists(), 422, 'Le message cite n appartient pas a cette conversation.');
        }

        $attachment = $request->file('attachment');
        $payload = [
            'sender_id' => $request->user()->id,
            'recipient_id' => $user->id,
            'reply_to_id' => $data['reply_to_id'] ?? null,
            'content' => $data['content'] ?? null,
        ];

        if ($attachment) {
            $payload['attachment_path'] = $attachment->store('private-messages');
            $payload['attachment_name'] = $attachment->getClientOriginalName();
            $payload['attachment_mime'] = $attachment->getMimeType();
            $payload['attachment_size'] = $attachment->getSize();
        }

        $message = PrivateMessage::create($payload);

        InternalNotification::create([
            'user_id' => $user->id,
            'actor_id' => $request->user()->id,
            'title' => 'Nouveau message prive',
            'content' => $request->user()->name.' vous a envoye un message.',
            'type' => 'private_message',
            'data' => ['private_message_id' => $message->id],
            'is_read' => false,
            'read_at' => null,
        ]);

        return redirect()->route('private.messages.user', $user)->with('success', 'Message envoye.');
    }

    public function react(Request $request, PrivateMessage $privateMessage): RedirectResponse
    {
        $this->authorizeParticipant($request, $privateMessage);

        $data = $request->validate([
            'reaction' => ['required', 'string', Rule::in(['👍', '❤️', '😂', '😮', '😢', '🙏'])],
        ]);

        $existing = $privateMessage->reactions()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing?->reaction === $data['reaction']) {
            $existing->delete();
        } else {
            PrivateMessageReaction::updateOrCreate(
                ['private_message_id' => $privateMessage->id, 'user_id' => $request->user()->id],
                ['reaction' => $data['reaction']]
            );
        }

        return back();
    }

    public function destroy(Request $request, PrivateMessage $privateMessage): RedirectResponse
    {
        $this->authorizeParticipant($request, $privateMessage);

        $data = $request->validate([
            'scope' => ['required', Rule::in(['me', 'everyone'])],
        ]);

        if ($data['scope'] === 'everyone') {
            abort_unless($privateMessage->sender_id === $request->user()->id, 403);

            if ($privateMessage->attachment_path) {
                Storage::delete($privateMessage->attachment_path);
            }

            $privateMessage->delete();

            return back()->with('success', 'Message supprime pour tout le monde.');
        }

        $deletedAtColumn = $privateMessage->sender_id === $request->user()->id
            ? 'deleted_for_sender_at'
            : 'deleted_for_recipient_at';

        $privateMessage->update([$deletedAtColumn => now()]);

        return back()->with('success', 'Message supprime pour vous.');
    }

    private function conversationQuery(User $firstUser, User $secondUser)
    {
        return PrivateMessage::query()->where(function ($query) use ($firstUser, $secondUser): void {
            $query->where('sender_id', $firstUser->id)->where('recipient_id', $secondUser->id);
        })->orWhere(function ($query) use ($firstUser, $secondUser): void {
            $query->where('sender_id', $secondUser->id)->where('recipient_id', $firstUser->id);
        });
    }

    private function authorizeParticipant(Request $request, PrivateMessage $privateMessage): void
    {
        abort_unless(in_array($request->user()->id, [$privateMessage->sender_id, $privateMessage->recipient_id], true), 403);
    }

    private function authorizeVisibleParticipant(Request $request, PrivateMessage $privateMessage): void
    {
        $this->authorizeParticipant($request, $privateMessage);

        $deletedAtColumn = $privateMessage->sender_id === $request->user()->id
            ? 'deleted_for_sender_at'
            : 'deleted_for_recipient_at';

        abort_if($privateMessage->{$deletedAtColumn}, 404);
    }

    public function file(Request $request, PrivateMessage $privateMessage): Response|StreamedResponse
    {
        $this->authorizeParticipant($request, $privateMessage);
        abort_unless($privateMessage->attachment_path && Storage::exists($privateMessage->attachment_path), 404);

        return Storage::response($privateMessage->attachment_path);
    }

    public function download(Request $request, PrivateMessage $privateMessage): Response|StreamedResponse
    {
        $this->authorizeParticipant($request, $privateMessage);
        abort_unless($privateMessage->attachment_path && Storage::exists($privateMessage->attachment_path), 404);

        return Storage::download($privateMessage->attachment_path, $privateMessage->attachment_name ?: basename($privateMessage->attachment_path), ['Content-Type' => 'application/octet-stream']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DiscussionGroup;
use App\Models\InternalNotification;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageController extends Controller
{
    public function index(Request $request, DiscussionGroup $group): JsonResponse
    {
        $this->ensureMember($request, $group);

        $messages = $group->messages()
            ->with(['user:id,name,role,position,avatar_path', 'replyTo.user:id,name', 'reactions.user:id,name,role,position,avatar_path'])
            ->latest('id')
            ->limit(100)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (Message $message): array => [
                'id' => $message->id,
                'content' => $message->content,
                'status' => $message->status,
                'created_at' => $message->created_at?->format('d/m/Y H:i'),
                'attachment_name' => $message->attachment_name,
                'attachment_mime' => $message->attachment_mime,
                'attachment_size' => $message->attachment_size,
                'attachment_url' => $message->attachment_path ? route('messages.file', $message) : null,
                'download_url' => $message->attachment_path ? route('messages.download', $message) : null,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'role' => $message->user->role,
                    'position' => $message->user->position,
                    'avatar_url' => $message->user->avatar_path ? route('profile.avatar', $message->user) : null,
                ],
                'reply_to' => $message->replyTo ? [
                    'id' => $message->replyTo->id,
                    'user_name' => $message->replyTo->user->name,
                    'content' => $message->replyTo->content,
                ] : null,
                'reactions' => $message->reactions->groupBy('reaction')->map(fn ($items): array => [
                    'count' => $items->count(),
                    'reacted' => $items->contains('user_id', $request->user()->id),
                    'users' => $items->map(fn (MessageReaction $reaction): array => [
                        'id' => $reaction->user->id,
                        'name' => $reaction->user->name,
                        'role' => $reaction->user->role,
                        'position' => $reaction->user->position,
                        'avatar_url' => $reaction->user->avatar_path ? route('profile.avatar', $reaction->user) : null,
                    ])->values(),
                ]),
            ]);

        return response()->json(['messages' => $messages]);
    }

    public function typing(Request $request, DiscussionGroup $group): JsonResponse
    {
        $this->ensureMember($request, $group);
        $this->ensureCanPost($request, $group);

        Cache::put($this->typingKey($group->id, $request->user()->id), [
            'user_id' => $request->user()->id,
            'name' => $request->user()->name,
        ], now()->addSeconds(5));

        return response()->json(['ok' => true]);
    }

    public function typingStatus(Request $request, DiscussionGroup $group): JsonResponse
    {
        $this->ensureMember($request, $group);
        $typingUsers = $group->members
            ->where('id', '!=', $request->user()->id)
            ->map(fn (User $user): ?array => Cache::get($this->typingKey($group->id, $user->id)))
            ->filter()
            ->values();

        return response()->json(['users' => $typingUsers]);
    }

    public function store(Request $request, DiscussionGroup $group): RedirectResponse
    {
        $this->ensureMember($request, $group);
        $this->ensureCanPost($request, $group);

        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:4000', 'required_without:attachment'],
            'reply_to_id' => ['nullable', 'integer', 'exists:messages,id'],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt'],
        ]);

        if (! empty($data['reply_to_id'])) {
            abort_unless($group->messages()->whereKey($data['reply_to_id'])->exists(), 422, 'Le message cité n’appartient pas à ce groupe.');
        }

        $attachment = $request->file('attachment');
        $payload = [
            'user_id' => $request->user()->id,
            'content' => $data['content'] ?? null,
            'reply_to_id' => $data['reply_to_id'] ?? null,
            'status' => 'active',
        ];

        if ($attachment) {
            $payload['attachment_path'] = $attachment->store('group-messages');
            $payload['attachment_name'] = $attachment->getClientOriginalName();
            $payload['attachment_mime'] = $attachment->getMimeType();
            $payload['attachment_size'] = $attachment->getSize();
        }

        $message = $group->messages()->create($payload);

        $memberIds = $group->members()->pluck('users.id')->all();
        foreach ($memberIds as $memberId) {
            if ($memberId === $request->user()->id) {
                continue;
            }

            InternalNotification::create([
                'user_id' => $memberId,
                'actor_id' => $request->user()->id,
                'title' => 'Nouveau message de groupe',
                'content' => sprintf('%s a publié un message dans %s.', $request->user()->name, $group->name),
                'type' => 'message',
                'data' => ['message_id' => $message->id, 'group_id' => $group->id],
                'is_read' => false,
                'read_at' => null,
            ]);
        }

        return back()->with('success', 'Message envoyé.');
    }

    public function react(Request $request, Message $message): RedirectResponse
    {
        $this->ensureMessageMember($request, $message);

        $data = $request->validate([
            'reaction' => ['required', 'string', Rule::in(['👍', '❤️', '😂', '😮', '😢', '🙏'])],
        ]);

        $existing = MessageReaction::where('message_id', $message->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing?->reaction === $data['reaction']) {
            $existing->delete();
        } else {
            MessageReaction::updateOrCreate(
                ['message_id' => $message->id, 'user_id' => $request->user()->id],
                ['reaction' => $data['reaction']]
            );
        }

        return back();
    }

    public function destroy(Message $message): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user->role === 'directeur' || $message->user_id === $user->id, 403);

        $message->update(['status' => 'deleted']);
        $message->delete();

        return back()->with('success', 'Message supprimé.');
    }

    public function restore(Message $message): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user->role === 'directeur' || $message->user_id === $user->id, 403);

        $message->restore();
        $message->update(['status' => 'active']);

        return back()->with('success', 'Message restauré.');
    }

    public function file(Request $request, Message $message): Response|StreamedResponse
    {
        $this->ensureMessageMember($request, $message);
        abort_unless($message->attachment_path && Storage::exists($message->attachment_path), 404);

        return Storage::response($message->attachment_path);
    }

    public function download(Request $request, Message $message): Response|StreamedResponse
    {
        $this->ensureMessageMember($request, $message);
        abort_unless($message->attachment_path && Storage::exists($message->attachment_path), 404);

        return Storage::download($message->attachment_path, $message->attachment_name ?: basename($message->attachment_path), ['Content-Type' => 'application/octet-stream']);
    }

    private function ensureMessageMember(Request $request, Message $message): void
    {
        $this->ensureMember($request, $message->discussionGroup);
    }

    private function ensureCanPost(Request $request, DiscussionGroup $group): void
    {
        $user = $request->user();
        $canPost = $user->role === 'directeur'
            || $group->created_by === $user->id
            || $group->posting_mode === 'all'
            || $group->members()->where('users.id', $user->id)->wherePivot('can_post', true)->exists();

        abort_unless($canPost, 403, 'Ce groupe est en lecture seule pour votre compte.');
    }

    private function typingKey(int $groupId, int $userId): string
    {
        return 'cem.typing.'.$groupId.'.'.$userId;
    }

    private function ensureMember(Request $request, DiscussionGroup $group): void
    {
        abort_unless(
            $group->members()->where('users.id', $request->user()->id)->exists()
                || $request->user()->role === 'directeur',
            403
        );
    }
}

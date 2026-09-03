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
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function index(Request $request, DiscussionGroup $group): JsonResponse
    {
        $this->ensureMember($request, $group);

        $messages = $group->messages()
            ->with(['user:id,name,role,position,avatar_path', 'replyTo.user:id,name', 'reactions'])
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
                ]),
            ]);

        return response()->json(['messages' => $messages]);
    }

    public function typing(Request $request, DiscussionGroup $group): JsonResponse
    {
        $this->ensureMember($request, $group);

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

        $data = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
            'reply_to_id' => ['nullable', 'integer', 'exists:messages,id'],
        ]);

        if (! empty($data['reply_to_id'])) {
            abort_unless($group->messages()->whereKey($data['reply_to_id'])->exists(), 422, 'Le message cité n’appartient pas à ce groupe.');
        }

        $message = $group->messages()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'reply_to_id' => $data['reply_to_id'] ?? null,
            'status' => 'active',
        ]);

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

    public function restore(int $messageId): RedirectResponse
    {
        $message = Message::withTrashed()->findOrFail($messageId);
        $user = auth()->user();
        abort_unless($user->role === 'directeur' || $message->user_id === $user->id, 403);

        $message->restore();
        $message->update(['status' => 'active']);

        return back()->with('success', 'Message restauré.');
    }

    private function ensureMessageMember(Request $request, Message $message): void
    {
        $this->ensureMember($request, $message->discussionGroup);
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

<?php

namespace App\Http\Controllers;

use App\Models\DiscussionGroup;
use App\Models\InternalNotification;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, DiscussionGroup $group): RedirectResponse
    {
        abort_unless(
            $group->members()->where('users.id', $request->user()->id)->exists() || $request->user()->role === 'directeur',
            403
        );

        $data = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $message = $group->messages()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'status' => 'active',
        ]);

        $memberIds = $group->members()->pluck('users.id')->all();
        foreach ($memberIds as $memberId) {
            if ($memberId === $request->user()->id) {
                continue;
            }

            InternalNotification::create([
                'user_id' => $memberId,
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
}

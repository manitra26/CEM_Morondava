<?php

namespace App\Http\Controllers;

use App\Models\InternalNotification;
use App\Models\Message;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = InternalNotification::where('user_id', auth()->id())
            ->with('actor')
            ->latest()
            ->get();

        foreach ($notifications as $notification) {
            if ($notification->actor) {
                continue;
            }

            $actor = null;
            if ($notification->data['report_id'] ?? null) {
                $actor = Report::with('user')->find($notification->data['report_id'])?->user;
            } elseif ($notification->data['message_id'] ?? null) {
                $actor = Message::with('user')->find($notification->data['message_id'])?->user;
            }

            if ($actor) {
                $notification->setRelation('actor', $actor);
            }
        }

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(InternalNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        InternalNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}

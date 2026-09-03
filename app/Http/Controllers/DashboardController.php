<?php

namespace App\Http\Controllers;

use App\Models\DiscussionGroup;
use App\Models\InternalNotification;
use App\Models\Message;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $isDirector = $user->role === 'directeur';

        $reportsQuery = Report::with('user')->latest('submitted_at');
        if (! $isDirector) {
            $reportsQuery->where('user_id', $user->id);
        }

        $groupsQuery = DiscussionGroup::with(['creator', 'members'])->latest();
        if (! $isDirector) {
            $groupsQuery->whereHas('members', fn ($query) => $query->where('users.id', $user->id));
        }

        $recentMessagesQuery = Message::with(['user', 'discussionGroup'])->latest();
        if (! $isDirector) {
            $recentMessagesQuery->whereHas('discussionGroup.members', fn ($query) => $query->where('users.id', $user->id));
        }

        return view('dashboard', [
            'totalUsers' => User::count(),
            'totalReports' => $reportsQuery->count(),
            'totalGroups' => $groupsQuery->count(),
            'unreadNotifications' => InternalNotification::where('user_id', $user->id)->where('is_read', false)->count(),
            'recentReports' => $reportsQuery->take(5)->get(),
            'recentGroups' => $groupsQuery->take(5)->get(),
            'recentMessages' => $recentMessagesQuery->take(5)->get(),
            'notifications' => InternalNotification::where('user_id', $user->id)->latest()->take(6)->get(),
            'isDirector' => $isDirector,
        ]);
    }
}

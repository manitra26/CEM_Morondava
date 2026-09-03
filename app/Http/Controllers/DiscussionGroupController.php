<?php

namespace App\Http\Controllers;

use App\Models\DiscussionGroup;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscussionGroupController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $isDirector = $user->role === 'directeur';

        $groups = DiscussionGroup::with(['creator', 'members'])
            ->when(! $isDirector, fn ($query) => $query->whereHas('members', fn ($memberQuery) => $memberQuery->where('users.id', $user->id)))
            ->latest()
            ->get();

        $allUsers = $isDirector
            ? User::orderBy('name')->get()
            : collect();

        return view('groups.index', compact('groups', 'allUsers', 'isDirector'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $group = DiscussionGroup::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'created_by' => $request->user()->id,
        ]);

        $group->members()->attach($request->user()->id, ['joined_at' => now()]);

        return back()->with('success', 'Le groupe de discussion a été créé.');
    }

    public function show(DiscussionGroup $group): View
    {
        $user = auth()->user();
        $canManage = $user->role === 'directeur' || $group->created_by === $user->id;

        abort_unless($canManage || $group->members()->where('users.id', $user->id)->exists(), 403);

        $group->load([
            'creator',
            'members' => fn ($query) => $query->orderBy('name'),
            'messages.user',
        ]);

        $allUsers = User::orderBy('name')->get();

        return view('groups.show', [
            'group' => $group,
            'allUsers' => $allUsers,
            'isDirector' => $canManage,
        ]);
    }

    public function join(DiscussionGroup $group): RedirectResponse
    {
        $group->members()->syncWithoutDetaching([
            auth()->id() => ['joined_at' => now()],
        ]);

        return back()->with('success', 'Vous avez rejoint le groupe.');
    }

    public function leave(DiscussionGroup $group): RedirectResponse
    {
        $group->members()->detach(auth()->id());

        return back()->with('success', 'Vous avez quitté le groupe.');
    }

    public function updateMembers(Request $request, DiscussionGroup $group): RedirectResponse
    {
        $this->ensureCanManage($group);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'action' => ['required', 'in:add,remove'],
        ]);

        if ($data['action'] === 'add') {
            $group->members()->syncWithoutDetaching([
                $data['user_id'] => ['joined_at' => now()],
            ]);
        } else {
            $group->members()->detach($data['user_id']);
        }

        return back()->with('success', 'La liste des membres a été mise à jour.');
    }

    private function ensureCanManage(DiscussionGroup $group): void
    {
        $user = auth()->user();
        abort_unless($user->role === 'directeur' || $group->created_by === $user->id, 403);
    }
}

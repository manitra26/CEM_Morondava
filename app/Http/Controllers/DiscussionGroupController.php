<?php

namespace App\Http\Controllers;

use App\Models\DiscussionGroup;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DiscussionGroupController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $isDirector = $user->role === 'directeur';
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $groups = DiscussionGroup::with(['creator', 'members'])
            ->when(! $isDirector, fn ($query) => $query->whereHas('members', fn ($memberQuery) => $memberQuery->where('users.id', $user->id)))
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->get();

        $allUsers = $isDirector
            ? User::orderBy('name')->get()
            : collect();

        return view('groups.index', compact('groups', 'allUsers', 'isDirector', 'filters'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'directeur', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'group_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $group = DiscussionGroup::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'image_path' => $request->file('group_image')?->store('groups'),
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
            'messages.replyTo.user',
            'messages.reactions.user',
        ]);

        $allUsers = User::orderBy('name')->get();
        $canPost = $canManage || $group->posting_mode === 'all'
            || (bool) ($group->members->firstWhere('id', $user->id)?->pivot?->can_post ?? false);

        return view('groups.show', [
            'group' => $group,
            'messages' => $group->messages->sortBy('created_at')->values(),
            'allUsers' => $allUsers,
            'isDirector' => $canManage,
            'canPost' => $canPost,
        ]);
    }

    public function update(Request $request, DiscussionGroup $group): RedirectResponse
    {
        $this->ensureCanManage($group);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'posting_mode' => ['required', 'in:restricted,all'],
            'group_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('group_image')) {
            if ($group->image_path) {
                Storage::delete($group->image_path);
            }
            $data['image_path'] = $request->file('group_image')->store('groups');
        }

        unset($data['group_image']);
        $group->update($data);

        return back()->with('success', 'Les paramètres du groupe ont été mis à jour.');
    }

    public function image(DiscussionGroup $group): Response|StreamedResponse
    {
        abort_unless($group->image_path && Storage::exists($group->image_path), 404);

        return Storage::response($group->image_path);
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
            'action' => ['required', 'in:add,remove,allow,deny'],
        ]);

        if ($data['action'] === 'add') {
            $group->members()->syncWithoutDetaching([
                $data['user_id'] => ['joined_at' => now(), 'can_post' => false],
            ]);
        } elseif ($data['action'] === 'remove') {
            $group->members()->detach($data['user_id']);
        } else {
            abort_unless($group->members()->whereKey($data['user_id'])->exists(), 422);
            $group->members()->updateExistingPivot($data['user_id'], [
                'can_post' => $data['action'] === 'allow',
            ]);
        }

        return back()->with('success', 'La liste des membres a été mise à jour.');
    }

    private function ensureCanManage(DiscussionGroup $group): void
    {
        $user = auth()->user();
        abort_unless($user->role === 'directeur' || $group->created_by === $user->id, 403);
    }
}

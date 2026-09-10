@extends('layouts.app')

@section('title', $group->name)

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-3">
            @if($group->image_path)<img src="{{ route('groups.image', $group) }}" alt="Image du groupe" class="cem-avatar cem-avatar-lg">@endif
            <h1 class="fw-bold mb-1">{{ $group->name }}</h1>
        </div>
        <p class="cem-soft mb-0">{{ $group->description }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-cem" data-bs-toggle="modal" data-bs-target="#group-info-modal">☰ Infos du groupe</button>
        <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary">Retour</a>
        <form method="POST" action="{{ route('groups.join', $group) }}">
            @csrf
            <button type="submit" class="btn btn-outline-success">Rejoindre</button>
        </form>
        <form method="POST" action="{{ route('groups.leave', $group) }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Quitter</button>
        </form>
    </div>
</div>

<div class="row g-4 group-chat-layout">
    <div class="col-12">
        <div class="card cem-card mb-4 group-chat-card">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Messages</strong>
                <span id="chat-status" class="small text-success">Synchronisé</span>
                <span class="badge cem-badge">{{ $group->messages->count() }} message(s)</span>
            </div>
            <div id="chat-messages" class="card-body group-chat-scroll" data-messages-url="{{ route('messages.index', $group) }}" data-can-post="{{ $canPost ? '1' : '0' }}">
                @if($canPost)
                <form id="chat-form" method="POST" action="{{ route('messages.store', $group) }}" class="mb-4">
                    @csrf
                    <label class="form-label">Nouveau message</label>
                    <div id="reply-preview" class="d-none alert alert-info py-2 mb-3"><span>Réponse à <strong id="reply-user"></strong> : <span id="reply-text"></span></span><button type="button" id="cancel-reply" class="btn-close float-end"></button></div>
                    <input type="hidden" name="reply_to_id" id="reply-to-id">
                    <textarea id="chat-content" name="content" rows="3" class="form-control mb-3" placeholder="Écrivez votre message ici..." required>{{ old('content') }}</textarea>
                    <div id="typing-indicator" class="small cem-soft mb-3 d-none"><span class="typing-dots"><i></i><i></i><i></i></span> <span id="typing-label"></span></div>
                    <button id="chat-submit" type="submit" class="btn btn-cem">Publier</button>
                </form>

                @else
                    <div class="alert alert-info mb-4">Mode lecture seule : seuls les administrateurs et les membres autorisés peuvent publier dans ce groupe.</div>
                @endif

                @forelse ($messages as $message)
                    <div class="border rounded-4 p-3 mb-3 bg-white chat-message" data-message-id="{{ $message->id }}">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                @if($message->user->avatar_path)
                                    <img src="{{ route('profile.avatar', $message->user) }}" alt="Photo de {{ $message->user->name }}" class="cem-avatar cem-avatar-message">
                                @else
                                    <span class="cem-avatar cem-avatar-message cem-avatar-placeholder">{{ strtoupper(substr($message->user->name, 0, 1)) }}</span>
                                @endif
                                <div>
                                    <strong><a href="{{ route('profile.show', $message->user) }}" class="text-decoration-none">{{ $message->user->name }}</a></strong>
                                    <div class="cem-user-meta text-capitalize">{{ $message->user->role }}{{ $message->user->position ? ' - '.$message->user->position : '' }}</div>
                                    <div class="small cem-soft">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            @if(auth()->id() === $message->user_id || auth()->user()->role === 'directeur')
                                <form method="POST" action="{{ route('messages.destroy', $message) }}" onsubmit="return confirm('Supprimer ce message ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                                </form>
                            @endif
                        </div>
                        @if($message->replyTo)
                            <div class="cem-reply-quote mt-3"><strong>{{ $message->replyTo->user->name }}</strong><br>{{ \Illuminate\Support\Str::limit($message->replyTo->content, 120) }}</div>
                        @endif
                        <p class="mt-3 mb-2">{{ $message->content }}</p>
                        <div class="d-flex align-items-center gap-2 flex-wrap reaction-actions position-relative">
                            <button type="button" class="btn btn-light btn-sm reaction-trigger" data-reaction-target="reaction-picker-{{ $message->id }}" title="Ajouter une réaction">😊</button>
                            <div id="reaction-picker-{{ $message->id }}" class="btn-group btn-group-sm reaction-picker d-none" role="group">
                                @foreach(['👍', '❤️', '😂', '😮', '😢', '🙏'] as $reaction)
                                    <form method="POST" action="{{ route('messages.react', $message) }}">
                                        @csrf
                                        <button name="reaction" value="{{ $reaction }}" class="btn btn-light reaction-button" title="Réagir avec {{ $reaction }}">{{ $reaction }}</button>
                                    </form>
                                @endforeach
                            </div>
                            @if($canPost)
                            <button type="button" class="btn btn-outline-secondary btn-sm reply-message" data-reply-id="{{ $message->id }}" data-reply-user="{{ $message->user->name }}" data-reply-content="{{ $message->content }}">Répondre</button>
                            @endif
                            @foreach($message->reactions->groupBy('reaction') as $reaction => $items)
                                <button type="button" class="badge reaction-summary reaction-details-trigger {{ $items->contains('user_id', auth()->id()) ? 'reaction-selected' : '' }}" data-reaction-target="reaction-details-{{ $message->id }}-{{ md5($reaction) }}">{{ $reaction }} {{ $items->count() }}</button>
                                <div id="reaction-details-{{ $message->id }}-{{ md5($reaction) }}" class="reaction-details-popover d-none">
                                    <strong>{{ $items->count() }} réaction(s)</strong>
                                    @foreach($items as $reactionItem)
                                        <div class="d-flex align-items-center gap-2 mt-2">
                                            @if($reactionItem->user->avatar_path)
                                                <img src="{{ route('profile.avatar', $reactionItem->user) }}" alt="Photo de {{ $reactionItem->user->name }}" class="cem-avatar cem-member-avatar">
                                            @else
                                                <span class="cem-avatar cem-member-avatar cem-avatar-placeholder">{{ strtoupper(substr($reactionItem->user->name, 0, 1)) }}</span>
                                            @endif
                                            <div><a href="{{ route('profile.show', $reactionItem->user) }}" class="text-decoration-none fw-semibold">{{ $reactionItem->user->name }}</a><div class="cem-user-meta text-capitalize">{{ $reactionItem->user->role }}{{ $reactionItem->user->position ? ' - '.$reactionItem->user->position : '' }}</div></div>
                                            <span class="ms-auto">{{ $reaction }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center cem-soft py-4">Aucun message dans ce groupe.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="modal fade" id="group-info-modal" tabindex="-1" aria-labelledby="group-info-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content cem-card">
                <div class="modal-header cem-card-header">
                    <div>
                        <h2 class="modal-title h5 mb-1" id="group-info-modal-title">Infos du groupe</h2>
                        <div class="small cem-soft">{{ $group->name }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body group-info-body">
                    <div class="group-info-actions d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-cem" data-group-panel="#group-members-panel" aria-expanded="false">Membres <span class="badge bg-light text-dark">{{ $group->members->count() }}</span></button>
                        @if($isDirector)
                            <button type="button" class="btn btn-outline-secondary" data-group-panel="#group-settings-panel" aria-expanded="false">Paramètres du groupe</button>
                            <button type="button" class="btn btn-outline-secondary" data-group-panel="#group-manage-members-panel" aria-expanded="false">Gérer les membres</button>
                        @endif
                    </div>

                    <div id="group-members-panel" class="group-info-panel d-none">
                        <h3 class="h6 fw-bold mb-3">Membres du groupe</h3>
                        <div class="row g-2 group-members-grid">
                            @foreach($group->members as $member)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-2 border rounded-3 p-2">
                                        @if($member->avatar_path)
                                            <img src="{{ route('profile.avatar', $member) }}" alt="Photo de {{ $member->name }}" class="cem-avatar cem-member-avatar">
                                        @else
                                            <span class="cem-avatar cem-member-avatar cem-avatar-placeholder">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                        @endif
                                        <div class="flex-grow-1">
                                            <a href="{{ route('profile.show', $member) }}" class="text-decoration-none fw-semibold">{{ $member->name }}</a>
                                            <div class="cem-user-meta text-capitalize">{{ $member->role }}{{ $member->position ? ' - '.$member->position : '' }}</div>
                                            @if($member->phone)<div class="small cem-soft">{{ $member->phone }}</div>@endif
                                        </div>
                                        @if($isDirector && $member->id !== auth()->id())
                                            <form method="POST" action="{{ route('groups.members.update', $group) }}" onsubmit="return confirm('Exclure ce membre du groupe ?')">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                <input type="hidden" name="action" value="remove">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Exclure</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($isDirector)
                        <div id="group-settings-panel" class="group-info-panel d-none">
                            <h3 class="h6 fw-bold mb-3">Paramètres du groupe</h3>
                            <form method="POST" action="{{ route('groups.update', $group) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <label for="group-settings-name" class="form-label">Nom du groupe</label>
                                <input id="group-settings-name" name="name" class="form-control mb-3" value="{{ $group->name }}" required>
                                <label for="group-settings-description" class="form-label">Description</label>
                                <textarea id="group-settings-description" name="description" rows="3" class="form-control mb-3" required>{{ $group->description }}</textarea>
                                <label for="group-posting-mode" class="form-label">Droit de publication</label>
                                <select id="group-posting-mode" name="posting_mode" class="form-select mb-2">
                                    <option value="restricted" @selected($group->posting_mode === 'restricted')>Lecture seule pour les membres</option>
                                    <option value="all" @selected($group->posting_mode === 'all')>Tous les membres peuvent écrire</option>
                                </select>
                                <div class="small cem-soft mb-3">Les administrateurs peuvent toujours écrire. Dans le mode lecture seule, vous pouvez autoriser des membres individuellement depuis « Gérer les membres ».</div>
                                <label for="group-settings-image" class="form-label">Image du groupe</label>
                                <input id="group-settings-image" type="file" name="group_image" class="form-control mb-3" accept=".jpg,.jpeg,.png,.webp">
                                <button class="btn btn-cem">Enregistrer</button>
                            </form>
                        </div>

                        <div id="group-manage-members-panel" class="group-info-panel d-none">
                            <h3 class="h6 fw-bold mb-3">Gérer les membres</h3>
                            <label for="group-member-search" class="form-label">Ajouter un membre</label>
                            <div class="member-search-box mb-2">
                                <input id="group-member-search" type="search" class="form-control" placeholder="Rechercher par nom ou numéro de téléphone" autocomplete="off">
                                <button type="button" id="clear-group-member-search" class="member-search-clear d-none" aria-label="Effacer la recherche">&times;</button>
                            </div>
                            <div class="small cem-soft mb-2"><span id="group-member-result-count">{{ $allUsers->count() }}</span> utilisateur(s) trouvé(s)</div>
                            <div id="group-member-list" class="group-member-list">
                                @foreach($allUsers as $managedUser)
                                    @php($isMember = $group->members->contains('id', $managedUser->id))
                                    <div class="group-member-row d-flex align-items-center gap-2 border rounded-3 p-2 mb-2" data-member-search="{{ strtolower($managedUser->name.' '.$managedUser->phone) }}">
                                        <div class="flex-grow-1">
                                            <strong>{{ $managedUser->name }}</strong>
                                            <div class="small cem-soft">{{ $managedUser->phone ?: 'Téléphone non renseigné' }}</div>
                                        </div>
                                        @if($isMember)
                                            @php($memberRecord = $group->members->firstWhere('id', $managedUser->id))
                                            @php($memberCanPost = (bool) ($memberRecord?->pivot?->can_post ?? false))
                                            <form method="POST" action="{{ route('groups.members.update', $group) }}">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $managedUser->id }}">
                                                <input type="hidden" name="action" value="{{ $memberCanPost ? 'deny' : 'allow' }}">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">{{ $memberCanPost ? 'Lecture seule' : 'Autoriser à écrire' }}</button>
                                            </form>
                                            <span class="badge cem-badge">{{ $memberCanPost ? 'Peut écrire' : 'Lecture seule' }}</span>
                                            @if($managedUser->id !== auth()->id())
                                                <form method="POST" action="{{ route('groups.members.update', $group) }}" onsubmit="return confirm('Exclure ce membre du groupe ?')">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $managedUser->id }}">
                                                    <input type="hidden" name="action" value="remove">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Exclure</button>
                                                </form>
                                            @endif
                                        @else
                                            <form method="POST" action="{{ route('groups.members.update', $group) }}">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $managedUser->id }}">
                                                <input type="hidden" name="action" value="add">
                                                <button type="submit" class="btn btn-outline-success btn-sm">Ajouter</button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div id="group-member-empty" class="small cem-soft d-none">Aucun membre trouvé.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
(() => {
    const panelButtons = [...document.querySelectorAll('[data-group-panel]')];
    const panels = [...document.querySelectorAll('.group-info-panel')];
    const closePanels = () => {
        panels.forEach((panel) => panel.classList.add('d-none'));
        panelButtons.forEach((button) => button.setAttribute('aria-expanded', 'false'));
    };
    panelButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const panel = document.querySelector(button.dataset.groupPanel);
            const shouldOpen = panel?.classList.contains('d-none');
            closePanels();
            if (shouldOpen && panel) {
                panel.classList.remove('d-none');
                button.setAttribute('aria-expanded', 'true');
            }
        });
    });

    const input = document.querySelector('#group-member-search');
    const clearButton = document.querySelector('#clear-group-member-search');
    const resultCount = document.querySelector('#group-member-result-count');
    const rows = [...document.querySelectorAll('.group-member-row')];
    const empty = document.querySelector('#group-member-empty');
    if (!input || !empty) return;
    const filterMembers = () => {
        const search = input.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach((row) => {
            const matches = !search || row.dataset.memberSearch.includes(search);
            row.classList.toggle('d-none', !matches);
            if (matches) visible += 1;
        });
        if (resultCount) resultCount.textContent = visible;
        if (clearButton) clearButton.classList.toggle('d-none', !search);
        empty.classList.toggle('d-none', visible !== 0);
    };
    input.addEventListener('input', filterMembers);
    clearButton?.addEventListener('click', () => {
        input.value = '';
        filterMembers();
        input.focus();
    });
})();
</script>
<script>
(() => {
    const chat = document.querySelector('#chat-messages');
    const form = document.querySelector('#chat-form');
    const canPost = chat?.dataset.canPost === '1';
    const content = document.querySelector('#chat-content');
    const submit = document.querySelector('#chat-submit');
    const status = document.querySelector('#chat-status');
    if (!chat) return;
    const url = chat.dataset.messagesUrl;
    const render = (messages) => messages.forEach((message) => {
        if (chat.querySelector('[data-message-id=\"' + message.id + '\"]')) return;
        const item = document.createElement('div');
        item.className = 'border rounded-4 p-3 mb-3 bg-white chat-message';
        item.dataset.messageId = message.id;
        const avatar = document.createElement(message.user.avatar_url ? 'img' : 'div');
        avatar.className = 'cem-avatar cem-avatar-message' + (message.user.avatar_url ? '' : ' cem-avatar-placeholder');
        if (message.user.avatar_url) {
            avatar.src = message.user.avatar_url;
            avatar.alt = 'Photo de ' + message.user.name;
        } else {
            avatar.textContent = message.user.name.charAt(0).toUpperCase();
        }
        const name = document.createElement('strong');
        const nameLink = document.createElement('a');
        nameLink.href = '/profile/' + message.user.id;
        nameLink.className = 'text-decoration-none';
        nameLink.textContent = message.user.name;
        name.append(nameLink);
        const meta = document.createElement('div');
        meta.className = 'cem-user-meta text-capitalize';
        meta.textContent = message.user.role + (message.user.position ? ' - ' + message.user.position : '');
        const date = document.createElement('div');
        date.className = 'small cem-soft';
        date.textContent = message.created_at;
        const body = document.createElement('p');
        body.className = 'mt-3 mb-0';
        body.textContent = message.content;
        const header = document.createElement('div');
        header.className = 'd-flex justify-content-between flex-wrap gap-2';
        const identity = document.createElement('div');
        const details = document.createElement('div');
        details.append(name, meta, date);
        identity.append(avatar, details);
        header.append(identity);
        const quote = document.createElement('div');
        if (message.reply_to) {
            quote.className = 'cem-reply-quote mt-3';
            quote.textContent = message.reply_to.user_name + ': ' + message.reply_to.content;
        }
        const actions = document.createElement('div');
        actions.className = 'd-flex align-items-center gap-2 flex-wrap mt-2 reaction-actions position-relative';
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'btn btn-light btn-sm reaction-trigger';
        trigger.dataset.reactionTarget = 'reaction-picker-' + message.id;
        trigger.title = 'Ajouter une réaction';
        trigger.textContent = '😊';
        const picker = document.createElement('div');
        picker.id = 'reaction-picker-' + message.id;
        picker.className = 'btn-group btn-group-sm reaction-picker d-none';
        ['👍', '❤️', '😂', '😮', '😢', '🙏'].forEach((reaction) => {
            const reactionForm = document.createElement('form');
            reactionForm.method = 'POST';
            reactionForm.action = '/messages/' + message.id + '/reactions';
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = document.querySelector('meta[name=csrf-token]').content;
            const reactionButton = document.createElement('button');
            reactionButton.type = 'submit';
            reactionButton.name = 'reaction';
            reactionButton.value = reaction;
            reactionButton.className = 'btn btn-light reaction-button';
            reactionButton.textContent = reaction;
            reactionForm.append(token, reactionButton);
            picker.append(reactionForm);
        });
        const replyButton = document.createElement('button');
        replyButton.type = 'button';
        replyButton.className = 'btn btn-outline-secondary btn-sm reply-message';
        replyButton.dataset.replyId = message.id;
        replyButton.dataset.replyUser = message.user.name;
        replyButton.dataset.replyContent = message.content;
        replyButton.textContent = 'Répondre';
        actions.append(trigger, picker);
        if (canPost) {
            actions.append(replyButton);
        }
        Object.entries(message.reactions || {}).forEach(([reaction, reactionData]) => {
            const summary = document.createElement('button');
            summary.type = 'button';
            summary.className = 'badge reaction-summary reaction-details-trigger' + (reactionData.reacted ? ' reaction-selected' : '');
            summary.textContent = reaction + ' ' + reactionData.count;
            const detail = document.createElement('div');
            detail.className = 'reaction-details-popover d-none';
            detail.id = 'reaction-details-' + message.id + '-' + reaction.codePointAt(0);
            const title = document.createElement('strong');
            title.textContent = reactionData.count + ' réaction(s)';
            detail.append(title);
            reactionData.users.forEach((reactor) => {
                const row = document.createElement('div');
                row.className = 'd-flex align-items-center gap-2 mt-2';
                const avatar = document.createElement(reactor.avatar_url ? 'img' : 'div');
                avatar.className = 'cem-avatar cem-member-avatar' + (reactor.avatar_url ? '' : ' cem-avatar-placeholder');
                if (reactor.avatar_url) { avatar.src = reactor.avatar_url; avatar.alt = 'Photo de ' + reactor.name; } else { avatar.textContent = reactor.name.charAt(0).toUpperCase(); }
                const info = document.createElement('div');
                const name = document.createElement('a');
                name.href = '/profile/' + reactor.id;
                name.className = 'text-decoration-none fw-semibold';
                name.textContent = reactor.name;
                const meta = document.createElement('div');
                meta.className = 'cem-user-meta text-capitalize';
                meta.textContent = reactor.role + (reactor.position ? ' - ' + reactor.position : '');
                info.append(name, meta);
                const icon = document.createElement('span');
                icon.className = 'ms-auto';
                icon.textContent = reaction;
                row.append(avatar, info, icon);
                detail.append(row);
            });
            summary.dataset.reactionTarget = detail.id;
            actions.append(summary, detail);
        });

        item.append(header, quote, body, actions);
        chat.append(item);
    });
    const refresh = async () => {
        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            if (!response.ok) throw new Error('offline');
            render((await response.json()).messages);
            status.textContent = 'Synchronisé';
            status.className = 'small text-success';
        } catch (error) {
            status.textContent = 'Hors connexion';
            status.className = 'small text-danger';
        }
    };
    if (form) {
        form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!content.value.trim()) return;
        submit.disabled = true;
        try {
            const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            if (!response.ok) throw new Error('send');
            content.value = '';
            await refresh();
        } finally { submit.disabled = false; }
        });
    }
    refresh();
    window.setInterval(refresh, 2000);
})();
</script>
<script>
(() => {
    const replyPreview = document.querySelector('#reply-preview');
    const replyUser = document.querySelector('#reply-user');
    const replyText = document.querySelector('#reply-text');
    const replyId = document.querySelector('#reply-to-id');
    const content = document.querySelector('#chat-content');
    const cancel = document.querySelector('#cancel-reply');
    if (!replyPreview || !replyId || !content) return;
    document.addEventListener('click', (event) => {
        const button = event.target.closest('.reply-message');
        if (!button) return;
        replyId.value = button.dataset.replyId;
        replyUser.textContent = button.dataset.replyUser;
        replyText.textContent = button.dataset.replyContent;
        replyPreview.classList.remove('d-none');
        content.focus();
    });
    cancel.addEventListener('click', () => {
        replyId.value = '';
        replyPreview.classList.add('d-none');
    });
})();
</script>
<script>
(() => {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('.reaction-trigger, .reaction-details-trigger');
        document.querySelectorAll('.reaction-picker, .reaction-details-popover').forEach((popup) => {
            if (!trigger || popup.id !== trigger.dataset.reactionTarget) popup.classList.add('d-none');
        });
        if (trigger) {
            document.getElementById(trigger.dataset.reactionTarget)?.classList.toggle('d-none');
            event.stopPropagation();
        }
    });
})();
</script>
@endpush

<script>
(() => {
    const input = document.querySelector('#chat-content');
    const indicator = document.querySelector('#typing-indicator');
    const label = document.querySelector('#typing-label');
    if (!input || !indicator) return;
    let timer;
    const typingUrl = '{{ route('messages.typing', $group) }}';
    const statusUrl = '{{ route('messages.typing.status', $group) }}';
    input.addEventListener('input', () => {
        window.clearTimeout(timer);
        if (!input.value.trim()) return;
        fetch(typingUrl, { method: 'POST', body: new FormData(document.querySelector('#chat-form')), headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        timer = window.setTimeout(() => {}, 3000);
    });
    window.setInterval(async () => {
        const response = await fetch(statusUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        if (!response.ok) return;
        const users = (await response.json()).users;
        indicator.classList.toggle('d-none', users.length === 0);
        label.textContent = users.length ? users.map((user) => user.name).join(', ') + (users.length === 1 ? ' est en train d'écrire...' : ' sont en train d'écrire...') : '';
    }, 2000);
})();
</script>
@endsection

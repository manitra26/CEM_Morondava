@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<style>
    .private-messages-page { min-height: calc(100vh - 8rem); }
    .private-messages-card { height: calc(100vh - 11rem); min-height: 34rem; display: grid; grid-template-columns: minmax(18rem, 23rem) minmax(0, 1fr); overflow: hidden; }
    .private-contacts { width: auto; min-width: 0; border-right: 1px solid rgba(23,52,59,.1); display: flex; flex-direction: column; }
    .private-contact-list { min-height: 0; overflow-y: auto; }
    .private-contact { display: flex; gap: .75rem; align-items: center; padding: .8rem 1rem; color: inherit; text-decoration: none; border-bottom: 1px solid rgba(23,52,59,.07); }
    .private-contact:hover, .private-contact.active { background: rgba(28,124,108,.1); }
    .private-conversation { min-width: 0; min-height: 0; height: 100%; display: flex; flex-direction: column; }
    .private-conversation-body { min-height: 0; height: 0; flex: 1 1 auto; overflow-y: auto; padding: 1.25rem; background: radial-gradient(circle at top, rgba(28,124,108,.08), transparent 45%); }
    .private-bubble { max-width: min(75%, 42rem); padding: .7rem .9rem; border-radius: 1rem; box-shadow: 0 5px 14px rgba(23,52,59,.08); }
    .private-bubble.mine { margin-left: auto; color: white; background: linear-gradient(135deg, #1c7c6c, #165e54); border-bottom-right-radius: .25rem; }
    .private-bubble.theirs { background: white; border-bottom-left-radius: .25rem; }
    .private-attachment-preview { display: flex; align-items: center; gap: .75rem; padding: .65rem; margin-bottom: .75rem; border: 1px solid rgba(28,124,108,.25); border-radius: .85rem; background: rgba(28,124,108,.06); } .private-attachment-preview img { width: 4rem; height: 4rem; object-fit: cover; border-radius: .55rem; } .private-composer { border-top: 1px solid rgba(23,52,59,.1); }
    .private-attachment { display: inline-flex; align-items: center; gap: .5rem; padding: .45rem .7rem; border-radius: .7rem; background: rgba(216,124,77,.12); }
    @media (max-width: 767.98px) { .private-messages-card { height: auto; min-height: 0; display: block; } .private-contacts { width: 100%; max-height: 18rem; border-right: 0; border-bottom: 1px solid rgba(23,52,59,.1); } .private-conversation { height: 32rem; min-height: 32rem; } .private-bubble { max-width: 88%; } }
    .private-image-trigger { display: block; border: 0; padding: 0; margin: 0; background: transparent; cursor: zoom-in; }
    .private-bubble.mine .private-reply-button { color: #fff; font-weight: 600; text-decoration: underline; }
    .private-bubble.theirs .private-reply-button { color: #165e54; font-weight: 600; }
    .private-delete-menu { position: relative; }
    .private-delete-menu summary { color: inherit; cursor: pointer; list-style: none; }
    .private-delete-menu summary::-webkit-details-marker { display: none; }
    .private-delete-picker { position: absolute; z-index: 10; right: 0; bottom: 1.8rem; display: grid; gap: .35rem; min-width: 12rem; padding: .5rem; border-radius: .5rem; background: white; box-shadow: 0 8px 22px rgba(23,52,59,.2); } .private-image-trigger img { display: block; max-width: min(24rem, 100%); max-height: 24rem; border-radius: .7rem; object-fit: cover; } .private-message-actions { min-height: 1.5rem; } .private-reaction-menu { position: relative; } .private-reaction-menu summary { list-style: none; cursor: pointer; } .private-reaction-menu summary::-webkit-details-marker { display: none; } .private-reaction-picker { position: absolute; z-index: 10; bottom: 1.8rem; left: 0; display: flex; gap: .2rem; padding: .35rem; border-radius: 999px; background: white; box-shadow: 0 8px 22px rgba(23,52,59,.2); } .private-reaction-picker form { display: inline-flex; } .private-reaction-picker button { border: 0; background: transparent; border-radius: 50%; padding: .35rem; font-size: 1.2rem; } .private-reply-quote { border-left: 3px solid var(--cem-accent); padding-left: .6rem; opacity: .8; } .private-image-modal .modal-content { background: #111b1d; color: white; } .private-image-modal img { max-height: 70vh; max-width: 100%; object-fit: contain; } .private-image-modal .modal-footer { border-top-color: rgba(255,255,255,.15); } .private-image-modal.is-open { display: flex; align-items: center; background: rgba(0,0,0,.72); } .private-image-modal.is-open .modal-dialog { width: min(92vw, 70rem); margin: auto; }
</style>

<div class="private-messages-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1">Messages</h1>
            <p class="cem-soft mb-0">Recherchez un membre et communiquez directement avec lui.</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Voir les membres</a>
    </div>

    <div class="card cem-card private-messages-card">
        <aside class="private-contacts">
            <div class="p-3 border-bottom">
                <form method="GET" action="{{ route('private.messages.index') }}" class="d-flex gap-2">
                    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Rechercher un nom ou telephone" autocomplete="off">
                    <button class="btn btn-cem" type="submit">Rechercher</button>
                </form>
            </div>
            <div class="px-3 py-2 small cem-soft">{{ $users->count() }} membre(s)</div>
            <div class="private-contact-list">
                @forelse($users as $contact)
                    <a href="{{ route('private.messages.user', $contact) }}" class="private-contact {{ $user?->is($contact) ? 'active' : '' }}">
                        @if($contact->avatar_path)
                            <img src="{{ route('profile.avatar', $contact) }}" alt="Photo de {{ $contact->name }}" class="cem-avatar">
                        @else
                            <span class="cem-avatar cem-avatar-placeholder">{{ strtoupper(substr($contact->name, 0, 1)) }}</span>
                        @endif
                        <span class="flex-grow-1 min-w-0">
                            <strong class="d-block text-truncate">{{ $contact->name }}</strong>
                            <span class="small cem-soft text-truncate d-block">{{ $contact->position ?: ucfirst($contact->role) }}{{ $contact->phone ? ' - '.$contact->phone : '' }}</span>
                        </span>
                    </a>
                @empty
                    <div class="p-3 small cem-soft">Aucun membre trouve.</div>
                @endforelse
            </div>
        </aside>

        <section class="private-conversation">
            @if($user)
                <header class="p-3 border-bottom d-flex align-items-center gap-3">
                    @if($user->avatar_path)
                        <img src="{{ route('profile.avatar', $user) }}" alt="Photo de {{ $user->name }}" class="cem-avatar">
                    @else
                        <span class="cem-avatar cem-avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                    <div>
                        <h2 class="h5 mb-1">{{ $user->name }}</h2>
                        <div class="small cem-soft">{{ $user->position ?: ucfirst($user->role) }}{{ $user->phone ? ' - '.$user->phone : '' }}</div>
                    </div>
                </header>
                <div class="private-conversation-body" id="private-message-list">
                    @foreach($messages as $message)
                        @php($isMine = $message->sender_id === auth()->id())
                        @php($reactionGroups = $message->reactions->groupBy('reaction'))
                        @php($myReaction = $message->reactions->firstWhere('user_id', auth()->id())?->reaction)
                        <div class="d-flex mb-3 {{ $isMine ? 'justify-content-end' : '' }}">
                            <div class="private-bubble {{ $isMine ? 'mine' : 'theirs' }}">
                                @if($message->replyTo)
                                    <div class="private-reply-quote mb-2"><strong>{{ $message->replyTo->sender->name }}</strong><br>{{ Illuminate\Support\Str::limit($message->replyTo->content ?: $message->replyTo->attachment_name, 100) }}</div>
                                @endif
                                @if($message->content)<div class="text-break">{{ $message->content }}</div>@endif
                                @if($message->attachment_path)
                                    @if(str_starts_with((string) $message->attachment_mime, 'image/'))
                                        <a href="{{ route('private.messages.file', $message) }}" class="private-image-trigger mt-2" target="_blank" rel="noopener" title="Voir l'image {{ $message->attachment_name }}">
                                            <img src="{{ route('private.messages.file', $message) }}" alt="{{ $message->attachment_name }}">
                                        </a>
                                        <a href="{{ route('private.messages.download', $message) }}" class="private-attachment mt-2 text-decoration-none {{ $isMine ? 'text-white' : '' }}" download>
                                            <span>Télécharger l'image</span>
                                        </a>
                                    @else
                                        <a href="{{ route('private.messages.download', $message) }}" class="private-attachment mt-2 text-decoration-none {{ $isMine ? 'text-white' : '' }}"><span>Fichier</span><span class="text-truncate">{{ $message->attachment_name }}</span></a>
                                    @endif
                                @endif
                                <div class="small mt-2 opacity-75 text-end">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                                <div class="private-message-actions d-flex align-items-center gap-2 flex-wrap mt-2">
                                    @foreach($reactionGroups as $reaction => $items)
                                        <span class="badge reaction-summary {{ $myReaction === $reaction ? 'reaction-selected' : '' }}">{{ $reaction }} {{ $items->count() }}</span>
                                    @endforeach
                                    <details class="private-reaction-menu">
                                        <summary title="Reagir">😊</summary>
                                        <div class="private-reaction-picker">
                                            @foreach(['👍', '❤️', '😂', '😮', '😢', '🙏'] as $reaction)
                                                <form method="POST" action="{{ route('private.messages.react', $message) }}">
                                                    @csrf
                                                    <button type="submit" name="reaction" value="{{ $reaction }}" title="{{ $reaction }}">{{ $reaction }}</button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </details>
                                    <button type="button" class="btn btn-sm btn-link private-reply-button" data-reply-id="{{ $message->id }}" data-reply-user="{{ $message->sender->name }}" data-reply-content="{{ $message->content ?: $message->attachment_name }}">Répondre</button>
                                    <details class="private-delete-menu">
                                        <summary title="Supprimer le message">&#128465;</summary>
                                        <div class="private-delete-picker">
                                            <form method="POST" action="{{ route('private.messages.destroy', $message) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="scope" value="me">
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Supprimer pour moi</button>
                                            </form>
                                            @if($isMine)
                                                <form method="POST" action="{{ route('private.messages.destroy', $message) }}" onsubmit="return confirm('Supprimer ce message pour tout le monde ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="scope" value="everyone">
                                                    <button type="submit" class="btn btn-sm btn-danger w-100">Supprimer pour tout le monde</button>
                                                </form>
                                            @endif
                                        </div>
                                    </details>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <form id="private-message-form" method="POST" action="{{ route('private.messages.store', $user) }}" enctype="multipart/form-data" class="private-composer p-3">
                    @csrf
                    <input type="hidden" name="reply_to_id" id="private-reply-to-id">
                    <div id="private-reply-preview" class="alert alert-info py-2 d-none">
                        <span>Réponse à <strong id="private-reply-user"></strong> : <span id="private-reply-text"></span></span>
                        <button type="button" id="private-reply-cancel" class="btn-close float-end"></button>
                    </div>
                    <div id="private-attachment-preview" class="private-attachment-preview d-none">
                        <div id="private-attachment-thumbnail"></div>
                        <div class="flex-grow-1 min-w-0">
                            <strong id="private-attachment-name" class="d-block text-truncate"></strong>
                            <span id="private-attachment-size" class="small cem-soft"></span>
                        </div>
                        <button type="button" id="private-attachment-remove" class="btn btn-outline-danger btn-sm">Retirer</button>
                    </div>
                    <div class="d-flex gap-2 align-items-end">
                        <label class="btn btn-outline-secondary mb-0" title="Ajouter une photo ou un fichier">
                            <span>+</span>
                            <input id="private-attachment-input" type="file" name="attachment" class="d-none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.txt">
                        </label>
                        <textarea name="content" rows="2" class="form-control" placeholder="Ecrire un message...">{{ old('content') }}</textarea>
                        <button type="submit" class="btn btn-cem">Envoyer</button>
                    </div>
                    <div class="small cem-soft mt-2">Photos, PDF, Word, Excel, PowerPoint, ZIP ou fichiers texte, 20 Mo maximum.</div>
                </form>
            @else
                <div class="h-100 d-grid place-items-center text-center p-4">
                    <div><h2 class="h4">Choisissez un membre</h2><p class="cem-soft mb-0">Utilisez la recherche a gauche pour commencer une conversation privee.</p></div>
                </div>
            @endif
        </section>
    </div>
</div>
<div class="modal fade private-image-modal" id="private-image-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-secondary">
                <h2 class="modal-title h5" id="private-image-modal-title">Apercu de l image</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center"><img id="private-image-modal-image" src="" alt=""></div>
            <div class="modal-footer justify-content-between">
                <form id="private-modal-reaction-form" method="POST">
                    @csrf
                    <div class="d-flex gap-1">
                        @foreach(['👍', '❤️', '😂', '😮', '😢', '🙏'] as $reaction)
                            <button type="submit" name="reaction" value="{{ $reaction }}" class="btn btn-dark">{{ $reaction }}</button>
                        @endforeach
                    </div>
                </form>
                <a id="private-image-modal-download" href="#" class="btn btn-light" download>Télécharger</a>
            </div>
        </div>
    </div>
</div>
<script>
(() => {
    const input = document.querySelector('#private-attachment-input');
    const preview = document.querySelector('#private-attachment-preview');
    const thumbnail = document.querySelector('#private-attachment-thumbnail');
    const name = document.querySelector('#private-attachment-name');
    const size = document.querySelector('#private-attachment-size');
    const remove = document.querySelector('#private-attachment-remove');
    if (!input || !preview || !thumbnail || !name || !size || !remove) return;

    const formatSize = (bytes) => bytes < 1024 * 1024
        ? `${Math.max(1, Math.round(bytes / 1024))} Ko`
        : `${(bytes / (1024 * 1024)).toFixed(1)} Mo`;

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;
        name.textContent = file.name;
        size.textContent = formatSize(file.size);
        thumbnail.replaceChildren();
        if (file.type.startsWith('image/')) {
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = 'Apercu de ' + file.name;
            thumbnail.append(image);
        } else {
            const badge = document.createElement('span');
            badge.className = 'badge cem-badge p-3';
            badge.textContent = 'Fichier';
            thumbnail.append(badge);
        }
        preview.classList.remove('d-none');
    });

    remove.addEventListener('click', () => {
        input.value = '';
        thumbnail.replaceChildren();
        preview.classList.add('d-none');
    });
})();
</script>
<script>
(() => {
    const replyButtons = document.querySelectorAll('.private-reply-button');
    const replyId = document.querySelector('#private-reply-to-id');
    const replyPreview = document.querySelector('#private-reply-preview');
    const replyUser = document.querySelector('#private-reply-user');
    const replyText = document.querySelector('#private-reply-text');
    const replyCancel = document.querySelector('#private-reply-cancel');
    replyButtons.forEach((button) => button.addEventListener('click', () => {
        replyId.value = button.dataset.replyId;
        replyUser.textContent = button.dataset.replyUser;
        replyText.textContent = button.dataset.replyContent;
        replyPreview.classList.remove('d-none');
        document.querySelector('#private-message-form textarea[name="content"]')?.focus();
    }));
    replyCancel?.addEventListener('click', () => {
        replyId.value = '';
        replyPreview.classList.add('d-none');
    });
})();

(() => {
    const modalElement = document.querySelector('#private-image-modal');
    const image = document.querySelector('#private-image-modal-image');
    const title = document.querySelector('#private-image-modal-title');
    const download = document.querySelector('#private-image-modal-download');
    const reactionForm = document.querySelector('#private-modal-reaction-form');
    const closeButton = modalElement?.querySelector('[data-bs-dismiss="modal"]');
    if (!modalElement || !image || !download || !reactionForm) return;

    const closeModal = () => {
        modalElement.classList.remove('is-open');
        modalElement.setAttribute('aria-hidden', 'true');
        image.removeAttribute('src');
    };

    document.querySelectorAll('button.private-image-trigger').forEach((button) => button.addEventListener('click', () => {
        image.src = button.dataset.imageUrl;
        image.alt = button.dataset.imageName || 'Image';
        title.textContent = button.dataset.imageName || 'Apercu de l image';
        download.href = button.dataset.downloadUrl;
        reactionForm.action = '/private-messages/' + button.dataset.messageId + '/reactions';
        modalElement.classList.add('is-open');
        modalElement.setAttribute('aria-hidden', 'false');
    }));

    closeButton?.addEventListener('click', closeModal);
    modalElement.addEventListener('click', (event) => {
        if (event.target === modalElement) closeModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modalElement.classList.contains('is-open')) closeModal();
    });
})();
</script>
@endsection
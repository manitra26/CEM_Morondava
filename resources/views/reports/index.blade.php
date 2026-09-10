@extends('layouts.app')

@section('title', 'Rapports journaliers')

@section('content')
<div class="reports-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Rapports journaliers</h1>
            <p class="cem-soft mb-0">Envoyez vos rapports et retrouvez facilement l'historique.</p>
        </div>
        <button type="button" class="btn btn-cem px-4" data-bs-toggle="modal" data-bs-target="#report-create-modal">
            + Envoyer un rapport
        </button>
    </div>

    <div class="card cem-card reports-history-card">
        <div class="card-header cem-card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <strong>Historique des rapports</strong>
                <span class="badge cem-badge">{{ $reports->count() }} rapport(s)</span>
            </div>
            <form method="GET" action="{{ route('reports.index') }}" class="reports-filter-form">
                <div class="row g-2 align-items-end">
                    <div class="col-xl-5 col-lg-4">
                        <label for="report-search" class="form-label small mb-1">Rechercher</label>
                        <input id="report-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Titre, contenu ou auteur">
                    </div>
                    @if($isDirector)
                        <div class="col-xl-3 col-lg-3">
                            <label for="report-user" class="form-label small mb-1">Utilisateur</label>
                            <select id="report-user" name="user_id" class="form-select">
                                <option value="">Tous les utilisateurs</option>
                                @foreach($reportUsers as $reportUser)
                                    <option value="{{ $reportUser->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $reportUser->id)>{{ $reportUser->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-xl-2 col-lg-2 col-sm-6">
                        <label for="report-date-from" class="form-label small mb-1">Du</label>
                        <input id="report-date-from" type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-xl-2 col-lg-2 col-sm-6">
                        <label for="report-date-to" class="form-label small mb-1">Au</label>
                        <input id="report-date-to" type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-cem">Filtrer</button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body reports-history-scroll">
            @forelse ($reports as $report)
                <article class="border rounded-4 p-3 mb-3 bg-white report-history-item">
                    <div class="d-flex justify-content-between gap-3 flex-wrap">
                        <div>
                            <h5 class="mb-1">{{ $report->title }}</h5>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                @if($report->user->avatar_path)
                                    <img src="{{ route('profile.avatar', $report->user) }}" alt="Photo de {{ $report->user->name }}" class="cem-avatar cem-member-avatar">
                                @else
                                    <span class="cem-avatar cem-member-avatar cem-avatar-placeholder">{{ strtoupper(substr($report->user->name, 0, 1)) }}</span>
                                @endif
                                <div>
                                    <div class="small">Par <a href="{{ route('profile.show', $report->user) }}" class="text-decoration-none fw-semibold">{{ $report->user->name }}</a></div>
                                    <div class="cem-user-meta text-capitalize">{{ $report->user->role }}{{ $report->user->position ? ' - '.$report->user->position : '' }}</div>
                                    <div class="small cem-soft">{{ $report->submitted_at?->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap align-items-start">
                            @if($report->attachment_path)
                                <a href="{{ route('reports.download', $report) }}" class="btn btn-outline-secondary btn-sm">Télécharger</a>
                            @endif
                            @if($isDirector || auth()->id() === $report->user_id)
                                <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Supprimer ce rapport ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <p class="mt-3 mb-0">{{ $report->content }}</p>
                </article>
            @empty
                <div class="text-center cem-soft py-5">Aucun rapport ne correspond à votre recherche.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal fade" id="report-create-modal" tabindex="-1" aria-labelledby="report-create-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content cem-card">
            <div class="modal-header cem-card-header">
                <h2 class="modal-title h5 mb-0" id="report-create-modal-title">Envoyer un rapport</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="report-title" class="form-label">Titre</label>
                        <input id="report-title" type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="report-content" class="form-label">Contenu du rapport</label>
                        <textarea id="report-content" name="content" rows="8" class="form-control @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="report-attachment" class="form-label">Fichier joint</label>
                        <input id="report-attachment" type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                        <div class="form-text">PDF, Excel, Word, PowerPoint, image, texte ou ZIP jusqu'à 20 Mo.</div>
                        @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-cem">Envoyer le rapport</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($errors->has('title') || $errors->has('content') || $errors->has('attachment'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('report-create-modal')).show();
    });
</script>
@endif
@endpush
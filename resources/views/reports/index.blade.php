@extends('layouts.app')

@section('title', 'Rapports journaliers')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card cem-card">
            <div class="card-header cem-card-header">
                <strong>Envoyer un rapport</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Titre</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contenu du rapport</label>
                        <textarea name="content" rows="8" class="form-control" required>{{ old('content') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fichier joint</label>
                        <input type="file" name="attachment" class="form-control">
                        <div class="form-text">PDF, Excel, Word, PowerPoint, image, texte ou ZIP jusqu'à 20 Mo.</div>
                    </div>
                    <button type="submit" class="btn btn-cem w-100">Envoyer le rapport</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card cem-card">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Historique des rapports</strong>
                <span class="badge cem-badge">{{ $reports->count() }} rapport(s)</span>
            </div>
            <div class="card-body">
                @forelse ($reports as $report)
                    <div class="border rounded-4 p-3 mb-3 bg-white">
                        <div class="d-flex justify-content-between gap-3 flex-wrap">
                            <div>
                                <h5 class="mb-1">{{ $report->title }}</h5>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    @if($report->user->avatar_path)
                                        <img src="{{ route('profile.avatar', $report->user) }}" alt="Photo de {{ $report->user->name }}" class="cem-avatar cem-member-avatar">
                                    @else
                                        <span class="cem-avatar cem-member-avatar cem-avatar-placeholder">{{ strtoupper(substr($report->user->name, 0, 1)) }}</span>
                                    @endif
                                    <div><div class="small">Par <a href="{{ route('profile.show', $report->user) }}" class="text-decoration-none fw-semibold">{{ $report->user->name }}</a></div><div class="cem-user-meta text-capitalize">{{ $report->user->role }}{{ $report->user->position ? ' - '.$report->user->position : '' }}</div><div class="small cem-soft">{{ $report->submitted_at?->format('d/m/Y H:i') }}</div></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
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
                    </div>
                @empty
                    <div class="text-center cem-soft py-4">Aucun rapport enregistré.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

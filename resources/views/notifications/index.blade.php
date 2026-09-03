@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="card cem-card">
    <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
        <strong>Notifications internes</strong>
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-cem">Tout marquer comme lu</button>
        </form>
    </div>
    <div class="card-body">
        @forelse ($notifications as $notification)
            <div class="border rounded-4 p-3 mb-3 bg-white d-flex justify-content-between gap-3 flex-wrap">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h5 class="mb-0">{{ $notification->title }}</h5>
                        @if(! $notification->is_read)
                            <span class="badge cem-badge">Non lue</span>
                        @else
                            <span class="badge text-bg-secondary">Lue</span>
                        @endif
                    </div>
                    <div class="small cem-soft mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                    <p class="mt-2 mb-0">{{ $notification->content }}</p>
                </div>
                @if(! $notification->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm">Marquer lue</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="text-center cem-soft py-4">Aucune notification pour le moment.</div>
        @endforelse
    </div>
</div>
@endsection

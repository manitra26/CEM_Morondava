<?php

namespace App\Http\Controllers;

use App\Models\InternalNotification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $isDirector = $user->role === 'directeur';

        $reports = Report::with('user')
            ->when(! $isDirector, fn ($query) => $query->where('user_id', $user->id))
            ->latest('submitted_at')
            ->get();

        return view('reports.index', compact('reports', 'isDirector'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,txt,jpg,jpeg,png,gif,webp,zip',
                'extensions:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,txt,jpg,jpeg,png,gif,webp,zip',
                'max:20480',
            ],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('reports');
        }

        $report = Report::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'attachment_path' => $attachmentPath,
            'submitted_at' => now(),
        ]);

        $directors = User::where('role', 'directeur')->get();
        foreach ($directors as $director) {
            InternalNotification::create([
                'user_id' => $director->id,
                'actor_id' => $request->user()->id,
                'title' => 'Nouveau rapport journalier',
                'content' => sprintf('%s a envoyé le rapport "%s".', $request->user()->name, $report->title),
                'type' => 'report',
                'data' => ['report_id' => $report->id],
                'is_read' => false,
                'read_at' => null,
            ]);
        }

        return back()->with('success', 'Le rapport a été envoyé avec succès.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user->role === 'directeur' || $report->user_id === $user->id, 403);

        if ($report->attachment_path && Storage::exists($report->attachment_path)) {
            Storage::delete($report->attachment_path);
        }

        $report->delete();

        return back()->with('success', 'Le rapport a été supprimé.');
    }

    public function download(Report $report)
    {
        abort_unless($report->attachment_path && Storage::exists($report->attachment_path), 404);

        return Storage::download($report->attachment_path);
    }
}

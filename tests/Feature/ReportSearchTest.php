<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReportSearchTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_director_can_search_report_author_without_case_sensitivity(): void
    {
        $director = User::factory()->create(['role' => 'directeur']);
        $jean = User::factory()->create(['name' => 'Jean Rakoto']);
        $aina = User::factory()->create(['name' => 'Aina Randria']);

        Report::create([
            'user_id' => $jean->id,
            'title' => 'Rapport de Jean',
            'content' => 'Compte rendu des activités.',
            'submitted_at' => now(),
        ]);
        Report::create([
            'user_id' => $aina->id,
            'title' => 'Rapport de Aina',
            'content' => 'Autre compte rendu.',
            'submitted_at' => now(),
        ]);

        $this->actingAs($director)
            ->get(route('reports.index', ['search' => 'jean']))
            ->assertSee('Rapport de Jean')
            ->assertDontSee('Rapport de Aina')
            ->assertDontSee('id="report-user"', false);
    }
}

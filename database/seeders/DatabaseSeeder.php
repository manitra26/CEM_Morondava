<?php

namespace Database\Seeders;

use App\Models\DiscussionGroup;
use App\Models\InternalNotification;
use App\Models\Message;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $director = User::updateOrCreate(
            ['email' => 'm.randria@cem-morondava.mg'],
            [
                'name' => 'Marc Randria',
                'password' => Hash::make('password'),
                'role' => 'directeur',
                'position' => 'Directeur du centre',
                'department' => 'Direction',
                'phone' => '+261340000001',
                'bio' => 'Supervise la coordination interne du CEM.',
            ]
        );

        $employee = User::updateOrCreate(
            ['email' => 'j.rakoto@cem-morondava.mg'],
            [
                'name' => 'Jean Rakoto',
                'password' => Hash::make('password'),
                'role' => 'employe',
                'position' => 'Guide écotouristique',
                'department' => 'Exploitation',
                'phone' => '+261340000002',
                'bio' => 'Envoie les rapports journaliers et participe aux échanges.',
            ]
        );

        $employee2 = User::updateOrCreate(
            ['email' => 's.rasoa@cem-morondava.mg'],
            [
                'name' => 'Sofia Rasoa',
                'password' => Hash::make('password'),
                'role' => 'employe',
                'position' => 'Responsable accueil',
                'department' => 'Accueil',
                'phone' => '+261340000003',
                'bio' => 'Suit les demandes et partage les informations utiles.',
            ]
        );

        $group = DiscussionGroup::updateOrCreate(
            ['name' => 'Equipe exploitation'],
            [
                'description' => 'Coordination quotidienne des activités et des affectations.',
                'created_by' => $director->id,
            ]
        );

        $group->members()->syncWithoutDetaching([
            $director->id => ['joined_at' => now()],
            $employee->id => ['joined_at' => now()],
            $employee2->id => ['joined_at' => now()],
        ]);

        $report = Report::updateOrCreate(
            ['title' => 'Rapport journalier du 31/08/2026', 'user_id' => $employee->id],
            [
                'content' => 'Activités réalisées, visiteurs reçus, difficultés constatées et besoins du jour.',
                'attachment_path' => null,
                'submitted_at' => now(),
            ]
        );

        Message::updateOrCreate(
            [
                'discussion_group_id' => $group->id,
                'user_id' => $director->id,
                'content' => 'Merci de transmettre les rapports avant 17h chaque jour.',
            ],
            [
                'status' => 'active',
            ]
        );

        InternalNotification::updateOrCreate(
            [
                'user_id' => $director->id,
                'title' => 'Rapport reçu',
            ],
            [
                'content' => 'Le rapport journalier de Jean Rakoto a bien été enregistré.',
                'type' => 'report',
                'data' => ['report_id' => $report->id],
                'is_read' => false,
                'read_at' => null,
            ]
        );
    }
}

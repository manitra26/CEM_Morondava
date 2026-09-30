<?php

namespace Tests\Feature;

use App\Models\InternalNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class NotificationBadgeTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_navigation_badge_counts_unread_message_and_report_notifications(): void
    {
        $recipient = User::factory()->create(['role' => 'directeur']);
        $sender = User::factory()->create();
        $otherUser = User::factory()->create();

        $messageNotification = InternalNotification::create([
            'user_id' => $recipient->id,
            'actor_id' => $sender->id,
            'title' => 'Nouveau message de groupe',
            'content' => 'Un message a été publié dans un groupe.',
            'type' => 'message',
            'is_read' => false,
        ]);
        InternalNotification::create([
            'user_id' => $recipient->id,
            'actor_id' => $sender->id,
            'title' => 'Nouveau rapport journalier',
            'content' => 'Un rapport a été envoyé.',
            'type' => 'report',
            'is_read' => false,
        ]);
        InternalNotification::create([
            'user_id' => $recipient->id,
            'actor_id' => $sender->id,
            'title' => 'Nouveau message privé',
            'content' => 'Un message a été envoyé.',
            'type' => 'private_message',
            'is_read' => true,
        ]);
        InternalNotification::create([
            'user_id' => $otherUser->id,
            'actor_id' => $sender->id,
            'title' => 'Notification d’un autre utilisateur',
            'content' => 'Cette alerte ne doit pas être comptée.',
            'type' => 'report',
            'is_read' => false,
        ]);

        $this->actingAs($recipient)
            ->get(route('notifications.index'))
            ->assertSee('class="nav-message-count"', false)
            ->assertSee('aria-label="2 notification(s) non lue(s)"', false);

        $this->post(route('notifications.read', $messageNotification))
            ->assertRedirect();

        $this->get(route('notifications.index'))
            ->assertSee('aria-label="1 notification(s) non lue(s)"', false);

        $this->post(route('notifications.readAll'))
            ->assertRedirect();

        $this->get(route('notifications.index'))
            ->assertDontSee('<span class="nav-message-count"', false);
    }
}

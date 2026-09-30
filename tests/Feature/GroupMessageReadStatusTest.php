<?php

namespace Tests\Feature;

use App\Models\DiscussionGroup;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class GroupMessageReadStatusTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_last_group_message_shows_seen_and_unseen_status(): void
    {
        $author = User::factory()->create();
        $reader = User::factory()->create();

        $group = DiscussionGroup::create([
            'name' => 'Equipe exploitation',
            'description' => 'Discussion de groupe',
            'created_by' => $author->id,
        ]);

        $group->members()->attach([
            $author->id => ['can_post' => true],
            $reader->id => ['can_post' => true],
        ]);

        $firstMessage = Message::create([
            'discussion_group_id' => $group->id,
            'user_id' => $author->id,
            'content' => 'Bonjour',
        ]);

        $secondMessage = Message::create([
            'discussion_group_id' => $group->id,
            'user_id' => $author->id,
            'content' => 'Encore un message',
        ]);

        $response = $this->actingAs($author)
            ->get(route('groups.show', $group));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(2, substr_count($response->getContent(), 'group-message-status sent'));
        $response->assertSee('✓');
        $response->assertDontSee('✓✓');

        MessageRead::create([
            'message_id' => $firstMessage->id,
            'user_id' => $reader->id,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($author)
            ->get(route('groups.show', $group));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, substr_count($response->getContent(), 'group-message-status seen'));
        $response->assertSee('✓✓');

        MessageRead::create([
            'message_id' => $secondMessage->id,
            'user_id' => $reader->id,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($author)
            ->get(route('groups.show', $group));

        $response->assertOk();
        $response->assertSee('✓✓');
    }

    public function test_groups_list_shows_unread_message_badge_for_new_message(): void
    {
        $author = User::factory()->create();
        $reader = User::factory()->create();

        $group = DiscussionGroup::create([
            'name' => 'Groupe alert',
            'description' => 'Alerte de message',
            'created_by' => $author->id,
        ]);

        $group->members()->attach([
            $author->id => ['can_post' => true],
            $reader->id => ['can_post' => true],
        ]);

        Message::create([
            'discussion_group_id' => $group->id,
            'user_id' => $author->id,
            'content' => 'Nouveau message',
        ]);

        $response = $this->actingAs($reader)
            ->get(route('groups.index'));

        $response->assertOk();
        $response->assertSee('group-alert-badge');
        $response->assertSee('1');
    }
}

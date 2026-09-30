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
}

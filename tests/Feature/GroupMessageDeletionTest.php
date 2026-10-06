<?php

namespace Tests\Feature;

use App\Models\DiscussionGroup;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class GroupMessageDeletionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_member_can_delete_a_group_message_only_for_themselves(): void
    {
        [$author, $member, $group, $message] = $this->createMessage();

        $response = $this->actingAs($member)
            ->delete(route('messages.destroy', $message), ['scope' => 'me']);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Message supprimé pour vous.');
        $this->assertDatabaseHas('group_message_hidden', [
            'message_id' => $message->id,
            'user_id' => $member->id,
        ]);
        $this->assertDatabaseHas('messages', ['id' => $message->id, 'deleted_at' => null]);

        $this->actingAs($member)
            ->get(route('groups.show', $group))
            ->assertDontSee('Message visible uniquement pour le membre');

        $this->actingAs($author)
            ->get(route('groups.show', $group))
            ->assertSee('Message visible uniquement pour le membre')
            ->assertSee('Supprimer pour tout le monde');

        $this->actingAs($member)
            ->get(route('messages.index', $group))
            ->assertJsonMissing(['id' => $message->id]);
    }

    public function test_only_author_or_director_can_delete_group_message_for_everyone(): void
    {
        [$author, $member, $group, $message] = $this->createMessage();

        $this->actingAs($member)
            ->delete(route('messages.destroy', $message), ['scope' => 'everyone'])
            ->assertForbidden();
        $this->assertDatabaseHas('messages', ['id' => $message->id, 'deleted_at' => null]);

        $this->actingAs($author)
            ->get(route('groups.show', $group))
            ->assertSee('Supprimer pour moi')
            ->assertSee('Supprimer pour tout le monde');

        $response = $this->actingAs($author)
            ->delete(route('messages.destroy', $message), ['scope' => 'everyone']);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Message supprimé pour tout le monde.');
        $this->assertSoftDeleted('messages', ['id' => $message->id]);

        $this->actingAs($member)
            ->get(route('messages.index', $group))
            ->assertJsonMissing(['id' => $message->id])
            ->assertJsonPath('hidden_message_ids.0', $message->id);
    }

    public function test_non_member_cannot_hide_a_group_message(): void
    {
        [, , , $message] = $this->createMessage();
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->delete(route('messages.destroy', $message), ['scope' => 'me'])
            ->assertForbidden();

        $this->assertDatabaseMissing('group_message_hidden', [
            'message_id' => $message->id,
            'user_id' => $outsider->id,
        ]);
    }

    /**
     * @return array{User, User, DiscussionGroup, Message}
     */
    private function createMessage(): array
    {
        $author = User::factory()->create(['role' => 'employe']);
        $member = User::factory()->create(['role' => 'employe']);
        $group = DiscussionGroup::create([
            'name' => 'Groupe de test suppression',
            'description' => 'Test des suppressions de messages',
            'created_by' => $author->id,
        ]);
        $group->members()->attach([
            $author->id => ['can_post' => true],
            $member->id => ['can_post' => true],
        ]);
        $message = Message::create([
            'discussion_group_id' => $group->id,
            'user_id' => $author->id,
            'content' => 'Message visible uniquement pour le membre',
        ]);

        return [$author, $member, $group, $message];
    }
}

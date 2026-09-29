<?php

namespace Tests\Feature;

use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrivateMessageDeletionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_sender_can_delete_a_message_for_everyone(): void
    {
        Storage::fake();
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $message = PrivateMessage::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Image a supprimer',
            'attachment_path' => 'private-messages/image.png',
        ]);
        Storage::put($message->attachment_path, 'image content');

        $this->actingAs($sender)
            ->delete(route('private.messages.destroy', $message), ['scope' => 'everyone'])
            ->assertRedirect();

        $this->assertSoftDeleted('private_messages', ['id' => $message->id]);
        Storage::assertMissing($message->attachment_path);
    }

    public function test_recipient_cannot_delete_a_message_for_everyone(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $message = PrivateMessage::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Message protege',
        ]);

        $this->actingAs($recipient)
            ->delete(route('private.messages.destroy', $message), ['scope' => 'everyone'])
            ->assertForbidden();

        $this->assertDatabaseHas('private_messages', ['id' => $message->id, 'deleted_at' => null]);
    }

    public function test_recipient_can_delete_a_message_only_for_themselves(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $message = PrivateMessage::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Message a masquer',
        ]);

        $this->actingAs($recipient)
            ->delete(route('private.messages.destroy', $message), ['scope' => 'me'])
            ->assertRedirect();

        $this->assertDatabaseHas('private_messages', [
            'id' => $message->id,
            'deleted_for_sender_at' => null,
            'deleted_at' => null,
        ]);
        $this->assertNotNull($message->fresh()->deleted_for_recipient_at);

        $this->actingAs($recipient)
            ->get(route('private.messages.user', $sender))
            ->assertDontSee($message->content);

        $this->actingAs($sender)
            ->get(route('private.messages.user', $recipient))
            ->assertSee($message->content);
    }

    public function test_private_messages_show_read_status_for_sent_messages(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $message = PrivateMessage::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Message avec statut vu',
            'read_at' => now(),
        ]);

        $this->actingAs($sender)
            ->get(route('private.messages.user', $recipient))
            ->assertSee('message-status')
            ->assertSee('✓✓');

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_private_messages_show_unread_badge_for_new_messages(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        PrivateMessage::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Nouveau message non lu',
            'read_at' => null,
        ]);

        $this->actingAs($recipient)
            ->get(route('private.messages.index'))
            ->assertSee('unread-badge')
            ->assertSee('1');
    }
}

<?php

namespace Tests\Feature;

use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrivateMessageAttachmentTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_send_up_to_five_large_private_attachments(): void
    {
        Storage::fake();
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $attachments = [
            UploadedFile::fake()->create('image-lourde.jpg', 21 * 1024, 'image/jpeg'),
            ...array_map(
                fn (int $number): UploadedFile => UploadedFile::fake()->create("photo-{$number}.jpg", 20, 'image/jpeg'),
                range(1, 4)
            ),
        ];

        $response = $this->actingAs($sender)
            ->post(route('private.messages.store', $recipient), [
                'attachments' => $attachments,
            ]);

        $response->assertRedirect(route('private.messages.user', $recipient));
        $message = PrivateMessage::where('sender_id', $sender->id)->firstOrFail();
        $this->assertCount(5, $message->attachments);
        $this->assertSame('image-lourde.jpg', $message->attachments->first()->name);
        Storage::assertExists($message->attachments->first()->path);

        $this->actingAs($sender)
            ->get(route('private.messages.attachments.download', [$message, $message->attachments->first()]))
            ->assertSuccessful();

        $this->actingAs($sender)
            ->get(route('private.messages.user', $recipient))
            ->assertSee('photo-1.jpg');

        $this->actingAs(User::factory()->create())
            ->get(route('private.messages.attachments.file', [$message, $message->attachments->first()]))
            ->assertForbidden();
    }

    public function test_private_message_rejects_more_than_five_attachments(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $response = $this->actingAs($sender)
            ->from(route('private.messages.user', $recipient))
            ->post(route('private.messages.store', $recipient), [
                'attachments' => array_map(
                    fn (int $number): UploadedFile => UploadedFile::fake()->create("photo-{$number}.jpg", 20, 'image/jpeg'),
                    range(1, 6)
                ),
            ]);

        $response->assertRedirect(route('private.messages.user', $recipient));
        $response->assertSessionHasErrors('attachments');
        $this->assertDatabaseCount('private_messages', 0);
    }
}

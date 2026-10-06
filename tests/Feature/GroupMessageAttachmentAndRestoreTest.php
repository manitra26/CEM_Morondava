<?php

namespace Tests\Feature;

use App\Models\DiscussionGroup;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GroupMessageAttachmentAndRestoreTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_restore_soft_deleted_group_message(): void
    {
        $user = User::factory()->create();
        $group = DiscussionGroup::create([
            'name' => 'Groupe Test',
            'description' => 'Description test',
            'created_by' => $user->id,
        ]);
        $group->members()->attach($user->id, ['can_post' => true]);

        $message = Message::create([
            'discussion_group_id' => $group->id,
            'user_id' => $user->id,
            'content' => 'Message a supprimer et restaurer',
        ]);

        $message->delete();
        $this->assertSoftDeleted('messages', ['id' => $message->id]);

        $response = $this->actingAs($user)
            ->post(route('messages.restore', $message));

        $response->assertRedirect();
        $this->assertNotSoftDeleted('messages', ['id' => $message->id]);
    }

    public function test_user_can_send_group_message_with_attachment(): void
    {
        Storage::fake();

        $user = User::factory()->create();
        $group = DiscussionGroup::create([
            'name' => 'Groupe Test Attachments',
            'description' => 'Description test',
            'created_by' => $user->id,
        ]);
        $group->members()->attach($user->id, ['can_post' => true]);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)
            ->post(route('messages.store', $group), [
                'attachment' => $file,
            ]);

        $response->assertRedirect();

        $message = Message::where('discussion_group_id', $group->id)->first();
        $this->assertNotNull($message);
        $this->assertEquals('document.pdf', $message->attachment_name);
        $this->assertEquals('application/pdf', $message->attachment_mime);
        $this->assertNotNull($message->attachment_path);
        Storage::assertExists($message->attachment_path);

        $fileResponse = $this->actingAs($user)
            ->get(route('messages.file', $message));
        $fileResponse->assertSuccessful();
    }

    public function test_user_can_send_up_to_five_large_group_attachments(): void
    {
        Storage::fake();

        $user = User::factory()->create();
        $group = DiscussionGroup::create([
            'name' => 'Groupe avec plusieurs images',
            'description' => 'Description test',
            'created_by' => $user->id,
        ]);
        $group->members()->attach($user->id, ['can_post' => true]);
        $attachments = [
            UploadedFile::fake()->create('image-lourde.jpg', 21 * 1024, 'image/jpeg'),
            ...array_map(
                fn (int $number): UploadedFile => UploadedFile::fake()->create("photo-{$number}.jpg", 20, 'image/jpeg'),
                range(1, 4)
            ),
        ];

        $response = $this->actingAs($user)
            ->post(route('messages.store', $group), [
                'attachments' => $attachments,
            ]);

        $response->assertRedirect();
        $message = Message::where('discussion_group_id', $group->id)->firstOrFail();
        $this->assertCount(5, $message->attachments);
        $this->assertSame('image-lourde.jpg', $message->attachments->first()->name);
        Storage::assertExists($message->attachments->first()->path);

        $this->actingAs($user)
            ->get(route('messages.attachments.download', [$message, $message->attachments->first()]))
            ->assertSuccessful();

        $this->actingAs($user)
            ->get(route('messages.index', $group))
            ->assertJsonCount(5, 'messages.0.attachments')
            ->assertJsonPath('messages.0.attachments.0.name', 'image-lourde.jpg');
        $this->actingAs($user)
            ->get(route('groups.show', $group))
            ->assertSee('photo-1.jpg');

        $this->actingAs(User::factory()->create())
            ->get(route('messages.attachments.file', [$message, $message->attachments->first()]))
            ->assertForbidden();
    }

    public function test_group_message_rejects_more_than_five_attachments(): void
    {
        $user = User::factory()->create();
        $group = DiscussionGroup::create([
            'name' => 'Groupe limite fichiers',
            'description' => 'Description test',
            'created_by' => $user->id,
        ]);
        $group->members()->attach($user->id, ['can_post' => true]);

        $response = $this->actingAs($user)
            ->from(route('groups.show', $group))
            ->post(route('messages.store', $group), [
                'attachments' => array_map(
                    fn (int $number): UploadedFile => UploadedFile::fake()->create("photo-{$number}.jpg", 20, 'image/jpeg'),
                    range(1, 6)
                ),
            ]);

        $response->assertRedirect(route('groups.show', $group));
        $response->assertSessionHasErrors('attachments');
        $this->assertDatabaseCount('messages', 0);
    }
}

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
}

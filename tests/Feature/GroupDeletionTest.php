<?php

namespace Tests\Feature;

use App\Models\DiscussionGroup;
use App\Models\InternalNotification;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GroupDeletionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_group_creator_can_delete_group_and_related_data(): void
    {
        Storage::fake();
        $creator = User::factory()->create(['role' => 'employe']);
        $recipient = User::factory()->create();
        $groupImage = 'groups/equipe.png';
        $messageAttachment = 'group-messages/document.pdf';

        $group = DiscussionGroup::create([
            'name' => 'Equipe exploitation',
            'description' => 'Discussion de groupe',
            'image_path' => $groupImage,
            'created_by' => $creator->id,
        ]);
        $group->members()->attach([
            $creator->id => ['can_post' => true],
            $recipient->id => ['can_post' => true],
        ]);

        Storage::put($groupImage, 'group image');
        Storage::put($messageAttachment, 'message attachment');
        $message = Message::create([
            'discussion_group_id' => $group->id,
            'user_id' => $creator->id,
            'content' => 'Document partagé',
            'attachment_path' => $messageAttachment,
        ]);
        $additionalAttachmentPath = 'group-messages/photo.jpg';
        $message->attachments()->create([
            'path' => $additionalAttachmentPath,
            'name' => 'photo.jpg',
            'mime' => 'image/jpeg',
            'size' => 100,
        ]);
        Storage::put($additionalAttachmentPath, 'additional attachment');
        $notification = InternalNotification::create([
            'user_id' => $recipient->id,
            'actor_id' => $creator->id,
            'title' => 'Nouveau message de groupe',
            'content' => 'Un nouveau message a été publié.',
            'type' => 'message',
            'data' => ['group_id' => $group->id, 'message_id' => $message->id],
        ]);

        $this->actingAs($creator)
            ->get(route('groups.index'))
            ->assertSee('Supprimer le groupe');

        $this->actingAs($creator)
            ->delete(route('groups.destroy', $group))
            ->assertRedirect(route('groups.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('discussion_groups', ['id' => $group->id]);
        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
        $this->assertDatabaseMissing('internal_notifications', ['id' => $notification->id]);
        $this->assertDatabaseMissing('message_attachments', ['message_id' => $message->id]);
        Storage::assertMissing($groupImage);
        Storage::assertMissing($messageAttachment);
        Storage::assertMissing($additionalAttachmentPath);
    }

    public function test_group_member_cannot_delete_group_or_see_delete_button(): void
    {
        $creator = User::factory()->create(['role' => 'employe']);
        $member = User::factory()->create(['role' => 'employe']);
        $group = DiscussionGroup::create([
            'name' => 'Equipe exploitation',
            'description' => 'Discussion de groupe',
            'created_by' => $creator->id,
        ]);
        $group->members()->attach([
            $creator->id => ['can_post' => true],
            $member->id => ['can_post' => true],
        ]);

        $this->actingAs($member)
            ->get(route('groups.index'))
            ->assertDontSee('Supprimer le groupe');

        $this->actingAs($member)
            ->delete(route('groups.destroy', $group))
            ->assertForbidden();

        $this->assertModelExists($group);
    }

    public function test_director_can_delete_a_group_created_by_another_user(): void
    {
        $creator = User::factory()->create(['role' => 'employe']);
        $director = User::factory()->create(['role' => 'directeur']);
        $group = DiscussionGroup::create([
            'name' => 'Equipe exploitation',
            'description' => 'Discussion de groupe',
            'created_by' => $creator->id,
        ]);

        $this->actingAs($director)
            ->get(route('groups.index'))
            ->assertSee('Supprimer le groupe');

        $this->actingAs($director)
            ->delete(route('groups.destroy', $group))
            ->assertRedirect(route('groups.index'));

        $this->assertDatabaseMissing('discussion_groups', ['id' => $group->id]);
    }
}

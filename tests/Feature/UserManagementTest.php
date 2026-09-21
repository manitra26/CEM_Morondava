<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_director_can_filter_users_by_name(): void
    {
        $director = User::factory()->create(['role' => 'directeur']);
        $matchingUser = User::factory()->create(['name' => 'Anita Rakoto']);
        $otherUser = User::factory()->create(['name' => 'Bruno Randria']);

        $this->actingAs($director)
            ->get(route('users.index', ['search' => 'Anita']))
            ->assertSee($matchingUser->name)
            ->assertDontSee($otherUser->name);
    }

    public function test_director_can_filter_users_by_email_or_phone(): void
    {
        $director = User::factory()->create(['role' => 'directeur']);
        $emailMatch = User::factory()->create(['email' => 'mamy@example.test']);
        $phoneMatch = User::factory()->create(['phone' => '+261340001234']);
        $otherUser = User::factory()->create();

        $this->actingAs($director)
            ->get(route('users.index', ['search' => 'mamy@example.test']))
            ->assertSee($emailMatch->email)
            ->assertDontSee($otherUser->email);

        $this->actingAs($director)
            ->get(route('users.index', ['search' => '0001234']))
            ->assertSee($phoneMatch->phone)
            ->assertDontSee($otherUser->phone);
    }

    public function test_employee_cannot_view_user_management(): void
    {
        $employee = User::factory()->create(['role' => 'employe']);

        $this->actingAs($employee)
            ->get(route('users.index'))
            ->assertForbidden();
    }
}

<?php

namespace Tests\Unit;

use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserStatusTest extends TestCase
{
    public function test_user_is_online_when_last_seen_is_recent(): void
    {
        $user = new User([
            'last_seen_at' => Carbon::now()->subMinutes(2),
        ]);

        $this->assertTrue($user->isOnline());
    }

    public function test_user_is_offline_when_last_seen_is_old(): void
    {
        $user = new User([
            'last_seen_at' => Carbon::now()->subMinutes(10),
        ]);

        $this->assertFalse($user->isOnline());
    }
}

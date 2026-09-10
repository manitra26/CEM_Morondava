<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discussion_group_user', function (Blueprint $table): void {
            $table->boolean('can_post')->default(false)->after('joined_at');
        });
    }

    public function down(): void
    {
        Schema::table('discussion_group_user', function (Blueprint $table): void {
            $table->dropColumn('can_post');
        });
    }
};

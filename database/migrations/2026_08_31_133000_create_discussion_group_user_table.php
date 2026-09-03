<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussion_group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_group_id')->constrained('discussion_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            $table->unique(['discussion_group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_group_user');
    }
};

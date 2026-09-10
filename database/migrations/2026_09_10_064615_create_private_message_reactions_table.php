<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_message_reactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('private_message_id')->constrained('private_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reaction', 20);
            $table->timestamps();
            $table->unique(['private_message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_message_reactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('private_messages', function (Blueprint $table): void {
            $table->timestamp('deleted_for_sender_at')->nullable();
            $table->timestamp('deleted_for_recipient_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('private_messages', function (Blueprint $table): void {
            $table->dropColumn(['deleted_for_sender_at', 'deleted_for_recipient_at']);
            $table->dropSoftDeletes();
        });
    }
};

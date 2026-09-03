<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employe')->after('password');
            $table->string('position')->nullable()->after('role');
            $table->string('department')->nullable()->after('position');
            $table->string('phone', 30)->nullable()->after('department');
            $table->text('bio')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'position', 'department', 'phone', 'bio']);
        });
    }
};

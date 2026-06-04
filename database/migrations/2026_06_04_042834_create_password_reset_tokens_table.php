<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Users', function (Blueprint $table) {
            $table->timestamp('Password_Changed_At')->nullable()->after('Password_Hash');
        });
    }

    public function down(): void
    {
        Schema::table('Users', function (Blueprint $table) {
            $table->dropColumn('Password_Changed_At');
        });
    }
};
<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => Role::USER, 'name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Role::STAFF, 'name' => 'staff', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Role::ADMIN, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

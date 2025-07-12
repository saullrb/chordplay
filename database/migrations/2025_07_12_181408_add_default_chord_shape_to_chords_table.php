<?php

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
        Schema::table('chords', function (Blueprint $table) {
            $table->foreignId('default_shape_id')->nullable()->constrained('chord_shapes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chords', function (Blueprint $table) {
            $table->drop('default_shape_id');
        });
    }
};

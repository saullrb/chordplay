<?php

use App\Models\Chord;
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
        Schema::create('chord_shapes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Chord::class)->constrained()->restrictOnDelete();
            $table->string('frets');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chord_shapes');
    }
};

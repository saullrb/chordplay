<?php

declare(strict_types=1);

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
        Schema::create('chord_shapes', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Chord::class)->constrained()->cascadeOnDelete();
            $table->json('frets');
            $table->json('fingers');
            $table->json('barres');
            $table->json('midi');
            $table->integer('base_fret');
            $table->boolean('capo')->default(false);
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

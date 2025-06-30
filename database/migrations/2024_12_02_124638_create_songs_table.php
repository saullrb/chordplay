<?php

declare(strict_types=1);

use App\Enums\SongKeyEnum;
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
        Schema::create('songs', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('artist_id')->constrained()->cascadeOnDelete();
            $table->enum('key', SongKeyEnum::values());
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();

            $table->unique(['artist_id', 'name', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};

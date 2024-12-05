<?php

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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('artist_id')->constrained();
            $table->enum('key', array_column(SongKeyEnum::cases(), 'name'))->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();

            $table->unique(['artist_id', 'slug']);
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

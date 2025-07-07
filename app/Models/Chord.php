<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chord extends Model
{
    protected $fillable = ['name', 'key', 'suffix', 'default_shape_id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'suffix' => 'string',
        ];
    }

    public function shapes(): HasMany
    {
        return $this->hasMany(ChordShape::class);

    }

    public function defaultShape(): BelongsTo
    {
        return $this->belongsTo(ChordShape::class);
    }

    public static function shapesByChordName(array $names): array
    {
        return self::whereIn('name', $names)
            ->get()
            ->mapWithKeys(fn ($chord) => [
                $chord->name => [
                    'shapes' => $chord->shapes,
                    'defaultShapeId' => $chord->default_shape_id,
                ],
            ])
            ->toArray();
    }
}

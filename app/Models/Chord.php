<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $variation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Chord extends Model
{
    protected $fillable = ['name', 'key', 'suffix', 'positions'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'string',
            'variation' => 'string',
        ];
    }
}

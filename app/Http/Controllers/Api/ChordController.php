<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChordController extends Controller
{
    public function findChords(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chords' => 'required|array',
        ]);

        $chords = Chord::shapesByChordName($validated['chords']);

        return response()->json($chords);
    }
}

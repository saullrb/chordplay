<?php

namespace App\Enums;

enum SongLineContentType: string
{
    case LYRICS = 'lyrics';
    case CHORDS = 'chords';
    case EMPTY = 'empty';
}

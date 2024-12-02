<?php

namespace App\Enums;

enum SongSectionEnum: int
{
    case INTRO = 1;
    case VERSE = 2;
    case CHORUS = 3;
    case BRIDGE = 4;
    case PRE_CHORUS = 5;
    case INSTRUMENTAL = 6;
    case SOLO = 7;
    case OUTRO = 8;
}

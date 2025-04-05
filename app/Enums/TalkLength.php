<?php

namespace App\Enums;

Enum TalkLength: string
{
    case NORMAL = 'Normal - 30min';
    case LIGHTNING = 'Lightning - 15min';
    case KEYNOTE = 'Keynote';

    public static function getArray()
    {
        $enums = self::cases();
        return array_combine(array_map(fn($enum) => $enum->name, $enums), array_map(fn($enum) => $enum->value, $enums));    
    }

    public static function getIcon($state)
    {
        if($state == 'LIGHTNING'){
            return 'heroicon-o-bolt';
        } elseif($state == 'NORMAL'){
            return 'heroicon-o-clock';
        } elseif($state == 'KEYNOTE'){
            return 'heroicon-o-star';
        }
    }
}
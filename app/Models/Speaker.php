<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class Speaker extends Model
{
    use HasFactory;

    protected $casts = [
        'qualifications' => 'array',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Textarea::make('bio')
                ->required()
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->required()
                ->maxLength(255),
            CheckboxList::make('qualifications')
                ->searchable()
                ->columnSpanFull()
                ->bulkToggleable()
                ->options([
                    'bussiness-leader' => 'Business Leader',
                    'charisma' => 'Charisma Speaker',
                    'first-time-speaker' => 'First Time Speaker',
                    'hometown-hero' => 'Hometown Hero',
                    'humanitarian' => 'Humanitarian',
                    'laracasts-contributor' => 'Laracasts Contributor',
                    'twitter-influencer' => 'Twitter Influencer',
                    'youtube-influencer' => 'YouTube Influencer',
                    'open-source-contributor' => 'Open Source Contributor',
                    'unique-perspective' => 'Unique Perspective',
                ])
                ->descriptions([
                    'bussiness-leader' => 'Here is a nice long description',
                    'charisma' => 'Here is a nice long description',
                ])
                ->columns(3),
        ];
    }
}

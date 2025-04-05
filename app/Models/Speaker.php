<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Speaker extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'bio',
        'twitter_handle',
        'avatar',
        'qualifications',
    ];

    const QUALIFICATIONS = [
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
    ]; 

    protected $casts = [
        'qualifications' => 'array',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('speaker-images')
            ->useDisk('public');
    }

    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            SpatieMediaLibraryFileUpload::make('avatar')
                ->previewable()
                ->downloadable()
                ->collection('speaker-images')
                ->image(),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Textarea::make('bio')
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->maxLength(255),
            CheckboxList::make('qualifications')
                ->searchable()
                ->columnSpanFull()
                ->bulkToggleable()
                ->options(self::QUALIFICATIONS)
                ->descriptions([
                    'bussiness-leader' => 'Here is a nice long description',
                    'charisma' => 'Here is a nice long description',
                ])
                ->columns(3),
        ];
    }
}

<?php

namespace App\Models;

use App\Enums\Region;
use App\Enums\ConferenceStatus;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'website',
        'start_date',
        'end_date',
        'is_published',
        'status',
        'region',
        'venue_id',
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'status' => ConferenceStatus::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    // for more layout examples https://filamentphp.com/docs/3.x/forms/layout/getting-started
    public static function getForm()
    {
        return [
            Section::make('Conference Details')
                //->aside()
                ->collapsible()
                ->icon('heroicon-o-information-circle')
                ->description('Provide the details of the conference.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->default('My Conference')
                        ->label('Conference Name')
                        ->markAsRequired()
                        ->required()
                        ->helperText('Please enter the name of the conference.')
                        ->hint('The name of the conference.')
                        ->hintIcon('heroicon-o-information-circle')
                        ->hintAction(new Action('https://example.com'))
                        // ->rules(['max:255', new CustomRule()])
                        ->maxLength(60)
                        ->columnSpanFull(),
                    MarkdownEditor::make('description')
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('website')
                        ->label('Website')
                        ->prefix('https://')
                        ->hint('The link to the conference website.')
                        ->hintIcon('heroicon-o-link')
                        ->url()
                        ->required()
                        ->columnSpanFull(),
                    DateTimePicker::make('start_date')
                        ->default(now())
                        ->required(),
                    DateTimePicker::make('end_date')
                        ->required(),
                ]),
            Section::make('Location')
                ->collapsible()
                ->description('Fill in the details of the location.')
                ->icon('heroicon-o-map')
                ->columns(2)
                ->schema([
                    Select::make('region')
                        ->live()
                        ->required()
                        ->options(Region::class)
                        ->enum(Region::class),
                    Select::make('venue_id')
                        ->searchable()
                        ->preload()
                        ->editOptionForm(Venue::getForm())
                        ->createOptionForm(Venue::getForm())
                        ->hint('Select region first')
                        ->hintIcon('heroicon-o-information-circle')
                        ->relationship('venue', 'name', modifyQueryUsing: fn(\Illuminate\Database\Eloquent\Builder $query, Get $get) => $query->where('region', $get('region'))),
                    Fieldset::make('Status')
                        ->columns(1)
                        ->schema([
                            Select::make('status')
                                ->options(ConferenceStatus::class)
                                ->enum(ConferenceStatus::class)
                                ->required(),
                            Toggle::make('is_published')
                                ->label('Is Published')
                                ->default(true),
                        ])
                ]),
            Section::make('Speakers')
                ->collapsible()
                ->description('Select the speakers for the conference.')
                ->icon('heroicon-o-chat-bubble-oval-left')
                ->schema([
                    CheckboxList::make('speakers')
                        ->columnSpanFull()
                        ->label('')
                        ->relationship('speakers', 'name')
                        ->options(Speaker::all()->pluck('name', 'id'))
                        ->required()
                        ->columns(4),
                ]),
            Actions::make(
                [Action::make('star')
                    ->icon('heroicon-o-plus')
                    ->label('Create Fake Data')
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make()->toArray();
                        unset($data['id'], $data['venue_id']);
                        $livewire->form->fill($data);
                    })
                    ->visible(function(string $operation){
                        if($operation !== 'create')
                            return false;
                        if(! app()->environment('local'))
                            return false;
                        return true;
                    })]

            )
        ];
    }
}

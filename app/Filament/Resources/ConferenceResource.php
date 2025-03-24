<?php

namespace App\Filament\Resources;

use App\Enums\Region;
use App\Filament\Resources\ConferenceResource\Pages;
use App\Filament\Resources\ConferenceResource\RelationManagers;
use App\Models\Conference;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Rules\CustomRule;
use Filament\Forms\Components\Actions\Action;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->default('My Conference')
                    ->label('Conference Name')
                    ->markAsRequired()
                    ->required()
                    ->helperText('Please enter the name of the conference.')
                    ->hint('The name of the conference.')
                    ->hintIcon('heroicon-o-information-circle')
                    ->hintAction(new Action('https://example.com'))
                    // ->rules(['max:255', new CustomRule()])
                    ->maxLength(60),
                Forms\Components\RichEditor::make('description')
                    ->disableToolbarButtons(['italic'])
                    ->required(),
                Forms\Components\TextInput::make('website')
                    ->label('Website')
                    ->prefix('https://')
                    ->hint('The link to the conference website.')
                    ->hintIcon('heroicon-o-link')
                    ->url(),
                Forms\Components\DateTimePicker::make('start_date')
                    ->default(now())
                    ->required(),
                Forms\Components\DateTimePicker::make('end_date')
                    ->required(),
                Forms\Components\Checkbox::make('is_published')
                    ->default(true),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->required(),
                Forms\Components\Select::make('region')
                    ->live()
                    ->options(Region::class)
                    ->enum(Region::class),
                Forms\Components\Select::make('venue_id')
                ->searchable()
                ->preload()
                ->editOptionForm(Venue::getForm())
                ->createOptionForm(Venue::getForm())
                ->hint('Select region first')
                ->hintIcon('heroicon-o-information-circle')
                ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                    return $query->where('region', $get('region'));
                })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable(),
                Tables\Columns\TextColumn::make('venue.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}

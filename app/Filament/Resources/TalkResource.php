<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\RelationManagers;
use App\Models\Talk;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('abstract')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('speaker_id')
                    ->relationship('speaker', 'name')
                    ->required()
                    ->native(false),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(TalkStatus::class)
                    ->native(false)
                    ->required()
                    ->default(TalkStatus::SUBMITTED),
                Forms\Components\Select::make('length')
                    ->label("Length")
                    ->options(TalkLength::getArray())
                    ->required()
                    ->native(false),
                Actions::make(
                    [Action::make('star')
                        ->icon('heroicon-o-plus')
                        ->label('Create Fake Data')
                        ->action(function ($livewire) {
                            $data = Talk::factory()->make()->toArray();
                            unset($data['id']);
                            $livewire->form->fill($data);
                        })
                        ->visible(function (string $operation) {
                            if ($operation !== 'create')
                                return false;
                            if (! app()->environment('local'))
                                return false;
                            return true;
                        })]

                )
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->description(function (Talk $record) {
                        return Str::of($record->abstract)->limit(40);
                    })
                    ->sortable(),
                // Tables\Columns\TextColumn::make('abstract')
                //      ->searchable()
                //      ->wrap(),
                ImageColumn::make('speaker.avatar')
                    ->defaultImageUrl(function (Talk $record) {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode($record->speaker->name);
                    })
                    ->circular()
                    ->label('Avatar'),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('new_talk'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->color(function ($state) {
                        return $state->getColor();
                    })
                    ->badge(),
                Tables\Columns\IconColumn::make('length')
                    ->icon(fn($state) => TalkLength::getIcon($state)),
                // Tables\Columns\IconColumn::make('new_talk')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('new_talk'),
                SelectFilter::make('speaker_id')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->preload(),
                Filter::make('has_avatar')
                    ->label('Show only speakers with avatar')
                    ->toggle()
                    ->modifyQueryUsing(function ($query) {
                        return $query->whereHas('speaker', function ($query2) {
                            return $query2->whereNotNull('avatar');
                        });
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\Action::make('approve')
                        ->action(function (Talk $record) {
                            $record->approve();
                        })->after(
                            fn() => Notification::make()->success()->title('This talk was approved')
                                ->body('The speaker has been notified')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->send()
                        )
                        ->icon('heroicon-o-check-circle')
                        ->visible(function (Talk $record) {
                            return $record->status === TalkStatus::SUBMITTED;
                        })
                        ->color('success'),
                    Tables\Actions\Action::make('reject')
                        ->action(function (Talk $record) {
                            $record->reject();
                        })->after(
                            fn() => Notification::make()->danger()->title('This talk was rejected')
                                ->body('The speaker has been notified')
                                ->icon('heroicon-o-no-symbol')
                                ->color('danger')
                                ->send()
                        )
                        ->visible(function (Talk $record) {
                            return $record->status === TalkStatus::SUBMITTED;
                        })
                        ->requiresConfirmation()
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->action(function (Collection $records) {
                            $records->each->approve();
                        })
                        ->color('success')
                        // ->icon('heroicon-o-check-circle')
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
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}

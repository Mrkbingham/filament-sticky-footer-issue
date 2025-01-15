<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Squire\Models\Country;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email address')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->maxLength(255),

                        Forms\Components\Section::make('billing_address')->schema([
                            Forms\Components\TextInput::make('address_line_1'),
                            Forms\Components\TextInput::make('address_line_2'),
                            Forms\Components\TextInput::make('city'),
                            Forms\Components\TextInput::make('state'),
                            Forms\Components\TextInput::make('country'),
                        ]),

                        Forms\Components\Section::make('mailing_address')->schema([
                            Forms\Components\TextInput::make('address_line_1'),
                            Forms\Components\TextInput::make('address_line_2'),
                            Forms\Components\TextInput::make('city'),
                            Forms\Components\TextInput::make('state'),
                            Forms\Components\TextInput::make('country'),
                        ]),

                        Forms\Components\DatePicker::make('birthday')
                            ->maxDate('today'),
                    ])
                    ->columnSpan(['lg' => fn (?Customer $record) => $record === null ? 3 : 2]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->getStateUsing(fn ($record): ?string => Country::find($record->addresses->first()?->country)?->name ?? null),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function () {
                        Notification::make()
                            ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}

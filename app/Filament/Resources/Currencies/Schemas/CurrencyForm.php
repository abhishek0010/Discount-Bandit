<?php

namespace App\Filament\Resources\Currencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('symbol'),
                TextInput::make('rate')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}

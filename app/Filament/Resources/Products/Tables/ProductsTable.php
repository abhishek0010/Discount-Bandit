<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductStatusEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        Auth::user()->loadMissing('currency:id,code,rate');

        return $table
            ->modifyQueryUsing(function ($query) {
                $query->with([
                    'links',
                    'links.store',
                    'links.store.currency:id,code,rate',
                ]);
            })
            ->recordUrl(function ($record) {
                return (! $record->name) ? route('filament.admin.resources.products.edit', ['record' => $record]) : null;
            })
            ->columns([
                Grid::make([
                    'lg' => 10,
                ])
                    ->schema([
                        ImageColumn::make('image')
                            ->verticallyAlignCenter()
                            ->alignCenter()
                            ->imageSize('100%')
                            ->extraImgAttributes(['style' => 'max-height:200px; '])
                            ->columnSpan(3)
                            ->url(fn ($record): string => route('filament.admin.resources.products.edit',
                                ['record' => $record])
                            ),

                        Grid::make([
                            'lg' => 8,
                            'md' => 4,
                        ])
                            ->schema([

                                TextColumn::make('name')
                                    ->default("Fetching....")
                                    ->columnSpan(4)
                                    ->alignCenter()
                                    ->searchable()
                                    ->words(10)
                                    ->url(fn ($record): string => route('filament.admin.resources.products.edit',
                                        ['record' => $record])
                                    )
                                    ->sortable(),

                                TextColumn::make('status')
                                    ->columnSpan(2)
                                    ->badge()
                                    ->verticallyAlignCenter()
                                    ->alignEnd()
                                    ->badge(),

                                IconColumn::make('delete')
                                    ->getStateUsing(fn () => true)
                                    ->columnSpan(1)
                                    ->alignEnd()
                                    ->icon(Heroicon::Trash)
                                    ->color('danger')
                                    ->action(DeleteAction::make()),

                                IconColumn::make('is_favourite')
                                    ->columnSpan(1)
                                    ->alignEnd()
                                    ->icon(fn ($state) => ($state) ? Heroicon::Star : Heroicon::OutlinedStar)
                                    ->color('primary')
                                    ->action(fn ($record) => $record->update(['is_favourite' => ! $record->is_favourite])),

                            ])
                            ->columnSpan(7),

                        Stack::make([

                            Panel::make([
                                ViewColumn::make('links')
                                    ->view('filament.tables.columns.link-prices')
                                    ->columnSpanFull(),
                            ])
                                ->columnSpanFull(),

                        ])
                            ->space(3)
                            ->columnSpanFull(),

                    ]),
            ])
            ->contentGrid([
                'md' => 2,
            ])
            ->defaultSort('is_favourite', 'desc')

            ->filters([
                SelectFilter::make('category')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->options(ProductStatusEnum::class)
                    ->native(false)
                    ->preload()
                    ->multiple(),

                Filter::make('is_favourite')->query(function (Builder $query) {
                    $query->where('is_favourite');
                })
                    ->label('Favourite product')
                    ->toggle(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ProductsStats;
use App\Filament\Widgets\TotalStatsOverview;
use Filament\Schemas\Contracts\HasSchemas;

class Dashboard extends \Filament\Pages\Dashboard implements HasSchemas
{

    public function getWidgets(): array
    {
        return [
            ProductsStats::class,
        ];
    }

}

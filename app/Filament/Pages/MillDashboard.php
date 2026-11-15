<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MillOverview;
use Filament\Pages\Dashboard;

class MillDashboard extends Dashboard
{
    protected static ?string $title = 'Mill floor';

    public function getWidgets(): array
    {
        return [
            MillOverview::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}

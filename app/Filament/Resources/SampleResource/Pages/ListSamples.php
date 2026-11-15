<?php

namespace App\Filament\Resources\SampleResource\Pages;

use App\Filament\Resources\SampleResource;
use Filament\Resources\Pages\ListRecords;

class ListSamples extends ListRecords
{
    protected static string $resource = SampleResource::class;
}

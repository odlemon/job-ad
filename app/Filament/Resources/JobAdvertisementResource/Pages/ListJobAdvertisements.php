<?php

namespace App\Filament\Resources\JobAdvertisementResource\Pages;

use App\Filament\Resources\JobAdvertisementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobAdvertisements extends ListRecords
{
    protected static string $resource = JobAdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

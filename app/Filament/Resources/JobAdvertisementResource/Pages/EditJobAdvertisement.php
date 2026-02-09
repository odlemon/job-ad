<?php

namespace App\Filament\Resources\JobAdvertisementResource\Pages;

use App\Filament\Resources\JobAdvertisementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobAdvertisement extends EditRecord
{
    protected static string $resource = JobAdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

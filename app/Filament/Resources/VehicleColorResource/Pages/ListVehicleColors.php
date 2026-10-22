<?php

namespace App\Filament\Resources\VehicleColorResource\Pages;

use App\Filament\Resources\VehicleColorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleColors extends ListRecords
{
    protected static string $resource = VehicleColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

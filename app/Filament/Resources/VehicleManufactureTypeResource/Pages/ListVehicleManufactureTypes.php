<?php

namespace App\Filament\Resources\VehicleManufactureTypeResource\Pages;

use App\Filament\Resources\VehicleManufactureTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleManufactureTypes extends ListRecords
{
    protected static string $resource = VehicleManufactureTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

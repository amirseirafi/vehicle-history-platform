<?php

namespace App\Filament\Resources\VehicleManufactureTypeResource\Pages;

use App\Filament\Resources\VehicleManufactureTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleManufactureType extends EditRecord
{
    protected static string $resource = VehicleManufactureTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\VehicleColorResource\Pages;

use App\Filament\Resources\VehicleColorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleColor extends EditRecord
{
    protected static string $resource = VehicleColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

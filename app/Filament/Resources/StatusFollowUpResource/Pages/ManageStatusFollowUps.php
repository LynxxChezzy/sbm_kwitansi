<?php

namespace App\Filament\Resources\StatusFollowUpResource\Pages;

use App\Filament\Resources\StatusFollowUpResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStatusFollowUps extends ManageRecords
{
    protected static string $resource = StatusFollowUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

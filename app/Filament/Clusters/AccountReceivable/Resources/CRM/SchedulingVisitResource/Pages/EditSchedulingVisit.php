<?php

namespace App\Filament\Clusters\AccountReceivable\Resources\CRM\SchedulingVisitResource\Pages;

use App\Filament\Clusters\AccountReceivable\Resources\CRM\SchedulingVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchedulingVisit extends EditRecord
{
    protected static string $resource = SchedulingVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

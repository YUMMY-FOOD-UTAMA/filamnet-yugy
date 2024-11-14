<?php

namespace App\Filament\Clusters\AccountReceivable\Resources\CRM\SchedulingVisitResource\Pages;

use App\Filament\Clusters\AccountReceivable\Resources\CRM\SchedulingVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchedulingVisits extends ListRecords
{
    protected static string $resource = SchedulingVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

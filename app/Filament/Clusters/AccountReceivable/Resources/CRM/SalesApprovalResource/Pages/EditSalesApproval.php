<?php

namespace App\Filament\Clusters\AccountReceivable\Resources\CRM\SalesApprovalResource\Pages;

use App\Filament\Clusters\AccountReceivable\Resources\CRM\SalesApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesApproval extends EditRecord
{
    protected static string $resource = SalesApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

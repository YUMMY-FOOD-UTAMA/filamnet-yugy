<?php

namespace App\Filament\Clusters\AccountReceivable\Resources\CRM\SalesApprovalResource\Pages;

use App\Filament\Clusters\AccountReceivable\Resources\CRM\SalesApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesApproval extends CreateRecord
{
    protected static string $resource = SalesApprovalResource::class;
}

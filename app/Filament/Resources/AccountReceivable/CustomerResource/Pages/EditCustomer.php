<?php

namespace App\Filament\Resources\AccountReceivable\CustomerResource\Pages;

use App\Filament\Resources\AccountReceivable\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
//            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),

        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

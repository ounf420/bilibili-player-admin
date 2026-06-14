<?php

namespace App\Filament\Admin\Resources\PlayerQuotas\Pages;

use App\Filament\Admin\Resources\PlayerQuotas\PlayerQuotaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayerQuota extends EditRecord
{
    protected static string $resource = PlayerQuotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

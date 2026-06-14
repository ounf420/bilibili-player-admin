<?php

namespace App\Filament\Admin\Resources\PlayerQuotas\Pages;

use App\Filament\Admin\Resources\PlayerQuotas\PlayerQuotaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePlayerQuota extends CreateRecord
{
    protected static string $resource = PlayerQuotaResource::class;
}

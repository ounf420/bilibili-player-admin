<?php

namespace App\Filament\Admin\Resources\VipPlans\Pages;

use App\Filament\Admin\Resources\VipPlans\VipPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVipPlans extends ListRecords
{
    protected static string $resource = VipPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

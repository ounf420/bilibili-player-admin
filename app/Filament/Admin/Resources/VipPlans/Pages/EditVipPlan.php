<?php

namespace App\Filament\Admin\Resources\VipPlans\Pages;

use App\Filament\Admin\Resources\VipPlans\VipPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVipPlan extends EditRecord
{
    protected static string $resource = VipPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

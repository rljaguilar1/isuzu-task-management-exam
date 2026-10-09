<?php

namespace App\Filament\App\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyTaskStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $tasks = Task::query()->where('user_id', auth()->id());

        return [
            Stat::make('My tasks', (clone $tasks)->count())
                ->description('All assigned tasks')
                ->color('primary'),
            Stat::make('Pending', (clone $tasks)->where('is_completed', false)->count())
                ->description('Still to complete')
                ->color('warning'),
            Stat::make('Completed', (clone $tasks)->where('is_completed', true)->count())
                ->description('Finished tasks')
                ->color('success'),
        ];
    }
}

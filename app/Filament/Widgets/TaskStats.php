<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaskStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total tasks', Task::query()->count())
                ->description('Across all users')
                ->color('primary'),
            Stat::make('Pending tasks', Task::query()->where('is_completed', false)->count())
                ->description('Still in progress')
                ->color('warning'),
            Stat::make('Registered users', User::query()->count())
                ->description('Including administrators')
                ->color('success'),
        ];
    }
}

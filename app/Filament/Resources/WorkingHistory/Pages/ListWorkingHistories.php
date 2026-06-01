<?php

namespace App\Filament\Resources\WorkingHistory\Pages;

use App\Filament\Resources\WorkingHistory\WorkingHistoryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListWorkingHistories extends ListRecords
{
    protected static string $resource = WorkingHistoryResource::class;

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(\App\Models\TeacherWorkingHistory::where('status', 'pending')->count()),

            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved')),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected')),

            'all' => Tab::make('All'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'pending';
    }
}
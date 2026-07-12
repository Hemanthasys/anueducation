<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Models\AuditLog;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date / Time')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('System')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('module')
                    ->label('Module')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created', 'login_success' => 'success',
                        'updated'                   => 'warning',
                        'deleted', 'login_failed'    => 'danger',
                        'login_failed_suspicious'    => 'danger',
                        default                      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                TextColumn::make('school.name_en')
                    ->label('School')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('module')
                    ->options(fn () => AuditLog::query()
                        ->distinct()
                        ->orderBy('module')
                        ->pluck('module', 'module')
                        ->mapWithKeys(fn ($v, $k) => [$k => ucfirst(str_replace('_', ' ', $v))])
                        ->toArray()),

                SelectFilter::make('action')
                    ->options(fn () => AuditLog::query()
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->mapWithKeys(fn ($v, $k) => [$k => ucfirst($v)])
                        ->toArray()),

                SelectFilter::make('user_id')
                    ->label('User')
                    ->options(fn () => User::whereIn('id', AuditLog::query()->distinct()->pluck('user_id'))
                        ->orderBy('name')
                        ->pluck('name', 'id')),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->native(false),
                        DatePicker::make('until')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}

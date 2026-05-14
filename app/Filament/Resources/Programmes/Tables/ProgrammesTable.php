<?php

namespace App\Filament\Resources\Programmes\Tables;

use App\Models\Programme;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ProgrammesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('social_artwork')
                    ->label('Visual')
                    ->width(80)
                    ->height(50)
                    ->defaultImageUrl(fn ($record) => $record->youtube_id
                        ? 'https://img.youtube.com/vi/' . $record->youtube_id . '/mqdefault.jpg'
                        : null
                    ),
                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
                IconColumn::make('youtube_url')
                    ->label('Video')
                    ->boolean()
                    ->trueIcon('heroicon-o-play-circle')
                    ->falseIcon('heroicon-o-minus'),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'review'    => 'warning',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'published' => 'info',
                        default     => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('submittedBy.name')
                    ->label('Submitted By')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'review'    => 'Under Review',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        'published' => 'Published',
                    ]),
                SelectFilter::make('category')
                    ->options([
                        'academic'            => 'Academic',
                        'sports'              => 'Sports',
                        'arts'                => 'Arts',
                        'cultural'            => 'Cultural',
                        'health'              => 'Health',
                        'ict'                 => 'ICT',
                        'teacher_development' => 'Teacher Development',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('submit_review')
                    ->label('Submit for Review')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (Programme $record) =>
                        $record->status === 'draft' &&
                        Auth::user()->hasAnyRole(['content_creator', 'super_admin'])
                    )
                    ->requiresConfirmation()
                    ->action(fn (Programme $record) => $record->update([
                        'status'       => 'review',
                        'submitted_by' => Auth::id(),
                    ])),

                Action::make('approve')
                    ->label('Approve & Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Programme $record) =>
                        $record->status === 'review' &&
                        Auth::user()->hasAnyRole(['zonal_director', 'super_admin'])
                    )
                    ->requiresConfirmation()
                    ->action(fn (Programme $record) => $record->update([
                        'status'       => 'published',
                        'approved_by'  => Auth::id(),
                        'published_at' => now(),
                    ])),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Programme $record) =>
                        $record->status === 'review' &&
                        Auth::user()->hasAnyRole(['zonal_director', 'zonal_officer', 'super_admin'])
                    )
                    ->requiresConfirmation()
                    ->action(fn (Programme $record) => $record->update([
                        'status'      => 'rejected',
                        'approved_by' => Auth::id(),
                    ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
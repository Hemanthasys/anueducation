<?php

namespace App\Filament\Resources\News\Tables;

use App\Models\News;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->width(80)
                    ->height(50),
                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
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
                TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->default('—'),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
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
                        'general'   => 'General',
                        'academic'  => 'Academic',
                        'events'    => 'Events',
                        'sports'    => 'Sports',
                        'awards'    => 'Awards',
                        'circulars' => 'Circulars',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                ->visible(fn (News $record) =>
                    auth()->user()?->can('content.approve') ||
                    (auth()->user()?->can('content.news') && $record->status === 'draft')
                ),

                Action::make('submit_review')
                    ->label('Submit for Review')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (News $record) =>
                        $record->status === 'draft' &&
                        Auth::user()->can('content.news')
                    )
                    ->requiresConfirmation()
                    ->action(fn (News $record) => $record->update([
                        'status'       => 'review',
                        'submitted_by' => Auth::id(),
                    ])),

                Action::make('approve')
                    ->label('Approve & Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (News $record) =>
                        $record->status === 'review' &&
                        Auth::user()->can('content.approve')
                    )
                    ->requiresConfirmation()
                    ->action(fn (News $record) => $record->update([
                        'status'      => 'published',
                        'approved_by' => Auth::id(),
                        'published_at' => now(),
                    ])),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (News $record) =>
                        $record->status === 'review' &&
                        Auth::user()->can('content.approve')
                    )
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(fn (News $record, array $data) => $record->update([
                        'status'      => 'rejected',
                        'reviewed_by' => Auth::id(),
                    ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                      ->visible(fn () => auth()->user()?->can('content.approve')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
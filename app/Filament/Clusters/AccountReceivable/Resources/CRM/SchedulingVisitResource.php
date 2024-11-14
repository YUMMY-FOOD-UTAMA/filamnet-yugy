<?php

namespace App\Filament\Clusters\AccountReceivable\Resources\CRM;

use App\Filament\Clusters\AccountReceivable;
use App\Filament\Clusters\AccountReceivable\Resources\CRM\SchedulingVisitResource\Pages;
use App\Filament\Clusters\AccountReceivable\Resources\CRM\SchedulingVisitResource\RelationManagers;
use App\Models\CRM\SchedulingVisit;
use App\Models\Customer\Customer;
use App\Models\Employee;
use App\Models\SalesMapping;
use App\Primitive\CustomerStatus;
use App\Primitive\SalesMappingStatus;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class SchedulingVisitResource extends Resource
{
    protected static ?string $model = SalesMapping::class;

    protected static ?string $navigationLabel = 'Scheduling Visits';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = AccountReceivable::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationGroup = 'CRM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Entry Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Sales Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.area.subRegion.region.name')
                    ->label('Region Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.area.name')
                    ->label('Area Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.customerCategory.name')
                    ->label('Customer Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.status')
                    ->label('Customer Status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.status')
                    ->label('Customer Status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_visit')
                    ->label('Customer Status')
                    ->sortable()
                    ->formatStateUsing(function ($state, SalesMapping $record) {
                        return Carbon::parse($state)->format('M, d Y') . ' - ' . Carbon::parse($record->end_visit)->format('M, d Y');
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Visit Status')
                    ->sortable()
                    ->color(fn($state) => SalesMappingStatus::getStatusColor($state))
                    ->formatStateUsing(fn($state) => "<strong>{$state}</strong>")
                    ->html(),
                Tables\Columns\TextColumn::make('end_visit')
                    ->label('Expired At')
                    ->formatStateUsing(fn($state, $record) => $record->expiredAtTheDay())
            ])
            ->filters([
                SelectFilter::make('employee_id')
                    ->label('Sales Name')
                    ->relationship('employee', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('customer_id')
                    ->label('Customer Name')
                    ->relationship('customer', 'name', function (Builder $query) {
                        return $query->where('name', '<>', '');
                    })
                    ->searchable()
                    ->preload(),
                SelectFilter::make('customer_status')
                    ->options(CustomerStatus::options())
                    ->query(
                        fn(array $data, Builder $query): Builder => $query->when(
                            $data['value'],
                            fn(Builder $query, $value): Builder => $query->whereHas('customer', function ($q) use ($value) {
                                $q->where('status', $value);
                            })
                        )
                    ),
                DateRangeFilter::make('visit_range_date')
                    ->label('Visit Range Date')
                    ->modifyQueryUsing(fn(Builder $query, ?Carbon $startDate, ?Carbon $endDate, $dateString) => $query->when(!empty($dateString),
                        fn(Builder $query, $date): Builder => $query
                            ->where('start_visit', '>=', $startDate)
                            ->where('end_visit', '<=', $endDate))
                    )
                    ->withIndicator()
            ])
            ->actions([
                Action::make('cancelled')
                    ->label('Cancelled')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (SalesMapping $record) {
                        $record->where('status', SalesMappingStatus::WAITING_APPROVAL)->update([
                            'status' => SalesMappingStatus::CANCELLED,
                        ]);
                        Notification::make()
                            ->title('Success')
                            ->body('Cancel Scheduling Visit Successfully')
                            ->success()
                            ->send();
                    })
                    ->disabled(fn(SalesMapping $record): bool => $record->status !== SalesMappingStatus::WAITING_APPROVAL)
            ])
            ->bulkActions([
                BulkAction::make('bulk_cancelled')
                    ->label('Cancelled')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each(function ($record) use (&$isWarn) {
                            $record->where('status', SalesMappingStatus::WAITING_APPROVAL)->update(['status' => SalesMappingStatus::CANCELLED]);
                        });

                        Notification::make()
                            ->title('Success')
                            ->body('Cancel Scheduling Visits Successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn(SalesMapping $record): bool => $record->status === SalesMappingStatus::WAITING_APPROVAL,
            )
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedulingVisits::route('/'),
            'create' => Pages\CreateSchedulingVisit::route('/create'),
            'edit' => Pages\EditSchedulingVisit::route('/{record}/edit'),
        ];
    }
}

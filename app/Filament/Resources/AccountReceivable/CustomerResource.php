<?php

namespace App\Filament\Resources\AccountReceivable;

use App\Filament\Resources\AccountReceivable\CustomerResource\Pages;
use App\Filament\Resources\AccountReceivable\CustomerResource\RelationManagers;
use App\Models\AccountReceivable\Customer;
use App\Models\Customer\Customer as CustomerModel;
use App\Models\Customer\CustomerCategory;
use App\Models\Region\Area;
use App\Models\Region\Region;
use App\Models\Region\SubRegion;
use Cassandra\Custom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Layout;

class CustomerResource extends Resource
{
    protected static ?string $model = CustomerModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Accounts Receivable';
    protected static ?string $navigationLabel = 'List Of Customer';
    protected static ?string $recordTitle = 'name';
    protected static ?string $navigationTitle = 'Customers';
    protected static ?string $breadcrumb = null;

    protected static int $globalSearchResultsLimit = 20;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return "white";
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'company_name'];
    }

    public static function getGlobalSearchResultDetails(Model $model): array
    {
        return [
            'name' => $model->name,
            'company_name' => $model->company_name,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('area.subRegion.region.name')
                    ->label('Region Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area.subRegion.region.covered')
                    ->label('Region Covered')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area.subRegion.name')
                    ->label('Sub Region Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area.name')
                    ->label('Area Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customerSegment.name')
                    ->label('Customer Segment')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customerCategory.name')
                    ->label('Customer Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Customer Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_person_phone')
                    ->label('Contact Person')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->form([
                        Forms\Components\Select::make('region_id')
                            ->label('Region Name')
                            ->options(
                                Region::all()->pluck('name', 'id')
                            )
                            ->reactive()
                            ->afterStateUpdated(
                                function (callable $set, callable $get) {
                                    $set('sub_region_id', null);
                                    $set('region_covered', $get('region_id'));
                                }
                            ),
                        Forms\Components\Select::make('region_covered')
                            ->label('Region Covered')
                            ->options(
                                Region::all()->pluck('covered', 'id')
                            )
                            ->reactive()
                            ->afterStateUpdated(
                                function (callable $set, callable $get) {
                                    $set('region_id', $get('region_covered'));
                                }
                            ),
                        Forms\Components\Select::make('sub_region_id')
                            ->label('Sub Region Name')
                            ->options(
                                function (callable $get) {
                                    if (filled($region_id = $get('region_id'))) {
                                        $region = Region::find($region_id);
                                        return $region->subRegions->pluck('name', 'id')->all();
                                    }
                                    return null;
                                }
                            )
                            ->reactive()
                            ->afterStateUpdated(fn(callable $set) => $set('area_id', null)),
                        Forms\Components\Select::make('area_id')
                            ->label('Area Name')
                            ->options(
                                function (callable $get) {
                                    if (filled($subRegions = $get('sub_region_id'))) {
                                        return SubRegion::find($subRegions)->areas->pluck('name', 'id')->all();
                                    }

                                    return null;
                                }
                            )
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['area_id'])) {
                            return $query->where('area_id', $data['area_id']);
                        } else if (isset($data['sub_region_id'])) {
                            return $query->whereHas('area.subRegion', function (Builder $query) use ($data) {
                                $query->where('id', $data['sub_region_id']);
                            });
                        } else if (isset($data['region_id'])) {
                            return $query->whereHas('area.subRegion.region', function (Builder $query) use ($data) {
                                $query->where('id', $data['region_id']);
                            });
                        }

                        return $query;
                    }),
                SelectFilter::make('customer_category_id')
                    ->label('Customer Category')
                    ->options(
                        CustomerCategory::all()->pluck('name', 'id')->all()
                    ),
                Tables\Filters\TrashedFilter::make(),
            ], layout: FiltersLayout::Dropdown)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->persistFiltersInSession()
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
//                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected function getTableFiltersLayout(): ?string
    {
        return Layout::AboveContent;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}

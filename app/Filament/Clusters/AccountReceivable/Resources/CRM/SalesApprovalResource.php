<?php

namespace App\Filament\Clusters\AccountReceivable\Resources\CRM;

use App\Filament\Clusters\AccountReceivable;
use App\Filament\Clusters\AccountReceivable\Resources\CRM\SalesApprovalResource\Pages;
use App\Filament\Clusters\AccountReceivable\Resources\CRM\SalesApprovalResource\RelationManagers;
use App\Models\CRM\SalesApproval;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesApprovalResource extends Resource
{
    protected static ?string $model = SalesApproval::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = AccountReceivable::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationGroup = 'CRM';

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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesApprovals::route('/'),
            'create' => Pages\CreateSalesApproval::route('/create'),
            'edit' => Pages\EditSalesApproval::route('/{record}/edit'),
        ];
    }
}

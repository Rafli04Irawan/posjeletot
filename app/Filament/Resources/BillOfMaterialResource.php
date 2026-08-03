<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillOfMaterialResource\Pages;
use App\Models\BillOfMaterial;
use App\Models\Produk;
use App\Models\BahanBaku;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BillOfMaterialResource extends Resource
{
    protected static ?string $model = BillOfMaterial::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Manajemen POS';
    protected static ?string $navigationLabel = 'Bill of Material';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('produk_id')
                    ->label('Produk Jadi')
                    ->options(Produk::pluck('nama_produk', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('bahan_baku_id')
                    ->label('Bahan Baku')
                    ->options(BahanBaku::pluck('nama_bahan', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah Bahan')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('satuan')
                    ->label('Satuan')
                    ->options([
                        'gram' => 'Gram',
                        'kg' => 'Kilogram',
                        'ml' => 'Mililiter',
                        'liter' => 'Liter',
                        'pcs' => 'Pcs',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('produk.nama_produk')
                    ->label('Produk Jadi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('bahanBaku.nama_bahan')
                    ->label('Bahan Baku')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah'),

                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i'),
                Tables\Columns\TextColumn::make('produk_bisa_dibuat')
                    ->label('Bisa Dibuat')
                    ->getStateUsing(function ($record) {
                        return $record->produk->jumlahBisaDibuat() . ' pcs';
                    }),
            ])
            ->defaultGroup('produk.nama_produk')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillOfMaterials::route('/'),
            'create' => Pages\CreateBillOfMaterial::route('/create'),
            'edit' => Pages\EditBillOfMaterial::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->role === 'admin' || $user->hasMenuPermission('bill_of_material');
    }
}
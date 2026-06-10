<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BahanBakuResource\Pages;
use App\Models\BahanBaku;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BahanBakuResource extends Resource
{
    protected static ?string $model = BahanBaku::class;

    protected static ?string $modelLabel = 'Bahan Baku';
    protected static ?string $pluralModelLabel = 'Bahan Baku';
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Manajemen POS';
    protected static ?string $navigationLabel = 'Bahan Baku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_bahan')
                    ->label('Nama Bahan')
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

                Forms\Components\TextInput::make('stok')
                    ->label('Stok')
                    ->required()
                    ->formatStateUsing(fn ($state) => $state ? number_format((int) $state, 0, ',', '.') : null)
                    ->dehydrateStateUsing(fn ($state) => (int) str_replace('.', '', $state))
                    ->rules(['required']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_bahan')
                    ->label('Nama Bahan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan'),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Stok')
                    ->formatStateUsing(fn ($state, $record) =>
                        number_format((int) $state, 0, ',', '.') . ' ' . $record->satuan
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i'),
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
            'index' => Pages\ListBahanBakus::route('/'),
            'create' => Pages\CreateBahanBaku::route('/create'),
            'edit' => Pages\EditBahanBaku::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }
}
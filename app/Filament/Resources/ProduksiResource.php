<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduksiResource\Pages;
use App\Models\Produksi;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Outlet;

class ProduksiResource extends Resource
{
    protected static ?string $model = Produksi::class;

    protected static ?string $modelLabel = 'Produksi';
    protected static ?string $pluralModelLabel = 'Produksi';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Persediaan';
    protected static ?string $navigationLabel = 'Produksi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('produk_id')
                ->label('Produk yang Diproduksi')
                ->options(Produk::pluck('nama_produk', 'id'))
                ->searchable()
                ->live()
                ->required(),

            Forms\Components\TextInput::make('jumlah')
                ->label('Jumlah Produksi')
                ->numeric()
                ->suffix('pcs')
                ->required()
                ->minValue(1)
                ->live()
                ->helperText(function (Get $get): string {
                    if (!$get('produk_id')) {
                        return 'Pilih produk untuk melihat kapasitas berdasarkan bahan baku.';
                    }

                    $produk = Produk::find($get('produk_id'));
                    if (!$produk) {
                        return '';
                    }

                    $perhitungan = $produk->hitungProduksi((int) ($get('jumlah') ?: 0));
                    $pesan = 'Maksimal dapat dibuat: ' . number_format($perhitungan['maksimal'], 0, ',', '.') . ' pcs.';

                    if ($perhitungan['kekurangan']) {
                        $namaBahan = collect($perhitungan['kekurangan'])
                            ->pluck('nama')
                            ->implode(', ');
                        $pesan .= ' Stok kurang: ' . $namaBahan . '.';
                    }

                    return $pesan;
                }),
            Forms\Components\Select::make('outlet_id')
            ->label('Outlet')
            ->relationship('outlet', 'nama_outlet')
            ->searchable()
            ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('produk.nama_produk')
                    ->label('Produk')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah Produksi')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . ' pcs'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Produksi')
                    ->dateTime('d M Y H:i'),
                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->label('Outlet')
                    ->badge()
                    ->placeholder('-')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Filter::make('tanggal_produksi')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal Produksi'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['tanggal'] ?? null,
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                        );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduksis::route('/'),
            'create' => Pages\CreateProduksi::route('/create'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (!$user instanceof \App\Models\User) {
            return false;
        }

        return $user->role === 'admin' || $user->hasMenuPermission('produksi');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['produk', 'outlet']);
    }
}
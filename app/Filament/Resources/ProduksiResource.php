<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduksiResource\Pages;
use App\Models\Produksi;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ProduksiResource extends Resource
{
    protected static ?string $model = Produksi::class;

    protected static ?string $modelLabel = 'Produksi';
    protected static ?string $pluralModelLabel = 'Produksi';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Manajemen POS';
    protected static ?string $navigationLabel = 'Produksi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('produk_id')
                ->label('Produk yang Diproduksi')
                ->options(Produk::pluck('nama_produk', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('jumlah')
                ->label('Jumlah Produksi')
                ->numeric()
                ->suffix('pcs')
                ->required()
                ->minValue(1),
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
}
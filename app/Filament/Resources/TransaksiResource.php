<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;
    
    
     protected static ?string $modelLabel = 'List Transaksi';
    protected static ?string $pluralModelLabel = 'List Transaksi';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'List Transaksi';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
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
            Tables\Columns\TextColumn::make('id')->label('ID'),
            Tables\Columns\TextColumn::make('total')->money('IDR'),
            Tables\Columns\TextColumn::make('bayar')->money('IDR'),
            Tables\Columns\TextColumn::make('kembalian')->money('IDR'),
            Tables\Columns\TextColumn::make('metode_pembayaran')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'cash' => 'success',
                    'qris' => 'info',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Tanggal')
                ->dateTime('d M Y H:i'),
                
                Tables\Columns\TextColumn::make('pesanan')
                ->label('Pesanan')
                ->getStateUsing(function ($record) {
                    if ($record->details->isEmpty()) {
                        return '-';
                    }

                    return $record->details->map(function ($item) {
                        $nama = $item->produk ? $item->produk->nama_produk : 'Produk tidak ditemukan';
                        return $nama . ' (' . $item->qty . 'x)';
                    })->join(', ');
                })
                ->wrap(),
            ])
            ->filters([
                 Tables\Filters\Filter::make('tanggal')
                ->form([
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Pilih Tanggal'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['tanggal'],
                        fn (Builder $query, $date): Builder =>
                            $query->whereDate('created_at', $date)
                    );
                }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
            'edit' => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('details.produk');

        if (auth()->user()?->role === 'pegawai') {
            $query->where('outlet_id', auth()->user()->outlet_id);
        }

        return $query;
    }
    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'pegawai']);
    }
}

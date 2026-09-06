<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanOutletResource\Pages;
use App\Models\LaporanOutlet;
use App\Models\Outlet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LaporanOutletResource extends Resource
{
    protected static ?string $model = LaporanOutlet::class;
    protected static ?string $modelLabel = 'Laporan Outlet';
    protected static ?string $pluralModelLabel = 'Laporan Outlet';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Laporan Outlet';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('outlet_id')
                ->label('Outlet')
                ->options(Outlet::pluck('nama_outlet', 'id'))
                ->searchable()
                ->required()
                ->visible(fn (): bool => auth()->user()?->role === 'admin')
                ->default(fn (): ?int => auth()->user()?->outlet_id),

            Forms\Components\DatePicker::make('periode_mulai')
                ->label('Periode Mulai')
                ->required(),

            Forms\Components\DatePicker::make('periode_selesai')
                ->label('Periode Selesai')
                ->afterOrEqual('periode_mulai')
                ->required(),

            Forms\Components\FileUpload::make('file_path')
                ->label('File Laporan PDF')
                ->disk('public')
                ->directory('laporan-outlet')
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(10240)
                ->downloadable()
                ->openable()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->label('Outlet')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('periode_mulai')
                    ->label('Periode')
                    ->date('d M Y')
                    ->formatStateUsing(fn ($state, LaporanOutlet $record): string => $record->periode_mulai->format('d M Y') . ' s/d ' . $record->periode_selesai->format('d M Y')),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Diunggah Oleh')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Upload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_path')
                    ->label('Dokumen')
                    ->formatStateUsing(fn (): string => 'Lihat PDF')
                    ->url(fn (LaporanOutlet $record): string => asset('storage/' . ltrim($record->file_path, '/')))
                    ->openUrlInNewTab()
                    ->color('primary'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->role === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->role === 'admin'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanOutlets::route('/'),
            'create' => Pages\CreateLaporanOutlet::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['outlet', 'uploader']);

        if (auth()->user()?->role !== 'admin') {
            $query->where('outlet_id', auth()->user()?->outlet_id);
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (!$user instanceof \App\Models\User) {
            return false;
        }

        return $user->role === 'admin'
            || $user->hasMenuPermission('laporan_outlet')
            || in_array($user->role, ['pegawai', 'kasir'], true);
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->role === 'admin';
    }
}

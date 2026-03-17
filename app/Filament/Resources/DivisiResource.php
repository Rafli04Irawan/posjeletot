<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisiResource\Pages;
use App\Models\Divisi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DivisiResource extends Resource
{
    protected static ?string $model = Divisi::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Manajemen Divisi';
    protected static ?string $pluralModelLabel = 'Daftar Divisi';
    protected static ?string $modelLabel = 'Divisi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Divisi')
                    ->description('Isi data divisi dengan lengkap dan jelas')
                    ->icon('heroicon-o-information-circle')
                    ->schema([

                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Divisi')
                            ->placeholder('Contoh: Human Resource')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('singkatan')
                            ->label('Singkatan')
                            ->placeholder('Contoh: HRD')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Gunakan singkatan yang umum digunakan'),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->placeholder('Jelaskan fungsi atau tugas divisi...')
                            ->rows(4)
                            ->columnSpanFull(),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Divisi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('singkatan')
                    ->label('Singkatan')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->deskripsi),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Belum ada data divisi')
            ->emptyStateDescription('Silakan tambahkan divisi baru terlebih dahulu')
            ->emptyStateIcon('heroicon-o-folder-open');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisis::route('/'),
            'create' => Pages\CreateDivisi::route('/create'),
            'edit' => Pages\EditDivisi::route('/{record}/edit'),
        ];
    }
}
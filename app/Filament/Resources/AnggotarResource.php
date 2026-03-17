<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnggotarResource\Pages;
use App\Models\Anggotar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnggotarResource extends Resource
{
    protected static ?string $model = Anggotar::class;

    protected static ?string $navigationLabel = 'Anggota';
    protected static ?string $modelLabel = 'Anggota';
    protected static ?string $pluralModelLabel = 'Anggota';
    protected static ?string $navigationIcon = 'heroicon-o-users';

 public static function form(Form $form): Form
{
    return $form->schema([

        Forms\Components\Section::make('🧑 Data Pribadi')
            ->description('Isi data diri dengan lengkap')
            ->columns(2)
            ->schema([

                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->placeholder('Masukkan nama lengkap'),

                Forms\Components\TextInput::make('tempat_lahir')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->required(),

                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ])
                    ->native(false)
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'Pelajar' => 'Pelajar',
                        'Mahasiswa' => 'Mahasiswa',
                        'Umum' => 'Umum',
                    ])
                    ->native(false)
                    ->required(),

                Forms\Components\TextInput::make('agama')
                    ->default('Islam')
                    ->disabled()
                    ->dehydrated(true),

                Forms\Components\Select::make('status_pendidikan')
                    ->options([
                        'Pelajar' => 'Pelajar',
                        'Mahasiswa' => 'Mahasiswa',
                        'Umum' => 'Umum',
                    ])
                    ->native(false)
                    ->required(),

                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull()
                    ->rows(3)
                    ->required(),

                Forms\Components\TextInput::make('no_hp')
                    ->tel()
                    ->prefix('+62')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),

            ]),

        Forms\Components\Section::make('🎓 Pendidikan')
            ->columns(2)
            ->schema([

                Forms\Components\TextInput::make('pendidikan_terakhir')->required(),
                Forms\Components\TextInput::make('nama_sekolah')->required(),

                Forms\Components\TextInput::make('jurusan'),
                Forms\Components\TextInput::make('tahun_masuk'),

            ]),

        Forms\Components\Section::make('📅 Keanggotaan')
            ->columns(2)
            ->schema([

                Forms\Components\DatePicker::make('tanggal_daftar')
                    ->default(now())
                    ->required(),

                Forms\Components\Select::make('divisi')
                    ->options([
                        'Pelajar' => 'Pelajar',
                        'Mahasiswa' => 'Mahasiswa',
                        'Umum' => 'Umum',
                    ])
                    ->native(false)
                    ->required(),
            ]),

        Forms\Components\Section::make('📂 Upload Dokumen')
            ->columns(3)
            ->schema([

                Forms\Components\FileUpload::make('foto')
                    ->image()
                    ->imagePreviewHeight('150')
                    ->directory('foto'),

                Forms\Components\FileUpload::make('kartu_pengenal')
                    ->directory('kartu'),

                Forms\Components\FileUpload::make('formulir')
                    ->directory('formulir'),

            ]),
    ]);
}

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('nama')->searchable()->sortable(),

            Tables\Columns\TextColumn::make('jenis_kelamin')->badge(),

            Tables\Columns\TextColumn::make('status')->badge(),

            Tables\Columns\TextColumn::make('no_hp'),

            Tables\Columns\TextColumn::make('divisi')->badge(),

            Tables\Columns\TextColumn::make('tanggal_daftar')->date(),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
}

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnggotars::route('/'),
            'create' => Pages\CreateAnggotar::route('/create'),
            'edit' => Pages\EditAnggotar::route('/{record}/edit'),
        ];
    }
}
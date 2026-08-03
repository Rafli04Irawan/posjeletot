<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen User';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'User';
    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(fn ($record) => $record === null)
                ->nullable()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null),

            Forms\Components\Select::make('role')
                ->label('Role')
                ->options([
                    'admin' => 'Admin',
                    'pegawai' => 'Pegawai',
                    'kasir' => 'Kasir',
                ])
                ->required()
                ->default('pegawai'),

            Forms\Components\Select::make('outlet_id')
                ->label('Outlet')
                ->relationship('outlet', 'nama_outlet')
                ->searchable()
                ->nullable(),

            Forms\Components\CheckboxList::make('menu_permissions')
                ->label('Menu yang Bisa Diakses')
                ->options([
                    'dashboard' => 'Dashboard',
                    'kasir' => 'Kasir',
                    'transaksi' => 'List Transaksi',
                    'produk' => 'Produk',
                    'kategori' => 'Kategori Produk',
                    'bahan_baku' => 'Bahan Baku',
                    'bill_of_material' => 'Bill of Material',
                    'produksi' => 'Produksi',
                    'outlet' => 'Outlet',
                ])
                ->columns(2)
                ->helperText('Centang menu yang boleh diakses oleh user ini.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'admin' => 'success',
                        'pegawai' => 'warning',
                        'kasir' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->label('Outlet')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('menu_permissions')
                    ->label('Akses Menu')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : '-')
                    ->wrap(),
            ])
            ->filters([])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }
}

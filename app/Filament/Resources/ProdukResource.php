<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $modelLabel = 'Produk';
    protected static ?string $pluralModelLabel = 'Produk';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $navigationGroup = 'Manajemen POS';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_produk')
                ->label('Nama Produk')
                ->required()
                ->autofocus(),

            Forms\Components\Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->rows(3),

            Forms\Components\TextInput::make('harga')
                ->label('Harga')
                ->numeric()
                ->required()
                ->prefix('Rp'),

            Forms\Components\TextInput::make('stok')
                ->label('Stok')
                ->numeric()
                ->default(0)
                ->minValue(0),

            Forms\Components\Select::make('kategori_id')
                ->label('Kategori')
                ->relationship('kategori', 'nama_kategori')
                ->searchable()
                ->required(),

            Forms\Components\FileUpload::make('gambar')
                ->label('Gambar Produk')
                ->image()
                ->disk('public')
                ->directory('produk')
                ->visibility('public')
                ->imagePreviewHeight('180')
                ->maxSize(2048),
            Forms\Components\Select::make('outlet_id')
            ->label('Outlet')
            ->relationship('outlet', 'nama_outlet')
            ->searchable()
            ->required()
            ->visible(fn () => auth()->user()?->role === 'admin')
            ->default(fn () => auth()->user()?->outlet_id),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Grid::make(1)->schema([
                    Stack::make([
                        ImageColumn::make('gambar')
                            ->label('')
                            ->disk('public')
                            ->height(180)
                            ->extraImgAttributes([
                                'style' => '
                                    width: 100%;
                                    height: 180px;
                                    object-fit: cover;
                                    border-radius: 14px;
                                    display: block;
                                ',
                            ]),

                        TextColumn::make('nama_produk')
                            ->label('Nama Produk')
                            ->weight('bold')
                            ->size('lg')
                            ->alignCenter()
                            ->searchable(),

                        TextColumn::make('kategori.nama_kategori')
                            ->label('Kategori')
                            ->badge()
                            ->color('primary')
                            ->alignCenter(),

                        TextColumn::make('harga')
                            ->label('Harga')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                            ->weight('bold')
                            ->alignCenter(),

                        TextColumn::make('stok')
                            ->label('Stok')
                            ->badge()
                            ->alignCenter()
                            ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                            ->formatStateUsing(fn ($state) => $state . ' pcs'),
                    ])
                    ->space(3)
                    ->extraAttributes([
                        'style' => '
                            padding: 14px;
                            background: white;
                            border-radius: 18px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                            overflow: hidden;
                            min-height: 340px;
                        ',
                    ]),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
                'xl' => 3,
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->label('Filter Kategori'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-m-trash')
                    ->color('danger'),
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
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->role === 'admin' || $user->hasMenuPermission('produk');
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->role === 'pegawai') {
            $query->where('outlet_id', auth()->user()->outlet_id);
        }

        return $query;
    }
}
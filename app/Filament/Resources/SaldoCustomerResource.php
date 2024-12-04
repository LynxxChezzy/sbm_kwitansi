<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaldoCustomerResource\Pages;
use App\Filament\Resources\SaldoCustomerResource\RelationManagers;
use App\Models\SaldoCustomer;
use App\Models\StatusFollowUp;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SaldoCustomerResource extends Resource
{
    protected static ?string $model = SaldoCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Manajemen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->placeholder('Pilih Customer')
                    ->relationship('customer', 'nama')
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Select::make('tipe_transaksi_id')
                    ->label('Tipe Transaksi')
                    ->placeholder('Pilih Tipe Transaksi')
                    ->relationship('tipeTransaksi', 'nama')
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('status_follow_up_id')
                    ->label('Status')
                    ->placeholder('pilih Status')
                    ->relationship('statusFollowUp', 'nama')
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('deskripsi')
                    ->label('Deskripsi')
                    ->placeholder('Masukkan Deskripsi')
                    ->columnSpanFull()
                    ->maxLength(50)
                    ->hidden()
                    ->required(),
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->placeholder('Pilih Tanggal')
                    ->native(false)
                    ->required(),
                Forms\Components\DatePicker::make('masa')
                    ->label('Masa tenggang')
                    ->placeholder('Pilih Masa Tenggang')
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('deskripsi')
                    ->label('Deskripsi')
                    ->placeholder('Masukkan Deskripsi')
                    ->columnSpanFull()
                    ->maxLength(50)
                    ->required(),
                Forms\Components\TextInput::make('nilai')
                    ->label('Nilai')
                    ->placeholder('Masukkan Nilai')
                    ->numeric()
                    ->prefix('Rp')
                    ->suffix('.00')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->columnSpanFull()
                    ->reactive()
                    ->debounce(800)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Set nilai_jatuh_tempo sama dengan nilai
                        $set('nilai_jatuh_tempo', $state);
                    })
                    ->required(),
                Forms\Components\TextInput::make('nilai_jatuh_tempo')
                    ->label('Nilai Jatuh Tempo')
                    ->placeholder('Masukkan Nilai jatuh Tempo')
                    ->numeric()
                    ->prefix('Rp')
                    ->suffix('.00')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->required(),
                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan')
                    ->placeholder('Masukkan Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Customer')
                    ->description(function (SaldoCustomer $record) {
                        return $record->tipeTransaksi->nama;
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('nomor')
                    ->label('Nomor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('masa')
                    ->label('Masa Tenggang')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nilai')
                    ->label('Harga')
                    ->numeric()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('nilai_jatuh_tempo')
                    ->label('Harga Jatuh Tempo')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('StatusFollowUp.id')
                    ->label('Status Follow Up')
                    ->badge()
                    ->formatStateUsing(fn($record) => $record->StatusFollowUp?->nama)
                    ->color(fn($state) => match ((int)$state) {
                        1 => 'warning',
                        2 => 'warning',
                        3 => 'success',
                        default => 'secondary',
                    })
            ])
            ->filters([
                SelectFilter::make('customer.nama')
                    ->label('Customer')
                    ->relationship('customer', 'nama')
                    ->native(false)
                    ->preload()
                    ->searchable(),
                SelectFilter::make('tipeTransaksi.nama')
                    ->label('Tipe Transaksi')
                    ->relationship('tipeTransaksi', 'nama')
                    ->native(false)
                    ->preload()
                    ->searchable(),
            ])
            ->actions([
                Action::make('Cetak')
                    ->label('Cetak')
                    ->button()
                    ->url(fn(SaldoCustomer $record): string => route('saldoCustomer.cetak', ['id' => $record->id]))
                    ->icon('heroicon-o-document-text')
                    ->openUrlInNewTab(),
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSaldoCustomers::route('/'),
        ];
    }
}

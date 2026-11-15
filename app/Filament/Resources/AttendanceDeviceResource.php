<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceDeviceResource\Pages\CreateAttendanceDevice;
use App\Filament\Resources\AttendanceDeviceResource\Pages\EditAttendanceDevice;
use App\Filament\Resources\AttendanceDeviceResource\Pages\ListAttendanceDevices;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;

class AttendanceDeviceResource extends Resource
{
    protected static ?string $model = AttendanceDevice::class;

    protected static ?string $navigationLabel = 'Attendance devices';

    protected static ?int $navigationSort = 80;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-finger-print';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'People';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'factory-manager', 'hr']) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Device')->columns(2)->schema([
                TextInput::make('name')->required(),
                TextInput::make('provider')->default('virtual')->required(),
                TextInput::make('model')->default('auto'),
                TextInput::make('ip'),
                TextInput::make('port')->numeric()->default(4370),
                TextInput::make('serial_number'),
                TextInput::make('status')->default('offline'),
                TextInput::make('timezone')->default('Asia/Dhaka'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('provider')->badge(),
                TextColumn::make('ip'),
                TextColumn::make('status')->badge(),
                TextColumn::make('last_synced_at')->dateTime(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make()])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceDevices::route('/'),
            'create' => CreateAttendanceDevice::route('/create'),
            'edit' => EditAttendanceDevice::route('/{record}/edit'),
        ];
    }
}

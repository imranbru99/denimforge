<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceLogResource\Pages\ListAttendanceLogs;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;

class AttendanceLogResource extends Resource
{
    protected static ?string $model = AttendanceLog::class;

    protected static ?string $navigationLabel = 'Attendance punches';

    protected static ?int $navigationSort = 81;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-clock';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'People';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'factory-manager', 'hr']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Punch')->columns(2)->schema([
                TextInput::make('employee_id'),
                DateTimePicker::make('punched_at'),
                TextInput::make('punch_type'),
                TextInput::make('verify_mode'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_id')->searchable(),
                TextColumn::make('punched_at')->dateTime()->sortable(),
                TextColumn::make('punch_type')->badge(),
                TextColumn::make('verify_mode'),
                TextColumn::make('device.name'),
            ])
            ->defaultSort('punched_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceLogs::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleManufactureTypeResource\Pages;
use App\Models\VehicleManufactureType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehicleManufactureTypeResource extends Resource
{
    protected static ?string $model = VehicleManufactureType::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'مدیریت سامانه';

    protected static ?int $navigationSort = 40;

    public static function getModelLabel(): string
    {
        return 'نوع تولید';
    }

    public static function getPluralModelLabel(): string
    {
        return 'انواع تولید';
    }

    public static function getNavigationLabel(): string
    {
        return 'انواع تولید و سال';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canManageSystem() === true;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canManageSystem() === true;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->canManageSystem() === true;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->canManageSystem() === true;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('عنوان')
                ->required()
                ->maxLength(100),

            TextInput::make('code')
                ->label('کد انگلیسی')
                ->required()
                ->alphaDash()
                ->unique(ignoreRecord: true)
                ->maxLength(30)
                ->helperText(
                    'مثال: imported یا domestic'
                ),

            Select::make('calendar')
                ->label('تقویم سال ساخت')
                ->options([
                    'gregorian' => 'میلادی',
                    'jalali' => 'شمسی',
                ])
                ->required()
                ->native(false),

            TextInput::make('min_year')
                ->label('حداقل سال مجاز')
                ->numeric()
                ->integer()
                ->required()
                ->minValue(1),

            TextInput::make('sort_order')
                ->label('ترتیب نمایش')
                ->numeric()
                ->integer()
                ->required()
                ->default(0)
                ->minValue(0),

            Toggle::make('is_active')
                ->label('فعال است')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('کد')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('calendar')
                    ->label('تقویم')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'gregorian' => 'میلادی',
                            'jalali' => 'شمسی',
                            default => '-',
                        }
                    ),

                TextColumn::make('min_year')
                    ->label('حداقل سال')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('ترتیب نمایش')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean(),

                TextColumn::make('vehicles_count')
                    ->label('تعداد خودرو')
                    ->counts('vehicles')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت فعال'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف انتخاب‌شده‌ها'),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleManufactureTypes::route('/'),
            'create' => Pages\CreateVehicleManufactureType::route('/create'),
            'edit' => Pages\EditVehicleManufactureType::route('/{record}/edit'),
        ];
    }
}

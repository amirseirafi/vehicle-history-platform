<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleColorResource\Pages;
use App\Models\VehicleColor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehicleColorResource extends Resource
{
    protected static ?string $model = VehicleColor::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'مدیریت سامانه';

    public static function getModelLabel(): string
    {
        return 'رنگ خودرو';
    }

    public static function getPluralModelLabel(): string
    {
        return 'رنگ‌های خودرو';
    }

    public static function getNavigationLabel(): string
    {
        return 'رنگ‌های خودرو';
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
            TextInput::make('code')
                ->label('کد داخلی')
                ->required()
                ->maxLength(50)
                ->alphaDash()
                ->unique(ignoreRecord: true)
                ->helperText(
                    'فقط حروف انگلیسی، عدد، خط تیره و زیرخط؛ مثال: metallic_silver'
                ),

            TextInput::make('name')
                ->label('نام رنگ')
                ->required()
                ->maxLength(100),

            TextInput::make('sort_order')
                ->label('ترتیب نمایش')
                ->numeric()
                ->required()
                ->default(0)
                ->minValue(0),

            Toggle::make('is_active')
                ->label('رنگ فعال است')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('نام رنگ')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('کد داخلی')
                    ->searchable()
                    ->copyable(),

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
            ->defaultSort('sort_order', 'asc')
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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleColors::route('/'),
            'create' => Pages\CreateVehicleColor::route('/create'),
            'edit' => Pages\EditVehicleColor::route('/{record}/edit'),
        ];
    }
}

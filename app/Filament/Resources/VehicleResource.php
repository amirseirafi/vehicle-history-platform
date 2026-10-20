<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'مدیریت سامانه';

    public static function getModelLabel(): string
    {
        return 'خودرو';
    }

    public static function getPluralModelLabel(): string
    {
        return 'خودروها';
    }

    public static function getNavigationLabel(): string
    {
        return 'خودروها';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('شناسه خودرو')
                ->schema([
                    TextInput::make('vin')
                        ->label('VIN')
                        ->maxLength(17)
                        ->minLength(17)
                        ->unique(ignoreRecord: true)
                        ->alphaNum()
                        ->dehydrateStateUsing(
                            fn (?string $state): ?string => $state === null
                                ? null
                                : strtoupper(trim($state))
                        )
                        ->extraInputAttributes([
                            'style' => 'text-transform: uppercase',
                        ])
                        ->helperText('شماره VIN معمولاً ۱۷ کاراکتر است.'),

                    TextInput::make('chassis_number')
                        ->label('شماره شاسی')
                        ->maxLength(100),

                    TextInput::make('engine_number')
                        ->label('شماره موتور')
                        ->maxLength(100),

                    TextInput::make('plate_number')
                        ->label('شماره پلاک')
                        ->maxLength(30),
                ])
                ->columns(2),

            Section::make('مشخصات خودرو')
                ->schema([
                    TextInput::make('brand')
                        ->label('برند')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('model')
                        ->label('مدل')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('trim')
                        ->label('تیپ')
                        ->maxLength(100),

                    TextInput::make('color')
                        ->label('رنگ')
                        ->maxLength(50),

                    TextInput::make('manufacture_year_gregorian')
                        ->label('سال ساخت میلادی')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(2100),

                    TextInput::make('manufacture_year_jalali')
                        ->label('سال ساخت شمسی')
                        ->numeric()
                        ->minValue(1300)
                        ->maxValue(1500)
                        ->helperText('تبدیل خودکار سال‌ها را در مرحله بعد اضافه می‌کنیم.'),

                    TextInput::make('current_mileage')
                        ->label('آخرین کیلومتر ثبت‌شده')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('کیلومتر'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vin')
                    ->label('VIN')
                    ->searchable()
                    ->copyable()
                    ->placeholder('ثبت نشده'),

                TextColumn::make('brand')
                    ->label('برند')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model')
                    ->label('مدل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('manufacture_year_gregorian')
                    ->label('سال ساخت')
                    ->formatStateUsing(function ($state, Vehicle $record): string {
                        if (
                            $record->manufacture_year_gregorian &&
                            $record->manufacture_year_jalali
                        ) {
                            return "{$record->manufacture_year_gregorian} / {$record->manufacture_year_jalali}";
                        }

                        return (string) (
                            $state
                            ?? $record->manufacture_year_jalali
                            ?? '-'
                        );
                    }),

                TextColumn::make('color')
                    ->label('رنگ')
                    ->placeholder('ثبت نشده'),

                TextColumn::make('current_mileage')
                    ->label('کیلومتر')
                    ->numeric()
                    ->placeholder('ثبت نشده'),

                TextColumn::make('created_at')
                    ->label('تاریخ ثبت')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([])
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}

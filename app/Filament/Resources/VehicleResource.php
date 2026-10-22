<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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

    public static function canViewAny(): bool
    {
        return auth()->user()?->canWorkWithVehicles() === true;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canWorkWithVehicles() === true;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->canWorkWithVehicles() === true;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->canManageSystem() === true;
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
                        ->alphaNum()
                        ->regex('/^[A-Za-z0-9]{17}$/')
                        ->unique(ignoreRecord: true)
                        ->dehydrateStateUsing(
                            fn (?string $state): ?string => $state === null
                                ? null
                                : strtoupper(trim($state))
                        )
                        ->extraInputAttributes([
                            'style' => 'text-transform: uppercase',
                            'maxlength' => 17,
                        ])
                        ->helperText('شماره VIN معمولاً ۱۷ کاراکتر است.'),
                    TextInput::make('chassis_number')
                        ->label('شماره شاسی')
                        ->maxLength(100),
                    TextInput::make('engine_number')
                        ->label('شماره موتور')
                        ->maxLength(100),
                ])
                ->columns(3),

            Section::make('پلاک خودرو')
                ->description('نمونه: ایران ۵۰ | ۹۱۲ و ۵۳')
                ->schema([
                    TextInput::make('plate_province_code')
                        ->label('کد استان')
                        ->numeric()
                        ->length(2)
                        ->regex('/^[0-9]{2}$/')
                        ->maxLength(2)
                        ->minLength(2)
                        ->extraInputAttributes([
                            'inputmode' => 'numeric',
                            'maxlength' => 2,
                        ])
                        ->helperText('دو رقم کد استان؛ صفر در این بخش مجاز است.'),
                    TextInput::make('plate_three_digits')
                        ->label('عدد سه‌رقمی')
                        ->numeric()
                        ->length(3)
                        ->regex('/^[1-9][0-9]{2}$/')
                        ->maxLength(3)
                        ->minLength(3)
                        ->extraInputAttributes([
                            'inputmode' => 'numeric',
                            'maxlength' => 3,
                        ])
                        ->helperText('سه رقم؛ رقم اول نمی‌تواند صفر باشد.'),
                    Select::make('plate_letter')
                        ->label('حرف پلاک')
                        ->options([
                            'الف' => 'الف',
                            'ب' => 'ب',
                            'پ' => 'پ',
                            'ت' => 'ت',
                            'ث' => 'ث',
                            'ج' => 'ج',
                            'چ' => 'چ',
                            'ح' => 'ح',
                            'خ' => 'خ',
                            'د' => 'د',
                            'ذ' => 'ذ',
                            'ر' => 'ر',
                            'ز' => 'ز',
                            'ژ' => 'ژ',
                            'س' => 'س',
                            'ش' => 'ش',
                            'ص' => 'ص',
                            'ض' => 'ض',
                            'ط' => 'ط',
                            'ظ' => 'ظ',
                            'ع' => 'ع',
                            'غ' => 'غ',
                            'ف' => 'ف',
                            'ق' => 'ق',
                            'ک' => 'ک',
                            'گ' => 'گ',
                            'ل' => 'ل',
                            'م' => 'م',
                            'ن' => 'ن',
                            'و' => 'و',
                            'ه' => 'ه',
                            'ی' => 'ی',
                            'D' => 'د',
                            'S' => 'س',
                            'ویژه' => 'ویژه',
                            'معلولین' => 'معلولین',
                            'تاکسی' => 'تاکسی',
                            'عمومی' => 'عمومی',
                        ])
                        ->searchable()
                        ->native(false),
                    TextInput::make('plate_two_digits')
                        ->label('عدد دو رقمی')
                        ->numeric()
                        ->length(2)
                        ->regex('/^[1-9][0-9]$/')
                        ->maxLength(2)
                        ->minLength(2)
                        ->extraInputAttributes([
                            'inputmode' => 'numeric',
                            'maxlength' => 2,
                        ])
                        ->helperText('دو رقم؛ رقم اول نمی‌تواند صفر باشد.'),
                ])
                ->columns(4),

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
                        ->label('تیپ یا مدل دقیق')
                        ->maxLength(100),
                    Select::make('body_style')
                        ->label('نوع بدنه')
                        ->options([
                            'hatchback' => 'هاچ‌بک',
                            'sedan' => 'سدان',
                            'crossover' => 'کراس‌اوور',
                            'suv' => 'شاسی‌بلند',
                            'van' => 'ون',
                            'single_cab_pickup' => 'وانت پیکاپ تک‌کابین',
                            'double_cab_pickup' => 'وانت پیکاپ دوکابین',
                        ])
                        ->searchable()
                        ->native(false),
                    Select::make('manufacture_type')
                        ->label('نوع تولید')
                        ->options([
                            'imported' => 'وارداتی',
                            'domestic' => 'تولید داخل',
                        ])
                        ->live()
                        ->required()
                        ->native(false)
                        ->helperText('نوع سال واردشده را مشخص می‌کند.'),
                    TextInput::make('manufacture_year')
                        ->label('سال ساخت')
                        ->numeric()
                        ->required()
                        ->minValue(
                            fn (Get $get): int => $get('manufacture_type') === 'domestic'
                                ? 1300
                                : 1900
                        )
                        ->maxValue(
                            fn (Get $get): int => $get('manufacture_type') === 'domestic'
                                ? self::currentJalaliYear() + 1
                                : now()->year + 1
                        )
                        ->helperText(
                            fn (Get $get): string => $get('manufacture_type') === 'domestic'
                                ? 'سال را به شمسی وارد کنید؛ مثال: ۱۴۰۵'
                                : 'سال را به میلادی وارد کنید؛ مثال: ۲۰۲۶'
                        ),
                    Select::make('color')
                        ->label('رنگ')
                        ->options(
                            fn (Get $get): array => self::colorOptionsWithCurrentValue(
                                $get('color')
                            )
                        )
                        ->searchable()
                        ->native(false)
                        ->live()
                        ->placeholder('رنگ خودرو را انتخاب کنید'),
                    TextInput::make('color_other')
                        ->label('رنگ جدید')
                        ->maxLength(50)
                        ->visible(
                            fn (Get $get): bool => $get('color') === 'other'
                                || filled($get('color_other'))
                        )
                        ->required(fn (Get $get): bool => $get('color') === 'other')
                        ->live()
                        ->afterStateUpdated(
                            function (?string $state, Set $set): void {
                                if (filled($state)) {
                                    $set('color', trim($state));
                                }
                            }
                        )
                        ->dehydrated(false)
                        ->helperText('رنگ موردنظر را وارد کنید.'),
                    TextInput::make('current_mileage')
                        ->label('آخرین کیلومتر ثبت‌شده')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('کیلومتر'),
                ])
                ->columns(3),
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
                TextColumn::make('body_style')
                    ->label('نوع بدنه')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'hatchback' => 'هاچ‌بک',
                            'sedan' => 'سدان',
                            'crossover' => 'کراس‌اوور',
                            'suv' => 'شاسی‌بلند',
                            'van' => 'ون',
                            'single_cab_pickup' => 'وانت پیکاپ تک‌کابین',
                            'double_cab_pickup' => 'وانت پیکاپ دوکابین',
                            default => '-',
                        }
                    ),
                TextColumn::make('manufacture_type')
                    ->label('نوع تولید')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'imported' => 'وارداتی',
                            'domestic' => 'تولید داخل',
                            default => '-',
                        }
                    ),
                TextColumn::make('manufacture_year')
                    ->label('سال ساخت')
                    ->placeholder('ثبت نشده'),
                TextColumn::make('iranian_plate')
                    ->label('پلاک')
                    ->state(
                        fn (Vehicle $record): string => $record->iranian_plate ?? '-'
                    ),
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

    private static function colorOptions(): array
    {
        return [
            'سفید' => 'سفید',
            'سفید صدفی' => 'سفید صدفی',
            'مشکی' => 'مشکی',
            'نقره‌ای' => 'نقره‌ای',
            'خاکستری' => 'خاکستری',
            'دودی' => 'دودی',
            'آبی' => 'آبی',
            'سرمه‌ای' => 'سرمه‌ای',
            'قرمز' => 'قرمز',
            'زرشکی' => 'زرشکی',
            'قهوه‌ای' => 'قهوه‌ای',
            'کرم' => 'کرم',
            'طلایی' => 'طلایی',
            'نارنجی' => 'نارنجی',
            'زرد' => 'زرد',
            'سبز' => 'سبز',
            'بنفش' => 'بنفش',
            'صورتی' => 'صورتی',
            'مسی' => 'مسی',
            'سایر' => 'other',
        ];
    }

    private static function colorOptionsWithCurrentValue(?string $current): array
    {
        $options = self::colorOptions();

        if (
            filled($current)
            && $current !== 'other'
            && ! array_key_exists($current, $options)
        ) {
            $options = [$current => $current] + $options;
        }

        return $options;
    }

    private static function currentJalaliYear(): int
    {
        $gregorianYear = now()->year;

        return $gregorianYear - (now()->month >= 3 ? 621 : 622);
    }
}

<?php

namespace App\Support;

use App\Models\VehicleManufactureType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;

class VehicleManufactureFields
{
    /**
     * فیلدهای مشترک نوع تولید و سال ساخت
     */
    public static function fields(): array
    {
        return [
            self::manufactureTypeField(),
            self::manufactureYearField(),
        ];
    }

    /**
     * فیلد نوع تولید
     */
    public static function manufactureTypeField(): Select
    {
        return Select::make('manufacture_type_id')
            ->label('نوع تولید')
            ->options(
                fn (): array => VehicleManufactureType::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray()
            )
            ->searchable()
            ->native(false)
            ->live()
            ->required()
            ->afterStateUpdated(
                function (?int $state, Set $set): void {
                    $set('manufacture_year', null);
                }
            )
            ->helperText(
                'نوع تولید، تقویم سال ساخت را مشخص می‌کند.'
            );
    }

    /**
     * فیلد سال ساخت با اعتبارسنجی وابسته به نوع تولید
     */
    public static function manufactureYearField(): TextInput
    {
        return TextInput::make('manufacture_year')
            ->label('سال ساخت')
            ->numeric()
            ->integer()
            ->required()
            ->minValue(
                function (Get $get): int {
                    $type = self::selectedType($get);

                    return $type?->min_year ?? 1900;
                }
            )
            ->maxValue(
                function (Get $get): int {
                    $type = self::selectedType($get);

                    return $type?->currentMaxYear()
                        ?? now()->year + 1;
                }
            )
            ->rules(
                function (Get $get): array {
                    return [
                        function (
                            string $attribute,
                            mixed $value,
                            \Closure $fail
                        ) use ($get): void {
                            $type = self::selectedType($get);

                            if (! $type) {
                                $fail(
                                    'ابتدا نوع تولید خودرو را انتخاب کنید.'
                                );

                                return;
                            }

                            $year = (int) $value;
                            $minYear = (int) $type->min_year;
                            $maxYear = (int) $type->currentMaxYear();

                            if (
                                $year < $minYear
                                || $year > $maxYear
                            ) {
                                $calendar = $type->isGregorian()
                                    ? 'میلادی'
                                    : 'شمسی';

                                $fail(
                                    "برای خودروی {$type->name}، سال باید "
                                    . "{$calendar} و بین {$minYear} تا "
                                    . "{$maxYear} باشد."
                                );
                            }
                        },
                    ];
                }
            )
            ->helperText(
                function (Get $get): string {
                    $type = self::selectedType($get);

                    if (! $type) {
                        return 'ابتدا نوع تولید را انتخاب کنید.';
                    }

                    $calendar = $type->isGregorian()
                        ? 'میلادی'
                        : 'شمسی';

                    return "سال را به {$calendar} وارد کنید؛ "
                        . "محدوده مجاز: {$type->min_year} تا "
                        . "{$type->currentMaxYear()}";
                }
            );
    }

    /**
     * نوع تولید انتخاب‌شده را از دیتابیس می‌خواند.
     */
    private static function selectedType(
        Get $get
    ): ?VehicleManufactureType {
        $typeId = $get('manufacture_type_id');

        if (blank($typeId)) {
            return null;
        }

        return VehicleManufactureType::query()
            ->whereKey($typeId)
            ->where('is_active', true)
            ->first();
    }
}

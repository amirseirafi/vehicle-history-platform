<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vin',
        'chassis_number',
        'brand',
        'model',
        'trim',
        'body_style',

        'manufacture_type',
        'manufacture_year',

        'manufacture_year_gregorian',
        'manufacture_year_jalali',

        // ستون قدیمی برای سازگاری موقت
        'color',

        // ساختار جدید رنگ
        'color_id',
        'custom_color',

        'engine_number',

        // ستون قدیمی پلاک
        'plate_number',

        // اجزای پلاک ایران
        'plate_province_code',
        'plate_three_digits',
        'plate_letter',
        'plate_two_digits',

        // فعلاً در مدل باقی می‌ماند؛
        // در فرم ثبت خودرو استفاده نخواهد شد.
        'current_mileage',
    ];

    protected function casts(): array
    {
        return [
            'manufacture_type' => 'string',
            'manufacture_year' => 'integer',
            'manufacture_year_gregorian' => 'integer',
            'manufacture_year_jalali' => 'integer',
            'current_mileage' => 'integer',
        ];
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(VehicleColor::class, 'color_id');
    }

    public function inspectionReports(): HasMany
    {
        return $this->hasMany(InspectionReport::class);
    }

    public function getDisplayColorAttribute(): ?string
    {
        if (filled($this->custom_color)) {
            return $this->custom_color;
        }

        return $this->color?->name;
    }

    public function getIranianPlateAttribute(): ?string
    {
        $provinceCode = trim((string) $this->plate_province_code);
        $threeDigits = trim((string) $this->plate_three_digits);
        $letter = trim((string) $this->plate_letter);
        $twoDigits = trim((string) $this->plate_two_digits);

        if (
            $provinceCode === ''
            && $threeDigits === ''
            && $letter === ''
            && $twoDigits === ''
        ) {
            return null;
        }

        $leftPart = trim(
            $threeDigits
            . ' '
            . $letter
            . ' '
            . $twoDigits
        );

        if ($provinceCode === '') {
            return $leftPart !== '' ? $leftPart : null;
        }

        return trim("ایران {$provinceCode} | {$leftPart}");
    }

    public function getManufactureTypeLabelAttribute(): ?string
    {
        return match ($this->manufacture_type) {
            'imported' => 'وارداتی',
            'domestic' => 'تولید داخل',
            default => null,
        };
    }
}

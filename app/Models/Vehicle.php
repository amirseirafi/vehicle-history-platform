<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

        'manufacture_year_gregorian',
        'manufacture_year_jalali',

        'color',
        'engine_number',

        // فیلد قدیمی؛ برای سازگاری با اطلاعات قبلی حفظ می‌شود.
        'plate_number',

        // اجزای پلاک ایران
        'plate_province_code',
        'plate_three_digits',
        'plate_letter',
        'plate_two_digits',

        'current_mileage',
    ];

    protected function casts(): array
    {
        return [
            'manufacture_year_gregorian' => 'integer',
            'manufacture_year_jalali' => 'integer',
            'current_mileage' => 'integer',
        ];
    }

    public function inspectionReports(): HasMany
    {
        return $this->hasMany(InspectionReport::class);
    }

    /**
     * نمایش پلاک به شکل:
     * ایران 50 | 912 و 53
     */
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
}

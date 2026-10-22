<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class InspectionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_uuid',
        'report_number',
        'inspection_center_id',
        'inspector_id',
        'customer_id',
        'vehicle_id',
        'status',
        'inspection_date',
        'mileage',
        'summary',
        'general_notes',
        'pdf_path',
        'pdf_hash',
        'finalized_at',
        'sms_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'inspection_date' => 'datetime',
            'finalized_at' => 'datetime',
            'sms_sent_at' => 'datetime',
            'mileage' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (InspectionReport $report): void {
            $report->public_uuid ??= (string) Str::uuid();

            $report->report_number ??=
                'R-' . now()->format('YmdHis') . '-'
                . strtoupper(Str::random(5));

            // تاریخ و ساعت فقط هنگام ایجاد گزارش تعیین می‌شود.
            $report->inspection_date ??= now();

            // مرکز و کارشناس از کاربر واردشده دریافت می‌شوند.
            if (auth()->check()) {
                $report->inspector_id ??= auth()->id();

                $report->inspection_center_id ??=
                    auth()->user()->inspection_center_id;
            }
        });
    }

    public function inspectionCenter(): BelongsTo
    {
        return $this->belongsTo(InspectionCenter::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function inspectionItems(): HasMany
    {
        return $this->hasMany(
            InspectionItem::class,
            'inspection_report_id'
        );
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }
}

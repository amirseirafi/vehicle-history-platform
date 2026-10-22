<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InspectionReportResource\Pages;
use App\Models\Customer;
use App\Models\InspectionCenter;
use App\Models\InspectionReport;
use App\Models\User;
use App\Models\Vehicle;
use App\Support\InspectionChecklist;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InspectionReportResource extends Resource
{
    protected static ?string $model = InspectionReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'مدیریت سامانه';

    public static function getModelLabel(): string
    {
        return 'گزارش کارشناسی';
    }

    public static function getPluralModelLabel(): string
    {
        return 'گزارش‌های کارشناسی';
    }

    public static function getNavigationLabel(): string
    {
        return 'گزارش‌های کارشناسی';
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
            Wizard::make([
                Step::make('آورنده خودرو')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Select::make('customer_id')
                            ->label('آورنده خودرو')
                            ->options(
                                fn (): array => Customer::query()
                                    ->orderBy('full_name')
                                    ->get()
                                    ->mapWithKeys(
                                        fn (Customer $customer): array => [
                                            $customer->id =>
                                                $customer->full_name
                                                . ' - '
                                                . $customer->mobile,
                                        ]
                                    )
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('full_name')
                                    ->label('نام و نام خانوادگی')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('mobile')
                                    ->label('شماره موبایل')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),

                                Select::make('relation_to_vehicle')
                                    ->label('نسبت با خودرو')
                                    ->options([
                                        'owner' => 'مالک',
                                        'buyer' => 'خریدار',
                                        'seller' => 'فروشنده',
                                        'representative' => 'نماینده مالک',
                                        'driver' => 'راننده یا آورنده',
                                        'other' => 'سایر',
                                    ])
                                    ->required()
                                    ->default('owner'),

                                ToggleButtons::make('sms_consent')
                                    ->label('رضایت دریافت پیامک')
                                    ->boolean()
                                    ->grouped()
                                    ->default(false),

                                ToggleButtons::make('data_consent')
                                    ->label('رضایت ثبت و پردازش اطلاعات')
                                    ->boolean()
                                    ->grouped()
                                    ->default(false),
                            ])
                            ->createOptionUsing(
                                fn (array $data): int =>
                                    Customer::create($data)->getKey()
                            )
                            ->helperText(
                                'اگر آورنده خودرو در فهرست نیست، از گزینه ایجاد استفاده کنید.'
                            ),
                    ]),

                Step::make('خودرو')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        Select::make('vehicle_id')
                            ->label('خودرو')
                            ->options(
                                fn (): array => Vehicle::query()
                                    ->orderBy('brand')
                                    ->orderBy('model')
                                    ->get()
                                    ->mapWithKeys(
                                        fn (Vehicle $vehicle): array => [
                                            $vehicle->id => trim(
                                                "{$vehicle->brand} "
                                                . "{$vehicle->model}"
                                                . (
                                                    $vehicle->vin
                                                        ? " - {$vehicle->vin}"
                                                        : ''
                                                )
                                            ),
                                        ]
                                    )
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('vin')
                                    ->label('VIN')
                                    ->maxLength(17)
                                    ->minLength(17)
                                    ->unique(table: 'vehicles', column: 'vin')
                                    ->alphaNum()
                                    ->dehydrateStateUsing(
                                        fn (?string $state): ?string =>
                                            $state === null
                                                ? null
                                                : strtoupper(trim($state))
                                    )
                                    ->extraInputAttributes([
                                        'style' => 'text-transform: uppercase',
                                    ]),

                                TextInput::make('chassis_number')
                                    ->label('شماره شاسی')
                                    ->maxLength(100),

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
                                    ->maxValue(1500),

                                TextInput::make('engine_number')
                                    ->label('شماره موتور')
                                    ->maxLength(100),

                                TextInput::make('plate_number')
                                    ->label('شماره پلاک')
                                    ->maxLength(30),

                                TextInput::make('current_mileage')
                                    ->label('آخرین کیلومتر ثبت‌شده')
                                    ->numeric()
                                    ->minValue(0),
                            ])
                            ->createOptionUsing(
                                fn (array $data): int =>
                                    Vehicle::create($data)->getKey()
                            )
                            ->helperText(
                                'اگر خودرو قبلاً ثبت نشده است، از گزینه ایجاد استفاده کنید.'
                            ),
                    ]),

                Step::make('اطلاعات کارشناسی')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Hidden::make('inspection_center_id')
                            ->default(
                                fn (): ?int =>
                                    Auth::user()?->inspection_center_id
                            )
                            ->required()
                            ->dehydrated(),

                        Hidden::make('inspector_id')
                            ->default(
                                fn (): ?int => Auth::id()
                            )
                            ->required()
                            ->dehydrated(),

                        Hidden::make('status')
                            ->default('draft')
                            ->dehydrated(),

                        TextInput::make('mileage')
                            ->label('کیلومتر خودرو در زمان کارشناسی')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('کیلومتر')
                            ->required(),

                        Textarea::make('summary')
                            ->label('خلاصه نتیجه کارشناسی')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('general_notes')
                            ->label('توضیحات عمومی')
                            ->rows(5)
                            ->columnSpanFull(),

                        Repeater::make('inspectionItems')
                            ->label('چک‌لیست بدنه و شاسی')
                            ->relationship('inspectionItems')
                            ->schema([
                                Hidden::make('part_key'),
                                Hidden::make('part_name'),
                                Hidden::make('side'),

                                TextInput::make('part_display')
                                    ->label('قطعه')
                                    ->formatStateUsing(
                                        function (
                                            $state,
                                            Get $get
                                        ): string {
                                            $partName =
                                                $get('part_name') ?? '';

                                            $side = $get('side');

                                            return trim(
                                                $partName
                                                . ($side
                                                    ? " - سمت {$side}"
                                                    : '')
                                            );
                                        }
                                    )
                                    ->disabled()
                                    ->dehydrated(false),

                                ToggleButtons::make('status')
                                    ->label('وضعیت قطعه')
                                    ->options(
                                        InspectionChecklist::statusOptions()
                                    )
                                    ->inline()
                                    ->grouped()
                                    ->live()
                                    ->required()
                                    ->disableOptionWhen(
                                        function (
                                            string $value,
                                            Get $get
                                        ): bool {
                                            return $value === 'original'
                                                && filled($get('notes'));
                                        }
                                    )
                                    ->afterStateUpdated(
                                        function (
                                            ?string $state,
                                            Set $set
                                        ): void {
                                            if ($state === 'original') {
                                                $set('notes', null);
                                            }
                                        }
                                    ),

                                Textarea::make('notes')
                                    ->label('توضیحات وضعیت قطعه')
                                    ->rows(2)
                                    ->live()
                                    ->disabled(
                                        fn (Get $get): bool =>
                                            $get('status') === 'original'
                                    )
                                    ->required(
                                        fn (Get $get): bool =>
                                            $get('status') !== 'original'
                                    )
                                    ->rules(
                                        function (
                                            Get $get
                                        ): \Closure {
                                            return function (
                                                string $attribute,
                                                mixed $value,
                                                \Closure $fail
                                            ) use ($get): void {
                                                $status = $get('status');

                                                if (
                                                    $status === 'original'
                                                    && filled($value)
                                                ) {
                                                    $fail(
                                                        'برای قطعه سالم و فابریک، توضیحات باید خالی باشد.'
                                                    );
                                                }

                                                if (
                                                    $status !== 'original'
                                                    && blank($value)
                                                ) {
                                                    $fail(
                                                        'برای این وضعیت، توضیحات قطعه الزامی است.'
                                                    );
                                                }
                                            };
                                        },
                                    ),
                            ])
                            ->default(
                                collect(InspectionChecklist::parts())
                                    ->map(
                                        fn (array $part): array => [
                                            'part_key' =>
                                                $part['part_key'],
                                            'part_name' =>
                                                $part['part_name'],
                                            'side' =>
                                                $part['side'],
                                            'status' =>
                                                'not_checked',
                                            'notes' => null,
                                        ]
                                    )
                                    ->values()
                                    ->all()
                            )
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(
                                fn (array $state): ?string => trim(
                                    ($state['part_name'] ?? '')
                                    . (
                                        ! empty($state['side'])
                                            ? ' - سمت '
                                            . $state['side']
                                            : ''
                                    )
                                )
                            )
                            ->columnSpanFull(),
                    ]),
            ])
                ->columnSpanFull()
                ->persistStepInQueryString(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('report_number')
                    ->label('شماره گزارش')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('vehicle.brand')
                    ->label('برند'),

                TextColumn::make('vehicle.model')
                    ->label('مدل'),

                TextColumn::make('vehicle.vin')
                    ->label('VIN')
                    ->searchable(),

                TextColumn::make('customer.full_name')
                    ->label('آورنده خودرو'),

                TextColumn::make('inspectionCenter.name')
                    ->label('مرکز کارشناسی'),

                TextColumn::make('inspector.name')
                    ->label('کارشناس'),

                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'draft' => 'پیش‌نویس',
                            'submitted' => 'ارسال‌شده برای بررسی',
                            'approved' => 'تأییدشده',
                            'finalized' => 'نهایی‌شده',
                            'cancelled' => 'لغوشده',
                            default => '-',
                        }
                    ),

                TextColumn::make('inspection_date')
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
            'index' => Pages\ListInspectionReports::route('/'),
            'create' => Pages\CreateInspectionReport::route('/create'),
            'edit' => Pages\EditInspectionReport::route('/{record}/edit'),
        ];
    }
}

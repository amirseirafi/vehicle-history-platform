<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InspectionCenterResource\Pages;
use App\Models\InspectionCenter;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InspectionCenterResource extends Resource
{
    protected static ?string $model = InspectionCenter::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'مدیریت سامانه';

    public static function getModelLabel(): string
    {
        return 'مرکز کارشناسی';
    }

    public static function getPluralModelLabel(): string
    {
        return 'مراکز کارشناسی';
    }

    public static function getNavigationLabel(): string
    {
        return 'مراکز کارشناسی';
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
            Section::make('اطلاعات مرکز')
                ->schema([
                    TextInput::make('name')
                        ->label('نام مرکز')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('slug')
                        ->label('شناسه انگلیسی مرکز')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->alphaDash()
                        ->helperText('مثال: markaz-karshenasi-tehran'),

                    FileUpload::make('logo_path')
                        ->label('لوگوی مرکز')
                        ->image()
                        ->disk('public')
                        ->directory('inspection-centers/logos')
                        ->imageEditor(),

                    ColorPicker::make('primary_color')
                        ->label('رنگ اصلی مرکز')
                        ->default('#2563eb'),

                    Toggle::make('is_active')
                        ->label('مرکز فعال است')
                        ->default(true),
                ])
                ->columns(2),

            Section::make('اطلاعات تماس')
                ->schema([
                    TextInput::make('phone')
                        ->label('تلفن ثابت')
                        ->tel()
                        ->maxLength(30),

                    TextInput::make('mobile')
                        ->label('شماره موبایل')
                        ->tel()
                        ->maxLength(30),

                    TextInput::make('city')
                        ->label('شهر')
                        ->maxLength(100),

                    Textarea::make('address')
                        ->label('آدرس')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('شرایط و قوانین مرکز')
                ->schema([
                    Textarea::make('terms_text')
                        ->label('متن شرایط و قوانین')
                        ->rows(8)
                        ->columnSpanFull()
                        ->helperText('این متن در گزارش کارشناسی مرکز نمایش داده می‌شود.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('لوگو')
                    ->disk('public')
                    ->circular(),

                TextColumn::make('name')
                    ->label('نام مرکز')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city')
                    ->label('شهر')
                    ->searchable(),

                TextColumn::make('mobile')
                    ->label('موبایل'),

                TextColumn::make('slug')
                    ->label('شناسه'),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean(),

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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInspectionCenters::route('/'),
            'create' => Pages\CreateInspectionCenter::route('/create'),
            'edit' => Pages\EditInspectionCenter::route('/{record}/edit'),
        ];
    }
}

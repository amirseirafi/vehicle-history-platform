<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\InspectionCenter;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'مدیریت سامانه';

    public static function getModelLabel(): string
    {
        return 'کاربر';
    }

    public static function getPluralModelLabel(): string
    {
        return 'کاربران';
    }

    public static function getNavigationLabel(): string
    {
        return 'کاربران';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('نام و نام خانوادگی')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('ایمیل')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('رمز عبور')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->dehydrateStateUsing(
                    fn (?string $state): ?string => filled($state)
                        ? Hash::make($state)
                        : null
                ),

            Select::make('role')
                ->label('نقش کاربر')
                ->options([
                    'admin' => 'مدیر سامانه',
                    'center_manager' => 'مدیر مرکز',
                    'inspector' => 'کارشناس خودرو',
                ])
                ->required()
                ->default('inspector'),

            Select::make('inspection_center_id')
                ->label('مرکز کارشناسی')
                ->options(
                    fn (): array => InspectionCenter::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('برای مدیر سامانه می‌تواند خالی باشد.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('نقش')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'admin' => 'مدیر سامانه',
                            'center_manager' => 'مدیر مرکز',
                            'inspector' => 'کارشناس خودرو',
                            default => $state ?? '-',
                        }
                    ),

                TextColumn::make('inspectionCenter.name')
                    ->label('مرکز کارشناسی')
                    ->placeholder('بدون مرکز'),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

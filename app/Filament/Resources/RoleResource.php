<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Role;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Permission;
use Filament\Tables\Table;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\PermissionRegistrar;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoleResource\RelationManagers;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('name')
                            ->label(__('Name'))
                            ->options(UserRoleEnum::class),
                    ])
                    ->columns([
                        'sm' => 2,
                    ]),
                Forms\Components\Section::make('Izin yang diberikan')
                    ->schema(static::getPermissionEntitySchema())
                    ->collapsible(false)
                    ->columns([
                        'sm' => 4,
                    ]),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->header(view('filament.tables.header', [
                'heading' => 'Data Role Pengguna'
            ]))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getPermissionEntitySchema(): array
    {
        $permissions = app(PermissionRegistrar::class)->getPermissions();
        $protectedPermissionContexts = Permission::getProtectedPermissionContexts();
        $permissionsGroupedByEntity = $permissions
            ->filter(fn($permission) => !in_array($permission->context, $protectedPermissionContexts))
            ->groupBy(function ($permission) {
                // Split the permission name by the first colon
                // Example : "User:update" become ["User", "update"]
                [$context, $action] = explode(':', $permission->name);

                return $context;
            })->map(function ($permissions, $key) {


                return Fieldset::make($key)
                    ->label(Str::headline($key))
                    ->columns(1)
                    ->extraAttributes([
                        'class' => 'h-full',
                    ])
                    ->columnSpan([
                        'lg' => 2,
                        'xl' => 1,
                    ])
                    ->schema($permissions->map(function ($permission) {
                        [$context, $action] = explode(':', $permission->name);
                        return Checkbox::make('permissions.' . $permission->name)
                            ->label(Str::headline($action));
                    })
                        ->toArray());
            })->toArray();

        return $permissionsGroupedByEntity;
    }
}

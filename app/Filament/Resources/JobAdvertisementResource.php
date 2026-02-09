<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobAdvertisementResource\Pages;
use App\Models\JobAdvertisement;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class JobAdvertisementResource extends Resource
{
    protected static ?string $model = JobAdvertisement::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-briefcase';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Job Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Job Information')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('requirements')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('benefits')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Job Details')
                    ->schema([
                        Forms\Components\Select::make('employment_type')
                            ->options([
                                'full-time' => 'Full Time',
                                'part-time' => 'Part Time',
                                'contract' => 'Contract',
                                'freelance' => 'Freelance',
                                'internship' => 'Internship',
                            ]),
                        Forms\Components\Select::make('experience_level')
                            ->options([
                                'entry' => 'Entry Level',
                                'mid' => 'Mid Level',
                                'senior' => 'Senior Level',
                                'executive' => 'Executive',
                            ]),
                        Forms\Components\TextInput::make('salary_min')
                            ->numeric(),
                        Forms\Components\TextInput::make('salary_max')
                            ->numeric(),
                        Forms\Components\TextInput::make('currency')
                            ->default('USD')
                            ->maxLength(3),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_remote')
                            ->default(false),
                        Forms\Components\DatePicker::make('application_deadline'),
                    ])->columns(2),
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'closed' => 'Closed',
                                'archived' => 'Archived',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->visible(fn ($get) => $get('status') === 'published'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_remote')
                    ->boolean()
                    ->label('Remote'),
                Tables\Columns\TextColumn::make('views_count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'closed' => 'warning',
                        'archived' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('company_id')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_remote')
                    ->label('Remote')
                    ->placeholder('All jobs')
                    ->trueLabel('Remote only')
                    ->falseLabel('On-site only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobAdvertisements::route('/'),
            'create' => Pages\CreateJobAdvertisement::route('/create'),
            'edit' => Pages\EditJobAdvertisement::route('/{record}/edit'),
        ];
    }
}

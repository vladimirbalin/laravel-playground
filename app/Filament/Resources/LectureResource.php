<?php

namespace App\Filament\Resources;

use App\Enums\LectureContentTypeEnum;
use App\Filament\Resources\LectureResource\Pages;
use App\Filament\Resources\LectureResource\RelationManagers;
use App\Models\Lecture\Category;
use App\Models\Lecture\Lecture;
use App\Models\LectureContentType;
use App\Models\LecturePaymentType;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LectureResource extends Resource
{
    protected static ?string $model = Lecture::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\Grid::make(1)
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\TextInput::make('id')
                                ->label('ID, заполняется автоматически')
                                ->disabled()
                                ->visible(false),
                            Forms\Components\TextInput::make('title')
                                ->label('Наименование лекции')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('lector_id')
                                ->label('Лектор')
                                ->relationship('lector', 'name')
                                ->required(),
                            Forms\Components\Select::make('category_id')
                                ->label('Подкатегория лекции')
                                ->options(Category::subcategories()->get()->pluck('title', 'id'))
                                ->required(),
                        ]),
                    Forms\Components\FileUpload::make('preview_picture')
                        ->directory('images/lectures')
                        ->label('Превью лекции')
                        ->maxSize(10240)
                        ->image()
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('4:3')
                        ->imageResizeTargetWidth('640')
                        ->imageResizeTargetHeight('480'),
                ])->columns(2),
                Forms\Components\Card::make([
                    Forms\Components\RichEditor::make('description')
                        ->label('Описание лекции')
                        ->toolbarButtons([
                            'bold',
                            'h2',
                            'h3',
                            'italic',
                            'redo',
                            'strike',
                            'undo',
                            'preview',
                        ])
                        ->maxLength(65535),
                ]),
                Forms\Components\Section::make('Тип контента лекции')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('content_type_id')
                            ->options(LectureContentType::all()->pluck('title_ru', 'id')->toArray())
                            ->reactive()
                            ->label('Тип')
                            ->required()
                            ->afterStateUpdated(function (callable $set, callable $get, ?Model $record, string $context) {
                                if ($context === 'create') {
                                    return;
                                }

                                if ($record->contentType->id != $get('content_type_id')) {
                                    $set('content', null);
                                } elseif (
                                    $record->contentType->id == $get('content_type_id')
                                    && $record->contentType->id == LectureContentTypeEnum::PDF->value
                                ) {
                                    $set('content', [$record->content]);
                                } elseif (
                                    $record->contentType->id == $get('content_type_id')
                                ) {
                                    $set('content', $record->content);
                                }
                            }),

                        Forms\Components\TextInput::make('content')
                            ->label('kinescope id (должен быть уникальным)')
                            ->visible(function (callable $get) {
                                return $get('content_type_id') == LectureContentTypeEnum::KINESCOPE->value;
                            })
                            ->required()
                            ->unique(table: Lecture::class, column: 'content', ignoreRecord: true)
                            ->afterStateHydrated(function (TextInput $component, ?Model $record, string $context) {
                                if ($context === 'create') {
                                    return;
                                }

                                if ($record->contentType->id != LectureContentTypeEnum::PDF->value) {
                                    $component->state($record->content);
                                } else {
                                    $component->state([$record->content]);
                                }
                            }),

                        Forms\Components\FileUpload::make('content')
                            ->label('pdf')
                            ->directory('pdf')
                            ->rules(['mimes:pdf'])
                            ->required()
                            ->visible(function (callable $get) {
                                return $get('content_type_id') == LectureContentTypeEnum::PDF->value;
                            })
                            ->afterStateHydrated(function (Forms\Components\FileUpload $component, ?Model $record, string $context) {
                                if ($context === 'create') {
                                    return;
                                }

                                if ($record->contentType->id != LectureContentTypeEnum::PDF->value) {
                                    $component->state($record->content);
                                } else {
                                    $component->state([$record->content]);
                                }
                            }),

                        Forms\Components\TextInput::make('content')
                            ->label('ссылка на youtube/rutube видео')
                            ->visible(function (callable $get) {
                                return $get('content_type_id') == LectureContentTypeEnum::EMBED->value;
                            })
                            ->required()
                            ->afterStateHydrated(function (TextInput $component, ?Model $record, string $context) {
                                if ($context === 'create') {
                                    return;
                                }

                                if ($record->contentType->id != LectureContentTypeEnum::PDF->value) {
                                    $component->state($record->content);
                                } else {
                                    $component->state([$record->content]);
                                }
                            }),
                    ]),
                Forms\Components\Section::make('Форма распространения')
                    ->schema([

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('payment_type_id')
                                ->options(LecturePaymentType::all()->pluck('title_ru', 'id'))
                                ->label('Форма распространения')
                                ->required()->columnSpan(1),
                        ]),
                        Forms\Components\Grid::make(1)->schema([
                            Forms\Components\Toggle::make('show_tariff_1')
                                ->label('тариф 1')
                                ->default(true),
                            Forms\Components\Toggle::make('show_tariff_2')
                                ->label('тариф 2')
                                ->default(true),
                            Forms\Components\Toggle::make('show_tariff_3')
                                ->label('тариф 3')
                                ->default(true),
                        ])->columnSpan(1),
                        Forms\Components\Grid::make(1)->schema([
                            Forms\Components\Toggle::make('is_published')
                                ->required()
                                ->label('опубликованная')
                                ->default(true),
                            Forms\Components\Toggle::make('is_recommended')
                                ->label('рекомендованная')
                                ->required(),
                        ])->columnSpan(1),
                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Наименование')
                    ->limit(35)
                    ->tooltip(fn (Model $record): string => $record->title)
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.title')
                    ->label('Подкатегория')
                    ->limit(25)
                    ->tooltip(fn (Model $record): string => isset($record->category) ? $record->category->title : '')
                    ->toggleable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('lector.name')
                    ->label('Лектор')
                    ->tooltip(fn (Model $record): string => isset($record->lector) ? $record->lector->name : '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('contentType.title_ru')
                    ->label('Тип')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('preview_picture')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Превью изображение лекции'),
                Tables\Columns\TextColumn::make('averageRate.rating')
                    ->default('пока нет оценок')
                    ->label('Рейтинг, из 10')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликована ли')
                    ->boolean()
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
            'index' => Pages\ListLectures::route('/'),
            'create' => Pages\CreateLecture::route('/create'),
            'edit' => Pages\EditLecture::route('/{record}/edit'),
        ];
    }
}

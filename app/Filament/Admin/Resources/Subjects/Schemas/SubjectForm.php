<?php

namespace App\Filament\Admin\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;

class SubjectForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                // Basic Information Section
                Select::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->columnSpan(3)
                    ->helperText('Select the school this subject belongs to'),

                TextInput::make('name')
                    ->label('Subject Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Mathematics, Physics, English Literature')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (! $state) return;

                        // Auto-generate subject code if not set
                        $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $state), 0, 6));
                        $set('code', $code . '101');
                    })
                    ->columnSpan(2)
                    ->helperText('Full name of the subject as it appears in curriculum'),

                TextInput::make('code')
                    ->label('Subject Code')
                    ->maxLength(20)
                    ->placeholder('e.g., MATH101, PHY201, ENG301')
                    ->unique(ignoreRecord: true)
                    ->suffixIcon('heroicon-o-hashtag')
                    ->columnSpan(1)
                    ->helperText('Unique identifier for the subject'),

                // Classification Section
                Select::make('type')
                    ->label('Subject Type')
                    ->options([
                        'core' => ' Core Subject',
                        'elective' => ' Elective Subject',
                        'extra_curricular' => ' Extra-Curricular',
                    ])
                    ->default('core')
                    ->required()
                    ->live()
                    ->columnSpan(1)
                    ->helperText('Core: Mandatory, Elective: Optional, Extra-curricular: Non-academic'),

                TextInput::make('credits')
                    ->label('Credit Hours')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(10)
                    ->step(0.5)
                    ->suffix('credits')
                    ->columnSpan(1)
                    ->helperText('Academic credit value (typically 1-6 credits)')
                    ->rules(['numeric', 'min:1', 'max:10']),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => ' Active',
                        'inactive' => ' Inactive'
                    ])
                    ->default('active')
                    ->required()
                    ->columnSpan(1)
                    ->helperText('Only active subjects appear in class schedules'),

                // Description Section
                RichEditor::make('description')
                    ->label('Subject Description')
                    ->placeholder(' Provide a comprehensive description including:
 Learning objectives and outcomes
 Curriculum overview and key topics
 Prerequisites (if any)
 Assessment methods
 Career relevance')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'bulletList',
                        'orderedList',
                        'link',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull()
                    ->helperText('Rich text editor with formatting options'),

                // System Information (only on edit)
                Placeholder::make('created_info')
                    ->label('Created')
                    ->content(fn($record): string => $record?->created_at?->format('M j, Y g:i A') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? 'Not created yet')
                    ->columnSpan(1)
                    ->hidden(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),

                Placeholder::make('updated_info')
                    ->label('Last Modified')
                    ->content(fn($record): string => $record?->updated_at?->format('M j, Y g:i A') . ' (' . $record?->updated_at?->diffForHumans() . ')' ?? 'Not modified yet')
                    ->columnSpan(1)
                    ->hidden(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),

                Placeholder::make('stats_info')
                    ->label('Statistics')
                    ->content(
                        fn($record): string =>
                        $record ?
                            " Teachers: " . $record->teachers()->count() . " | Classes: " . $record->classes()->count() :
                            'Statistics will appear after creation'
                    )
                    ->columnSpan(1)
                    ->hidden(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
            ])
            ->columns(3);
    }
}

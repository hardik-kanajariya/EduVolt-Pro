<?php

namespace App\Filament\Student\Resources\MyAssignmentsResource\Pages;

use App\Filament\Student\Resources\MyAssignmentsResource;
use App\Models\AssignmentSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class SubmitAssignment extends Page
{
    protected static string $resource = MyAssignmentsResource::class;

    protected static string $view = 'filament.student.resources.my-assignments-resource.pages.submit-assignment';

    public $assignment;
    public $submission;
    public $content = '';
    public $attachments = [];
    public $status = 'draft';

    public function mount($record): void
    {
        $user = Auth::user();
        
        if (!$user || !$user->isStudent() || !$user->student) {
            redirect()->route('filament.student.auth.login');
        }

        $this->assignment = $record;
        
        // Check if student has existing submission
        $this->submission = AssignmentSubmission::where('assignment_id', $this->assignment->id)
            ->where('student_id', $user->student->id)
            ->first();

        if ($this->submission) {
            $this->content = $this->submission->content ?? '';
            $this->attachments = $this->submission->attachments ?? [];
            $this->status = $this->submission->status;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Assignment Details')
                    ->schema([
                        Forms\Components\Placeholder::make('title')
                            ->content($this->assignment->title ?? ''),

                        Forms\Components\Placeholder::make('description')
                            ->content($this->assignment->description ?? ''),

                        Forms\Components\Placeholder::make('due_date')
                            ->content($this->assignment->due_date ? $this->assignment->due_date->format('M d, Y H:i') : ''),

                        Forms\Components\Placeholder::make('total_marks')
                            ->content($this->assignment->total_marks ?? ''),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Your Submission')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->required(),

                        Forms\Components\FileUpload::make('attachments')
                            ->multiple()
                            ->directory('assignment-submissions')
                            ->acceptedFileTypes(['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'])
                            ->maxSize(10240), // 10MB

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Save as Draft',
                                'submitted' => 'Submit for Review',
                            ])
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        try {
            if ($this->submission) {
                // Update existing submission
                $this->submission->update([
                    'content' => $data['content'],
                    'attachments' => $data['attachments'] ?? [],
                    'status' => $data['status'],
                    'submitted_at' => $data['status'] === 'submitted' ? now() : $this->submission->submitted_at,
                ]);
            } else {
                // Create new submission
                $this->submission = AssignmentSubmission::create([
                    'assignment_id' => $this->assignment->id,
                    'student_id' => $user->student->id,
                    'content' => $data['content'],
                    'attachments' => $data['attachments'] ?? [],
                    'status' => $data['status'],
                    'submitted_at' => $data['status'] === 'submitted' ? now() : null,
                ]);
            }

            $message = $data['status'] === 'submitted' 
                ? 'Assignment submitted successfully!' 
                : 'Assignment saved as draft!';

            Notification::make()
                ->title($message)
                ->success()
                ->send();

            $this->redirect(route('filament.student.resources.my-assignments.index'));

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to save assignment submission. Please try again.')
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Save')
                ->action('submit')
                ->icon('heroicon-o-document-check'),
        ];
    }

    public function getTitle(): string
    {
        return 'Submit Assignment: ' . $this->assignment->title;
    }
}

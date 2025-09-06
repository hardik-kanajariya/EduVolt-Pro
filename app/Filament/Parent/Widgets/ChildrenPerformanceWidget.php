<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Student;
use App\Models\Grade;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class ChildrenPerformanceWidget extends BaseWidget
{
    protected static ?string $heading = 'Children Recent Performance';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();

        // Get children for this parent
        $children = Student::whereHas('user', function ($query) use ($user) {
            $query->where('parent_email', $user->email);
        })->orWhere('parent_email', $user->email)->get();

        if ($children->isEmpty()) {
            return $table->query(Grade::query()->whereRaw('1 = 0'));
        }

        return $table
            ->query(
                Grade::query()
                    ->whereIn('student_id', $children->pluck('id'))
                    ->with(['student.user', 'subject'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Child Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject'),
                Tables\Columns\TextColumn::make('exam_name')
                    ->label('Exam/Test'),
                Tables\Columns\TextColumn::make('marks_obtained')
                    ->label('Marks Obtained')
                    ->numeric(),
                Tables\Columns\TextColumn::make('total_marks')
                    ->label('Total Marks')
                    ->numeric(),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('Percentage')
                    ->getStateUsing(function ($record) {
                        return $record->total_marks > 0 ?
                            round(($record->marks_obtained / $record->total_marks) * 100, 1) . '%' :
                            'N/A';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->total_marks == 0) return 'gray';
                        $percentage = ($record->marks_obtained / $record->total_marks) * 100;
                        return $percentage >= 60 ? 'success' : ($percentage >= 40 ? 'warning' : 'danger');
                    }),
                Tables\Columns\TextColumn::make('grade')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'A+' => 'success',
                        'A' => 'success',
                        'B+' => 'info',
                        'B' => 'info',
                        'C' => 'warning',
                        'D' => 'danger',
                        'F' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10]);
    }
}

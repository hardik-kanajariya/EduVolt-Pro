@extends('reports.layout')

@section('content')
<div class="section">
    <h2 class="section-title">Progress Summary</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">{{ count($data['student_progress'] ?? []) }}</div>
            <div class="stat-label">Total Students</div>
        </div>
        @if(isset($data['statistics']['average_overall_progress']))
        <div class="stat-item">
            <div class="stat-value">{{ number_format($data['statistics']['average_overall_progress'], 1) }}%</div>
            <div class="stat-label">Average Progress</div>
        </div>
        @endif
        @if(isset($data['statistics']['students_above_average']))
        <div class="stat-item">
            <div class="stat-value">{{ $data['statistics']['students_above_average'] }}</div>
            <div class="stat-label">Above Average</div>
        </div>
        @endif
        @if(isset($data['statistics']['students_below_average']))
        <div class="stat-item">
            <div class="stat-value">{{ $data['statistics']['students_below_average'] }}</div>
            <div class="stat-label">Below Average</div>
        </div>
        @endif
    </div>
</div>

@if(isset($data['student_progress']) && count($data['student_progress']) > 0)
<div class="section">
    <h2 class="section-title">Student Progress Details</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Overall Progress (%)</th>
                <th>Grade</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['student_progress'] as $progress)
            <tr>
                <td>{{ $progress->student->name }}</td>
                <td>{{ $progress->schoolClass->name }}</td>
                <td>{{ $progress->subject->name }}</td>
                <td>{{ number_format($progress->overall_progress, 1) }}%</td>
                <td>{{ $progress->current_grade ?? 'N/A' }}</td>
                <td>{{ $progress->updated_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if(isset($data['statistics']['subject_breakdown']) && count($data['statistics']['subject_breakdown']) > 0)
<div class="section page-break">
    <h2 class="section-title">Subject-wise Performance</h2>
    @foreach($data['statistics']['subject_breakdown'] as $subject => $stats)
    <div class="summary-card">
        <h4>{{ $subject }}</h4>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $stats['student_count'] }}</div>
                <div class="stat-label">Students</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($stats['average_progress'], 1) }}%</div>
                <div class="stat-label">Avg Progress</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($stats['highest_progress'], 1) }}%</div>
                <div class="stat-label">Highest</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($stats['lowest_progress'], 1) }}%</div>
                <div class="stat-label">Lowest</div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@if(isset($data['recommendations']) && count($data['recommendations']) > 0)
<div class="section">
    <h2 class="section-title">Recommendations</h2>
    <ul>
        @foreach($data['recommendations'] as $recommendation)
        <li>{{ $recommendation }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection
@extends('reports.layout')

@section('content')
<style>
    .attendance-status {
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 12px;
    }
    .attendance-status-present {
        background-color: #d4edda;
        color: #155724;
    }
    .attendance-status-late {
        background-color: #fff3cd;
        color: #856404;
    }
    .attendance-status-absent {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
<div class="section">
    <h2 class="section-title">Attendance Overview</h2>
    <div class="stats-grid">
        @if(isset($data['statistics']['total_students']))
        <div class="stat-item">
            <div class="stat-value">{{ $data['statistics']['total_students'] }}</div>
            <div class="stat-label">Total Students</div>
        </div>
        @endif
        @if(isset($data['statistics']['overall_attendance_rate']))
        <div class="stat-item">
            <div class="stat-value">{{ number_format($data['statistics']['overall_attendance_rate'], 1) }}%</div>
            <div class="stat-label">Overall Attendance</div>
        </div>
        @endif
        @if(isset($data['statistics']['present_today']))
        <div class="stat-item">
            <div class="stat-value">{{ $data['statistics']['present_today'] }}</div>
            <div class="stat-label">Present Today</div>
        </div>
        @endif
        @if(isset($data['statistics']['absent_today']))
        <div class="stat-item">
            <div class="stat-value">{{ $data['statistics']['absent_today'] }}</div>
            <div class="stat-label">Absent Today</div>
        </div>
        @endif
    </div>
</div>

@if(isset($data['attendance_records']) && count($data['attendance_records']) > 0)
<div class="section">
    <h2 class="section-title">Detailed Attendance Records</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Student</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Time In</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['attendance_records'] as $record)
            <tr>
                <td>{{ $record->date->format('M d, Y') }}</td>
                <td>{{ $record->student->name }}</td>
                <td>{{ $record->schoolClass->name }}</td>
                <td>{{ $record->subject->name }}</td>
                <td>
                    <span class="attendance-status attendance-status-{{ $record->status }}">
                        {{ ucfirst($record->status) }}
                    </span>
                </td>
                <td>{{ $record->time_in ? $record->time_in->format('H:i') : 'N/A' }}</td>
                <td>{{ $record->remarks ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if(isset($data['daily_summary']) && count($data['daily_summary']) > 0)
<div class="section page-break">
    <h2 class="section-title">Daily Attendance Summary</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Students</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>Attendance Rate (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['daily_summary'] as $date => $summary)
            <tr>
                <td>{{ $date }}</td>
                <td>{{ $summary['total'] ?? 0 }}</td>
                <td>{{ $summary['present'] ?? 0 }}</td>
                <td>{{ $summary['absent'] ?? 0 }}</td>
                <td>{{ $summary['late'] ?? 0 }}</td>
                <td>{{ number_format($summary['rate'] ?? 0, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if(isset($data['student_attendance_summary']) && count($data['student_attendance_summary']) > 0)
<div class="section">
    <h2 class="section-title">Student-wise Attendance Summary</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Class</th>
                <th>Total Days</th>
                <th>Present Days</th>
                <th>Absent Days</th>
                <th>Late Days</th>
                <th>Attendance Rate (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['student_attendance_summary'] as $student => $summary)
            <tr>
                <td>{{ $student }}</td>
                <td>{{ $summary['class'] ?? 'N/A' }}</td>
                <td>{{ $summary['total_days'] ?? 0 }}</td>
                <td>{{ $summary['present_days'] ?? 0 }}</td>
                <td>{{ $summary['absent_days'] ?? 0 }}</td>
                <td>{{ $summary['late_days'] ?? 0 }}</td>
                <td>
                    <span style="
                                    background-color: {{ $summary['attendance_rate'] >= 90 ? '#d4edda' : ($summary['attendance_rate'] >= 75 ? '#fff3cd' : '#f8d7da') }};
                                    color: {{ $summary['attendance_rate'] >= 90 ? '#155724' : ($summary['attendance_rate'] >= 75 ? '#856404' : '#721c24') }};
                                    padding: 2px 8px;
                                    border-radius: 3px;
                                    font-weight: bold;
                                ">
                        {{ number_format($summary['attendance_rate'] ?? 0, 1) }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if(isset($data['poor_attendance_alerts']) && count($data['poor_attendance_alerts']) > 0)
<div class="section">
    <h2 class="section-title">Poor Attendance Alerts</h2>
    <div class="summary-card" style="background-color: #f8d7da; border-color: #f5c6cb;">
        <h4 style="color: #721c24;">Students with Poor Attendance (Below 75%)</h4>
        <ul>
            @foreach($data['poor_attendance_alerts'] as $alert)
            <li>{{ $alert }}</li>
            @endforeach
        </ul>
    </div>
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
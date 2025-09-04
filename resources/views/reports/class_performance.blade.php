@extends('reports.layout')

@section('content')
    <div class="section">
        <h2 class="section-title">Class Performance Overview</h2>
        <div class="stats-grid">
            @if(isset($data['statistics']['total_students']))
            <div class="stat-item">
                <div class="stat-value">{{ $data['statistics']['total_students'] }}</div>
                <div class="stat-label">Total Students</div>
            </div>
            @endif
            @if(isset($data['statistics']['average_grade']))
            <div class="stat-item">
                <div class="stat-value">{{ number_format($data['statistics']['average_grade'], 1) }}</div>
                <div class="stat-label">Average Grade</div>
            </div>
            @endif
            @if(isset($data['statistics']['attendance_rate']))
            <div class="stat-item">
                <div class="stat-value">{{ number_format($data['statistics']['attendance_rate'], 1) }}%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
            @endif
            @if(isset($data['statistics']['assignment_completion']))
            <div class="stat-item">
                <div class="stat-value">{{ number_format($data['statistics']['assignment_completion'], 1) }}%</div>
                <div class="stat-label">Assignment Completion</div>
            </div>
            @endif
        </div>
    </div>

    @if(isset($data['grade_distribution']) && count($data['grade_distribution']) > 0)
        <div class="section">
            <h2 class="section-title">Grade Distribution</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Number of Students</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['grade_distribution'] as $grade => $count)
                        <tr>
                            <td>{{ $grade }}</td>
                            <td>{{ $count }}</td>
                            <td>{{ isset($data['statistics']['total_students']) && $data['statistics']['total_students'] > 0 ? number_format(($count / $data['statistics']['total_students']) * 100, 1) : 0 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if(isset($data['subject_performance']) && count($data['subject_performance']) > 0)
        <div class="section page-break">
            <h2 class="section-title">Subject-wise Performance</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Students Enrolled</th>
                        <th>Average Grade</th>
                        <th>Highest Grade</th>
                        <th>Lowest Grade</th>
                        <th>Pass Rate (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['subject_performance'] as $subject => $performance)
                        <tr>
                            <td>{{ $subject }}</td>
                            <td>{{ $performance['student_count'] ?? 0 }}</td>
                            <td>{{ number_format($performance['average_grade'] ?? 0, 1) }}</td>
                            <td>{{ number_format($performance['highest_grade'] ?? 0, 1) }}</td>
                            <td>{{ number_format($performance['lowest_grade'] ?? 0, 1) }}</td>
                            <td>{{ number_format($performance['pass_rate'] ?? 0, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if(isset($data['top_performers']) && count($data['top_performers']) > 0)
        <div class="section">
            <h2 class="section-title">Top Performers</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student Name</th>
                        <th>Overall Grade</th>
                        <th>Attendance (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['top_performers'] as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ number_format($student->overall_grade ?? 0, 1) }}</td>
                            <td>{{ number_format($student->attendance_rate ?? 0, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if(isset($data['attendance_summary']) && count($data['attendance_summary']) > 0)
        <div class="section">
            <h2 class="section-title">Attendance Summary</h2>
            <div class="summary-card">
                <h4>Class Attendance Trends</h4>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Attendance Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['attendance_summary'] as $date => $summary)
                            <tr>
                                <td>{{ $date }}</td>
                                <td>{{ $summary['present'] ?? 0 }}</td>
                                <td>{{ $summary['absent'] ?? 0 }}</td>
                                <td>{{ $summary['late'] ?? 0 }}</td>
                                <td>{{ number_format($summary['rate'] ?? 0, 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(isset($data['recommendations']) && count($data['recommendations']) > 0)
        <div class="section">
            <h2 class="section-title">Recommendations for Improvement</h2>
            <ul>
                @foreach($data['recommendations'] as $recommendation)
                    <li>{{ $recommendation }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection

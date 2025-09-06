<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $content['subject'] }} - {{ $school->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .school-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 0;
        }
        .school-address {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }
        .subject-line {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .subject-line h2 {
            margin: 0;
            color: #dc2626;
            font-size: 18px;
        }
        .content {
            margin: 20px 0;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 15px;
        }
        .fee-details {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .fee-item {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px dotted #cbd5e0;
        }
        .fee-item:last-child {
            border-bottom: none;
            font-weight: bold;
            background: #edf2f7;
            margin: 15px -10px -10px;
            padding: 15px 10px 10px;
            border-radius: 0 0 5px 5px;
        }
        .fee-label {
            font-weight: 500;
            color: #374151;
        }
        .fee-value {
            color: #059669;
            font-weight: 600;
        }
        .due-date {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .due-date strong {
            color: #d97706;
            font-size: 16px;
        }
        .instructions {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
        }
        .contact-info {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            background: #059669;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 15px 0;
        }
        .btn:hover {
            background: #047857;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 class="school-name">{{ $school->name }}</h1>
            <div class="school-address">{{ $school->address }}</div>
            <div class="school-address">Phone: {{ $school->phone }} | Email: {{ $school->email }}</div>
        </div>

        <div class="subject-line">
            <h2>{{ $content['subject'] }}</h2>
        </div>

        <div class="content">
            <div class="greeting">{{ $content['greeting'] }}</div>
            
            <p>{{ $content['body'] }}</p>

            <div class="fee-details">
                <h3 style="margin-top: 0; color: #374151;">Payment Details</h3>
                
                <div class="fee-item">
                    <span class="fee-label">Student Name:</span>
                    <span class="fee-value">{{ $student->name }}</span>
                </div>
                
                <div class="fee-item">
                    <span class="fee-label">Admission Number:</span>
                    <span class="fee-value">{{ $student->admission_number }}</span>
                </div>
                
                <div class="fee-item">
                    <span class="fee-label">Class:</span>
                    <span class="fee-value">{{ $student->class_name ?? 'N/A' }}</span>
                </div>
                
                <div class="fee-item">
                    <span class="fee-label">Fee Type:</span>
                    <span class="fee-value">{{ $installment->installment_name }}</span>
                </div>
                
                <div class="fee-item">
                    <span class="fee-label">Amount Due:</span>
                    <span class="fee-value">{{ $content['amount'] }}</span>
                </div>
                
                @if($content['late_fee'])
                <div class="fee-item">
                    <span class="fee-label">Late Fee:</span>
                    <span class="fee-value" style="color: #dc2626;">{{ $content['late_fee'] }}</span>
                </div>
                @endif
            </div>

            <div class="due-date">
                <strong>Due Date: {{ $installment->due_date->format('d M Y') }}</strong>
            </div>

            <div class="instructions">
                <strong>Payment Instructions:</strong><br>
                {{ $content['instructions'] }}
            </div>

            <div class="contact-info">
                <strong>Need Help?</strong><br>
                {{ $content['contact'] }}
            </div>

            <p style="margin-top: 30px;">{{ $content['closing'] }}</p>
        </div>

        <div class="footer">
            <p><strong>Reminder #{{ $reminder->reminder_number }}</strong></p>
            <p>This is an automated reminder. Please do not reply to this email.</p>
            <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

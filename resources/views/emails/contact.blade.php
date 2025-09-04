<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #3b82f6;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background-color: #f9fafb;
        }

        .field {
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            color: #374151;
        }

        .value {
            background-color: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>New Contact Form Submission</h1>
        <p>EduVault Pro</p>
    </div>

    <div class="content">
        <div class="field">
            <div class="label">Name:</div>
            <div class="value">{{ $name }}</div>
        </div>

        <div class="field">
            <div class="label">Email:</div>
            <div class="value">{{ $email }}</div>
        </div>

        @if($phone)
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">{{ $phone }}</div>
            </div>
        @endif

        @if($school_name)
            <div class="field">
                <div class="label">School/Organization:</div>
                <div class="value">{{ $school_name }}</div>
            </div>
        @endif

        <div class="field">
            <div class="label">Inquiry Type:</div>
            <div class="value">{{ ucfirst(str_replace('_', ' ', $inquiry_type)) }}</div>
        </div>

        <div class="field">
            <div class="label">Message:</div>
            <div class="value">{{ nl2br(e($message)) }}</div>
        </div>
    </div>

    <div style="text-align: center; padding: 20px; color: #6b7280;">
        <p>This email was sent from the EduVault Pro contact form.</p>
    </div>
</body>

</html>
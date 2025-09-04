<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank You - EduVault Pro</title>
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
            background-color: #10b981;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background-color: #f9fafb;
        }

        .highlight {
            background-color: #ecfdf5;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Thank You!</h1>
        <p>EduVault Pro</p>
    </div>

    <div class="content">
        <p>Dear {{ $name }},</p>

        <div class="highlight">
            <p><strong>Thank you for contacting EduVault Pro!</strong></p>
            <p>We have received your inquiry regarding:
                <strong>{{ ucfirst(str_replace('_', ' ', $inquiry_type)) }}</strong></p>
        </div>

        <p>Our team will review your inquiry and get back to you within 24-48 hours. We appreciate your interest in
            EduVault Pro and look forward to assisting you.</p>

        <p>If you have any urgent questions, please don't hesitate to call us at <strong>+91-9876543210</strong> or
            email us directly at <strong>support@eduvaultpro.com</strong>.</p>

        <p>Best regards,<br>
            <strong>The EduVault Pro Team</strong>
        </p>
    </div>

    <div style="text-align: center; padding: 20px; color: #6b7280; border-top: 1px solid #e5e7eb;">
        <p>EduVault Pro - Complete Digital Education Management Solution</p>
        <p>Visit us at: <a href="http://localhost:8000" style="color: #3b82f6;">www.eduvaultpro.com</a></p>
    </div>
</body>

</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .field { margin-bottom: 16px; }
        .label { font-weight: bold; color: #555; }
        .value { margin-top: 4px; }
        .message-box { background: #f5f5f5; padding: 16px; border-radius: 4px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Contact Form Submission</h2>

        <div class="field">
            <div class="label">Name</div>
            <div class="value">{{ $submission->name }}</div>
        </div>

        <div class="field">
            <div class="label">Email</div>
            <div class="value"><a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></div>
        </div>

        <div class="field">
            <div class="label">Message</div>
            <div class="value message-box">{{ $submission->message }}</div>
        </div>

        @if ($submission->data)
            <div class="field">
                <div class="label">Additional Data</div>
                <div class="value">
                    @foreach ($submission->data as $key => $value)
                        <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <hr>
        <p style="color: #999; font-size: 12px;">
            Submitted on {{ $submission->created_at->format('F j, Y \a\t g:i A') }}
        </p>
    </div>
</body>
</html>

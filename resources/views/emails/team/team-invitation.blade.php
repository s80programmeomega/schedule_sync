<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .team-logo {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if($team->logo)
            <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="team-logo">
            @endif
            <h1>You're invited to join {{ $team->name }}</h1>
        </div>

        <p>Hi there!</p>

        <p><strong>{{ $inviter->name }}</strong> has invited you to join <strong>{{ $team->name }}</strong> as a
            <strong>{{ $role }}</strong>.</p>

        @if($team->description)
        <p><em>{{ $team->description }}</em></p>
        @endif

        <div style="text-align: center;">
            <a href="{{ $acceptUrl }}" class="btn">Accept Invitation</a>
        </div>

        <p><small>This invitation expires on {{ $expiresAt->format('M j, Y \a\t g:i A') }}.</small></p>

        <div class="footer">
            <p>If you didn't expect this invitation, you can safely ignore this email.</p>
            <p>© {{ date('Y') }} ScheduleSync. All rights reserved.</p>
        </div>
    </div>
</body>

</html>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to SmartPOS</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1F3A6E; color: white; padding: 10px; text-align: center; }
        .content { margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 0.8em; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to SmartPOS!</h2>
        </div>
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            <p>Your account has been successfully created. Below are your login credentials:</p>
            <ul>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Password:</strong> {{ $password }}</li>
            </ul>
            <p>Please login and change your password as soon as possible.</p>
            <p><a href="{{ url('/login') }}">Login to SmartPOS</a></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SmartPOS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

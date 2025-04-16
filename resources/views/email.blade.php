<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2>Your One-Time Password (OTP)</h2>
        <p>Hello,</p>
        <p>Your OTP code is: <strong>{{ $otp }}</strong></p>
        <p>This code will expire in {{ config('otp.expires_in') }} minutes.</p>
        <p>If you didn't request this OTP, please ignore this email.</p>
        <p>Best regards,<br>Your Application Team</p>
    </div>
</body>
</html> 
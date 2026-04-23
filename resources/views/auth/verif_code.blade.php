<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verification Code</title>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f4f4; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #333333; border-top: 6px solid #D32F2F; }
        .header { background-color: #213C71; padding: 25px; text-align: center; }
        .content { padding: 40px 30px; text-align: center; }
        .h1 { font-size: 20px; font-weight: bold; color: #213C71; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .p { font-size: 16px; color: #555555; line-height: 1.5; margin-bottom: 30px; }
        .code-container { background-color: #F8F9FA; border: 2px dashed #213C71; border-radius: 8px; padding: 20px; display: inline-block; margin-bottom: 30px; }
        .code { font-size: 36px; font-weight: 900; color: #D32F2F; letter-spacing: 8px; margin: 0; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #888888; }
        .footer-logo { height: 30px; margin-bottom: 10px; opacity: 0.8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main">
            <tr>
                <td class="header">
                    <h2 style="color: #ffffff; margin: 0; font-size: 18px; text-transform: uppercase;">BatStateU - BERC</h2>
                    <p style="color: #ffffff; margin: 5px 0 0 0; font-size: 10px; letter-spacing: 2px;">Ethics Review Committee</p>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <h1 class="h1">Verify Your Account</h1>
                    <p class="p">You are receiving this email because a registration request was made for the BERC Management System. Use the code below to complete your verification:</p>

                    <h1 class="h1">Hi, {{ $code }}!</h1>

                    <div class="code-container">
                        <p class="code">Your verification code is: {{ $name }}</p>
                    </div>

                    <p class="p" style="font-size: 13px;">If you did not initiate this request, you can safely ignore this email.</p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p>&copy; {{ date('Y') }} Batangas State University - TNEU<br>The National Engineering University</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Recuperación de Contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2>Hola,</h2>
    <p>Has solicitado restablecer tu contraseña para acceder a tu cuenta en <strong>dfmSecure</strong>.</p>
    <p>Tu código de verificación de 6 dígitos es:</p>
    <div style="background-color: #f4f4f4; padding: 15px; text-align: center; border-radius: 8px; margin: 20px 0;">
        <h3 style="font-size: 32px; letter-spacing: 5px; color: #51653e; margin: 0;">{{ $token }}</h3>
    </div>
    <p>Por favor ingresa este código en la aplicación para crear tu nueva contraseña.</p>
    <p style="font-size: 14px; color: #666; margin-top: 30px;">Si no solicitaste este cambio, puedes ignorar este correo de forma segura.</p>
</body>
</html>

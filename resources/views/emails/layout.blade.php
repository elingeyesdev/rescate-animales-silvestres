<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Rescate Animal')</title>
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
            background-color: #3c8dbc;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3c8dbc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .button-success {
            background-color: #28a745;
        }
        .button-danger {
            background-color: #dc3545;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #3c8dbc;
            padding: 15px;
            margin: 15px 0;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>@yield('header', 'Sistema de Rescate Animal')</h1>
    </div>
    <div class="content">
        @yield('content')
    </div>
    <div class="footer">
        <p>Este es un correo automático del Sistema de Rescate Animal. Por favor, no responda a este mensaje.</p>
        <p>&copy; {{ date('Y') }} Sistema de Rescate Animal. Todos los derechos reservados.</p>
    </div>
</body>
</html>


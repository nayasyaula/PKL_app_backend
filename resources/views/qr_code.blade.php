{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
</head>
<body>
    <h1>User ID: {{ $userId }}</h1>
    <!-- Menampilkan QR Code -->
    {!! QrCode::size(100)->generate(request()->url()) !!}
    <p>Scan me to return to the original page.</p>
</body>
</html> --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
</head>
<body>
    <h1>Scan QR Code to Mark Attendance</h1>
    <!-- Menampilkan QR Code -->
    {!! QrCode::size(300)->generate(route('mark-attendance')) !!}
</body>
</html>


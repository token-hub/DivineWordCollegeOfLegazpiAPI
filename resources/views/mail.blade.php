<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <p><Strong>From :</Strong> {{ $mail['name'] }} </p>
    <p><Strong>Email :</Strong> {{ $mail['email'] }} </p>
    <p><Strong>Message:</Strong></p>
    {{ $mail['message'] }}
</body>
</html>
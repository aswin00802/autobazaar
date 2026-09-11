<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subjectLine ?? env('APP_NAME') }}</title>
</head>
<body>
    {!! nl2br($bodyContent) !!}
</body>
</html>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body>
<div>
    <h3>Hi {{ $competitor->first_name }}!</h3>

    <p>
        We have received your registration for following events:
    <ul>
        @foreach ($competitor->events as $event)
            <li>{{ $event->title }}
        @endforeach
    </ul>
    </p>
</div>
</body>
</html>

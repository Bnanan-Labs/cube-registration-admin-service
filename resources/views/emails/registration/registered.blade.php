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
    <p>Hi {{ $competitor->first_name }},</p>
    <p>
        We have received your registration for the Rubik's WCA European Championship 2022.
    </p>
    <p>You have registered for the following events:</p>
    <ul>
        @foreach ($competitor->events as $event)
            <li>{{ $event->title }}</li>
        @endforeach
    </ul>
    <p>
        Your registration will be accepted upon payment of the registration fee. If you have been accepted as a volunteer already your registration will be processed manually without payment. You can check the status of your registration and make your payment <a href="https://registration.wca2022.eu/competitor">here</a>.
    </p>
    <p>Regards, the Rubik's WCA European Championship 2022 organization team.</p>
</div>
</body>
</html>

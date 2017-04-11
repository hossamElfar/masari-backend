<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Congratulations {{$client->first_name}} your schedule has been approved !!</h2>

<div>
    Hi {{$client->first_name}},
    <br>
    Your meeting with our expert {{$expert->first_name.' '.$expert->second_name}} has been approved
    <br>
    The date of the meeting is <h3> {{$timing}}</h3>
    <br>
    email of the expert is <h5>{{$expert->email}}</h5> the expert will contact you soon
    <br>
    Have a fruitful experience!
    <br>
    Regards,<br>
    Masari Administration Team
</div>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<strong>Hey {{$user->username}}</strong>, <br>
thanks for regestring in our service 

to complete the registeration , you have to verify you email ,
click on the following link and enter the code that is in below 

link : {{$user->getVerificationEmailLink()}} 

<h2>code : 
{{$user->code}}
</h2>
    


</body>
</html>
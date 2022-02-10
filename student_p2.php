<?php
session_start();
?>

<!DOCTYPE html>
<html>

<?php

if(!isset($_SESSION['user_id'])){
    header("Location: http://localhost/stdMS/login.php", true,301);
    exit();
} else if($_SESSION['user_type'] != "student"){
    header("Location: http://localhost/stdMS/error.php?error=not_allowed", true,301);
    exit();
}

?>
<head>
    <title>Student Page2</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/612d434467.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./css1/Student_p2.css">
</head>
<body>
<?php include "name_logout.php";?>
<div class="container"> <!-- may be this will go under logout-->

    <div class="jumbotron" style="background: none; color: white; font-family: PT Sans,sans-serif; text-align: center;">
        <h1>Student Requests</h1>
    </div>
    <div class="jumbotron1" style="background: none;">
        <div class="container1">
            <div class="buttons">
                <div class="container2">
                    <input type="radio" name="selection" id="New Request">
                <label class="types" for="New Request">
                    <i class="fas fa-folder-plus"></i>
            <span>
                <a href="http://localhost/stdMS/new_request.php" class="link">New Request</a>
            </span>
    </label>
    </div>
    <div class="container3">
    <input type="radio" name="selection" id="My Requests">
    <label class="types" for="My Requests">
        <i class="fas fa-copy"></i>
        <span>
            <a href="http://localhost/stdMS/my_requests.php" class="link">My Requests</a>
        </span>
    </label>
</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

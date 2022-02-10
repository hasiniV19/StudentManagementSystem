<!--<!DOCTYPE html>-->
<!--<html lang="en">-->

<?php
if(!isset($_SESSION)){
    session_start();
}
$name = $_SESSION["name"];
if (isset($_POST["logout"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    session_destroy();
    print_r($_SESSION);
    header("Location: http://localhost/stdMS/login.php", true,301);
    exit();
}
?>
<!--<head>-->
<!--    <title>Name_Logout</title>-->
<!--    <meta charset="utf-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1">-->
<!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">-->
<!--    <script src="https://kit.fontawesome.com/612d434467.js" crossorigin="anonymous"></script>-->
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
<!--    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>-->
<!--    <link rel="stylesheet" type="text/css" href="./css1/name_logout.css">-->
<!--</head>-->

<link rel="stylesheet" type="text/css" href="./css1/name_logout.css">

<!--<body>-->
<div class="sticky">
    <form method="post" action="name_logout.php">
    <div class="name">
        <i class="far fa-user"></i>
        <!--<label>Hasini Madushika</label>-->
        <label><?php echo $name;?></label>
        <input class="btn" type="submit" name="logout" value="LOGOUT" style="border: none;border-radius: 4px;cursor: pointer;color: white;background-color: #ff4d4d;font-weight: bold;font-size: 15px;"/>
        </div>
    </form>
</div>
<!--</body>-->
<!--</html>-->

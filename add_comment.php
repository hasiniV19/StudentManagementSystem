<?php
session_start();
?>

<!DOCTYPE html>
<html>

<?php

if(!isset($_SESSION['user_id'])){
    header("Location: http://localhost/stdMS/login.php", true,301);
    exit();
} elseif(!isset($_SESSION['request_id'])){
    header("Location: http://localhost/stdMS/error.php?error=not_found", true,301);
    exit();
}

$name = $_SESSION["name"];
if (isset($_POST["logout"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    session_destroy();
    header("Location: http://localhost/stdMS/login.php", true,301);
    exit();
}
?>

<?php
error_reporting(-1);
ini_set('display_errors', 'true');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require "db_connection.php";
$request_id = $_SESSION["request_id"];
$sender_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $conn = OpenCon();

    if(!empty($_POST["comment"]) && isset($_POST["submit"])){
        $comment = test_input($_POST["comment"]);
        $stmt = $conn->prepare("INSERT INTO comments(`request_id`,`sender_id`,`message`)
                                      VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $request_id, $sender_id, $comment);
        $stmt->execute();
        header("Location: http://localhost/stdMS/request_details.php?id=$request_id", true, 301);
        exit();
    }
    header("Location: http://localhost/stdMS/request_details.php?id=$request_id", true, 301);
    exit();
    CloseCon();
}

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>


<head>
    <title>Add Comment</title>
    <script src="https://kit.fontawesome.com/612d434467.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="./css1/add_comment.css">
</head>
<body>
<?php //include "name_logout.php"?>
<div class="sticky">
    <form method="post" action="add_comment.php">
        <div class="name">
            <i class="far fa-user" style="color: white;margin-left: 20px;font-size: 25px;
            font-weight: bold;"></i>
            <!--<label>Hasini Madushika</label>-->
            <label style="color: white;margin-left: 20px;font-size: 25px;font-family: Arial,sans-serif;font-weight: bold;"><?php echo $name;?></label>
            <input class="btn" type="submit" name="logout" value="LOGOUT" style="border: none;border-radius: 4px;cursor: pointer;color: white;background-color: #ff4d4d;font-weight: bold;font-size: 15px;float: right;"/>
        </div>
    </form>
</div>

<h1>Please add the comment below</h1>
<form action="add_comment.php" method="post">
    <div class="text1">
        <div class="row1">
    <input type="text" placeholder="Enter a comment" name="comment" value="" style="font-weight: bold; font-size: 15px;"/>
        </div>
        <div class="row2">
    <input class="enter" type="submit" name="submit" value="Enter" />
    <input class="cancel" type="submit" name="cancel" value="Cancel" style="background-color:#ff4d4d; "/>
        </div>
    </div>
</form>
</body>
</html>

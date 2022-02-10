<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<?php

if(isset($_SESSION['user_id'])){
    if($_SESSION['user_type'] == "student"){
        header("Location: http://localhost/stdMS/student_p2.php", true,301);
        exit();
    }
    else if($_SESSION['user_type'] == "staff"){
        header("Location: http://localhost/stdMS/student_requests.php", true,301);
        exit();
    }
}

// this file and all included/required files
error_reporting(-1);
ini_set('display_errors', 'true');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require "db_connection.php";

$loginErr = $usernameErr = $passwordErr = $selectionErr ="";
$username = $password = $selection = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = OpenCon();

    if (empty($_POST["username"])){
        $usernameErr = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
    }

    if (empty($_POST["password"])){
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }

    if (empty($_POST["selection"])) {
        $selectionErr = "Selection is required";
        } else {
            $selection = test_input($_POST["selection"]);

            // student
            if($selection == "student"){
                $sql = "SELECT `user_id` FROM students WHERE `username`=?";
                $loginErr = checkLogin($conn, $sql, $username, $password);
                if($loginErr == ""){
                    $_SESSION["user_type"] = "student";
                    header("Location: http://localhost/stdMS/student_p2.php", true,301);
                    exit();
                }

            } else{
                $sql = "SELECT `user_id` FROM staff WHERE `username`=?";
                $loginErr = checkLogin($conn, $sql, $username, $password);
                if($loginErr == ""){
                    $_SESSION["user_type"] = "staff";
                    header("Location: http://localhost/stdMS/student_requests.php", true,301);
                    exit();
                }
            }
        }
    CloseCon($conn);
}

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function checkLogin($conn, $sql, $username, $password)
{
    $loginErr = "";
    $stmt1 = $conn->prepare($sql);
    $stmt1->bind_param("s", $username);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $user1 = $result1->fetch_assoc();
    if ($user1 == NULL) {
        $loginErr = "Invalid login. Please try again.";
    } else {
        $user_id = $user1["user_id"];
        // store login time
        $stmt2 = $conn->prepare("SELECT `password` FROM authentication WHERE `user_id`=?");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $user2 = $result2->fetch_assoc();
        if ($user2 != NULL) {
            $realPassword = $user2["password"];
            if ($realPassword != $password) {
                $loginErr = "Invalid login. Please try again.";
            } else{
                $loginTimestmt = $conn->prepare("UPDATE authentication SET `last_logged_in` = now() WHERE `user_id`=?");
                $loginTimestmt->bind_param("i", $user_id);
                $loginTimestmt->execute();
                $_SESSION["user_id"] = $user_id; // add user_id to $_SESSION

                // get name of the user
                $stmt3 = $conn->prepare("SELECT `name` FROM users WHERE `id`=?");
                $stmt3->bind_param("i", $user_id);
                $stmt3->execute();
                $result3 = $stmt3->get_result()->fetch_assoc();
                $name = $result3["name"];
                $_SESSION["name"] = $name;
            }
        }
    }
    return $loginErr;
}
?>
<head>
    <title>Login Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/612d434467.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./css1/login.css">
</head>
<body>

<div class="container">
    <div class="jumbotron" style="background: none; color: white; font-family: PT Sans,sans-serif;">
        <h1>Student Request Management System</h1>
    </div>
    <div class="row">
        <div class="col-sm-12">
<form method="POST" action="login.php"> <!-- changed action -->
    <div class="jumbotron1" style="background: none;">
        <div class="container1">
        <h1>Login</h1>
        <div class="invalid">
        <label class="error"><?php echo $loginErr;?></label> <!-- login error-->
        </div>
        <div class="textbox">
            <i class="fas fa-user-tie" style="color: white;"></i>
            <input type="text" placeholder="Username" name="username" value="<?php echo $username;?>"/> <!--changed value-->
        </div>
            <label class="error">* <?php echo $usernameErr;?></label> <!-- if no username is entered -->

        <div class="textbox">
            <i class="fas fa-lock" style="color: white;"></i>
            <input type="password" placeholder="Password" name="password" value="<?php echo $password;?>"/> <!--changed value-->
        </div>
            <label class="error">* <?php echo $passwordErr;?></label> <!-- if no password is entered -->
        <div class="buttons">
            <input type="radio" name="selection"
            <?php if(isset($selection) && $selection == "student") echo "checked";?>
            value="student" id="student" style="margin-left: 20px;">
            <label for="student">
                <i class="fas fa-user-graduate"></i>
                <span>Student</span>
            </label>

            <input type="radio" name="selection"
            <?php if(isset($selection) && $selection == "staff") echo "checked";?>
            value="staff" id="staff" style="margin-left: 10px;">
            <label for="staff">
                <i class="fas fa-users-cog"></i>
                <span>Staff</span>
            </label>
            <div class="selectione">
            <label class="error">* <?php echo $selectionErr;?></label> <!-- if no selection is selected -->
            </div>
        </div>

        <input class="btn" type="submit" name="submit" value="Sign in"/> <!-- changed -->
    </div>
    </div>
</form>
</div>
</div>
</div>
</body>
</html>

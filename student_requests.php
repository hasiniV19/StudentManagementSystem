<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<?php

if(!isset($_SESSION['user_id'])){
    header("Location: http://localhost/stdMS/login.php", true,301);
    exit();
} else if($_SESSION['user_type'] != "staff"){
    header("Location: http://localhost/stdMS/error.php?error=not_allowed", true,301);
    exit();
}

require "db_connection.php";

$conn = OpenCon();
$user_id = $_SESSION["user_id"];

if (isset($_POST["filter"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

    $index = test_input($_POST["index"]);
    $student_name = test_input($_POST["name"]);
    $type = test_input($_POST["type"]);
    $status = test_input($_POST["status"]);

    $sql = "SELECT `id`, `index_number`, `type`,`status` FROM requests";
    $countVar = 0;
    if(!(empty($index) && empty($student_name) && empty($type) && empty($status))){
        $vars = array();
        $sql = $sql . " WHERE ";
        if(!empty($index)){
            $sql = $sql . " `index_number`=? AND ";
            array_push($vars, $index);
            $countVar += 1;
        }
        if(!empty($student_name)){
            $sql = $sql . " `name`=? AND ";
            array_push($vars, $student_name);
            $countVar += 1;
        }
        if(!empty($type)){
            $sql = $sql . " `type`=? AND ";
            array_push($vars, $type);
            $countVar += 1;
        }
        if(!empty($status)){
            $sql = $sql . " `status`=? AND ";
            array_push($vars, $status);
            $countVar += 1;
        }
        $sql = rtrim($sql, " AND ");

        $stmt4 = $conn->prepare($sql);

        if($countVar == 1) {
            $stmt4->bind_param(str_repeat("s", $countVar), $vars[0]);
        }
        else if($countVar == 2){
            $stmt4->bind_param(str_repeat("s", $countVar), $vars[0], $vars[1]);
        }
        else if($countVar == 3){
            $stmt4->bind_param(str_repeat("s", $countVar), $vars[0], $vars[1], $vars[2]);
        }
        else if($countVar == 4) {
            $stmt4->bind_param(str_repeat("s", $countVar), $vars[0], $vars[1], $vars[2], $vars[3]);
        }
        $stmt4->execute();
        $data = $stmt4->get_result();
    }

    if($countVar == 0){
        $stmt4 = $conn->prepare($sql);
        $stmt4->execute();
        $data = $stmt4->get_result();
    }


}
else{
    $index = "";
    $student_name = "";
    $type = "";
    $status = "";
    $stmt1 = $conn->prepare("SELECT `id`, `index_number`, `type`,`status` FROM requests");
    $stmt1->execute();
    $data = $stmt1->get_result();
}

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/612d434467.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./css1/Students_Requests_sahan.css">
    <title>Requests of students</title>
</head>
<body>
<?php include "name_logout.php"?>
<div class="container">
    <div class="jumbotron" style="background: none; text-align: center; font-family: PT Sans, sans-serif;color: white;">
    <h1>
        Requests of students
    </h1>
    </div>
    <div class="jumbotron" style="background: none;  text-align: center;">
        <div class="table-responsive">
    <table class="table2">
        <form method="POST" action="student_requests.php">
            <thead>
            <tr>
                <th scope="row">
                    <input class="filter" type="text" name="index" placeholder="Index" value="<?php echo $index;?>">
                </th>
                <th>
                    <input class="filter" type="text" name="name" placeholder="Name" value="<?php echo $student_name;?>">
                </th>
                <th>
                    <select class="filter" name="type">
                        <option value="">Type of Requests</option>
                        <option value="Late Add/Drop Request"
                            <?php if(isset($type) && $type == "Late Add/Drop Request") echo "selected";?>>
                            Late Add/Drop Request
                        </option>
                        <option value="Repeat Exam Request"
                            <?php if(isset($type) && $type == "Repeat Exam Request") echo "selected";?>>
                            Repeat Exam Request
                        </option>
                        <option value="Extend Assignment Submission Request"
                            <?php if(isset($type) && $type == "Extend Assignment Submission Request") echo "selected";?>>
                            Extend Assignment Submission Request
                        </option>
                        <option value="Other"
                            <?php if(isset($type) && $type == "Other") echo "selected";?>>
                            Other
                        </option>
                    </select>
                </th>
                <th>
                    <select class="filter" name="status">
                        <option value="">Status</option>
                        <option value="Pending"
                            <?php if(isset($status) && $status == "Pending") echo "selected";?>>
                            Pending
                        </option>
                        <option value="Accepted"
                            <?php if(isset($status) && $status == "Accepted") echo "selected";?>>
                            Accepted
                        </option>
                        <option value="Declined"
                            <?php if(isset($status) && $status == "Declined") echo "selected";?>>
                            Declined
                        </option>
                        <option value="RequestedMore"
                            <?php if(isset($status) && $status == "RequestedMore") echo "selected";?>>
                            Requested More
                        </option>
                    </select>
                </th>
                <th>
                    <!--<i class="fas fa-filter"></i>-->
                    <input class="btn" type="submit" name="filter" value="Filter"/> <!-- type is different:button -->
                </th>
            </tr>
            </thead>
        <form>
    </table>
</div>
<br>
<?php
echo "
<div class='jumbotron' style='background: none; text-align: center; font-family: PT Sans, sans-serif;color: white;'>
            <div class='table-responsive'>
  <table class='table1'>
    <thead>
    <tr>
    <th>Index</th>
    <th>Name</th>
    <th>Type of Request</th>
    <th>Status</th>
    <th>Details</th>
    </tr>
    </thead>";
if ($data->num_rows == 0) {

    echo "
        <tbody>
                <tr>
                    <td scope='row'></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th>
                 
                    </th>
                </tr>
            </tbody>";
} if($data->num_rows>0) {

    while ($row = $data->fetch_assoc()) {
        $request_id = $row["id"];
        $index_number = $row["index_number"];
        $request_type = $row["type"];
        $request_status = $row["status"];

        $stmt2 = $conn->prepare("SELECT `user_id` FROM students WHERE `index_number`=?");
        $stmt2->bind_param("s", $index_number);
        $stmt2->execute();
        $result1 = $stmt2->get_result()->fetch_assoc();

        $user_id = $result1["user_id"];

        $stmt3 = $conn->prepare("SELECT `name` FROM users WHERE `id`=?");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();
        $result2 = $stmt3->get_result()->fetch_assoc();

        $name = $result2["name"];
        echo "
        <tbody>
                <tr>
                    <td scope='row'>$index_number</td>
                    <td>$name</td>
                    <td>$request_type</td>
                    <td>$request_status</td>
                    <th>
                    
                     
                    <a href='http://localhost/stdMS/request_details.php?id=$request_id'>More</a>
                    </th>
                </tr>
            </tbody>";
    }

    echo "
    </table>
    </div>";
}

CloseCon($conn);
?>
    </div>
</body>
</html>

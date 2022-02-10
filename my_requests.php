<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">


<?php

if(!isset($_SESSION['user_id'])){
    header("Location: http://localhost/stdMS/login.php", true,301);
    exit();
} else if($_SESSION['user_type'] != "student"){
    header("Location: http://localhost/stdMS/error.php?error=not_allowed", true,301);
    exit();
}

require "db_connection.php";

$conn = OpenCon();
$user_id = $_SESSION["user_id"];

$stmt1 = $conn->prepare("SELECT `index_number` FROM students WHERE `user_id`=?");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$result = $stmt1->get_result()->fetch_assoc();

$index_number = $result["index_number"];

$stmt2 = $conn->prepare("SELECT `id`, `index_number`, `type`,`status` FROM requests WHERE `index_number`=?");
$stmt2->bind_param("s", $index_number);
$stmt2->execute();
$data = $stmt2->get_result();

?>


<head>
    <title>My requests</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/612d434467.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./css1/My_Requests_sahan.css">
</head>
<body>
<?php include "name_logout.php";?>
<?php

    echo "<a href='http://localhost/stdMS/student_p2.php' style='color: yellowgreen; font-weight: bold; font-size: 20px; margin-left: 40px; margin-bottom: 50px; margin-top: 50px; padding-bottom: 50px'>
        Back
        </a>";

?>
<div class="jumbotron" style="background: none; text-align: center; font-family: PT Sans, sans-serif;color: white;">
<h1>My requests</h1>
</div>
<?php
echo "
<div class='jumbotron' style='background: none;'>
<div class='table-responsive'>
  <table class='table'>
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
} else {


    while ($row = $data->fetch_assoc()) {
        $request_id = $row["id"];
        $request_type = $row["type"];
        $request_status = $row["status"];

        // name of the user
        $name = $_SESSION["name"];

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
    </div>
    </div>";
}
CloseCon($conn);
?>

</body>
</html>

<?php
session_start();
?>
<!DOCTYPE html>
<html>
<?php

if(!isset($_SESSION['user_id'])){
    header("Location: http://localhost/stdMS/login.php", true,301);
    exit();
}
//else if(!isset($_GET['id'])){
//    header("Location: http://localhost/stdMS/error.php?error=not_found", true,301);
//    exit();
//}

error_reporting(-1);
ini_set('display_errors', 'true');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require "db_connection.php";


//echo implode(",", $_SESSION);

if (isset($_POST["respond"]) && $_SERVER["REQUEST_METHOD"] == "POST"){
    $request_id = $_SESSION["request_id"];
    $response = $_POST["response"];

    if(!empty($response)) {
        $conn = OpenCon();

        $sql = "UPDATE requests SET `status`=? WHERE `id`=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $response, $request_id);
        $stmt->execute();
        CloseCon($conn);
    }
    header("Location: http://localhost/stdMS/student_requests.php", true, 301);
    exit();
} else{
    $request_id = $_GET["id"];
    $_SESSION["request_id"] = $request_id;
    $conn = OpenCon(); // open connection

    $sql1 = "SELECT `index_number`,`type`, `description` FROM requests WHERE `id`=?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $request_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result()->fetch_assoc();

    $indexNumber= $result1["index_number"];
    $type = $result1["type"];
    $description = $result1["description"];

    $sql2 = "SELECT `user_id` FROM students WHERE `index_number`=?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $indexNumber);
    $stmt2->execute();
    $result2 = $stmt2->get_result()->fetch_assoc();

    $user_id = $result2["user_id"];

    $sql3 = "SELECT `name` FROM users WHERE `id`=?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $user_id);
    $stmt3->execute();
    $result3 = $stmt3->get_result()->fetch_assoc();

    $student_name = $result3["name"];

    $stmt4 = $conn->prepare("SELECT `id`,`sender_id`,`date`,`message` FROM comments WHERE `request_id`=?");
    $stmt4->bind_param("i", $request_id);
    $stmt4->execute();
    $result4 = $stmt4->get_result();

    if($_SESSION["user_type"] == "student"){
        $fileUploadErrs = array();
        $target_dir = "uploads/"; // the directory where the file is going to be placed

        if(isset($_POST["submit"])) {

            $countfiles = count($_FILES["fileToUpload"]["name"]);
            for ($i = 0; $i < $countfiles; $i++) {
                $target_file = $target_dir . $request_id . $_FILES['fileToUpload']['name'][$i];
                $filename = $_FILES['fileToUpload']['name'][$i];

                $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                if (file_exists($target_file)) {
                    array_push($fileUploadErrs, $filename . " already added");
                } elseif ($_FILES['fileToUpload']['size'][$i] > 15000000) {
                    array_push($fileUploadErrs, $filename . " is too large");
                } elseif ($fileType != "pdf" && $fileType != "docx" && $fileType != "jpeg" && $fileType != "png" && $fileType != "jpg") {
                    array_push($fileUploadErrs, $filename . " is not JPG, JPEG, PNG, PDF, DOCX type");
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
                        $stmtfile = $conn->prepare("INSERT INTO evidence (`request_id`, `file_name`)
                                          VALUES (?,?)");
                        $stmtfile->bind_param("ss", $request_id, $_FILES['fileToUpload']['name'][$i]);
                        $stmtfile->execute();
                    } else {
                        array_push($fileUploadErrs, "There is an error uploading " . $filename);
                    }
                }
            }
        }
    }
}


?>
<head>
    <title>Student Page4</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/612d434467.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./css1/Student_p4.css">
</head>
<body>
<?php include "name_logout.php";?>

<?php
if($_SESSION["user_type"] == "student"){
    echo "<a href='http://localhost/stdMS/my_requests.php' style='font-weight: bold; font-size: 20px; margin-left: 40px; margin-bottom: 50px; color: yellowgreen; padding-bottom: 50px'>
        Back
        </a>";
}
elseif($_SESSION["user_type"] == "staff"){
    echo "<a href='http://localhost/stdMS/student_requests.php' style='font-weight: bold; font-size: 20px; margin-left: 40px; margin-bottom: 50px; color: yellowgreen; padding-bottom: 50px'>
        Back
        </a>";
}
?>

<div class="jumbotron" style="background: none; color: white; font-family: PT Sans,sans-serif;">

<h1><?php
    if($_SESSION["user_type"] == "student") {
        echo "My Request";
    } else if($_SESSION["user_type"] == "staff"){
        echo "Request Details";
    }
    ?></h1>
</div>
<div class="container">
        <?php
        if($_SESSION["user_type"] == "staff"){
        echo"
        <div class='row'>
            <div class='col-25'>
                <label for='fname'>Name</label>
            </div>
            <div class='col-75'>
                <label>$student_name</label>
            </div>
        </div>
        <div class='row'>
            <div class='col-25'>
                <label for='ind'>Index Number</label>
            </div>
            <div class='col-75'>
                <label>$indexNumber</label>
            </div>
        </div>";}
        ?>
        <div class="row">
            <div class="col-25">
                <label for="types">Type of Request</label>
            </div>
            <div class="col-75">
                <label><?php echo $type;?></label>
            </div>
        </div>
        <div class="row">
            <div class="col-25">
                <label for="details">Details</label>
            </div>
            <div class="col-75">
                <label><?php echo $description;?></label>
            </div>
        </div>
        <div class="row">
            <div class="col-25">
                <label for="myfile">Evidence</label>
            </div>
            <?php
                $stmtevidence = $conn->prepare("SELECT `file_name` FROM evidence WHERE `request_id` =?");
                $stmtevidence->bind_param("i", $request_id);
                $stmtevidence->execute();
                $file_names = $stmtevidence->get_result();

                if($file_names ->num_rows == 0){
                    echo "<label class='error'>No evidence</label>";
                }
                while ($row = $file_names->fetch_assoc()){

                    $filename = $row['file_name'];
                    echo "<a href='uploads/$request_id$filename' style='color: white;'>$filename</a><br>";
                }

                if($_SESSION["user_type"] == "student"){
                    echo "
                    <form action='request_details.php?id=$request_id' method='post' enctype='multipart/form-data'>
                        <div class='col-75e'>
                            <input type='file' name='fileToUpload[]' id='fileToUpload' multiple>
                            <input type='submit' value='Upload Files' name='submit' style='padding:4px 4px 4px 4px; background-color: #4CAF50; '>
                        <label class='error'>*";

                    foreach ($fileUploadErrs as $fileUploadErr){
                        echo $fileUploadErr. "<br>";
                    }

                    echo '
                        </<label>
                        </div>
                    </form>
                    ';
                }
            ?>

        </div>
        <div class="row">
            <div class="col-25">
                <label for="comments">Comments</label>
            </div>
            <div class="col-75">
                <div class="commentSec" style="overflow-y: scroll; height: 100px;">
                    <?php
                    if($result4->num_rows > 0){
                        while ($row = $result4->fetch_assoc()) {
                            $date = $row["date"];
                            $sender_id = $row["sender_id"];
                            $message = $row["message"];

                            // get name
                            $stmt5 = $conn->prepare("SELECT `name` FROM users WHERE `id`=?");
                            $stmt5->bind_param("i", $sender_id);
                            $stmt5->execute();
                            $result5 = $stmt5->get_result()->fetch_assoc();
                            $sender_name = $result5["name"];

                            echo "
                                <div class='commentSub' style='background-color: white;'>
                                    <div>
                                        <div class='date'>$date</div>
                                        <div class='commentator'>$sender_name</div>
                                    </div>
                                    <div class='comment'>$message</div>
                                </div><br>";
                        }
                    }
                    CloseCon($conn);
                    ?>
                </div>
                <span><a href="http://localhost/stdMS/add_comment.php"style="color:black; font-weight: bold;">Add Comment</a></span>

            </div>
        </div>
    <?php


    if($_SESSION["user_type"] == "staff"){
        echo "
        <form action='request_details.php' method='post'>
        <div class='row'>
            <div class='col-25'>
                <label for='details'>Respond</label>
            </div>
            <div class='col-75'>
                <select class='op' name='response' style='width: 100%;   font-size: 20px; font-weight: bold;font-family: PT Sans,sans-serif;'>
                    <option value=''>response</option>
                    <option value='Accepted'>Accept</option>
                    <option value='Declined'>Decline</option>
                    <option value='RequestedMore'>Request More</option>
                </select>
            </div>
        </div>
        <div class='row1'>
            <input type='submit' name='respond' value='Submit' style='font-weight: bold; font-size: 15px; '>
        </div>
        </form>
        ";
    }

    ?>


</div>



</body>
</html>

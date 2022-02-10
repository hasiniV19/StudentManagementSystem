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

error_reporting(-1);
ini_set('display_errors', 'true');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require "db_connection.php";

$detailsErr = "";
$fileUploadErrs = array();
$target_dir = "uploads/"; // the directory where the file is going to be placed
$user_id = $_SESSION["user_id"];
if(isset($_POST["cancel"]) && $_SERVER["REQUEST_METHOD"] == "POST"){
    header("Location: http://localhost/stdMS/student_p2.php", true,301);
    exit();
}

if (isset($_POST["submit"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = OpenCon();

    $request_entered = false;
    $stmtindex = $conn->prepare("SELECT `index_number` FROM students WHERE `user_id`=?");
    $stmtindex->bind_param("i", $user_id);
    $stmtindex->execute();
    $result = $stmtindex->get_result()->fetch_assoc();

    $index_number = $result["index_number"];

    if (!empty($_POST["types"])) {
        $request_type = test_input($_POST["types"]);
    }

    if (empty($_POST["details"])){
        $detailsErr = "Details is required";
    } else {
        $details = test_input($_POST["details"]);
        if(empty($details)){
            $detailsErr = "Details is invalid";
        } else{
            $stmt = $conn->prepare("INSERT INTO requests (`index_number`, `name`, `type`, `status`, `description`)
                                          VALUES (?, ?, ?, 'Pending', ?)");
            $stmt->bind_param("ssss", $index_number, $_SESSION["name"], $request_type, $details);
            $stmt->execute();
            $request_entered = true;
            $request_id = $stmt->insert_id;
        }
    }

    // changed here............. checked whether user upload a file or not
    if($request_entered && $_FILES["fileToUpload"]["size"][0] == 0) {
        header("Location: http://localhost/stdMS/student_p2.php", true,301);
        exit();
    }
    if ($request_entered && $_FILES["fileToUpload"]["size"][0] != 0) {
        $countfiles = count($_FILES["fileToUpload"]["name"]);
        /*
        if($countfiles == 0){
            header("Location: http://localhost/stdMS/student_p2.php", true,301);
            exit();
        }
        */
        for ($i = 0; $i < $countfiles; $i++) {
            $target_file = $target_dir . $request_id . $_FILES['fileToUpload']['name'][$i];
            $filename = $_FILES['fileToUpload']['name'][$i];

            $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            if (file_exists($target_file)) {
                array_push($fileUploadErrs, $filename." already added");
            } elseif ($_FILES['fileToUpload']['size'][$i] > 15000000){
                array_push($fileUploadErrs, $filename." is too large");
            } elseif ($fileType != "pdf" && $fileType != "docx" && $fileType != "jpeg" && $fileType != "png" && $fileType != "jpg"){
                array_push($fileUploadErrs, $filename. " is not JPG, JPEG, PNG, PDF, DOCX type");
            }

            else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
                    $stmtfile = $conn->prepare("INSERT INTO evidence (`request_id`, `file_name`)
                                          VALUES (?,?)");
                    $stmtfile->bind_param("ss", $request_id, $_FILES['fileToUpload']['name'][$i]);
                    $stmtfile->execute();
                } else {
                    array_push($fileUploadErrs, "There is an error uploading ". $filename);
                }
            }
        }
        if(empty($fileUploadErrs)){
            header("Location: http://localhost/stdMS/student_p2.php", true,301);
            exit();
        }
        // changed here.....................deleted the entries which file upload is failed.
        else{
            $stmtdelreqentry = $conn->prepare("DELETE FROM requests WHERE `id`=?");
            $stmtdelreqentry->bind_param("i", $request_id);
            $stmtdelreqentry->execute();
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
?>
<head>
    <title>Student Page3</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/612d434467.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./css1/Student_p3.css">
</head>
<body>
<?php include "name_logout.php";?>
<div class="jumbotron" style="background: none; color: white; font-family: PT Sans,sans-serif;">
<h1>New Request</h1>
</div>
<div class="jumbotron" style="background: none;">
    <div class="container">
    <form action="new_request.php" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-25">
                <label for="types">Type of Request</label>
            </div>
            <div class="col-75">
                <select id="types" name="types">
                    <option value="Late Add/Drop Request"
                        <?php if(isset($request_type) && $request_type == "Late Add/Drop Request") echo "selected";?>>
                        Late Add/Drop Request
                    </option>
                    <option value="Repeat Exam Request"
                        <?php if(isset($request_type) && $request_type == "Repeat Exam Request") echo "selected";?>>
                        Repeat Exam Request
                    </option>
                    <option value="Extend Assignment Submission Request"
                        <?php if(isset($request_type) && $request_type == "Extend Assigment Submission Request") echo "selected";?>>
                        Extend Assignment Submission Request
                    </option>
                    <option value="Other"
                        <?php if(isset($request_type) && $request_type == "Other") echo "selected";?>>
                        Other
                    </option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-25">
                <label for="details">Details</label>
            </div>
            <div class="col-75">
                <textarea id="details" name="details" placeholder="" style="height:200px"><?php if(isset($details)){echo $details;} ?></textarea>
                <label class="error">*<?php echo $detailsErr;?></label>
            </div>

        </div>
        <div class="row">
            <div class="col-25">
                <label for="myfile">Evidence</label>
            </div>
            <div class="col-75">
                <input type="file" name="fileToUpload[]" id="fileToUpload" multiple>
                <label class="error">*
                <?php
                foreach ($fileUploadErrs as $fileUploadErr){
                    echo $fileUploadErr. "<br>";
                }
                ?>
                </label>
            </div>
            <div class="row">
                <div class="col-75">
                    <input type="submit" name="submit" value="Submit" style="font-weight: bold; font-size: 15px; ">
                    <input type="submit" name="cancel" value="Cancel" style="background-color: #ff4d4d;font-weight: bold; font-size: 15px;"/>
                </div>
            </div>
    </form>
</div>
</div>

</body>
</html>


<?php

function OpenCon()
{

$servername ="";
$username ="";
$password ="";
$dbname = "student_management_system";

$conn= new mysqli($servername, $username, $password, $dbname);
if($conn-> connect_error){
    die("Connection failed: ". $conn->connect_error);
}

return $conn;
}

function CloseCon($conn)
{
    if (isset($conn)) {
        $conn -> close();
    }
}
?>

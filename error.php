<?php

if($_GET['error'] == "not_allowed"){
    echo "<h1>You are not allowed to access</h1>";
} elseif($_GET['error'] == "not_found"){
    echo "<h1>Page not found</h1>";
}
?>
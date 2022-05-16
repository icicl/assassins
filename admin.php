<?php
session_start();

if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true){
    header("location: newgame.php");
    exit;
  }

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    if ($username == "icicl" && $password == "") {
        $_SESSION["admin"] = true;
        header("location: newgame.php");
    } else {
        echo "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Identification</title>
</head>
<body>
    <div class="wrapper">
        <h2>Identify yourself.</h2>
        <p>Enter your name.</p>
        <form action="admin.php" method="post" id="form">
            <input type="text", id="textbox", name="username">
            <input type="password", id="passbox", name="password">
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>


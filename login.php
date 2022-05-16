<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
  }

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = trim($_POST["name"]);
    $file_ = fopen("log_.txt", "a+");
    fwrite($file_, date("[Y-m-d H:i:s]\t") .  $_SERVER['REMOTE_ADDR'] . "\tlogin.php\t" . $name . "\n");
    if (strlen($name) == 0 || !file_exists("codenames/" . $name)) {
        echo "User " . $name . " is not registered as active.";
    } else {
        $_SESSION["loggedin"] = true;
        $_SESSION["name"] = $name;
        header("location: index.php");
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
        <form action="login.php" method="post" id="form" onsubmit="return checkName()">
            <input type="text", id="textbox", name="name", onkeydown="function(e){ if (e.keyCode == 13) checkName()}">
            <input type="submit" value="confirm">
        </form>
    </div>
    <script>
        function checkName(){
            var name=document.getElementById("textbox").value;
            console.log(name);
            if (name.length > 0 && confirm("Are you " + name + "?")) {
                return true;
            }
            return false;
        }
    </script>
</body>
</html>


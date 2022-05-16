<?php
session_start();
if(!(isset($_SESSION["admin"]) && $_SESSION["admin"] === true)){
	header("location: admin.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = trim($_POST["name"]);// the title/name of the ongoing game
    if (strlen($name) == 0 || file_exists("games/" . $name)) {
        echo "Game title " . $name . " is invalid.";
    } else {
        mkdir("games/" . $name);
		mkdir("games/" . $name . "/targets");
		foreach (array("o","b","d","s","k") as $house) {
			mkdir("games/" . $name . "/" . $house);
			mkdir("games/" . $name . "/" . $house . "/0");
			foreach (array_slice(scandir("people/" . $house),2) as $person){
				fclose(fopen("games/" . $name . "/" . $house . "/0/" . $person, "w+"));
			}
		}
		fwrite(fopen("active", "w+"), $name);
		$people = array_slice(scandir("people/all"),2);
		foreach ($people as $person) {
			fwrite(fopen("games/" . $name . "/" . $person, "w+"), "3\n"); // number of lives
			$target_list = array();//list of the four initial targets
			$house = file_get_contents("people/all/" . $person);
			foreach (array("o","b","d","s","k") as $enemy_house) {
				if ($house == $enemy_house) {
					continue;
				}
				$i = 0;
				$targets = array();//potential targets from each house
				while (count($targets) == 0){
					$targets = array_slice(scandir("games/" . $name . "/" . $enemy_house . "/" . $i), 2);
					shuffle($targets);
					$i++;
				}
				$ii = 0;
				while (true){//move added target into next tier of how many times they are targeted
					if (file_exists("games/" . $name . "/" . $enemy_house . "/" . $ii . "/" . $targets[0])) {
						if (!file_exists("games/" . $name . "/" . $enemy_house . "/" . ($ii + 1))) {
							mkdir("games/" . $name . "/" . $enemy_house . "/" . ($ii + 1));
						}
						rename("games/" . $name . "/" . $enemy_house . "/" . $ii . "/" . $targets[0], "games/" . $name . "/" . $enemy_house . "/" . ($ii + 1) . "/" . $targets[0]);
						break;
					}
					$ii++;
				}
				array_push($target_list, $targets[0]);
			}
			$f = fopen("games/" . $name . "/targets/" . $person, "w+");
			fwrite($f, implode("\n", $target_list));
			fclose($f);
		}
	}
}
?>
<html>
	<body>
		<form action="newgame.php" method="post" id="form">
            <input type="text", id="textbox", name="name">
            <input type="submit" value="confirm">
        </form>
</body>
</html>

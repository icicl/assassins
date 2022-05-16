<?php
session_start();
if(!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)){
	header("location: login.php");
}
if ($_SESSION["name"] == "") {
	$_SESSION['loggedin'] = false;
	header("location: login.php");
}
if (!file_exists("games/" . file_get_contents("active") . "/targets/" . file_get_contents("codenames/" . $_SESSION["name"]))) {
	header("location: index.php");
}
$file_ = fopen("log_.txt", "a+");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$game = file_get_contents("active");
    $killer_ = trim($_POST["killer"]);
	$killer = file_get_contents("codenames/" . $killer_);
	if (strlen($killer) == 0 || !file_exists("games/" . $game . "/targets/" . $killer)) {// todo handle bad codename exploit
		echo "internal error :(";
		exit;
	}
    $victim = trim($_POST["victim"]);
	$victim_house = file_get_contents("people/all/" . $victim);
	fwrite($file_, date("[Y-m-d H:i:s]\t") .  $_SERVER['REMOTE_ADDR'] . "\teliminate.php\t" . $_SESSION["name"] . " eliminates " . $victim . "\n");
	$target_valid = false;
	foreach (explode("\n", file_get_contents("games/" . $game . "/targets/" . $killer)) as $target) {
		$target = str_replace("\r", "", $target);
		if (strlen($target) > 0) {
			if ($victim == $target) {
				$target_valid = true;
			};
		}
	}
    if (strlen($victim) == 0 || !file_exists("games/" . $game . "/" . $victim)) {
        echo "Invalid target name.";
	} else if (!($killer_ == $_SESSION["name"] && $target_valid)) {//todo move killer_ check to start
		echo "Incorrect target.";
    } else {
		$f = fopen("games/" . $game . "/" . $killer, "a");
		fwrite($f, $victim . "\n");
		fclose($f);
		$target_lives = (int) substr(file_get_contents("games/" . $game . "/" . $victim), 0, 1);
		$f = fopen("games/" . $game . "/" . $victim, "c");
		fwrite($f, "" . ($target_lives - 1), 1);
		fclose($f);
		if ($target_lives == 1) {//if was final kill
			foreach (explode("\n",file_get_contents("games/" . $game . "/targets/" . $victim)) as $victar) {//adjust each of the victims targets to be in the proper targeted count bracket
				$victar = str_replace("\r", "", $victar);
				if (strlen($victar) == 0) {
					continue;
				}
				$i = 1;
				$victar_house = file_get_contents("people/all/" . $victar);
				while (true){
					if (file_exists("games/" . $game . "/" . $victar_house . "/" . $i . "/" . $victar)) {
						rename("games/" . $game . "/" . $victar_house . "/" . $i . "/" . $victar, "games/" . $game . "/" . $victar_house  . "/" . ($i - 1) . "/" . $victar);
						break;
					}
					$i++;
				}
			}
			$i = 1;
			while (true){//delete victim's targeting bracket label
				if (file_exists("games/" . $game . "/" . $victim_house . "/" . $i . "/" . $victim)) {
					unlink("games/" . $game . "/" . $victim_house . "/" . $i . "/" . $victim);
					break;
				}
				$i++;
			}
			unlink("games/" . $game . "/targets/" . $victim);
		
			foreach (array_slice(scandir("games/" . $game . "/targets"), 2) as $player) {//todo: more efficient to count then move that way. also technically more fair to assign new targs in random order, prob doesnt matter
				$targets_orig = array();
				$targ_idx = -1;
				$tmp = 0;
				foreach (explode("\n", file_get_contents("games/" . $game . "/targets/" . $player)) as $target) {
					$target = str_replace("\r", "", $target);
					if (strlen($target) > 0) {
						$targets_orig[] = $target;
					}
					if ($victim == $target) {
						$targ_idx = $tmp;
					}
					$tmp++;
				}
				if ($targ_idx != -1) {
					$i = 0;
					$loop = true;
					while ($loop){
						if (!file_exists("games/" . $game . "/" . $victim_house . "/" . $i)) {
							array_splice($targets_orig, $targ_idx, 1);
							if ($player == $killer) {
								echo "Target eliminated. No other remaining targets of eliminated type.";
							}
							break;
						}
						$targets = array_slice(scandir("games/" . $game . "/" . $victim_house . "/" . $i), 2);
						shuffle($targets);
						for ($j = 0; $j < count($targets); $j++) {
							if (!in_array($targets[$j], $targets_orig)){
								array_splice($targets_orig, $targ_idx, 1, $targets[$j]);
								$ii = 0;
								if ($player == $killer) {
									echo "Target eliminated. Your new target is " . $targets[$j] . ".";//todo return to dashboard
								}
								while (true){
									if (file_exists("games/" . $game . "/" . $victim_house . "/" . $ii . "/" . $targets[$j])) {
										if (!file_exists("games/" . $game . "/" . $victim_house . "/" . ($ii + 1))) {
											mkdir("games/" . $game . "/" . $victim_house . "/" . ($ii + 1));
										}
										rename("games/" . $game . "/" . $victim_house . "/" . $ii . "/" . $targets[$j], "games/" . $game . "/" . $victim_house . "/" . ($ii + 1) . "/" . $targets[$j]);
										break;
									}
									$ii++;
								}
								$loop = false;
								break;
							}
						}
						$i++;
					}
					$f = fopen("games/" . $game . "/targets/" . $player, "w");
					fwrite($f, implode("\n", $targets_orig));
					fclose($f);
				}
			}
		} else {
			$targets_orig = array();
			$targ_idx = -1;
			$tmp = 0;
			foreach (explode("\n", file_get_contents("games/" . $game . "/targets/" . $killer)) as $target) {
				$target = str_replace("\r", "", $target);
				if (strlen($target) > 0) {
					$targets_orig[] = $target;
				}
				if ($victim == $target) {
					$targ_idx = $tmp;
				}
				$tmp++;
			}
			if ($targ_idx != -1) {//todo -  redundant?
				$i = 0;
				$loop = true;
				while ($loop){
					if (!file_exists("games/" . $game . "/" . $victim_house . "/" . $i)) {
						echo "Target elimated. No other remaining targets of eliminated type.";
						array_splice($targets_orig, $targ_idx, 1);
						break;
					}
					$targets = array_slice(scandir("games/" . $game . "/" . $victim_house . "/" . $i), 2);
					shuffle($targets);
					for ($j = 0; $j < count($targets); $j++) {
						if (!in_array($targets[$j], $targets_orig)){
							array_splice($targets_orig, $targ_idx, 1, $targets[$j]);
							echo "Target eliminated. Your new target is " . $targets[$j] . ".";//todo return to dashboard
							$ii = 0;
							while (true){
								if (file_exists("games/" . $game . "/" . $victim_house . "/" . $ii . "/" . $targets[$j])) {
									if (!file_exists("games/" . $game . "/" . $victim_house . "/" . ($ii + 1))) {
										mkdir("games/" . $game . "/" . $victim_house . "/" . ($ii + 1));
									}
									rename("games/" . $game . "/" . $victim_house . "/" . $ii . "/" . $targets[$j], "games/" . $game . "/" . $victim_house . "/" . ($ii + 1) . "/" . $targets[$j]);
									break;
								}
								$ii++;
							}
							$loop = false;
							break;
						}
					}
					$i++;
				}
				$ii = 1;
				while (true){
					if (file_exists("games/" . $game . "/" . $victim_house . "/" . $ii . "/" . $victim)) {
						rename("games/" . $game . "/" . $victim_house . "/" . $ii . "/" . $victim, "games/" . $game . "/" . $victim_house . "/" . ($ii - 1) . "/" . $victim);
						break;
					}
					$ii++;
				}
				$f = fopen("games/" . $game . "/targets/" . $killer, "w");
				fwrite($f, implode("\n", $targets_orig));
				fclose($f);
			}
		}
		echo "<script>document.getElementById(\"form\").style.display=\"none\"";
    }
} else {
	fwrite($file_, date("[Y-m-d H:i:s]\t") .  $_SERVER['REMOTE_ADDR'] . "\teliminate.php\t" . $_SESSION["name"] . "\n");
	echo "Enter the name of the target you have assassinated. (case-sensitive).";
}
?>
<html>
	<body>
		<form action="eliminate.php" method="post" id="form">
			<input type="hidden", name="killer", value=<?php echo $_SESSION["name"];?>>
            <input type="text", id="textbox", name="victim">
            <input type="submit" value="confirm">
        </form>
</body>
</html>

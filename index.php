<html lang="en" style="scroll-behavior:smooth">
<head>
    <meta charset="UTF-8">
  <title>TBP Assassina</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=spy.css>

</head>


<?php
session_start();
if(!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)){
	header("location: login.php");
}
if ($_SESSION["name"] == "") {
	$_SESSION['loggedin'] = false;
	header("location: login.php");
}
$game = file_get_contents("active");
$name = $_SESSION["name"];
$truename = file_get_contents("codenames/" . $name);
$active = file_exists("games/" . $game . "/targets/" . $truename);
$targets = $active ? explode("\n",file_get_contents("games/" . $game . "/targets/" . $truename)) : array($truename);
//$kills[] = substr_count(file_get_contents("games/" . $game . "/" . $target), "\n") - 1;

$file_ = fopen("log_.txt", "a+");
fwrite($file_, date("[Y-m-d H:i:s]\t") .  $_SERVER['REMOTE_ADDR'] . "\tindex.php\t" . $name . "\n");
?>

<main>
<div class=grayout id=grayout>
    <img src="tl80.png" alt="Default">
  </div>
  <div class=popup id=maincard>
    <div class=name></div>
    <img src=''>
    <div class=team></div>
    <div class=about></div>
</div>

<section>
<div class=pcontainer><?php echo ($active ? "Greetings" : "Farewell") . ", Agent " . $name . "." . ($active ? " Here are your current targets:" : "");?></div>
<div class=member-container id="alpha">
  <?php foreach($targets as $target) {
    $target = str_replace("\r", "", $target);// windows monent -.-
    if (strlen($target) == 0) {
      continue;
    }
    $kills = substr_count(file_get_contents("games/" . $game . "/" . $target), "\n") - 1;
    echo "<div class=person onclick=\"show(this)\"><img src=\"images/" . $target . ".jpg\">
    <div class=name>" . $target . "</div>
	  <div class=team>" . $kills . " confirmed kill" . ($kills == 1 ? "" : "s") . ".</div>
	  <div class=about >Affiliation: TBP CA-A</div>
    </div>
    ";
  }
  ?>
  </div>
</section>
<section>
	<br />
  <?php if ($active) echo "<div class=pcontainer>To report a kill click <a href=\"eliminate.php\">here</a>.</div>";?>
  <div class=pcontainer><a href="leaderboard.php">Lethality leaderboard.</a></div>
</section>
<script>
  alpha = document.getElementById("alpha");
  sz = alpha.children.length;
  left = ((100 - sz * 16) / (sz + 1));
  for (child of alpha.children) {
    child.style.marginLeft = left + "%";
  }
    let gray=document.getElementById('grayout');
    let card=document.getElementById('maincard');
    let c_img,c_name,c_team,c_about;
    let card_visible=false;
    card_c = card.children;
    for(var i = 0; i < card_c.length; i++) {
      if(card_c[i].tagName=='IMG'){
        c_img=card_c[i];
      }
      if(card_c[i].className == 'name') {
        c_name=card_c[i];
      }
      if(card_c[i].className == 'team') {
        c_team=card_c[i];
      }
      if(card_c[i].className == 'about') {
        c_about=card_c[i];
      }
    }

  function show(div){
    console.log(div);
    c = div.children;
    gray.style.display='block';
    card.style.display='block';
    card_visible=true;
    c_name.innerHTML='';
    c_team.innerHTML='';
    c_img.src='';
    c_about.innerHTML='';
    for(var i = 0; i < c.length; i++) {
      if(c[i].tagName=='IMG'){
        c_img.src=c[i].src;
      }
      if(c[i].className == 'name') {
          c_name.innerHTML=c[i].innerHTML;
      }
      if(c[i].className == 'team') {
        c_team.innerHTML=c[i].innerHTML;
      }
      if(c[i].className == 'about') {
        c_about.innerHTML=c[i].innerHTML;
      }
    }
  }

  gray.onclick = function(e){
    if (card_visible && e.target != card){
      gray.style.display='none';
      card.style.display='none';
    }
  }

  document.onkeydown = function(e) {
    if(e.key === "Escape") {
      gray.style.display='none';
      card.style.display='none';
    }
  }
  </script>
</main>
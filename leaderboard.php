<html lang="en" style="scroll-behavior:smooth">
<head>
    <meta charset="UTF-8">
    <title>TBP Assassins Leaderboard</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>
<body>
    <div class="board"></div>
    <script>
        people = [];
        const house_colors = {"b":"#f66", "d":"#88f", "k":"#8f8", "s":"#fd7", "o":"#fff"};
        <?php
        $file_ = fopen("log_.txt", "a+");
        fwrite($file_, date("[Y-m-d H:i:s]\t") .  $_SERVER['REMOTE_ADDR'] . "\tleaderboard.php\n");
        $game = file_get_contents("active");
        foreach (scandir("games/".$game) as $file) {
            if (is_file("games/".$game."/".$file)) {
                echo "people.push([\"" . $file . "\", " . (substr_count(file_get_contents("games/" . $game . "/" . $file), "\n") - 1) . ", " . substr(file_get_contents("games/" . $game . "/" . $file),0,1) . ", \"" . file_get_contents("people/all/" . $file) . "\"]);\n";
            }
        }
        ?>
        people.sort(function(a, b) {
            return b[1] - a[1];
        });
        people.splice(0, 0, ["Assassin Name", "Kills", "Lives", "o"]);
        szy = 20;
        boardelement=document.getElementsByClassName("board")[0];
        boardelement.style.height = Math.min(window.innerHeight, (((90) + 6) * people.length + 6)) + "px";
        boardelement.style.width = 790 + "px";
        function resize(){
            width=window.innerWidth;
            boardelement.style.height = Math.min(window.innerHeight, (((90) + 6) * people.length + 6)) + "px";
            boardelement.style.left=(width-790)/2+"px";
        }
        resize();
        window.onresize=function(){resize()};

        b=document.getElementsByClassName("board")[0];
        b.innerHTML = "";
        for (person of people){
            r=b.appendChild(document.createElement("div"));
            r.className="row";
            r.style.height = (90)+"px";
            r.style.backgroundColor = house_colors[person[3]];

            t=r.appendChild(document.createElement("div")); //profile image
            t.className="tile";
            ti = t.appendChild(document.createElement("img"));
            ti.src = "images/" + person[0] + ".jpg"
            ti.style.width = "70px";
            ti.style.height = "70px";
            ti.style.margin = "5px";
            
            t=r.appendChild(document.createElement("div")); // player name
            t.className="tile";
            t.style.width = "470px";
            t.innerHTML = person[0];

            t=r.appendChild(document.createElement("div")); // kill count
            t.className="tile";
            t.innerHTML = person[1];
            //            t.style.backgroundColor = ["#808", "#a0a", "#d0d", "#f0f"][person[2]];

            t=r.appendChild(document.createElement("div")); // life tally holder
            t.className="tile";
            t.style.backgroundColor;

            for (_ = 0; _ < 3 - person[2]; _++) {
                tt=t.appendChild(document.createElement("div")); // life tally
                tt.className="life";
            }
        }
        document.getElementsByClassName("tile")[3].innerHTML = "Lives"

    </script>
</body>
</html>
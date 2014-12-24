<html>
<head>
<meta name="txtweb-appkey" content="e8ce1337-7b55-4cea-a0f7-c619e52e6c44">
</head>

<?php
include_once('../simple_html_dom.php');
include_once('isl.php');
date_default_timezone_set('Asia/Kolkata');

$url="http://www.livescore.com/soccer/india/super-league-play-off/fixtures/all/";
$htm=file_get_html($url);

//echo $_GET['txtweb-mobile'];
$htm = file_get_html($url);
$table = $htm->find('div[class=content] table[class=league-table]');
$i = 0;
$j = 0;

if (isset($_GET['url'])) {

    $url = "http://www.livescore.com" . $_GET['url'];
    getDetailedScore($url);
    $htm = file_get_html($url);
    echo $htm->plaintext;
    $row = $htm->find('table[class=match-details match-ellipsis league-table mtn] tr');
    $hometeam = $htm->find('th[class=home]', 0)->getAttribute('title');
    $awayteam = $htm->find('th[class=awy]', 0)->getAttribute('title');
    foreach ($row as $ele) {

        if ($ele->getAttribute('class') == 'menu')
            continue;
        if ($ele->find('[class=title]'))
            echo "-<br>";
        echo ucwords($ele->plaintext);
	$shortTeam=array("FC Goa"=>"Goa","Chennaiyin FC"=>"Chennai","Delhi Dynamos FC"=>"Delhi","FC Pune City"=>"Pune","Northeast United FC"=>"NUFC",
				"Kerala Blasters FC"=>"Keralam","Atletico de Kolkata"=>"Kolkata","Mumbai City FC"=>"Mumbai");
	$shortAwayTeam=$shortTeam[trim($awayteam)];
	$shortHomeTeam=$shortTeam[$hometeam];
        if ($ele->find('span[class=inc yellowcard left]'))
            echo " [YC] ({$awayteam})";
        if ($ele->find('span[class=inc yellowcard right]'))
            echo " [YC] ({$hometeam})";
        if ($ele->find('span[class=inc redcard left]'))
            echo " [RED] ({$awayteam})";
        if ($ele->find('span[class=inc redcard right]'))
            echo " [RED] ({$hometeam})";
        if ($ele->find('span[class=inc redyellowcard right]'))
            echo " [2nd Yellow(RED] ({$hometeam})";
        if ($ele->find('span[class=inc redyellowcard left]'))
            echo " [2nd Yellow(RED)] ({$awayteam})";
        if ($ele->find('span[class=inc goal-miss left]'))
            echo " [Pen. Miss] ({$awayteam})";
        if ($ele->find('span[class=inc goal-miss right]'))
            echo " [Pen. Miss] ({$hometeam})";
        if ($ele->find('span[class=inc goal right]'))
            echo " (GOAL-{$shortHomeTeam})";
        if ($ele->find('span[class=inc goal left]'))
            echo " (GOAL-{$shortAwayTeam})";
        echo "<br>";
    }
    $home = 1;

    if ($stats = $htm->find('table[data-type=stats] tr[class]'))
        echo "<br>STATS<br>";
    foreach ($stats as $ele) {
        foreach ($ele->find('td') as $cols)
            echo $cols->plaintext . ' ';
        echo "<br>";
    }
    $subin = $htm->find('[class=inc sub-in]');
    $subout = $htm->find('[class=inc sub-out]');

    $subs = array();
    
    $sub = $htm->find('tr[class*=subs] td');
    //echo "-<br>SUBS:<br>";

    foreach ($sub as $ele) {
        if ($ele->find('[class=inc sub-in]')) {
            if ($ele->getAttribute('class') == 'min')
                echo $ele->plaintext;
            if ($ele->getAttribute('class') == "ply") {
                $names = $ele->find('div[class]');
                foreach ($names as $name)
                    if ($name->getAttribute('class') == "inc sub-in") {
                        echo $name->plaintext . " IN ";
                        $team = $home ? $hometeam : $awayteam;
                        
                        echo "<br>";
                        $home = $home ? 0 : 1;
                    }
                    else
                        echo $name->parent()->prev_sibling()->plaintext . " " . $name->plaintext . " OUT/";
              
            }
          
        }
    }
    echo "<br>";

    die;
}


$scorerow = "";
foreach ($table as $ele) {
    
    $rows = $ele->find('tr');
    foreach ($rows as $row) {
      
        if (!$homepage)
            if ($date = $row->find('[class=date]', 0)) {
                echo "<br>" . $date->plaintext;
                if ($stage = $row->find('[class=league] span', 0))
                    echo " - " . $stage->plaintext;
                echo "<br>";
                continue;
            }
        if ($comp = $row->find('[class=league]', 0)) {
            
            echo $comp->plaintext . '<br>';
            
            continue;
        }
        foreach ($row->find('td') as $col) {

            if ($col->find('a[class=scorelink]', 0)) {
                $url = $col->find('a[class=scorelink]', 0)->href;
            }
            if ($col->getAttribute('class') == "fd") {
                if (stristr($col->plaintext, ":")) {
                 
                    date_default_timezone_set("GMT");
                    $str = strtotime($col->plaintext);
                    date_default_timezone_set("Asia/Kolkata");
                    $time = date("g:i A", $str);
                    $scorerow = $scorerow . "{$time} : ";
                } else {
                    if (!$homepage)
                        if (!stristr($col->plaintext, "FT") && $col->plaintext != " Postp. ")
                            $scorerow = $scorerow . "LIVE ";
                    $scorerow = $scorerow . $col->plaintext . " ";
                }
            }
            else
                $scorerow = $scorerow . $col->plaintext . " ";
        }
        if (strpos($url, "/soccer") == 0 && !strstr($url, "livescore") && !empty($url)) {
            echo "<a href='http://ec2-184-169-171-66.us-west-1.compute.amazonaws.com/txtweb/isl_scores.php?url={$url}&league={$league}'> $scorerow </a>";
            $url = "";
        }
        else
            echo $scorerow;
        echo "<br>";
        $scorerow = "";
        
    }
    echo "<br>";
}
echo "-<br>";

function getDetailedScore($url)
{
	$htm=file_get_html($url);
	$rows=$htm->find('div[class*=row-]');
	$teams= explode(" vs ",$htm->find('title',0)->plaintext);
	$hometeam=str_replace("LiveScore : ","",$teams[0]);
	$awayteam=$teams[1];
	$shortTeam=array("FC Goa"=>"Goa","Chennaiyin FC"=>"Chennai","Delhi Dynamos FC"=>"Delhi","FC Pune City"=>"Pune","Northeast United FC"=>"NUFC",
				"Kerala Blasters FC"=>"Keralam","Atletico de Kolkata"=>"Kolkata","Mumbai City FC"=>"Mumbai");
	$awayteam=$shortTeam[trim($awayteam)];
	$hometeam=$shortTeam[$hometeam];
	foreach($rows as $row)
	{

		echo $row->plaintext;
		if($row->getAttribute('class')!="row row-tall")
		{
		if($row->find('span[class=inc redyellowcard]'))
			echo "(2nd Yellow-RED Card!)";
		if($row->find('span[class=inc redcard]'))
			echo "(RED Card!!)";
		if($row->find('span[class=name]',0))
		{
		$playername=$row->find('span[class=name]',0);
		if(strlen($playername->plaintext)<1)
			$playername=$row->find('span[class=name]',1);
		if($playername->parent()->parent()->getAttribute('class')=="ply tright")
			echo "({$hometeam})";
		else
			echo "({$awayteam})";
		}
		}
		echo "<br>";
	}

	die;

}

?>

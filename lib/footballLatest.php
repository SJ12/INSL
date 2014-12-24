<?php
include_once('../simple_html_dom.php');
$GLOBALS['livematch']=0;
function getLatest()
{
global $livematch;
$response='';
$url="http://www.livescore.com/soccer/india/super-league-play-off/";
//$url="http://www.livescore.com/soccer/india/super-league/results";
$htm=file_get_html($url);
$htm = file_get_html($url);
$table = $htm->find('div[class=content] table[class=league-table]');
$upComing=0;
$concluded=0;
$live=0;
foreach ($table as $ele) {
    
    $rows = $ele->find('tr');
    foreach ($rows as $row) {
      
        if (!$homepage)
            if ($date = $row->find('[class=date]', 0)) {
	
               $dateofmatch= trim($date->plaintext);
				
                /*if ($stage = $row->find('[class=league] span', 0))
                    echo " - " . $stage->plaintext;
                echo "<br>";*/
                continue;
            }
        if ($comp = $row->find('[class=league]', 0)) {
            
            $response=$response.$comp->plaintext . '<br>';
            
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
                    $scorerow = $scorerow . "{$time}:  ";
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

       if( $dateofmatch==date('F j'))
//if( $dateofmatch=="December 4")	
{
	//echo $dateofmatch;
     	if(stristr($scorerow,"? - ?"))
	{
		if($upComing==0)
		$response=$response."--UpComing--<br>";
		$scorerow=str_replace("? - ?","vs",$scorerow);
		$upComing=1;
	}
	if(stristr($scorerow,"FT"))
	{
		if($concluded==0)
		$response=$response."--Concluded--<br>";
		$scorerow=str_replace(" FT ","",$scorerow);
		$concluded=1;
	}
	if(stristr($scorerow,"LIVE"))
	{
		$livematch=1;
		if($live==0)
		$response=$response.":: LIVE ::<br>";
		$scorerow=str_replace("LIVE ","",$scorerow);
		$live=1;
		
	}		
        if (strpos($url, "/soccer") == 0 && !strstr($url, "livescore") && !empty($url)) {
            $response=$response."<a href='http://ec2-184-169-171-66.us-west-1.compute.amazonaws.com/txtweb/isl_scores.php?url={$url}&league={$league}'> $scorerow </a>";
            $url = "";
        }
        else
            $response=$response.$scorerow;
	$response=$response."<br><br>";	
}
        $url = "";
        $scorerow = "";

        
    }
	}
if($response=='')
	$response="No matches today in Indian Super League.<br><br>Reply @insl for latest updates";
else
	$response.="<br>Reply @insl to follow live action!"; 

//$response="-Available Keywords-<br>@insl.twt - Live tweets<br>@insl.poll - Take ISL poll<br>@insl.quiz - Play ISL quiz<br>@insl.players playername - search for a player "; 
return $response;

}

?>

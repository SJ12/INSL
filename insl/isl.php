<?php
include_once('../lib/db.php');
include_once('../lib/utilities.php');
connect_db("txtweb");
echo "<html>   
<head>
<meta name=\"txtweb-appkey\" content=\"e8ce1337-7b55-4cea-a0f7-c619e52e6c44\">
</head>
<body>";
date_default_timezone_set("Asia/Kolkata");
//echo $_GET['txtweb-mobile'];
include_once('../simple_html_dom.php');
if (!debug_backtrace()) {
if(isset($_GET['txtweb-mobile']) && checkSource($_GET['txtweb-protocol'],$_GET['txtweb-mobile']))
{
subscribe();
}
$scheme = 'http';
        $query = parse_url($scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
       $hostUrl = $query['scheme'] . '://' . $query['host'] . $query['path']; //host url
$GLOBALS['hostUrl']=$hostUrl;
$GLOBALS['livematch']=0;

if($_GET['txtweb-message']=='lb')
	header("location: http://ec2-184-169-171-66.us-west-1.compute.amazonaws.com/PHPExcleReader/quiz.php?id=5438d71081ea0&txtweb-message=lb");
if($_GET['txtweb-message']=='res')
	header("location: http://ec2-184-169-171-66.us-west-1.compute.amazonaws.com/txtbrahma/poll.php?id=5438f0cfb5b71&txtweb-message=res");
if(isset($_GET['team']))
	getSquad($_GET['team'],$_GET['more']);
if(isset($_GET['player']))
	getProfile($_GET['player']);

if(isset($_GET['fixtures']))
	getSchedule();
if(isset($_GET['news']))
	getNews();
if(isset($_GET['table']))
	getStandings();
if(isset($_GET['des']))
	getNewsDes($_GET['des']);


if(isset($_GET['teams']))
	getTeams();

if(isset($_GET['players']))
{
	if(isset($_GET['txtweb-message']) && trim($_GET['txtweb-message'])!='')
		searchPlayers($_GET['txtweb-message']);
	else
	getPlayers();
}
if(isset($_GET['teambio']))
	getTeamBio($_GET['teambio']);

if(isset($_GET['tv']))
	getTelecast();
if(isset($_GET['latest']))
	getLatest();

if(isset($_GET['search']))
	searchPlayers($_GET['search']);

if(isset($_GET['top']))
	getTopScorers();

getHomePage();

}

{
	$url="https://mobile.twitter.com/".$username;
	$i=0;
	echo $url;
	while($i<18)
		echo $username[$i++]."-";
	$htm=file_get_html($url);
	$fullname=$htm->find('div[class=fullname]',0)->plaintext;
	return $fullname;


}
function getProperTweet($tweet)
{
	$words=explode(" ",$tweet);
	$find=array();
	$replace=array();
	foreach($words as $word)          
	{
		
		if(stristr($word, "@"))
		{
			$username=explode("@",strip_tags($word));
			print_r($username);      
			$fullname=getFullName($username[1]);
			array_push($find,"@".$username[1]);
			array_push($replace,$fullname);
			
			
		}
	}
	print_r($find);
	print_r($replace);
	return str_replace($find,$replace,$tweet);

}
*/
function getLiveTweets()
{
global $livematch;

$username="IndSuperLeague";

$url="https://mobile.twitter.com/".$username;


$htm=file_get_html($url);
$find=array();
$replace=array();
$i=0;
$next=$htm->find('div[class=w-button-more] a',0);
$items=$htm->find('[class=tweet-text]');
$id=explode("=",$next->href,2);
echo "--Live Tweets--<br>";
foreach($items as $ele)
{
	if(stristr($ele->innertext,"Never miss a moment!")) 
		continue;	
	echo strip_tags(str_ireplace($find,$replace,$ele->innertext));
	echo " (".$ele->parent()->parent()->parent()->find('td[class=timestamp]',0)->plaintext." ago)";
	echo "<br><br>";
	break;
}



}
function getStats()
{
	$url="http://www.flashscore.com/soccer/india/isl/";
	$htm=file_get_html($url);
	print $htm->plaintext;
	$stats=$htm->find('div[class=stats-table-container]span[class=team_name_span]');
	foreach($stats as $stat)
		echo $stat->plaintext.'<br>';
	
}
function getLatest()
{
global $livematch;
$url="http://www.livescore.com/soccer/india/super-league-play-off/";
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
				

                    echo " - " . $stage->plaintext;
                echo "<br>";*/
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
{

     	if(stristr($scorerow,"? - ?"))
	{
		if($upComing==0)
		echo "--UpComing--<br>";
		$scorerow=str_replace("? - ?","vs",$scorerow);
		$upComing=1;
	}
	if(stristr($scorerow,"FT"))
	{
		if($concluded==0)
		echo "--Concluded--<br>";
		$scorerow=str_replace(" FT ","",$scorerow);
		$concluded=1;
	}
	if(stristr($scorerow,"LIVE"))
	{
		$livematch=1;
		if($live==0)
		echo ":: LIVE ::<br>";
		$scorerow=str_replace("LIVE ","",$scorerow);
		$live=1;
		
	}		
        if (strpos($url, "/soccer") == 0 && !strstr($url, "livescore") && !empty($url)) {
            echo "<a href='http://ec2-184-169-171-66.us-west-1.compute.amazonaws.com/txtweb/isl_scores.php?url={$url}&league={$league}'> $scorerow </a>";
            $url = "";
        }
        else
            echo $scorerow;
	echo "<br><br>";	
}
        $url = "";
        $scorerow = "";

        
    }
	}



}
function getTelecast()
{

	echo "First time in the history of broadcast in India, spanning over 8 channels in sports & general entertainment category across 5 languages, ISL will be televised on:-<br>
	<br> Star Sports 2 (English)<br>Star Sports 3 (Hindi)<br>Star Sports HD 2 (English)<br>Star Gold (Hindi)<br>Star Utsav (Hindi)<br>Asianet Movies (Malayalam)<br>Jalsha Movies (Bengali)<br>Suvarna Plus (Kannada)
	<br><br>Live streaming: www.starsports.com";
	exit;
}
function getHomePage()
{

	getLatest();
	
	getLiveTweets();

	global $hostUrl;
	$hash=$_GET['txtweb-mobile'];
	echo "<a href='{$hostUrl}?top='> Top Scorers(NEW!) </a><br>";
	echo "<a href='http://ec2-184-169-171-66.us-west-1.compute.amazonaws.com/txtweb/isl_scores.php'> Fixtures </a><br>";
	echo "<a href='{$hostUrl}?news='> News </a><br>";
	echo "<a href='http://ec2-184-169-171-66.us-west-1.compute.amazonaws.com/txtbrahma/twitter_on_sms.php?id=5436c53341996'> Twitter feeds </a><br>";
	echo "<a href='http://ec2-184-169-171-66.us-west-1.compute.amazonaws.com/txtweb/isl.php?teams='> Teams </a><br>";
	echo "<a href='{$hostUrl}?players='> Players </a><br>";
	echo "<a href='{$hostUrl}?table='> Standings </a><br>";
	echo "<a href='http://ec2-184-169-171-66.us-west-1.compute.amazonaws.com/txtbrahma/poll.php?id=5438f0cfb5b71'> Take ISL poll </a><br>";
	echo "<a href='http://txtquiz.vishal.pro/txtquiz.php?id=36'> Play ISL quiz! </a>";



}
function getStandings()
{
	$url="http://www.livescore.com/soccer/india/super-league/";
	$htm=file_get_html($url);
	$rows=$htm->find('table[class=league-wc table mtn] tr[class!=tb2]');
	foreach($rows as $row)
	{
		$cols=$row->find('td');
		$colIndex=-1;
		foreach($cols as $col)
		{
			
			if($colIndex==1)
				echo $col->plaintext."<br>";
			if($colIndex==0)
				echo trim($col->plaintext).") ";
			if($colIndex>1)
			echo getPos($colIndex).":".$col->plaintext.' ';
			$colIndex++;
		}
		echo "<br><br>";
	}
	echo "*NB:<br>M - Matches Played<br>W - Won<br>D - Drawn<br>L - Lost<br>GS - Goals Scored<br>GA - Goals Allowed<br>GD - Goal Difference<br>Pts - Points<br>";
	exit;
}
function getPos($index)
{
	switch($index)
	{
		case '2':return "M";
		case '3':return "W";
		case '4':return "D";
		case '5':return "L";
		case '6':return "GS";
		case '7':return "GA";
		case '8':return "GD";
		case '9':return "Pts";
	}

}
function getCountDown()
{
	$htm=file_get_html("http://www.indiansuperleague.com/");
	$date1 = date("Y-m-d");
	$date2 = "2014-10-12";

$diff = abs(strtotime($date2) - strtotime($date1));

$years = floor($diff / (365*60*60*24));
$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

printf("Just %d day to go...!<br>", $days);
}

function getTeamBio($teamUrl)
{
	$teamUrl=str_replace("m.indiansuperleague.com/","m.indiansuperleague.com/isl-2014/teams/",$teamUrl);
	$htm=file_get_html($teamUrl);
	$paras=$htm->find('div[class=sqd-tm-desc] p');
	foreach($paras as $para)
		echo $para->plaintext.'<br>';

	exit;
}
function getTeams()
{
	
	$url="http://m.indiansuperleague.com/isl-2014/teams";
	global $hostUrl;
	$htm=file_get_html($url);

	$teams=$htm->find('div[class=tm-bx]');
	foreach($teams as $team)
	{
		$teamUrl=$team->find('a',0)->href;
		echo "<a href='{$hostUrl}?teambio={$teamUrl}/squad'> {$team->plaintext} </a><br>";

	}
	echo "</body></html>";
	exit;
}
function getPlayers()
{
	
	$url="http://m.indiansuperleague.com/isl-2014/teams";
	global $hostUrl;
	$htm=file_get_html($url);

	$teams=$htm->find('div[class=tm-bx]');
	echo "Choose team:<br>";
	foreach($teams as $team)
	{
		$teamUrl=$team->find('a',0)->href;
		echo "<a href='{$hostUrl}?team={$teamUrl}/squad&more=0'> {$team->plaintext} </a><br>";

	}
	echo "<br>Or<br><br>";
	echo "<form action='{$hostUrl}' method='get' class='txtweb-form'>";
	echo "Player Name";
	echo "<input type='text' name='search'>";
	echo "</form>";
	echo "to search for a player"; 
	echo "</body></html>";
	exit;
}
function getNewsDes($newsUrl)
{
	$url="http://m.indiansuperleague.com/".$newsUrl;
	$htm=file_get_html($url);
	global $hostUrl;
	$paras=$htm->find('div[class=stry-para] p');
	foreach($paras as $para)
	{
		echo $para->plaintext.'<br>';


	}


	exit;
}


function getNews()
{
	$url="http://m.indiansuperleague.com/news";
	$htm=file_get_html($url);
	global $hostUrl;
	$stories=$htm->find('div[class=ot-box-row]');
	foreach($stories as $story)
	{
		$item= $story->find('div[class=ft-box-txt]',0)->find('a',0);
		echo "<a href='{$hostUrl}?des={$item->href}'> {$item->plaintext} </a><br>";


	}
	exit;


}
function getSchedule()
{
	$url="http://m.indiansuperleague.com/isl-2014/schedules-fixtures";
	$htm=file_get_html($url);
	global $hostUrl;
	$matches=$htm->find('table[class=scheduleTable] tr[class]');
	foreach($matches as $match)
	{
		echo $match->find('div[class=dt-num]',0)->plaintext;
		echo " @".$match->find('div[class=dt-time]',0)->plaintext.", ";
		echo $match->find('meta[itemprop=name]',0)->getAttribute('content');
		echo "<br>-<br>";
	}
	exit;


}

function getSquad($teamUrl,$more)
{
	$htm=file_get_html($teamUrl);
	global $hostUrl;
	if($more)
	$categories=array("Midfielder","Forward");
	else
	$categories=array("Goalkeeper","Defender");
	foreach($categories as $ele)
	{
	echo " - ".$ele.'- <br>';
	//print 'div[data-id='.$ele.'] div[class=sqd-row-1]';
	$players=$htm->find('div[data-id='.$ele.'] div[class=sqd-row-1]');
	foreach($players as $player)
	{
		$playerUrl=$player->find('a',0)->href;
		echo "<a href='{$hostUrl}?player={$playerUrl}'> ";
		echo $player->find('div[class=plyr-box-nm-sq]',0)->plaintext;
		echo " (".ucwords($player->find('div[class=plr-ctry-nm-sq]',0)->plaintext);
		//echo ",".$player->find('div[class=plyr-box-pos-sq]',0)->plaintext;
		echo ")";
		if($player->find('div[class=plyr-star]'))
			echo "MARQUEE Player";
		echo "</a>";
		echo "<br>";

	}
	
	echo "<br>";
	}
	if(!$more)
	echo "<a href='{$hostUrl}?team={$teamUrl}/squad&more=1'> More </a><br>";
	echo "</body></html>";
	exit;



}

function getProfile($playerUrl)
{
	$htm=file_get_html('http://m.indiansuperleague.com'.$playerUrl);
	global $hostUrl;
	echo $htm->find('[class=plyr-name]',0)->plaintext.'<br>';
	echo $htm->find('[class=plyr-title]',0)->plaintext.'<br>';
	echo $htm->find('[class=plyr-countaryName]',0)->plaintext.'<br>';
	$stats=$htm->find('div[class=plyr-islDetail] div[class=isl-td-cnt]');
	foreach($stats as $stat)
	{
		echo $stat->find('[class=plyr-attr]',0)->plaintext." : ";
		echo $stat->find('[class=plyr-value]',0)->plaintext."<br>";

	}
	echo "<br>";
	echo "Season stats:<br>";
	$stats=$htm->find('div[class=plyrSeason-stats] div[class=isl-td-cnt]');
	foreach($stats as $stat)
	{
		echo $stat->find('[class=stats-attr]',0)->plaintext." : ";
		echo $stat->find('[class=stats-value]',0)->plaintext."<br>";

	}
	echo "<br>";
	if($htm->find('div[class=prfl-sec]',1))
	{
	echo "Career:<br>";
	foreach($htm->find('div[class=prfl-sec]',1)->find('li') as $ele)
		echo "- ".$ele->plaintext.'<br>';
	echo "<br>";
	}
	echo "Biography:<br>";
	echo $htm->find('div[class=prfl-sec]',0)->plaintext.'<br><br>';
	
	echo "</body></html>";
	exit;



}
function getTopScorers()
{
	$url="http://islsport.com/isl-top-scorers/";
	$htm=file_get_html($url);
	$rows=$htm->find('tbody tr');
	foreach($rows as $row)
	{
		$cols=$row->find('td');
		$i=0;
		foreach($cols as $col)
		{
			$i++;
			if($col->find('strong'))
				break;
			if($i==1)
				continue;
			if($i==3)
				echo "(";
			if($i==2)
				//getPlayerLink($col->plaintext);
				echo $col->plaintext;
			else
				echo $col->plaintext;
			if($i==3)
				echo ") - ";
			if($i==5)
				echo "goals";
			if($i==4)
				echo "";
			
		
		}
		echo "<br>-<br>";
	}
	die;


}
function searchPlayers($name)
{
	$url="http://m.indiansuperleague.com/isl-2014/players";
	$htm=file_get_html($url);
	global $hostUrl;
	$players=$htm->find('div[class=si-playerList] table tr');
	$found=0;
	echo "Search results:<br>";
	foreach($players as $player)
	{
		$playerName=$player->find('td[class=si-player-txt]',0)->plaintext;
		if(stristr($playerName,$name))
		{
		$playerUrl=str_replace("/isl-2014/teams","",$player->find('td[class=si-player-txt]',0)->find('a',0)->href);
		$parts=explode("/",$playerUrl,3);
		$playerUrl="/".$parts[1]."/squad/".$parts[2];
		echo "<a href='{$hostUrl}?player={$playerUrl}'> ";
		echo $player->find('td[class=si-player-txt]',0)->plaintext;
		echo "(".trim($player->find('td[class=si-team-txt]',0)->plaintext).")";
		echo "</a><br>";
		$found=1;
		}
	}
	if(!$found)
		echo "Sorry, nothing found for '{$name}'";
	echo "</body></html>";
	exit;
}
function getPlayerLink($name)
{
	$url="http://m.indiansuperleague.com/isl-2014/players";
	$htm=file_get_html($url);
	global $hostUrl;
	$players=$htm->find('div[class=si-playerList] table tr');
	$found=0;
	//echo "Search results:<br>";
	foreach($players as $player)
	{
		$playerName=$player->find('td[class=si-player-txt]',0)->plaintext;
		if(stristr($playerName,$name))
		{
		
		$playerUrl=str_replace("/isl-2014/teams","",$player->find('td[class=si-player-txt]',0)->find('a',0)->href);
		$parts=explode("/",$playerUrl,3);
		$playerUrl="/".$parts[1]."/squad/".$parts[2];
		$link="<a href='{$hostUrl}?player={$playerUrl}'> ";
		$link.= $player->find('td[class=si-player-txt]',0)->plaintext;
	
		$link.= "</a><br>";
		$found++;
		}
	}
	if($found!=1)
			$link= $name;
	echo $link;
	return $link;
}
function subscribe()
{
	$hash=$_GET['txtweb-mobile'];

	$sql="insert into isl_push values('{$hash}',NOW())";
	$res=mysql_query($sql);

}
?>

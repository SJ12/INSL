<?php

$username=*****;
$password=********;
$host="localhost";
$link = mysql_connect($host,$username, $password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

function connect_db($dbname)
{

	global $link;
	$db_selected = mysql_select_db($dbname, $link);
	//return array($db_selected,$link);


}
function num_of_rows($table)
{
	$res=mysql_query("select * from {$table}");
	return mysql_num_rows($res);



}
function insert($tablename,$values)
{
	$sql="insert into {$tablename} values(";
	foreach($values as $value)
		$sql=$sql."'".$value."',";

}

?>

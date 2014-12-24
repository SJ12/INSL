<?php

function checkSource($protocolId,$hash)
{
	if($protocolId==1000 && $hash!="b9206aca-a635-4441-8262-d1a85f2fb140")
		$bySms=1;
	else
		$bySms=0;

	return $bySms;


}

<?php
//error_reporting(E_ALL ^ E_NOTICE);
session_start();

$fname = "wall.txt";
$wall = file_get_contents($fname);
$records = explode("|",$wall);	

if ($_POST['m'] == 0){
	if (filesize($fname) == 0){
		echo "";
		exit;
	}
	if (!$wall){ echo "Error opening file"; exit;}
	check_max($records,$fname);
	$jstring = array();
	if (count($records) < 6){
		foreach($records as $record){
			$info = explode(":::",$record);
			$unit = array('Name'=>$info[0],'Msg'=>$info[1],'Avt'=>$info[2]);
			array_push($jstring,$unit);
		}
	}
	else{
		for ($i = count($records)-6;$i < count($records);$i++){
			$info = explode(":::",$records[$i]);
			$unit = array('Name'=>$info[0],'Msg'=>$info[1],'Avt'=>$info[2]);
			array_push($jstring,$unit);
		}
	}
	echo json_encode($jstring);
}
elseif ($_POST['m'] == 1){
	if (!isset($_SESSION['time_prev'])){
		$a = explode(' ',microtime());
		$_SESSION['time_prev'] = $a[1];
	}
	else{
		$a = explode(' ',microtime());
		$_SESSION['time'] = $a[1];
		$dif = $_SESSION['time'] - $_SESSION['time_prev'];
		
		$tp = explode(' ',microtime());
		$_SESSION['time_prev'] = $tp[1];
		if ($dif < 2){
		exit;
		}
	}
	check_max($records,$fname);
	$log = fopen($fname,"a+");
	$text = htmlspecialchars($_POST['msg']);
	
	if (filesize($fname) > 0){
	$ftext = '|'.$_SESSION['user'].':::'.$text.':::'.$_SESSION['avatar'];
	}
	else{
	$ftext = $_SESSION['user'].':::'.$text.':::'.$_SESSION['avatar'];
	}
	fwrite($log, $ftext);
	fclose($log);
	$dif = $_SESSION['time'] - $_SESSION['time_prev'];
	$reply = array('Name'=>$_SESSION['user'],'Msg'=>$text,'Avt'=>$_SESSION['avatar'],'Time'=>$dif);
	echo json_encode($reply);
}

function check_max($rec, $fname){
	define('max_rec',1000);
	if (count($rec) > max_rec){
		shift_up($rec, $fname);
	}
}

function shift_up($rec, $fname){
	$nrec = array();
		for ($i=0; $i < count($rec)-1; $i++){
			if ($i > 0) $nrec[$i] = '|'.$rec[$i+1];
			else $nrec[$i] = $rec[$i+1];
		}
	file_put_contents($fname, $nrec);
}
?>
<?php 
ini_set('max_execution_time',100000000);
function outputProgress($current, $total) {
    echo "<span style='position: absolute;z-index:$current;background:#FFF;'>" . round($current / $total * 100) . "% </span>";
    myFlush();
    sleep(1);
}
function letdownload($filename,$i)
{
	echo "<br><a href=\"http://e621downloader.github.io/".$filename."\" target=\"_blank\">Download Part ".$i."</a><br>";
	myFlush();
	sleep(1);
}
/**
 * Flush output buffer
 */
function myFlush() {
    echo(str_repeat(' ', 256));
    if (@ob_get_contents()) {
        @ob_end_flush();
    }
    flush();
}

function get_data($url) {
  $ch = curl_init();
  $timeout = 50;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$j=1;
//for($j=1;$j<=100;$j++){
$html = get_data("https://e621.net/post/index/".$j."/".$_POST['srch']);

$cont=strstr($html, 'Cookie.setup();');

$times=substr_count($cont,"\",\"file_ext");
	
	$zip = new ZipArchive;
	$zip->open($_POST['srch'].$j.".zip", ZipArchive::CREATE);
for($i=1;$i<=$times;$i++)
{
	$url=get_string_between($cont, "\"sample_url\":\"" , "\",\"sample_width");
	file_put_contents($i.'.'.pathinfo($url,PATHINFO_EXTENSION),file_get_contents($url));
	$zip->addFile($i.'.'.pathinfo($url,PATHINFO_EXTENSION));
	outputProgress($i,$times);
	$cont=strstr($cont,"\",\"sample_width");
	$cont=substr($cont,17);
}
$zip->close();
for($i=1;$i<=$times;$i++){
	if(file_exists($i.'.'.pathinfo($url,PATHINFO_EXTENSION)))
		unlink($i.'.'.pathinfo($url,PATHINFO_EXTENSION));	
}

letdownload($_POST['srch'].$j.".zip",$j);
if($times<75)
	$j=101;

?>
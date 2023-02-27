<?php

header('Content-Type: application/json; charset=utf-8');


  
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function full_url_origin( $s, $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function path_url( $s, $use_forwarded_host = false )
{
    $fullURL	=	 full_url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
    
    $break 		= explode('/', $fullURL);
	$current_file_name = $break[count($break) - 1]; 
	$pathURL	=	str_replace($current_file_name, '', $fullURL);
	return $pathURL;
}



function security_input($val){

    $val = strip_tags($val); // حذف اكواد html
    $val = trim($val); // حذف المسافات من البداية والنهاية
    $val = addslashes($val);
	$val = htmlspecialchars($val, ENT_QUOTES, "utf-8");
    $val = str_replace('"','',$val);
    $val = str_replace("'",'',$val);
    $val = stripslashes($val);

    return $val; // ارجاع قيمة الدالة
}




if(!isset($_GET["user"]))
	exit -1;

# create and load the HTML
include_once('simple_html_dom.php');
$html = new simple_html_dom();

$cstart		=	0;
$pagesize	=	100;
$count		=	0;


$url	=	"http://scholar.google.com/citations?hl=en&user=" . security_input($_GET["user"])."&view_op=list_works&sortby=pubdate&cstart=".$cstart."&pagesize=".$pagesize;


$html->load_file($url);

print "{\n \"total_citations\": " . $html->find("#gsc_rsb_st td.gsc_rsb_std", 0)->plaintext . ",\n";

$s = " \"citations_per_year\": { ";
$years = $html->find('.gsc_g_t');
$scores = $html->find('.gsc_g_al');
foreach($scores as $key => $score) {
	$s .= "\n  \"" . trim($years[$key]->plaintext) ."\": ". trim($score->plaintext) . ",";
}
print substr($s, 0, -1) . "\n },\n";




$str = " \"Publications\": [";

start:

if(isset($count) AND $count==100)
{
	$cstart		=	100;
	$pagesize	=	200;
		unset($html);
		$html = new simple_html_dom();
		$url	=	"http://scholar.google.com/citations?hl=en&user=" . security_input($_GET["user"])."&view_op=list_works&sortby=pubdate&cstart=".$cstart."&pagesize=".$pagesize;
		$html->load_file($url);
}


if(isset($count) AND $count==200)
{
		$cstart		=	200;
		$pagesize	=	300;
		unset($html);
		$html = new simple_html_dom();
		$url	=	"http://scholar.google.com/citations?hl=en&user=" . security_input($_GET["user"])."&view_op=list_works&sortby=pubdate&cstart=".$cstart."&pagesize=".$pagesize;
		$html->load_file($url);
}



foreach($html->find("#gsc_a_t .gsc_a_tr") as $pub) {

	$str .= "\n  {\n    \"Title\": \"" . trim($pub->find(".gsc_a_at", 0)->plaintext);
	$parsed = get_string_between($pub, '<td class="gsc_a_t"><a href="', '" class="gsc_a_at"');
	$str .= "\",\n    \"View_citation_on_scholar\": \"" . "https://scholar.google.com".str_replace('amp;', '', $parsed);
	$word   = explode( ':', $parsed );
	$str .= "\",\n    \"Fitch_details\": \"" . $absolute_url = path_url( $_SERVER )."view_citation.php?id=".security_input($_GET["user"]).":".$word[1];
	$str .= "\",\n    \"Authors\": \"" . trim($pub->find(".gs_gray", 0)->plaintext);
	$str .= "\",\n    \"Venue\": \"" .trim($pub->find(".gs_gray", 1)->plaintext);
	
	if(!is_numeric($pub->find(".gsc_a_ac", 0)->plaintext))
		$str .= "\",\n    \"Citations\": 0";
	else
		$str .= "\",\n    \"Citations\": " . $pub->find(".gsc_a_ac", 0)->plaintext;
	
	if($pub->find(".gsc_a_h", 0)->plaintext == " ")
		$str .= ",\n    \"Year\": 0";
	else
		$str .= ",\n    \"Year\": " . $pub->find(".gsc_a_h", 0)->plaintext;

	$str .= "\n  },";
	
	$count++;
}

if(isset($count) AND $count==100)
{
	
		sleep(2);
		goto start;
}

if(isset($count) AND $count==200)
{	
		sleep(2);
		goto start;
}


print substr($str, 0, -1) . "\n ]\n }";
//print '  "total_Fetch" ';

?>

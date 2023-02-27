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

if(!isset($_GET["id"]))
	exit -1;
// prepare url
$id	=	security_input($_GET['id']);
$word   = explode( ':',$id);	
$url	=	 "https://scholar.google.com/citations?view_op=view_citation&hl=en&oe=ASCII&user=".$word[0]."&citation_for_view=".$word[0].":".$word[1];

# create and load the HTML
include('simple_html_dom.php');
$html = new simple_html_dom();

$html->load_file($url);


$str 	= 	"";

foreach($html->find("#gs_bdy_ccl #gsc_vcpb") as $pub) {
	
	
	//die();
	$str .= "{ \n   \"Title\": \"" . trim($pub->find(".gsc_oci_title_link", 0)->plaintext);
	
	$parsed = get_string_between($pub, '<div class="gsc_oci_field">Authors</div><div class="gsc_oci_value">', '</div>');
	$str .= "\",\n    \"Authors\": \"" .str_replace('amp;', '', $parsed);
	
	if( $pub->find(".gsc_oci_title_link", 0)==TRUE AND $pub->find(".gsc_oci_merged_snippet", 0)==TRUE  )	{
		$variable	=	str_replace(trim($pub->find(".gsc_oci_title_link", 0)->plaintext), '', trim($pub->find(".gsc_oci_merged_snippet", 0)->plaintext));
		$str .= "\",\n    \"Scholar articles\": \"" . substr($variable, 0, strpos($variable, "&#8230"));
	}
	
	if( $pub->find(".gsc_oci_title_ggi", 0)==TRUE )	
		$str .= "\",\n    \"Source\": \"" . trim($pub->find(".gsc_oci_title_ggi", 0)->plaintext);
	else
		$str .= "\",\n    \"Source\": \"" ;	
	
	$parsed = get_string_between($pub, '<div class="gsc_oci_title_ggi"><a href="', '" data-clk');
	$str .= "\",\n    \"File\": \"" .str_replace('amp;', '', $parsed);
	
	
	
	$parsed = get_string_between($pub, '<a class="gsc_oci_title_link" href="', '" data-clk');
	$str .= "\",\n    \"Source URL\": \"" .str_replace('amp;', '', $parsed);
	
	$parsed = get_string_between($pub, '<div class="gsc_oci_field">Publication date</div><div class="gsc_oci_value">', '</div>');
	$str .= "\",\n    \"Publication date\": \"" .str_replace('amp;', '', $parsed);
	
	$parsed = get_string_between($pub, '<div class="gsc_oci_field">Journal</div><div class="gsc_oci_value">', '</div>');
	$str .= "\",\n    \"Journal\": \"" .str_replace('amp;', '', $parsed);
	
	$parsed = get_string_between($pub, '<div class="gsc_oci_field">Volume</div><div class="gsc_oci_value">', '</div>');
	$str .= "\",\n    \"Volume\": \"" .str_replace('amp;', '', $parsed);
	
	$parsed = get_string_between($pub, '<div class="gsc_oci_field">Issue</div><div class="gsc_oci_value">', '</div>');
	$str .= "\",\n    \"Issue\": \"" .str_replace('amp;', '', $parsed);
	
	$parsed = get_string_between($pub, '<div class="gsc_oci_field">Publisher</div><div class="gsc_oci_value">', '</div>');
	$str .= "\",\n    \"Publisher\": \"" .str_replace('amp;', '', $parsed);
	
	if( $pub->find(".gsh_small", 0)==TRUE )	
		$str .= "\",\n    \"Description\": \"" . trim($pub->find(".gsh_small", 0)->plaintext)."\"";
	else
		$str .= "\",\n    \"Description\": \"";
	

	$str .= "\n  ";
	
}

print substr($str, 0, -1) . "\n }";

?>

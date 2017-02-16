<?php

#ini_set(default_charset, "utf-8");
#header('Content-Type: application/json; charset=utf-8');
header('Content-Type: application/json');

function iconv($a, $b, $c) {
	return $c;
}

if(!isset($_GET["user"]))
	exit -1;

# create and load the HTML
include('simple_html_dom.php');
$html = new simple_html_dom();
$html->load_file("http://scholar.google.se/citations?user=" . $_GET["user"]);

print "{\n \"total_citations\": " . $html->find("#gsc_rsb_st td.gsc_rsb_std", 0)->plaintext . ",\n";

$s = " \"citations_per_year\": {";
$years = $html->find('#gsc_g_x .gsc_g_t');
$scores = $html->find('#gsc_g_bars .gsc_g_al');
foreach($scores as $key => $score) {
	$s .= "\n  \"" . $years[$key]->plaintext ."\": ". $score->plaintext . ",";
}
print substr($s, 0, -1) . "\n },\n";


$str = " \"publications\": [";
foreach($html->find("#gsc_a_t .gsc_a_tr") as $pub) {
	$str .= "\n  {\n    \"title\": \"" . $pub->find(".gsc_a_at", 0)->plaintext;
	$str .= "\",\n    \"authors\": \"" . $pub->find(".gs_gray", 0)->plaintext;
	$str .= "\",\n    \"venue\": \"" .$pub->find(".gs_gray", 1)->plaintext;
	if($pub->find(".gsc_a_ac", 0)->plaintext == "&nbsp;")
		$str .= "\",\n    \"citations\": 0";
	else
		$str .= "\",\n    \"citations\": " . $pub->find(".gsc_a_ac", 0)->plaintext;
	if($pub->find(".gsc_a_h", 0)->plaintext == " ")
		$str .= ",\n    \"year\": 0";
	else
		$str .= ",\n    \"year\": " . $pub->find(".gsc_a_h", 0)->plaintext;
	$str .= "\n  },";
}
print substr($str, 0, -1) . "\n ]\n}";
?>

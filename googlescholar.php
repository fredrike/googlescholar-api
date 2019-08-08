<?php

header('Content-Type: application/json; charset=utf-8');

if(!isset($_GET["user"]))
	exit -1;

# create and load the HTML
include('simple_html_dom.php');
$html = new simple_html_dom();
$html->load_file("http://scholar.google.se/citations?user=" . $_GET["user"]);

print "{\n \"total_citations\": " . $html->find("#gsc_rsb_st td.gsc_rsb_std", 0)->plaintext . ",\n";

$s = " \"citations_per_year\": { ";
$years = $html->find('.gsc_g_t');
$scores = $html->find('.gsc_g_al');
foreach($scores as $key => $score) {
	$s .= "\n  \"" . trim($years[$key]->plaintext) ."\": ". trim($score->plaintext) . ",";
}
print substr($s, 0, -1) . "\n },\n";


$str = " \"publications\": [";
foreach($html->find("#gsc_a_t .gsc_a_tr") as $pub) {
	$str .= "\n  {\n    \"title\": \"" . trim($pub->find(".gsc_a_at", 0)->plaintext);
	$str .= "\",\n    \"authors\": \"" . trim($pub->find(".gs_gray", 0)->plaintext);
	$str .= "\",\n    \"venue\": \"" .trim($pub->find(".gs_gray", 1)->plaintext);
	if(!is_numeric($pub->find(".gsc_a_ac", 0)->plaintext))
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

<?php
ini_set("allow_url_fopen", 1);
$query = $_REQUEST['query'];
$f = urldecode($query);
$f = explode(" ",$f);
$arr =array('query' => $f, "suggestions" => []);
foreach($f as $term)
{
$json = file_get_contents('http://54.245.69.202:8983/solr/mycore/suggest?indent=on&q='. $term . '&wt=json');
$obj = json_decode($json,true);
//$data = json_encode($obj);
//echo $obj;
//$arr =array('query' => $query, "suggestions" => []);
foreach($obj['suggest']['suggest'][$term]['suggestions'] as $p)
{
      array_push($arr['suggestions'], $p['term']);
}
$result = json_encode($arr);
}
echo $result;
?>


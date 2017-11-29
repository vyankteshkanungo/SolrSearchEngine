<?php
// make sure browsers see this page as utf-8 encoded HTML
include 'simple_html_dom.php';
ini_set('memory_limit', '-1');
header('Content-Type: text/html; charset=utf-8');
$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$handle = fopen("mapCNNDataFile.csv","r");
$urlFile = array();
$change=isset($_REQUEST['change'])? $_REQUEST['change'] : true;
while(($data = fgetcsv($handle, 1000, ","))!== FALSE)
{
	$urlFile[$data[0]]=$data[1];
}

fclose($handle);

$results = false;
if(isset($_GET['rank']))
   $rank=$_GET['rank'];
  else
   $rank="";
/*
if(isset($_REQUEST['q']))
{
  $query = $_REQUEST['q'];
  $suggestion = 
  $array=array();
  while($row = $fetch_array($suggestion))
  {
     $array[]=array(
        'label'
           );
  }
};
*/

if ($query)
{
//echo $query;
 // The Apache Solr Client library should be on the include path
 // which is usually most easily accomplished by placing in the
 // same directory as this script ( . or current directory is a default
 // php include path entry in the php.ini)
  
include 'SpellCorrector.php';
$arr = explode(" ",$query);
//echo $arr;
foreach($arr as $v)
{ 
  
$newquery = $newquery.SpellCorrector::correct($v)." ";
}
//$newquery = SpellCorrector::correct($query);
//if($query!=$newquery)
//{
  
//}
$oldquery = $query;
//echo $change;
if($change=="true")
{ $query = $newquery;} 
//echo $query; 
$additionalParameters=array(
  'sort'=>'pageRankFile desc'
  //'fq'=>'a filtering query',
  //'facet'=>'true',
  //'facet.field'=>array(
  //'field_1','field_2')
  );
 
 require_once('Apache/Solr/Service.php');
 // create a new solr service instance - host, port, and corename
 // path (all defaults in this example)
 $solr = new Apache_Solr_Service('localhost', 8983, '/solr/mycore/');
 // if magic quotes is enabled then stripslashes will be needed
 if (get_magic_quotes_gpc() == 1)
 {
 $query = stripslashes($query);
 }
 // in production code you'll always want to use a try /catch for any
 // possible exceptions emitted by searching (i.e. connection
 // problems or a query parsing error)
 //echo SpellCorrector::correct($query);
 try
 {
  if(isset($rank) && $rank=='Pagerank')
 $results = $solr->search($query, 0, $limit,$additionalParameters);
  else
  {
    $results = $solr->search($query, 0, $limit);
  }
 }
 catch (Exception $e)
 {
 // in production you'd probably log or email this error to an admin
 // and then show a special message to the user but for this example
 // we're going to show the full exception
 die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
 }
}
?>
<html>
<head>
 	<meta charset="utf-8"/>
 <title>PHP Solr Client Example</title>
 </head>
 <body>
 <form accept-charset="utf-8" method="get">
 <label for="q">Search:</label>
 
 <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($oldquery, ENT_QUOTES, 'utf-8');
//$i = 0;
 // while($select_query_array = SpellCorrector::correct($query))
//{
 // echo "<option value=''>".htmlspecialchars($select_query_array[0]);
//  $i++;
//}
 ?>"/>
 <input type="radio" name = "rank" <?php if(isset($rank) && $rank=="Lucene") echo "checked";?> value="Lucene"/> Lucene
 <input type="radio" name = "rank" <?php if(isset($rank) && $rank=="Pagerank") echo "checked";?> value="Pagerank"/>Pagerank
 <input type="submit"/>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
 </form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="jquery.autocomplete.js"></script>
<script>
$('#q').autocomplete({
	serviceUrl:encodeURIComponent('service.php')
  //      onSelect:function(suggestion,ui){
       
      //var query_word = ui.item.suggestion; 
       //window.location.href='/search.php?q='.query_word;
//}
});

function doSearch(term) {
    window.location.href = '/search.php?q=' + term;
}

</script>

<?php
// display results

if ($results)
{
 $total = (int) $results->response->numFound;
 $start = min(1, $total);
 $end = min($limit, $total);
?> 
<?php 
//include 'SpellCorrector.php';

//$newquery2 =  SpellCorrector::correct($query); 
//echo $newquery2
//echo $query;
//echo $oldquery;
if($query!=$oldquery)
{

?>
 <div> Did you mean : 
<b>
<?php 
  echo $query;
?>
</b>
<br>
    Search instead for :
<?php
  //echo $query;
  
   ?>
</div>
  <a href="search.php?q=<?php echo $oldquery ?>&rank=<?php echo $rank ?>&change=false"><?php echo $oldquery ?></a>
<br>
<?php
}
?> 

 <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
 <ol>
<?php
 // iterate result documents
 foreach ($results->response->docs as $doc)
 {
?>
 <li>
 <table style="border: 1px solid black; text-align: left">
<?php
 // iterate document fields / values

 $url="";
 foreach ($doc as $field => $value)
 {
    if($field=='id')
     {
        $temp = explode("/",$value);
	$url = array_pop($temp);
       //if($field=='title' || $field=='og_url')
        //{
?>
<tr>
 <th><?php echo htmlspecialchars($field, ENT_NOQUOTES, 'utf-8'); ?></th>
 <td>
     <?php echo htmlspecialchars($value, ENT_NOQUOTES, 'utf-8'); ?>
    <?php
      $filepath = $value; 
 // $textValues = html2text($value,true true); ?>
</td>
 </tr>
<?php
 // include 'html2text.php';
 // $textValues = html2text($value,true true);
 // $pos = strpos($textValues, $query);
 // echo $pos;
}
 else if($field == "title")
{
?>
 <tr>
 <th><?php echo htmlspecialchars($field, ENT_NOQUOTES, 'utf-8'); ?></th>
 <td>
<a href="<?php echo $urlFile[$url] ?>" target="_blank"/>
<?php
    echo htmlspecialchars($value, ENT_NOQUOTES, 'utf-8');
?>
</td>
 </tr>

<?php
} 
else if($field == "og_url") {

?>
   <tr>
 <th><?php echo htmlspecialchars($field, ENT_NOQUOTES, 'utf-8'); ?></th>
 <td>
     <a href="<?php echo htmlspecialchars($value, ENT_NOQUOTES, 'utf-8');?>" target="_blank"/>
<?php
    echo htmlspecialchars($value, ENT_NOQUOTES, 'utf-8');
?>
</td>
 </tr>
<?php
}

else if($field=="description")
{
?>
<tr>
 <th><?php echo htmlspecialchars($field, ENT_NOQUOTES, 'utf-8'); ?></th>
 <td>
     <?php echo htmlspecialchars($value, ENT_NOQUOTES, 'utf-8'); ?>
</td>
 </tr>
<?php
 }
  } 
  $textValues =false;
  $textValues = file_get_html($filepath);
  if($textValues) 
  {
// $parsed = strip_tags($textValues);
  $parsed = $textValues->plaintext;
 // echo $text;
	
//($textValues->find('body',0)->innertext);
  $lines = explode('.',$parsed); 
  $len = strlen($query);
?><tr>
<th>Text Snippet</th>
<td>
<?php
 $found =false;  
foreach($lines as $line)
 {
       if($found==false && stripos($line,$query)!==false)
         {  $line = preg_replace('/[^\w]+/', ' ', $line);
	/*	
        $pos = strpos($line,$query);
        // $line = substr($line,$pos+1);
	// $pos = strpos($line,$query);
	if($pos==true)
	{   
                if(strlen($line)>156)
                 {
                      if($pos>75){  
                        $line =substr($line,$p0s-75,156);
                        $pos=$pos-75;  
			}
			else
			{
			   $line=substr($line,0,156);
			}
			//$posr=strpos($line,$query);
			//echo $pos;
			$line_length= strlen($line);
                       // $line = substr($line,0,$posr)."<b>".substr($line,$posr,strlen($query))."</b>".substr($line,$posr+strlen($query));
                 }
		//$output.=$line;
		break;

	}
 */
 

?>

 <?php 
  $words = explode(" ",$line);
  $i=0;
  foreach($words as $text)
  {
    //$found =true;
    $i = $i+1;
    if(!strcasecmp($text, $query))
    {
       echo '<b>'.$text.'</b>&nbsp;';
       $found = true;
    }
    else
    {
      echo $text.'&nbsp;';
    }
    if($i == 20)
    {
      echo '</br>';
      $i = 0;
    }
  }
  if($found==true) break;
}
}
}
//echo "...".substr($line,0,$pos);?>
<b><?php //echo substr($line,$pos,strlen($query));?></b><?php //echo $query;?>
<?php// echo substr($line,$pos+strlen($query))."..."; echo $pos; ?>
</td>
</tr>
<?php

//include 'html2text.php';
 // $textValues = html2text($filepath,true true);
 // echo $filepath; 
 //$pos = strpos($textValues, $query);
  //echo $pos;
?>
 </table>
 </li>
<?php
}
?>
 </ol>
<?php
}
?>
 </body>
</html>

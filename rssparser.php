<!DOCTYPE html>
<html>
<head>
	<title>RSS Feed parsing</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,600' rel='stylesheet' type='text/css'>
	<style type="text/css">
	h1 {position:absolute;top:20%;left:40%;color:#444;font-size:28px;}
	.titel {font-size:12px;font-weight:600;font-family:'Sparkasse Rg';letter-spacing:0.03em;}
	.titel a {color:#666;text-decoration:none;}
	.titel a:hover {color:#ff0000;text-decoration:underline;}
	.container {width:350px;line-height:20px;border:2px solid #eaeaea;padding:0 10px;border-radius:5px;position:absolute;top:30%;left:40%;}
	</style>
</head>

<body>
<?php
// xml parsen und array push
function xml_parser($page,$container,$tags,$number,$cdata) {
  if (!$number) {$number=100;}
  $stories=0;
  $xml=file_get_contents($page); 
  preg_match_all("/<$container>.+<\/$container>/sU",$xml, $items);
  $items=$items[0];
  $itemsArray=array();
   foreach ($items as $item) {
    for($i=0; $i<count($tags); $i++) {
    preg_match("/<$tags[$i](.+)(<\/$tags[$i]>)/sU", $item, $tag);
    $this[$i]=preg_replace("/<$tags[$i]>(.+)(<\/$tags[$i]>)/sU",'$1',$tag);
    $this[$i]=array_map('html_entity_decode', $this[$i]);
    }
     if (count($itemsArray)<$number) {array_push($itemsArray, $this);}
   }
  $theData="<dl>";
  foreach ($itemsArray as $item) {
  for($i=0; $i<count($tags); $i++) {
  $data[$i]=$item[$i][0];}
   $title=$data[0];
   // filter unnecessary stuff
   $dpatterns[0]="/<img(.+)><\/img>/sU"; $dreplacements[0]='';
   $dpatterns[1]="/<img(.+)\/>/sU"; $dreplacements[1]='';
   $dpatterns[2]="/<(\/|)content?(.+|)>/sU"; $dreplacements[2]='';
   $dpatterns[3]="/border=\"0\"/sU"; $dreplacements[3]='';
   $dpatterns[4]="/<br(.+)\/>/sU"; $dreplacements[4]='';
   if ($cdata!='hide') {
    $dpatterns[5]="/<\!\[CDATA\[(.+)\]\]>/sU"; $dreplacements[5]='$1';
   }
   else {
    $dpatterns[5]="/<\!\[CDATA\[(.+)\]\]>/sU"; $dreplacements[5]='';
   }
   $description=preg_replace($dpatterns,$dreplacements,$data[1]);
   $link=preg_replace("/<link.+href=\"(.+)\"(.+|)\/>/sU",'$1',$data[2]);
   $date=$data[3];
   $theData.="
   <dt class=\"titel\"><a href=\"$link\" target=\"_blank\">$title</a></dt>\r";
  }
$theData.="</dl>";
return $theData;
}

// ticker setup - insert the URL to the RSS Feed of your choice here
$container='item';
$tags=array('title','description','link','pubDate');
$feed=xml_parser("http://www.rosenheim24.de/rosenheim/lk-rosenheim/rssfeed.rdf",$container,$tags,9,'');
?>

<h1>News from Rosenheim / Bavaria</h1>
<div class="container">

<?php
//  utf-8 FTW!
echo iconv('ISO-8859-1', 'utf-8', $feed);?>
</div>

</body>
</html>
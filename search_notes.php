<?php
error_reporting(0); ini_set("display_errors", 0);
$source = 'events.xml';
$sitemap = new SimpleXMLElement($source,null,true);

if(isset($_POST['search_text']) && $_POST['search_text'] != '')
{
	$search_text = strtolower($_POST['search_text']);
	$xml = simplexml_load_file($source, NULL, LIBXML_NOCDATA);
	$results = $xml->xpath("/root/notes[(desc)[contains(translate(text(), 'ABCDEFGHJIKLMNOPQRSTUVWXYZ', 'abcdefghjiklmnopqrstuvwxyz'), '".$search_text."')]]");
	
	if(is_array($results) && count($results))
	{
		foreach($results as $r)
		{
			echo date('Y-m-d', intval($r->timestamp)).'<br>'.$r->desc.'<br><br>';
		}
	}
}
?>
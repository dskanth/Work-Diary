<?php
error_reporting(0); ini_set("display_errors", 0);
$source = 'events.xml';
$sitemap = new SimpleXMLElement($source,null,true);

if(isset($_POST['day']) && $_POST['day'] > 0 && isset($_POST['month']) && $_POST['month'] > 0 && isset($_POST['year']) && $_POST['year'] > 0)
{
	$id = strtotime($_POST['year']."-".$_POST['month']."-".$_POST['day']);	
	$data = $sitemap->xpath('/root/notes[timestamp='.$id.']');
	
	if($data)
	{
		foreach ($data as $output) {		
		$response =  $output->desc;
		}	
		echo $response;
	}
}
?>
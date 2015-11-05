<?php
error_reporting(0); ini_set("display_errors", 0);
libxml_use_internal_errors(true);

function simplexml_insert_after(SimpleXMLElement $insert, SimpleXMLElement $target) {
		$target_dom = dom_import_simplexml($target);
		$insert_dom = $target_dom->ownerDocument->importNode(dom_import_simplexml($insert), true);
		if ($target_dom->nextSibling) {
			return $target_dom->parentNode->insertBefore($insert_dom, $target_dom->nextSibling);
		} else {
			return $target_dom->parentNode->appendChild($insert_dom);
		}
}

function put_xml($source, $date, $notes)
{
		$sx = simplexml_load_file($source);
		
		$xml_element = "\n\t\t\t";
		$xml_element .= "<notes>";
		$xml_element .= "\n\t\t\t";
		$xml_element .= "<timestamp>".strtotime($date)."</timestamp>";
		$xml_element .= "\n\t\t\t";
		$xml_element .= "<desc><![CDATA[".$notes."]]></desc>";
		$xml_element .= "\n\t\t";
		$xml_element .= "</notes>";
		$xml_element .= "\n\t\t\t";
		
		$insert = new SimpleXMLElement($xml_element);
		$target = current($sx->xpath("//notes[last()]"));
		simplexml_insert_after($insert, $target);
		$added = file_put_contents($source, $sx->asXML());
	
		if($added) {
			return 'success';
		}
		else {
			return '';
		}			
}

//print_r($_POST); exit;

if(isset($_POST['day']) && $_POST['day'] != '' && isset($_POST['month']) && $_POST['month'] != '' && isset($_POST['year']) && $_POST['year'] != '' 
&& isset($_POST['notes']) && $_POST['notes'] != '') {
	
	$source = 'events.xml';
	$date = date($_POST['year']."-".$_POST['month']."-".$_POST['day']);
	$sitemap = new SimpleXMLElement($source,null,true);
	$id = strtotime($date);
	$data = $sitemap->xpath('/root/notes[timestamp='.$id.']');	
	
	if(count($data) == 0)
	{	
		$add = put_xml($source, $date, $_POST['notes']);
		echo $add;
	}
	else
	{
		$sx = simplexml_load_file($source);

		foreach ($sx->xpath("/root/notes[timestamp='".$id."']") as $desc) {
			$dom=dom_import_simplexml($desc);
			$dom->parentNode->removeChild($dom);
			file_put_contents($source, $sx->asXML());
		}
		
		$add = put_xml($source, $date, $_POST['notes']);
		echo $add;
	}
}
else {
	echo '';
}
?>
<?php
error_reporting(0); ini_set("display_errors", 0);
if(isset($_POST['month']) && $_POST['month'] > 0 && isset($_POST['year']) && $_POST['year'] > 0)
{
$curr_month = $_POST['month'];
$curr_year = $_POST['year'];
$prev_month = $curr_month-1 < 1?12:$curr_month-1; 
$next_month = $curr_month+1 > 12?1:$curr_month+1;
$prev_year = $curr_year-1 > 1901?$curr_year-1:1902;
$next_year = $curr_year+1 < 2038?$curr_year+1:date("Y");
$lastday = date('t',strtotime($curr_month.'/1/'.$curr_year));
$notes_exist = array();

$id = strtotime(date($curr_year."-".$curr_month."-1"));
$week_start_day = date( "w", $id);
$wdays = 7;
$wdays-=$week_start_day;

$source = 'events.xml';
$sitemap = new SimpleXMLElement($source,null,true);

for($i=1; $i <= $lastday; $i++)
{
$id = strtotime(date($curr_year."-".$curr_month."-".$i));
$data = $sitemap->xpath('/root/notes[timestamp='.$id.']');
	if(count($data)) {
		$notes_exist[] = $i;
	}
}

$cal = '<table class="cal_nav" border="0"><tr>
<td><a href="javascript:void(0);" title="previous year" onClick="get_calendar('.$curr_month.', '.$prev_year.')"> << Year </a>&nbsp;&nbsp;&nbsp;
<a href="javascript:void(0);" title="previous month" onClick="get_calendar('.$prev_month.', '.$curr_year.')"> << Month </a>&nbsp;&nbsp;
<a href="javascript:void(0);" title="next month" onClick="get_calendar('.$next_month.', '.$curr_year.')"> Month >> </a>&nbsp;&nbsp;&nbsp;
<a href="javascript:void(0);" title="next year" onClick="get_calendar('.$curr_month.', '.$next_year.')"> Year >> </a>
</td></tr></table>';

$cal .= '<table class="week_days" width="300" border="0"><tr><td>Su</td><td>Mo</td><td>Tu</td><td>We</td><td>Th</td><td>Fr</td><td>Sa</td></tr></table>';

$cal .= '<table class="calendar" width="300" border="0"><tr>';

for($j=0; $j < $week_start_day; $j++)
{
	$cal .= '<td>&nbsp;</td>';	
}

$c=0;
for($i=1; $i <= $wdays; $i++)
{		
	if(!in_array($i, $notes_exist)) {
		if($week_start_day==$c && $i == 1) {
			$bg_color = ' style="background-color:#FF9898;"'; 
		}else{
			$bg_color = ' style="background-color:#f5f5f5;"'; 
		}
	} 
	else {
		$bg_color = ' style="background-color:#8AC007;"';
	}	
	
	$cal .= '<td><span id="day_'.$i.'"'.$bg_color.' onClick="get_notes('.$i.','.$curr_month.','.$curr_year.');">'.$i.'</span></td>';
	++$c;
	if($i % $wdays == 0) $cal .= '</tr><tr>';
}

$cc=0;

for($i=$wdays+1; $i <= $lastday; $i++)
{ 
	++$cc;
	if(!in_array($i, $notes_exist)) {
		if(($cc-1)%7 ==0) {
			$bg_color = ' style="background-color:#FF9898;"'; 
		}else{
			$bg_color = ' style="background-color:#f5f5f5;"';
		} 
	} 
	else {
		$bg_color = ' style="background-color:#8AC007;"';
	}
			
	$cal .= '<td><span id="day_'.$i.'"'.$bg_color.' onClick="get_notes('.$i.','.$curr_month.','.$curr_year.');">'.$i.'</span></td>';
	
	if($cc % 7 == 0) $cal .= '</tr><tr>';
}
$cal .= '</tr></table>';
echo $cal;
}
?>
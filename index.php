<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="expires" content="0"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<title>Work Diary</title>
	<link rel="stylesheet" href="css/style.css">
	<link href="css/jquery.alerts.css" rel="stylesheet" type="text/css">
	<!--<link type="text/css" rel="stylesheet" href="css/jquery-te-1.3.3.css" charset="utf-8" />-->
	<link rel="stylesheet" href="css/ckeditor.css">
</head>
<body>
<?php 
error_reporting(0); ini_set("display_errors", 0);
require_once 'config.php';

if ($_SERVER['PHP_AUTH_USER'] != $username || $_SERVER['PHP_AUTH_PW'] != $password) {
    header("WWW-Authenticate: Basic realm=\"Work Diary\"");
    header("HTTP/1.0 401 Unauthorized");
    echo 'This is what happens if you press cancel'; exit;
}

$date = getdate();
$curr_month = $date['mon'];
$curr_year = $date['year'];
$prev_month = $curr_month-1 < 1?12:$curr_month-1; 
$next_month = $curr_month+1 > 12?1:$curr_month+1;
$prev_year = $curr_year-1;
$next_year = $curr_year+1;
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
?>
<div id="main">
<div id="main_div">Work Diary</div>
<div id="main_div2">
<select id="month_selection" name="month_selection" onChange="load_calendar();">
<option value=""> month </option>
<?php
$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
foreach($months as $k=>$v)
{
	if($curr_month == ($k+1)) $selected = 'selected'; else $selected = '';
	echo '<option value="'.($k+1).'" '.$selected.'>'.$v.'</option>';
}
?>
</select>
<select id="year_selection" name="year_selection" onChange="load_calendar();">
<option value=""> year </option>
<?php
for($yr = 1902; $yr <= 2037; $yr++)
{
	if($curr_year == $yr) $selected = 'selected'; else $selected = '';
	echo '<option value="'.$yr.'" '.$selected.'>'.$yr.'</option>';
}
?>
</select>
</div>
<?php 
echo '<div id="date_selected"><div id="date_text"><b>'.date("M-Y").'</b></div>
<div id="loading_icon"><img src="load.gif" border="0" /></div>
<div id="search_box">
<input type="text" name="search_text" id="search_text" size="42" onKeyPress="return validate_enter_key(event)" />
&nbsp;<img src="search-icon.png" border="0" onClick="toggle_search();" title="Search" />
</div>
</div>'; 
?>
<div id="calendar_div">
<?php
echo '<table class="cal_nav" border="0"><tr>
<td colspan="7">
<a href="javascript:void(0);" title="previous year" onClick="get_calendar('.$curr_month.', '.$prev_year.')"> << Year </a>&nbsp;&nbsp;&nbsp;
<a href="javascript:void(0);" title="previous month" onClick="get_calendar('.$prev_month.', '.$curr_year.')"> << Month </a>&nbsp;&nbsp;
<a href="javascript:void(0);" title="next month" onClick="get_calendar('.$next_month.', '.$curr_year.')"> Month >> </a>&nbsp;&nbsp;&nbsp;
<a href="javascript:void(0);" title="next year" onClick="get_calendar('.$curr_month.', '.$next_year.')">  Year >> </a>
</td></tr>
</table>';

echo '<table class="week_days" width="300" border="0"><tr><td>Su</td><td>Mo</td><td>Tu</td><td>We</td><td>Th</td><td>Fr</td><td>Sa</td></tr></table>';

echo '<table class="calendar" width="300" border="0"><tr>';

for($j=0; $j < $week_start_day; $j++)
{
	echo '<td>&nbsp;</td>';	
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
	
	echo '<td><span id="day_'.$i.'"'.$bg_color.' onClick="get_notes('.$i.','.$curr_month.','.$curr_year.');">'.$i.'</span></td>';
	++$c;
	if($i % $wdays == 0) echo '</tr><tr>';
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
			
	echo '<td><span id="day_'.$i.'"'.$bg_color.' onClick="get_notes('.$i.','.$curr_month.','.$curr_year.');">'.$i.'</span></td>';
	
	if($cc % 7 == 0) echo '</tr><tr>';
}
echo '</tr></table>';
?>
</div>

<div id="notes_main">
<div id="notes_edit_div"></div>
<div id="notes_div"></div>
</div>

<div id="email_section">
<label><b>Send Work Report as Email: </b></label><br><br>
<input type="text" id="email_address" name="email_address" value="<?php echo $default_to_email; ?>" placeholder="Enter email">
&nbsp;&nbsp;<input type="button" id="send_email" name="send_email" value="Send Email" onClick="send_data_email()">
<br><p id="loading_send_email"></p>
</div>

</div>

<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery.alerts.js"></script>	
<script src="js/logger.js"></script>
<!--<script type="text/javascript" src="js/jquery-te-1.3.3.min.js" charset="utf-8"></script>-->
<script src="js/ckeditor.js"></script>
<script>
function send_data_email() {
	var to_email = $.trim($("#email_address").val());
	if(to_email == '') {
		alert("Please enter an email");
		$("#email_address").focus();
	}
	else {
	$.ajax({
			  url: 'mail.php',
			  type: 'POST',
			  data: { send_email: 1, send_to_email: to_email },
			  beforeSend: function() {
				  $("#loading_send_email").html('<img src="css/images/loading.gif" border="0">');
				  $("#send_email").prop('disabled', true);
			  },
			  complete: function() {
				  $("#send_email").prop('disabled', false);
			  },
			  success: function(data) {
				  //console.log(data);
				  
				  if(data == 1) {
					  $("#loading_send_email").css("color", "green");
					  $("#loading_send_email").html('Mail sent!');
				  }
				  else {
					  $("#loading_send_email").css("color", "red");
					  $("#loading_send_email").html('Oops, an error occurred. Try later.');
				  }
			  },
			  timeout: 30000, // 30 seconds
			  error: ajaxErrorHandler // handle error
	});
	}
}

function ajaxErrorHandler(request, type, errorThrown) {
		var message = "Oops…\n"; 
		switch (type) {
		case 'timeout':
		message += "The request timed out.";
		break;
		case 'notmodified':
		message += "The request was not modified but was not retrieved from the cache.";
		break;
		case 'parsererror':
		message += "XML/Json format is bad.";
		break;
		default:
		message += "HTTP Error (" + request.status + " " + request.statusText + ").";
		}
		message += "\n";
		//alert(message);
		$("#loading_send_email").html(message);
}
</script>
</body>
</html>
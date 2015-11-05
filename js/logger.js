var existing_notes = '';
var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "July", "Aug", "Sep", "Oct", "Nov", "Dec"];
selected_month = '';
selected_year = '';

$(document).keyup(function(e) {
  if (e.keyCode == 27) { 
  	close_add_form();
	$('#search_text').hide('slow');
  }  
});

	$(function() {
		$.ajaxSetup({ cache: false });
	});
	
	function get_notes(day, month, year)
	{
		$.ajax({
					type: "POST",
					url: "get_notes.php",
					cache: false,
					data: {day: day, month: month, year: year },
					beforeSend: function() {
					   $("#loading_icon").css("display", "block");
				    },
				    complete: function() {
					   $("#loading_icon").css("display", "none");
				    },
					timeout: 10000,
		  		    error: ajaxError,
					success: function(data) {						
					var month_name = monthNames[month-1];
								
						if(data != '') {	
							$("#notes_div").html(data);
							$("#notes_edit_div").html('<input type="button" id="edit_btn" name="edit" value="Edit" onclick="edit_notes('+day+','+month+','+year+')" /><span id="selected_date" style="display: inline; margin-left: 60px; font-weight: bold;">'+day+'-'+month_name+'-'+year+'</span>');
						}
						else {
							$("#notes_edit_div").html('<input type="button" id="add_btn" name="add" value="Add" onclick="add_notes('+day+','+month+','+year+')" /><span id="selected_date" style="display: inline; margin-left: 60px; font-weight: bold;">'+day+'-'+month_name+'-'+year+'</span>');
							add_notes(day, month, year);
						}
					}
				});
	}
	
	function ajaxError(request, type, errorThrown)
	{
		var message = "Oops...\n";
		
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
		alert(message);
	}
	
	function get_calendar(month, year)
	{
		$("#notes_div").html('');
		$("#notes_edit_div").html('');
		selected_month = month;
		selected_year = year;
		
		$("#month_selection").val(month);
		$("#year_selection").val(year);

		$.ajax({
					type: "POST",
					url: "get_calendar.php",
					cache: false,
					data: { month: month, year: year },
					beforeSend: function() {
					   $("#loading_icon").css("display", "block");
				    },
				    complete: function() {
					   $("#loading_icon").css("display", "none");
				    },
					timeout: 10000,
		  		    error: ajaxError,
					success: function(data) {						
						if(data != '') {
							$("#calendar_div").html(data);							
							var month_name = monthNames[month-1];
							$("#date_text").html('<b>'+month_name+'-'+year+'</b>');
						}
						else {
							$("#calendar_div").html('An error occurred. Please reload the page and try again.');
						}
					}
				});	
	}
	
	function add_notes(day, month, year)
	{
		$('input#add_btn').attr("disabled", true);
		existing_notes = '';
		$("#notes_div").html('<form name="add_notes" id="add_notes" method="post"><textarea name="notes" id="notes" class="ckeditor" rows="3" cols="25"></textarea><table><tr><td><input type="button" name="submit" id="submit" value="Save" onclick="javascript:validate_add_notes('+day+','+month+','+year+');"  /></td><td><input type="button" name="cancel" id="cancel" value="Cancel" onClick="javascript:close_add_form();"  /></td></tr></table></form>');		
		//$('.jqte-test').jqte();
		//$(".jqte_editor").focus();
		
		//var editor = CKEDITOR.instances['notes'];
		//if (editor) { editor.destroy(true); }
		
		CKEDITOR.config.width=350;
		CKEDITOR.config.height=200;
		CKEDITOR.replace('notes', CKEDITOR.config);
	}
	
	function edit_notes(day, month, year)
	{
		$('input#edit_btn').attr("disabled", true);
		existing_notes = $("#notes_div").html();
		$("#notes_div").html('<form name="edit_notes" id="edit_notes" method="post"><textarea name="notes" id="notes" class="ckeditor" rows="3" cols="25">'+existing_notes+'</textarea><table><tr><td><input type="button" name="submit" id="submit" value="Update" onclick="javascript:validate_add_notes('+day+','+month+','+year+');"  /></td><td><input type="button" name="cancel" id="cancel" value="Cancel" onClick="javascript:close_add_form();"  /></td></tr></table></form>');
		
		//$('.jqte-test').jqte();
		//$(".jqte_editor").focus();
		
		CKEDITOR.config.width=350;
		CKEDITOR.config.height=200;
		CKEDITOR.replace('notes', CKEDITOR.config);
	}
	
	function close_add_form()
	{
		if(existing_notes == '')
		{
			$("#notes_div").html('');
			$('input#add_btn').attr("disabled", false);
			$("input#add_btn").focus();
		}
		else
		{
			$("#notes_div").html(existing_notes);
			$('input#edit_btn').attr("disabled", false);
			$("input#edit_btn").focus();
		}		
	}
		
	function validate_add_notes(day, month, year)
	{
		var userText = $.trim($(".cke_contents iframe").contents().find("body").text());
		//userText = $('.jqte_editor').text().replace(/^\s+/, '').replace(/\s+$/, '');
		
		var ckeditor_data = CKEDITOR.instances.notes.getData();
		
		if(userText == '')
		{
			jAlert("Please enter notes");
		}
		else
		{
		$("#loading_icon").css("display", "block");		
		
		$.post("add_notes.php", { day: day, month: month, year: year, notes: ckeditor_data },
		function(data) {
			//alert(data);
			if(data == 'success')
			{
				var month_name = monthNames[month-1];
				$("#notes_div").html(ckeditor_data);
				$("#day_"+day).css("background-color", "#8AC007");
				$("#notes_edit_div").html('<input type="button" id="edit_btn" name="edit" value="Edit" onclick="edit_notes('+day+','+month+','+year+')" /><span id="selected_date" style="display: inline; margin-left: 60px; font-weight: bold;">'+day+'-'+month_name+'-'+year+'</span>');
			}
			else {
				jAlert('Could not add/edit notes, probably due to invalid format of xml file', "");
			}			
			$("#loading_icon").css("display", "none");
		});
		}
	}

	function toggle_search()
	{
		if($('#search_text').is(':visible'))
		{
			if($('#search_text').val() == '')
			$('#search_text').hide('slow');
			else
			{
				$("#loading_icon").css("display", "block");		
		
				$.post("search_notes.php", { search_text: $('#search_text').val() },
				function(data) {			
					if(data != '')
					{
						$("#notes_div").html(data);
						$("#notes_edit_div").html('');
					}
					else {
						jAlert('No search results found');
						$("#notes_div").html(data);
						$("#notes_edit_div").html('');
					}			
					$("#loading_icon").css("display", "none");
				});
			}
		}
		else
		{
			$('#search_text').show('slow');
			$('#search_text').focus();
		}
	}
	
	function validate_enter_key(e)
	{
		var keycode = e.keyCode || e.which;
		if(keycode == 13) {
			toggle_search();
		}
	}
	
	function load_calendar()
	{
		selected_month = $("#month_selection").val();
		selected_year = $("#year_selection").val();
		
		if(selected_month != '' && selected_year != '')
		{
			get_calendar(selected_month, selected_year);			
		}
				
	}
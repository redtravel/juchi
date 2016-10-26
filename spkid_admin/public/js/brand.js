function show_flag () {
	var flag_url = $('select[name=flag_id] option:selected').attr('rel');
	$('#flag_span').html('<img src="public/upload/flag/'+flag_url+'"/>');
}
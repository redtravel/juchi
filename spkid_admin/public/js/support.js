var sound=null;
var play_sound=false;
var cluetip_option={showTitle:false,arrows: true,width:'350px'};
$(function(){
	sound=$('#sound')[0];
	$('#float_log').dialog({autoOpen:false,width:300,modal:true,resizable:false,title:'备注'});
	$('div[id^=issue-]').cluetip(cluetip_option);
	var $tabs=$('#tabs').tabs({
		tabTemplate: "<li><a href='javascript:void(0)' title='#{href}'>#{label}</a></li>",
		remove:function(event,ui){
			
		},
		add: function( event, ui ) {
			$( ui.panel ).append( "<p>" + blocking_content + "</p>" );
		}
	});
	//转出
	$( ".btn_assign" ).live( "click", function() {
		var index = $( "input.btn_assign", $tabs ).index( $( this ) );
		close_issue(index,1);					
	});
	//关闭
	$( ".btn_close" ).live( "click", function() {
		var index = $( "input.btn_close", $tabs ).index( $( this ) );
		close_issue(index,0);					
	});
	//备注
	$( ".btn_log" ).live( "click", function() {
		var index = $( "input.btn_log", $tabs ).index( $( this ) );
		log_issue(index);
	});
	// 当点击开启某个会话时执行
	$( "#issue_list div.issue" ).live( "dblclick", function() {
		var issue_id=parseInt($(this).attr('id').substr(6));
		open_issue(issue_id,false);
	});

	// 发送消息 
	$('#tabs input.btn_sender').live('click',function(){
		var index=$('input.btn_sender',$tabs).index($(this));
		var container=$('div[id^=tabs-]:eq('+index+')',$tabs);
		var issue_id=parseInt(container.attr('id').substr(5));
		var message=$(':input[name=message_box]',container).val();
		$.ajax({
			url:'support/post_message',
			data:{issue_id:issue_id,message:message,rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				if (result.msg) {alert(result.msg)};
				if (result.err) {return false;};
				if (result.message) {
					var html=create_message_html(result.message);
					$('div.message_list',container).html($('div.message_list',container).html()+html).animate({scrollTop:99999});
					$(':input[name=message_box]',container).val('');
					var title=$('a[title=tabs-'+issue_id+']');	
					if(title.html().substr(0,5)=='【新消息】'){
						title.html(title.html().substr(5));
					}
				};
				
			}
		});
	});
	$('#tabs .message_box').live('keypress',function(e){
		if(e.keyCode==13){
			$(this).parents('table').find('input.btn_sender').click();
		}
	});
	$('#tabs span.btn_history').live('click',function(){
		var index=$('span.btn_history',$tabs).index($(this));
		var container=$('div[id^=tabs-]:eq('+index+')',$tabs);
		var issue_id=parseInt(container.attr('id').substr(5));
		load_history(issue_id,-1);
	});
	setInterval('refresh()',8000);
	
});

function open_issue (issue_id,quiet) {
	if (blocking) {
		if(!quiet) alert('正在处理前一个请求，请稍候...');
		return false;
	}
	blocking=issue_id;
		
	$.ajax({
		url:'support/open_issue',
		data:{issue_id:issue_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) {
				blocking=0;blocking_content='';
				return false;
			}
			blocking_content=result.html;
			var new_tag=result.has_new_msg?'【新消息】':'';
			var off_line=result.issue.user_close==1?'【离线】':'';
			$('#tabs').tabs('add','tabs-'+issue_id,new_tag+(result.issue.user_name?('用户-'+result.issue.user_name):('访客-'+issue_id))+off_line);
			blocking=0;blocking_content='';
			$( "#issue_list #issue-"+issue_id ).remove();
			$('#cluetip').hide();
		}
	});
	return true;
}

function refresh () {
	$.ajax({
		url:'support/refresh',
		data:{last_message_id:last_message_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			//取到列表和消息记录
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			if(result.last_message_id) last_message_id=result.last_message_id;
			if(result.issue_list) refresh_issue_list(result.issue_list);
			if(result.history) refresh_history(result.history);
			if(play_sound){
				play_sound=false;
				sound.play();
			}
		}
	});
}

function refresh_issue_list (issue_list) {
	//检查旧的
	$('#issue_list .issue').each(function(){
		var e=$(this);
		var issue_id=parseInt(e.attr('id').substr(6));
		if(issue_list[issue_id]==undefined) {
			e.remove();
			return;
		}
		var issue=issue_list[issue_id];
		if(issue.status!=0){
			e.remove();
			issue_list[issue_id]=undefined;
			return;
		}
		if(issue.user_name){
			$(e).html('用户-'+issue.user_name+'【'+(issue.admin_name==null?'':issue.admin_name)+'】');
		}else{
			$(e).html('访客-'+issue.rec_id+'【'+(issue.admin_name==null?'':issue.admin_name)+'】');
		}
		issue_list[issue_id]=undefined;
	});
	//增加新出现的
	for(i in issue_list){		
		var issue=issue_list[i];
		if(issue==undefined||issue.status!=0) continue;
		if(issue.rec_id==blocking) continue;//正在处理接听中的对话
		var html='<div class="issue" id="issue-'+issue.rec_id+'" rel="support/preview/'+issue.rec_id+'">';
		if(issue.user_name){
			html+='用户-'+issue.user_name;
		}else{
			html+='访客-'+issue.rec_id;
		}	
		html+='【'+(issue.admin_name==null?'':issue.admin_name)+'】'
		html+='</div>';
		$('#issue_list').append(html);
		$('div#issue-'+issue.rec_id).cluetip(cluetip_option);
		play_sound=true;
	}

	//关闭已关闭的
	var $tabs=$('#tabs');
	var issue_ids=new Array();
	$('div[id^=tabs-]',$tabs).each(function(){
		issue_ids.push(parseInt($(this).attr('id').substr(5)));
	});
	for (var i in issue_ids) {
		var issue_id=issue_ids[i];
		var issue=issue_list[issue_id];
		if(issue!=undefined&&issue.status==1) {
			//更改用户离线状态
			var title=$('a[title=tabs-'+issue_id+']');
			var title_str=$.trim(title.html());
			if(issue.user_close==1){
				if(title_str.substr(title_str.length-4)!='【离线】') title.html(title_str+'【离线】');
			}else{
				if(title_str.substr(title_str.length-4)=='【离线】') title.html(title_str.substr(0,title_str.length-4));
			}
			continue;
		}
		var index=$('div[id^=tabs-]',$tabs).index($('div[id=tabs-'+issue_id+']'));
		$tabs.tabs('remove',index);
	};
	

	//打开未打开的
	for(i in issue_list){		
		var issue=issue_list[i];
		if(issue==undefined||issue.status!=1) continue;
		if($('div[id=tabs-'+i+']').length>0) continue;
		open_issue(i,true);
	}
}

function refresh_history (history) {
	for(i in history){
		if($('#tabs-'+i).length<1) continue;
		var container=$('#tabs-'+i+' div.message_list');
		var html='';
		for(j in history[i]){
			var m=history[i][j];
			if($('.message_item[rel='+m.message_id+']').length>0) continue;
			html+=create_message_html(m);
		}
		if(html) {
			container.html(container.html()+html);
			var title=$('a[title=tabs-'+i+']');			
			if(title.html().substr(0,5)!='【新消息】'){
				title.html('【新消息】'+title.html());
			}
			container.animate({scrollTop:999999});
			play_sound=true;
		}
	}
}

function load_history (issue_id,page) {
	$.ajax({
		url:'support/load_history',
		data:{issue_id:issue_id,page:page,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			//取到列表和消息记录
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			if(result.html){
				$('#tabs div#tabs-'+issue_id+' div.history').html(result.html);
			}
			if(result.page){
				$('#tabs div#tabs-'+issue_id+' div.page').html(result.page);
				$('#tabs div#tabs-'+issue_id+' span.btn_history').hide();
			}
		}
	});
}


function create_message_html(message){
	html='';
	var html='<div class="message_item '+(message.qora==1?'a':'q')+'" rel="'+message.message_id+'">';
	html+='<span class="man">'+(message.admin_name?message.admin_name:(message.user_name?message.user_name:'访客'))+'</span>';
	html+=' <span class="time">['+message.create_date.substr(11)+']</span>： '+message.content+'</div>';
	
	return html;
}


/**
 * Close Issue
 */
function close_issue(index,save) {
	var $tabs=$('#tabs');
    var container=$('div[id^=tabs-]:eq('+index+')',$tabs);
	var issue_id=parseInt(container.attr('id').substr(5));
	if(!confirm('确定操作？')) return false;
	$.ajax({
		url:'support/close_issue',
		data:{issue_id:issue_id,save:save,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg)};
			if (result.err) {return false;};
			$tabs.tabs( "remove", index );
		}
	});
}

/**
 * 发送备注
 */
function log_issue(index) {	
	if($('#float_log').dialog('isOpen')){
		var issue_id = $(':hidden[name=log_issue_id]').val();
		var content=$.trim($(':input[name=log_box]').val());
		if(content==''){
			alert('请填写内容');
			return false;
		}
		$.ajax({
			url:'support/post_log',
			data:{issue_id:issue_id,content:content,rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				if(result.msg) alert(result.msg);
				if(result.err) return false;
				var container = $('div#tabs-'+issue_id);
				$('div.log',container).html(result.html);
				$(':input[name=log_box]').val('');
				$('#float_log').dialog('close')
			}
		});
		
	}else{
		var $tabs=$('#tabs');
		var container=$('div[id^=tabs-]:eq('+index+')',$tabs);
		var issue_id=parseInt(container.attr('id').substr(5));
		$(':hidden[name=log_issue_id]').val(issue_id);
		$('#float_log').dialog('open')
	}
	
}

function close_log(log_id){
	if(!confirm('确定关闭该备注?')) return false;
	$.ajax({
		url:'support/close_log',
		data:{log_id:log_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			var container = $('div#tabs-'+result.issue_id);
			$('div.log',container).html(result.html);
		}
	});
}
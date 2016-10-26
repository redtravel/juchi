/**
 * @param obj 事件源
 * @param id 主键
 * @confirm 是否需要确认提示
 */
function toggle(obj,url,id,field,confirmMsg){
    //确认信息不为空
    if(confirmMsg != ''){
        //点击否返回
         if(!confirm(confirmMsg)){
             return;
         }
    }
    var clazz = $.trim($(obj).attr('class'));
    var value = 1;
    if(clazz == 'yesForGif'){
        value = 0;
    }
    $.ajax({
		url : url,
		data : {id : id , value : value},
		dataType : 'json',
		type : 'POST',
		success : function(result){
            if (result.msg) {alert(result.msg)};
            if (result.err == 0) {
                if(clazz == 'yesForGif'){
                    $(obj).removeClass('yesForGif').addClass('noForGif');
                }else{
                    $(obj).removeClass('noForGif').addClass('yesForGif');
                }
            };
		}
	});
}

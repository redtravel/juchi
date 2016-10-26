/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 仓库管理
 */

//根据供应商获取批次
function get_purchase_batch( provider_name , batch_name ,msg ,is_use ){
    var select_provider = ( provider_name != null && provider_name != '') ? provider_name: 'purchase_provider' ;
    var select_batch = ( batch_name != null && batch_name != '') ? batch_name : 'purchase_batch' ;
    is_use = (is_use != null && is_use == "1" )? 1:0;//0;默认全部,1:供应商下可用批次

    var provider_id = $("[name='"+select_provider+"']").val();
    var batch_old = '<select name=\"'+select_batch+'\"><option value=0>请选择</optin><\/select>';
    if ( provider_id > 0 ){
        $.ajax({
            type: "POST",
            url: "purchase/get_purchase_batch",
            data: {
                provider_id:provider_id,is_use:is_use,batch_name:select_batch
            },
            dataType: "json",
            success: function(result ){
                var blank = '<select name=\"'+select_batch+'\">\n<\/select>';
                if(result == null || result == blank )
                {
                    if( msg != null &&　msg != "" ){
                        alert(msg);
                    }
                    result = batch_old;
                }
                $("[name='"+select_batch+"']").replaceWith(result );
            }
        });
    }else {
        $("[name='"+select_batch+"']").replaceWith(batch_old );
    }
}
//根据供应商获取其可用批次
function get_purchase_batch_use( msg ){
    get_purchase_batch( '' , '' ,msg ,1 );
}

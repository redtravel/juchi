//添加地址
$$('.form-ad-json').on('click', function(){
    var formData = JSON.stringify(myApp.formToJSON('#my-form'));
    $$.ajax({url:'/address/address_check',async:false,dataType:'json',data:{formdata:formData},success:function(data){
        if(data.mobile_check_err == 1){           
            location.href = data.v_url_add +'?v=' + Math.random();
        }else{
            myApp.alert(null, data.mobile_check_err);
        }
    }})
});


//更新地址
$$('.form-to-json').on('click', function(){
    var formData = JSON.stringify(myApp.formToJSON('#my-form'));
    $$.ajax({url:'/address/address_check',async:false,dataType:'json',data:{formdata:formData},success:function(data){
        if(data.mobile_check_err == 1){
            location.href = '/address/index?v=' + Math.random();
        }else{
            myApp.alert(null, data.mobile_check_err);
        }
    }})
});

//设置默认
function address_setdefault(address_id){
    $$.ajax({url:'/address/address_default',async:false,dataType:'json',data:{address_id:address_id},success:function(data){   
        if (0 == data.error){
            location.href = '/address/index?v=' + Math.random();
        }
    }});
}

//删除地址
function address_delete(address_id){
    $$.ajax({url:'/address/address_delete',async:false,dataType:'json',data:{address_id:address_id},       
        complete:function() {
            location.href = '/address/index?v=' + Math.random();
        }
    })
}

//选择省市
function change_region(type,value,are){
    if(type == 1){
        $$('select[name=city]')[0].options.length = 1;
        $$('select[name=district]')[0].options.length = 1;
    }
    if(type == 2){
        $$('select[name=district]')[0].options.length = 1;
    }
    $$.ajax({url: '/address/ajax_region',async:false,dataType: "json",data: {type:type,parent_id:value},success:function(msg){
        for(i in msg.list){
            $$('select[name='+are+']')[0].options.add(new Option(msg.list[i].region_name , msg.list[i].region_id));
        }

    }});
}

 <?php include APPPATH."views/mobile/header.php"; ?>
<style>
 

.list-block .input-item .label, .list-block.inputs-list .label, .list-block .input-item .floating-label, .list-block.inputs-list .floating-label{ font-size:1em;}
.list-block .label, .list-block .floating-label{ color:#9aa0b0;}
.list-block input[type="text"], .list-block input[type="password"], .list-block input[type="search"], .list-block input[type="email"], .list-block input[type="tel"], .list-block input[type="url"], .list-block input[type="date"], .list-block input[type="datetime-local"], .list-block input[type="time"], .list-block input[type="number"], .list-block select, 
.list-block textarea{ color:#9aa0b0; padding-left:10px;}
 
 
.list-block .focus-state .label,.list-block.focus-state .floating-label{ color:#9aa0b0;}
option {
    font-weight: normal;
    display: block;
    padding: 10px;
    white-space: pre; border:solid 1px #ccc;
    min-height: 1.4em; color:#000;
}

.list-block .item-inner{ padding-top:4px;}
.hu-dwxz i{   border-color: #fff rgba(0, 0, 0, 0) rgba(0, 0, 0, 0) rgba(0, 0, 0, 0); border-style: solid dashed dashed dashed;  vertical-align: middle;  float:right; margin-top:5px; border-width:8px;}
#submit-btn{width:100%}

.bjgrzl-hu li{ background-color:#0a6385; margin-bottom:2px; color:#d2d3d5; }

.list-block .input-item .item-inner, .list-block.inputs-list .item-inner{ margin-bottom:0;}

.not-paid-jt{  margin-right:20px; background-position:right 10px;}




</style>
<div class="view index view-main">
<div class="pages">
        <div data-page="index" class="page">
            <div class="navbar">
                <div class="navbar-inner">
		    <div class="left"><a href="#" class="link back"><i class="icon icon-back"></i></a></div>
                    <div class="center">编辑个人资料</div>
                </div>
            </div>
<div class="yywtoolbar">
        <div class="yywtoolbar-inner buttons-row">
            <a href="#" id="submit-btn" class="link payment-hu">保存</a>
        </div>
</div>
                <div class="page-content public-bg no-top" >
				<form action="/user/edit" method="POST" class="ajax-submit ">
                    <div class="list-block" style="margin-top:0;">
                    <ul class="bjgrzl-hu">
                    <li>
<div class="item-content">
                                <div class="item-media">昵称:</div>
                                <div class="item-inner">
                                    <div class="item-input"><input type="text" name="user_name" value="<?php echo $user->user_name?>" placeholder="例：逆水的鱼"></div>
                                </div>
</div> </li>
                   <li> <div class="item-content">
                                <div class="item-media">姓名:</div>
                                <div class="item-inner">
                                    <div class="item-input"><input type="text" name="real_name" value="<?php echo $user->real_name?>" placeholder="请填写真实姓名"></div>
                                </div>
</div></li>
                   <li>
                                <div class="item-content">
                                    <div class="item-media">职务:</div>
                                <div class="item-inner">
                                    <div class="item-input"><input type="text" name="company_position" value="<?php echo $user->company_position?>" placeholder="例：牙医"></div>
                                </div>
                                </div>
                        </li>
 
                         
                        <li>
                                <div class="item-content">
                                    <div class="item-media">单位名称:</div>
                                <div class="item-inner">
                                    <div class="item-input"><input type="text" name="company_name" value="<?php echo $user->company_name?>" placeholder="例：上海第九医院牙科门诊部"></div>
                                </div>
                                </div>
                        </li>
                        
                        <li>
                                <div class="item-content">
                                    <div class="item-media">单位性质:</div>
                                <div class="item-inner">
                                    <div class="item-input">
                                         <input type="text" placeholder="请填写单位性质" name="company_types" value="<?php echo $my_type?>" readonly id="company_types">
                                         <input type="hidden" name="company_type" id="company_type" value="<?php echo $user->company_type?>">
					 
                                   </div>
				   
                                </div>
				<div class="not-paid-jt"></div>
                                </div>
                        </li>
					
                 </ul>
		 
		 

		 
		 
		 
                    </div>
				</form>	
                </div>
        </div>
</div>
</div>
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
       
<script>
function isEmpty(dom){
    if ('' == $$(dom).val().replace(/(^\s*)|(\s*$)/g, ''))
        return true;
    return false;
}
      
$$('#submit-btn').click(function(e){
//function save(){ 
    e.preventDefault();
    if (isEmpty('input[name="real_name"]')){
        myApp.alert('姓名不能为空!');
    } else if(isEmpty('input[name="user_name"]')){
        myApp.alert('昵称不能为空!');
    } else if(isEmpty('input[name="company_name"]')){
        myApp.alert('单位名称不能为空!');
    } else if(isEmpty('input[name="company_position"]')){
        myApp.alert('职务不能为空!');
    } else if(0 == $$('input[name="company_type"]').val()){
        myApp.alert('请选择单位性质!');
    } else{
        $$('form.ajax-submit').trigger('submit');
    }
})
    $$('.back').click(function(){
        location.href = '/index-user';
    })
$$('form.ajax-submit').on('submitted', function (e) {
    //alert('submitted');
    var data = e.detail.data;
    if (data.res){
        myApp.confirm2(data.msg,'保存成功', '首页', '查看积分' 
            , function(){ location.href = data.share_url; }) 
            , function(){location.href = '/index-user'} 
    }
    else
        myApp.alert(null, data.msg);

})
myApp.picker({
    input: '#company_types',
    rotateEffect: true,
    toolbarCloseText: '完成',
    onChange:function(picker, values, displayValues){
        $$('#company_type').val(values[0]);
    }, 
    formatValue:function(picker, values, displayValues){
        return displayValues[0];
    }, 
    cols: [
        {
            textAlign: 'center',
            displayValues: ['<?php echo implode("', '", $company_type)?>'],
            values: ['<?php echo implode("', '", $values)?>']
        }
    ]
});
</script>
<?php include APPPATH."views/mobile/footer.php";?> 

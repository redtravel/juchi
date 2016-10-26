<?php if ($full_page): ?>
    <?php include APPPATH . "views/common/header.php"; ?>
    <link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
    <script type="text/javascript">
        function add_baby()
            {
                var v_flag = true;
                $('#newBabyBox .newBaby').each(function(i){
                    if(!v_flag) return;
                    var container = $(this);
                    var v_name = $("input[name=baby_name]", container).val();
                    var v_sex = $('input.baby_sex:checked', container).val();
                    var v_year = $('select[name=baby_birthdayYear]', container).val();
                    var v_month = $('select[name=baby_birthdayMonth]', container).val();
                    var v_day = $('select[name=baby_birthdayDay]', container).val();
                    if (v_name == undefined || v_sex == undefined || v_year == '' || v_month == '' || v_day == '') {
                        v_flag = false;
                    }
                });
                if (v_flag == false) {
                    alert('请先将已添加的宝宝资料填写完整');
                    return fales;
                }
                var obj = $('div.newBaby_tmp').clone();
                $('#newBabyBox').append(obj);
                $(':radio', obj).attr('name', new Date().getTime());
                obj.attr('class', 'newBaby').show();
            }
        $(function () {
                $('.profileEdit').click(function () {
                    $('a[name=add_submit]').parent().parent().parent().show();
                });
                if($('.newEditBaby').length<1){
                        add_baby();
                    }

                });
        var is_email_check = 0;
        function check_add_form()
        {
            // 取消错误显示的样式
            $('.list_block_content input').each(function(i){
                if($(this).css('border-color')=='rgb(255, 0, 0)'){
                    $(this).css('border-color', '');
                }
            });
            $('.list_block_content select').each(function(i){
                if($(this).css('border-color')=='rgb(255, 0, 0)'){
                    $(this).css('border-color', '');
                }
            });
            var email = document.getElementById('email').value;
            var mobile = document.getElementById('mobile').value;
            var user_name = $('input[type=hidden][name=user_name]').val();
            var real_name = $('input[type=text][name=real_name]').val();
            var sex = $('input[type=radio][name=sex]:checked').val();
            var birthdayYear = $('select[name=birthdayYear]').val();
            var birthdayMonth = $('select[name=birthdayMonth]').val();
            var birthdayDay = $('select[name=birthdayDay]').val();
            var real_name_len = real_name.replace(/[^\x00-\xff]/g, "**").length;
            var user_name_len = user_name.replace(/[^\x00-\xff]/g, "**").length;
            var favoriteCategory = $('select[name=favoriteCategory]').val();
            $('input[type=text][name=user_name]').css("border-color","");
            $('input[type=text][name=real_name]').css("border-color","");
            //$('input[type=text][name=baby_name]').css("border-color","");

                    var obj = null;
                    var err_msg = '';

                    $("#msgShow").html(err_msg);
                    if(err_msg == '' && $.trim(user_name) =='')
                    {
                        err_msg = "请输入您的昵称";
                        obj = $('input[type=text][name=user_name]');
                    }

                    if(err_msg == '' && user_name_len < 3)
                    {
//                        err_msg = '您的昵称过短，不能少于3个字符';
//                        obj = $('input[type=text][name=user_name]');
                    }

                    if(err_msg == '' && user_name_len > 12)
                    {
//                        err_msg = '您的昵称过长，不能超过12个字符';
//                        obj = $('input[type=text][name=user_name]');
                    }

                    if(err_msg == '' && $.trim(real_name) =='')
                    {
                        err_msg = "请填写真实姓名";
                        obj = $('input[type=text][name=real_name]');
                    }

                    if(err_msg == '' && real_name_len > 10)
                    {
                        err_msg = '您的姓名过长，不能超过10个字符';
                        obj = $('input[type=text][name=real_name]');
                    }
                    sex = sex == null ? 0 : sex;
                    if(err_msg == '' && sex ==0)
                    {
                        err_msg = "请选择您的性别";
                        obj = $('select[name=sex]');
                    }

                    if(err_msg == '' && birthdayYear == '')
                    {
                        err_msg = "请选择您的出生年份";
                        obj = $('select[name=birthdayYear]')
                    }

                    if(err_msg == '' && birthdayMonth =='')
                    {
                        err_msg = "请选择您的出生月份";
                        obj = $('select[name=birthdayMonth]')
                    }

                    if(err_msg == '' && birthdayDay =='')
                    {
                        err_msg = "请选择您的出生日期";
                        obj = $('select[name=birthdayDay]')
                    }
                    if(err_msg == '' && favoriteCategory =='')
                    {
                        err_msg = "请选择您最喜欢的分类";
                        obj = $('select[name=favoriteCategory]')
                    }
                    var v_baby_list = {};
                    var v_flag = true;
                     $('#newBabyBox .newBaby').each(function(i){
                            if(!v_flag) return;
                            j=i+1;
                            var container = $(this);
                            var v_name = $.trim($("input[name=baby_name]", container).val());
                            var v_sex = $('input[type=radio].baby_sex:checked', container).val();
                            var v_year = $('select[name=baby_birthdayYear]', container).val();
                            var v_month = $('select[name=baby_birthdayMonth]', container).val();
                            var v_day = $('select[name=baby_birthdayDay]', container).val();
                            var baby_name_len = v_name.length;
                            // 如果只有一个 baby 表单并且没有填写任何信息，pass
                            if(i==0 && v_name=='' && v_sex==undefined && v_year=='' && v_month=='' && v_day=='' && $('#newBabyBox .newBaby').length==1){
                                return;
                            }
                            if (v_name=='') {
                                err_msg = "请输入您第" + j + "个宝宝的姓名";
                                obj = $("input[name=baby_name]", container);
                                v_flag = false;
                                return;
                            }
                            if (baby_name_len>10) {
                                err_msg = "您第" + j + "个宝宝的姓名过长";
                                obj = $("input[name=baby_name]", container);
                                v_flag = false;
                                return;
                            }

                            if (v_sex == undefined) {
                                err_msg = "请选择您第" + j + "个宝宝的姓别";
                                obj = $('input[name=mbaby_sex]:checked', container);
                                v_flag = false;
                                return;
                            }

                            if (v_year == '') {
                                err_msg = "请选择您第" + j + "个宝宝的出生年份";
                                obj = $('select[name=baby_birthdayYear]', container);
                                v_flag = false;
                                return;
                            }

                            if (v_month == '') {
                                err_msg = "请选择您第" + j + "个宝宝的出生月份";
                                obj = $('select[name=baby_birthdayMonth]', container);
                                v_flag = false;
                                return;
                            }

                            if (v_day == '') {
                                err_msg = "请选择您第" + j + "个宝宝的出生月份";
                                obj = $('select[name=baby_birthdayDay]', container);
                                v_flag = false;
                                return;
                            }
                            
                            v_baby_list[i] = {};
                            v_baby_list[i]['baby_name'] = v_name;
                            v_baby_list[i]['baby_sex'] = v_sex;
                            v_baby_list[i]['baby_birthdayYear'] = v_year
                            v_baby_list[i]['baby_birthdayMonth'] = v_month;
                            v_baby_list[i]['baby_birthdayDay'] = v_day
                    });
		
                    if(err_msg != '')
                    {
                        if(obj)
                        {
                            obj.css("border-color","red");
                        }
                        $("#msgShow").html(err_msg);
                        //alert(err_msg);
                        return false;
                    }

                    $('input[type=button][name=add_submit]').attr("disabled", "disabled");

                    $.ajax({
                        url:'/user/profile_edit',
                        data:{is_ajax:true,email:email,mobile:mobile,user_name:user_name,real_name:real_name,sex:sex,
                            birthdayYear:birthdayYear,birthdayMonth:birthdayMonth,birthdayDay:birthdayDay,favoriteCategory:favoriteCategory,
                            baby_list:v_baby_list,
                            rnd:new Date().getTime()},
                        dataType:'json',
                        type:'POST',
                        success:function(result){
                            if(result.error==0){
                                alert(result.msg);
                                window.location.href = window.location.href;
                            }else{
                                alert(result.msg);
                            }
                            $('input[type=button][name=add_submit]').attr("disabled", "");
                        },
                        error:function(err) {
                            alert("提交失败，请重新尝试！");
                            location.href = location.href;
                        }
                    });

                    return false;
                }

                function valid_email()
                {
                    if (is_email_check == 1)
                    {
                        return false;
                    }
                    var email = document.getElementById('email').value;
                    if ($.trim(email) =='')
                    {
                        alert('请输如正确的Email地址');
                        return false;
                    }
                    if (isEmail(email) == false)
                    {
                        alert('请输如正确的Email地址');
                        return false;
                    }
                    $('#email_msg').html(" 正在发送中");
                    is_email_check = 1;
                    $.ajax({
                        url:'/user/send_email_valid',
                        data:{is_ajax:true,email:email,rnd:new Date().getTime()},
                        dataType:'json',
                        type:'POST',
                        success:function(result){
                            if(result.error==0){
                                alert(result.msg);
                                $('#email_msg').html(" 发送完成");
                                $('input[type=text][id=email]').attr("readOnly", "readonly");
                                is_email_check = 0;
                            }else{
                                alert(result.msg);
                                $('#email_msg').html(" 发送失败");
                                is_email_check = 0;
                            }
                        }
                    });
                    return false;
                }


                function change_region (region_id, num, target)
                {
                    if (region_id > 0)
                    {
                        $.ajax({
                            url:'/user/region_change',
                            data:{is_ajax:true,region_id:region_id,type:num,rnd:new Date().getTime()},
                            dataType:'json',
                            type:'POST',
                            success:function(result){
                                if(result.error==0){
                                    var sel = document.getElementById(target);
                                    sel.length = 1;
                                    sel.selectedIndex = 0;
                                    if (result.regions)
                                    {
                                        for (i = 0; i < result.regions.length; i ++ )
                                        {
                                            var opt = document.createElement("OPTION");
                                            opt.value = result.regions[i].region_id;
                                            opt.text  = result.regions[i].region_name;
                                            sel.options.add(opt);
                                        }
                                    }
                                }else{
                                    alert(result.msg);
                                }
                            }
                        });
                    }
                }

                function isEmail(email)
                {
                    var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;
                    return reg1.test( email );
                }
    </script>
    <div id="content">
        <div class="now_pos">
            <a href="/">首页</a>
            >
            <a href="/user">会员中心</a>
            >
            <a class="now">基本资料</a>
                <!-- come soon
		<a class="notice" href="/">全场满200减20!</a>
                -->
        </div>
        <div class="ucenter_left">
            <?php include APPPATH . "views/user/left.php"; ?>
        </div>
        <div class="ucenter_main">
            <div class="list_block" id="listdiv">
            <?php endif; ?>
            <h2>
                编辑个人资料
            </h2>
            <div class="list_block_content profileList">
                <table width="748" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <th colspan="2">
                                        <font class="font14">绑定手机、邮箱</font>
                                        <font class="bred bold"><?php if (!$user->email_validated || !$user->mobile_checked): ?>（注册并验证，即送<?php echo $user->arr_invite_rank->regist_point ?>积分！）<?php endif; ?></font>
                                    </th>
                                </tr>
                                <tr>
                                    <td width="12%" align="right">会员帐号 ：</td>
                                    <td width="88%"><?php echo isset($user->email) ?  $user->email : $user->mobile; ?>
                                        <input type="hidden" name="user_name" id="user_name" value="<?php echo $user->user_name ?>" />
                                    </td>
                                </tr>
                                
                                    <tr <?php if($user->union_sina || $user->union_qq || $user->union_zhifubao) print 'style="display:none;"';?>>
                                        <td align="right">绑定邮箱 ：</td>
                                        <td>
                                            <input type="text" name="email" id="email" value="<?php echo $user->email ?>" <?php if (!empty($user->email)) { ?> readonly="readonly"  <?php } ?>/>
                                            <!-- <font class="w150 inblock"><?php echo $user->email ?></font>已验证 -->
                                            <?php if ($user->email_validated): ?>已验证<?php else: ?><a href="#" onclick="valid_email();return false;" class="btn_g_122">去邮箱验证</a><?php endif; ?>
                                            <span id="email_msg"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">
                                            <span class="red l marginL10">*</span>绑定手机 ：
                                        </td>
                                        <td>
                                            <input type="hidden" id="mobile" value="<?php echo $user->mobile ?>" />
                                            <?php if (!empty($user->mobile)): ?>
                                                <span  class="c_o"><?php echo $user->mobile ?></span>
                                            <?php endif; ?><?php if ($user->mobile_checked): ?>已验证<?php else: ?>
                                            <a class="btn_g_122" id="mobileVv" href="/user/validate_mobile" onclick="void(0);" >验证手机</a>
                                            <!-- come soon <a class="btn_g_122" id="mobileV">免费获取短信验证码</a> -->
                                                <?php endif; ?>
                                            <div class="inblock" id="countdown">
                                                <a class="btn_gray_93">重新获取</a>
                                                <font>已发送,1分钟后可重新获取</font>
                                            </div>
                                        </td>
                                    </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--我的资料-->
                <div class="myInfoDiv">
		            <h5>
			        	<font class="font14">我的资料</font>
			        	<input type="hidden" name="real_name" id="real_name" value="<?php echo (isset($user->real_name) ? $user->real_name : "default"); ?>" />
			        	<font class="bred bold">（完善资料，即送<?php echo $user->arr_invite_rank->profile_point ?>积分！）</font>
		        	</h5>
		        	<div class="editorBox">
		        		<h4>
		        			<p class="pLeft">真实姓名：</p>
		        			<p name="perEdit" class="pRight"><input type="text" name="real_name" value="<?php print $user->real_name;?>"></p>
		        		</h4>
		        		<h4>
		        			<p class="pLeft">我的性别：</p>
		        			<p name="perEdit" class="pRight">
		        				<label for="sex_man1">
                                    <input id="sex_man1" class="inputRadio" type="radio" name="sex" value="1" <?php echo $user->sex == 1 ? 'checked' : ''; ?>>
                                    <font>男</font>
                                </label>
                                <label for="sex_women1">
                                    <input id="sex_women1" class="inputRadio" type="radio" name="sex" value="2" <?php echo $user->sex == 2 ? 'checked' : ''; ?>>
                                    <font>女</font>
                                </label>
                            </p>
		        		</h4>
		        		<h4>
		        			<p class="pLeft">我的生日：</p>
		        			<p name="perEdit" class="pRight">
                                <select name="birthdayYear">
                                    <option value="">请选择年</option>
                                    <?php for ($i = 1941; $i < 2010; $i++) { ?>
                                        <option <?php echo!empty($user->birthday) && substr($user->birthday, 0, 4) == $i ? 'selected' : '' ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                                    <?php } ?>
                                </select>
                                <select name="birthdayMonth">
                                    <option value="">请选择月</option>
                                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                                        <option <?php echo!empty($user->birthday) && substr($user->birthday, 5, 2) == sprintf("%02d", $i) ? 'selected' : '' ?> value="<?php echo sprintf("%02d", $i) ?>"><?php echo sprintf("%02d", $i) ?></option>
                                    <?php } ?>
                                </select>
                                <select name="birthdayDay">
                                    <option value="">请选择日</option>
                                    <?php for ($i = 1; $i <= 31; $i++) { ?>
                                        <option <?php echo!empty($user->birthday) && substr($user->birthday, 8, 2) == sprintf("%02d", $i) ? 'selected' : '' ?> value="<?php echo sprintf("%02d", $i) ?>"><?php echo sprintf("%02d", $i) ?></option>
                                    <?php } ?>
                                </select>
		        			</p>
		        		</h4>
		        		<h4>
		        			<p class="pLeft">最喜欢的分类 ：</p>
		        			<p name="perShow" class="pRight" style="display:none;"><?php print $user->favorite_category_name;?></p>
		        			<p name="perEdit" class="pRight">
		        				<select name="favoriteCategory" style="width:120px;">
                                    <option value="">请选择</option>
                                    <?php foreach($category_list as $cat):?>
                                    <option value="<?php print $cat['category_id'];?>" <?php print $cat['category_id']==$user->favorite_category?'selected':'';?>><?php print $cat['category_name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </p>
		        		</h4>
		        		
		        	</div>
		        </div>   
                
					
                <!-- 宝宝资料 -->
		        <div class="myInfoDiv">
		            <h5>
			        	<font class="font14">宝宝资料</font>
			        	<a id="btnAddBabyInfo" class="wGreen profileEdit" >[新增]</a>
		        	</h5>
		        	<!-- <h4><p><span>宝宝姓名 ：</span><s>东子</s><span>宝宝性别 ：</span><s>男</s><span>宝宝生日 ：</span><s>2013-10-01</s></h4> -->
		        	<?php foreach ($user->baby_list as $baby) : ?>
					<div class="newEditBaby"><p><span>宝宝姓名 ：</span><?=$baby['baby_name']?><span>宝宝性别 ：</span><?php echo $baby['baby_sex'] == 1 ? '男' : ($baby['baby_sex'] == 2 ? '女' : '') ?> <span>宝宝生日 ：</span><?= substr($baby['baby_birthday'], 0, 4) ?>年<?= substr($baby['baby_birthday'], 5, 2) ?>月<?= substr($baby['baby_birthday'], 8, 2) ?>日</p></div>
		            <?php endforeach; ?>
				</div>	

		        <!-- 新增宝宝资料 -->
		        <div id="newBabyBox">
            		
            		
            	</div>
                            <div class="bottomCtrl">
                            <input id="baby_id" type="hidden" value="0"/>
                                <a name="add_submit" onclick="check_add_form();" class="btn_g_75">提交</a>&nbsp;
                                <font id="msgShow" class="red"></font>
                                <font class="bottomText">请如实填写您的信息，<?php print SITE_NAME;?>不会透露您的私人信息，我们会根据信息为您提供更好的商品和服务！</font>
                            </div>

            </div>
            <?php if ($full_page): ?>
            </div>
        </div>
    </div>
    <div class="newBaby_tmp" style="display: none;">
            <div class="newBabyLeft">
                <p><span>宝宝姓名 ：</span> <input class="newInput" type="text" name="baby_name"><span>宝宝性别 ：</span>
                    <label>
                        <input class="inputRadio baby_sex" type="radio"  value="1">
                        <font>男</font>
                    </label>
                    <label>
                        <input class="inputRadio baby_sex" type="radio"  value="2">
                        <font>女</font>
                    </label>
                    &nbsp;&nbsp;
                    <span>宝宝生日 ：</span>
                    <select name="baby_birthdayYear" class="birthday">
                        <option value="">年</option>
                        <?php for ($i = 1995; $i <= intval(date('Y')); $i++): ?>
                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="baby_birthdayMonth" class="birthday">
                        <option value="">月</option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo sprintf("%02d", $i) ?>"><?php echo sprintf("%02d", $i) ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="baby_birthdayDay" class="birthday">
                        <option value="">日</option>
                        <?php for ($i = 1; $i <= 31; $i++): ?>
                            <option value="<?php echo sprintf("%02d", $i) ?>"><?php echo sprintf("%02d", $i) ?></option>
                        <?php endfor; ?>
                    </select>
                    <font class="c66">生日不可更改，请仔细填写！</font>
                </p>

            </div>
            <div class="newBabyRigth">
                <b class="subBabyInfo" onclick="$(this).parent().parent().remove();"></b>
            </div>
        </div>
	<script type="text/javascript">
$(function(){
	$('#btnAddBabyInfo').click(add_baby);
})
	
</script>
    <?php include APPPATH . 'views/common/footer.php'; ?>
    <?php endif; ?>

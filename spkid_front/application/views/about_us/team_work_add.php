<link href="<?php echo static_style_url('pc/css/bootstrap.css')?>" rel="stylesheet" type="text/css" media="all">
<?php include APPPATH . 'views/common/header.php'?>
<div class="about-us">
    <div class="about-con">
        <p class="course-tit">首页>合作咨询</p>
        <ul class="about-list clearfix">
            <li><a href="/about_us/about_us">关于演示站</a></li>
            <li><a href="/about_us/service">服务条款</a></li>
            <li><a href="/about_us/feedback">意见反馈</a></li>
            <li><a href="/about_us/sales_policy">售后政策</a></li>
            <li><a href="/about_us/team_work" class="about-currt">合作咨询</a></li>
            <li><a href="/about_us/join_us">加入我们</a></li>
        </ul>
        <div class="hezuohuoban clearfix">
            <div class="hezuoliucheng">
                <div class="hzlc-tit wanshan-tit">完善以下信息即可与演示站合作：</div>
                <div class="hz-properties">
                    <form method="post" action="#" name="team_Form">
                        <p class="hzsx">请选择合作属性：</p>
                        <ul class="hzdx clearfix">
                        <?php foreach ($team_type as $team_key => $team_val) { ?>
                            <li><input name="team_type" type="radio" <?php if($team_key == 0 ) echo "checked";?> value="<?php echo $team_key;?>"><?php echo $team_type[$team_key]?></li>
                        <? } ?>
                        </ul>
                        <div class="qymc"><label>企业名称</label>：<input name="team_company" type="text" class="hz-input"><span></span></div>
                        <div class="contact-ifo">
                            <p>联系人信息：</p>
                            <ul class="contact-lb clearfix">
                                <li><label>姓　　名：</label><input name="team_name" type="text" class="hz-input"><span></span></li>
                                <li><label>联系电话：</label><input name="team_tel" type="text" class="hz-input"><span></span></li>
                                <li><label>联系邮箱：</label><input name="team_email" type="text" class="hz-input"><span></span></li>
                                <li><label>验 证 码：</label><input name="team_code" type="text" class="hz-input2"><a href="javascript:void(0);" class="yanzhengma captcha" title="刷新"><img src="/user/show_verify" height="30"></a><span></span></li>
                            </ul>
                            <button class="hzzx-tj" type="submit">提交</button>
                        </div>
                    </form>
                </div> 
            </div>
            <div class="hezuozhongxin">
                <div class="hzzx-tit">合作中心热线:</div>
                <?php if( !empty($cooperation_center) )foreach($cooperation_center as $cpt){
                    echo adjust_path($cpt->ad_code);
                }?>        
                <div class="hzlc-picture">
                    <p>合作流程</p>
                    <ul class="hzlc-teb">
                        <li>提交合作信息</li>
                        <li>演示站致电合作方</li>
                        <li>确认合作</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="forgot-box" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header v-team-add">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">提示</h4>
            </div>
            <div class="modal-body v-team-button">
                <div><p>您的合作申请演示站已经收到，请耐心等待</p><p>客服将会与您联系</p></div>
                <button class="btn btn-lg btn-blue" onclick="javascript:location.reload();" type="submit">确定</button>
            </div>
        </div>
    </div>
</div>

<?php include APPPATH . 'views/common/footer.php'?>
<script type="text/javascript" src="<?php echo static_style_url('pc/js/bootstrap.min.js')?>"></script>
<script type="text/javascript" >
    $('.captcha').click(function(e){
        e.preventDefault();
        $('.captcha>img').attr('src', '/user/show_verify?v='+Math.random());
    })

    var team_type = $('input[name="team_type"]');
    var team_company = $('input[name="team_company"]');
    var team_name = $('input[name="team_name"]');
    var team_tel = $('input[name="team_tel"]');
    var team_email = $('input[name="team_email"]');
    var team_code = $('input[name="team_code"]');
    var email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var tel = /^(1[0-9]{10})|(0\d{2,3}-?\d{7,8})$/;

    team_type.click(function(){
        if($(this).val() == '2'){
            $(".qymc label").html("门诊名称");
        }else{
            $(".qymc label").html("企业名称");
        }
    })
    team_company.blur(function(){
        if ('' == team_company.val()) {
            team_company.parent().find("span").text('请填写正确的名称');
        } else {
            team_company.parent().find("span").text('');
        }
    })
    team_name.blur(function(){
        if ('' == team_name.val()) {
            team_name.parent().find("span").text('请输入您的姓名');
        } else {
            team_name.parent().find("span").text('');
        }
    })
    team_tel.blur(function(){
        if ('' == team_tel.val() || !tel.test(team_tel.val())) {
            team_tel.parent().find("span").text('请填写正确的联系电话');
        } else {
            team_tel.parent().find("span").text('');
        }
    })
    team_email.blur(function(){
        if ('' == team_email.val() || !email.test(team_email.val())) {
            team_email.parent().find("span").text('邮箱格式输入错误');
        } else {
            team_email.parent().find("span").text('');
        }
    })

    team_code.blur(function(){
        if ('' != $.trim(team_code.val())){
            $.get('/user/validate_code', {captcha:team_code.val()}, function(res){
                if (!res){
                    team_code.parent().find("span").text('');
                } else {
                    team_code.parent().find("span").text('验证码输入错误');
                }
            })
        }else {
            team_code.parent().find("span").text('验证码输入错误');
        }
    })

    $('form[name="team_Form"]').on('submit', function(e){
        e.preventDefault();
        $.ajax({url:'/about_us/check_add', data:$(this).serialize(), method:'POST', dataType:'json', success:function(data){
            if (data.error == 0 ){    
                $('input[name="'+data.team_err+'"]').parent().find("span").text(data.team_msg);        
            }else if(data.error == 1 ){
                $("#forgot-box").modal('show');
            }
        }
        });
    })
</script>
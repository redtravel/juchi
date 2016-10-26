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
        <div class="consultation-img"><a href="/about_us/team_work_add"><img src="<?php echo static_style_url('pc/images/hzzx-but.png');?>"></a></div>
        <div class="hezuohuoban clearfix">
            <div class="hezuoliucheng">
                <div class="hzlc-tit">合作流程</div>
                <ul class="hezuoliucheng-lb clearfix">
                    <li><span>1</span>提交合作信息</li>
                    <li><span>2</span>演示站致电合作方负责人</li>
                    <li><span>3</span>确定合作</li>
                </ul>
            </div>
            <div class="hezuozhongxin">
                <div class="hzzx-tit">合作中心热线:</div>
                <?php if( !empty($cooperation_center) )foreach($cooperation_center as $cpt){
                	echo adjust_path($cpt->ad_code);
            	}?>
            </div>
        </div>
    </div>
</div>
<?php include APPPATH . 'views/common/footer.php'?>

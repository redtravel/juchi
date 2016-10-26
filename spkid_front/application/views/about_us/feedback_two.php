<?php include APPPATH . 'views/common/header.php'?>
<div class="about-us">
    <div class="about-con">
        <p class="course-tit">首页>意见反馈</p>
        <ul class="about-list clearfix">
            <li><a href="/about_us/about_us">关于演示站</a></li>
            <li><a href="/about_us/service">服务条款</a></li>
            <li><a href="/about_us/feedback" class="about-currt">意见反馈</a></li>
            <li><a href="/about_us/sales_policy">售后政策</a></li>
            <li><a href="/about_us/team_work">合作咨询</a></li>
            <li><a href="/about_us/join_us">加入我们</a></li>
        </ul> 
        <div class="about-lb feedback"> 
            <p class="about-tit">意见反馈<span>Feedback</span></p>
            <div class="feedback-ganxie">
                <span>您的反馈已记录，感谢您的支持！</span>
                <p>页面将在<em id="mes">5</em>秒钟后自动跳转到</p>
                <p>http://pc.redtravel.cn</p>
            </div>    
        </div>
    </div>
</div>

<?php include APPPATH . 'views/common/footer.php'?>
<script language="javascript" type="text/javascript"> 
function run(){
    var s = document.getElementById("mes");
    if(s.innerHTML == 0){
        window.location.href='/';
        return false;
    }
    s.innerHTML = s.innerHTML * 1 - 1;
}
window.setInterval("run();", 1000);
</script> 
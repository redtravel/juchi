<div class="iContainFocusBox">       
    <div class="iContainFocusImg" >
        <ul id="FocusImgLi">
        </ul>
    </div>
    <div class="iContainFocusDot"></div>
    <div id="dotGroup">
    </div>
</div>
<script>
$(function(){
    var lunbo=[];
    <?foreach($list as $key=>$row):?>
    lunbo.push({start_time:'<?=$row->start_time?>',end_time:'<?=$row->end_time?>',img_url:'<?=$row->focus_img?>',link_url:'<?=$row->focus_url?>'});
    <?endforeach?>
    var curTime=CurentTime();
    var li="";
    var span="";
    var lunbo_index=0;
    for(var i=0;i<lunbo.length;i++){
        if(lunbo[i].start_time<curTime&&lunbo[i].end_time>curTime){
            li+="<li>";
            li+="<a id='iImg"+(i+1)+"' href='"+lunbo[i].link_url+"' target='_blank' title=''>";
            li+="<img "+(lunbo_index==0?"src":"psrc")+"='<?=IMG_HOST?>/"+lunbo[i].img_url+"' alt='' /></a>";
            li+="</li>";
            span+="<span id='dot"+(i+1)+"' "+(i==0?"class='dotActive'":"")+">"+(i+1)+"</span>";
            lunbo_index++;
        }
    }
    $("#FocusImgLi").append(li);
    $("#dotGroup").append(span);
    function CurentTime(){
          var now = new Date();
          var year = now.getFullYear();       //年
          var month = now.getMonth() + 1;     //月
          var day = now.getDate();            //日
          var hh = now.getHours();            //时
          var mm = now.getMinutes();          //分
          var clock = year + "-";
          if(month < 10)
             clock += "0";
          clock += month + "-";
          if(day < 10)
            clock += "0";
          clock += day + " ";
          if(hh < 10)
             clock += "0";
          clock += hh + ":";
          if (mm < 10) clock += '0';
            clock += mm;
          return(clock);
    }
});
</script>

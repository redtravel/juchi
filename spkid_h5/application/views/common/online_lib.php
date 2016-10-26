       <div class="mes_history_text" style="overflow-y:auto; overflow-x:hidden;">
       <h2>&nbsp;&nbsp;消息记录</h2>
       <?php if (!empty($message_all)): ?>
        <?php foreach ($message_all as $item): ?>
        <p><span class="<?php echo $item['qora']==0?'ol_cus':'ol_kf'; ?>"><?php echo $item['qora']==0?((isset($user_info->user_name) && !empty($user_info->user_name))?$user_info->user_name:'客户'):'本站客服'; ?> (<?php echo date('H:i:s',strtotime($item['create_date'])) ?>) </span><span class="ol_t"><?php echo $item['content'] ?></span></p>
        <?php endforeach; ?>
        <?php endif; ?>
        </div>
        <div class="mes_r_page"><a href="#" onclick="goto_page(1,0);return false;">|<</a>&nbsp;<a  href="#" onclick="goto_page(1,2);return false;"><</a>&nbsp;第<input type="text" style="width:20px;" id="cur_page" value="<?php echo $cur_page; ?>" onkeydown= "handle_enter(this,event); " />页/<?php echo $total_page; ?>页&nbsp;<a  href="#" onclick="goto_page(1,1);return false;">></a>&nbsp;<a href="#" onclick="goto_page(total_page,0);return false;">>|</a></div>



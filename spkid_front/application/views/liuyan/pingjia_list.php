<?php foreach ($list as $l): ?>
	

	<li class="clearfix">
	    <div class="avatar"><img src="<?php echo static_style_url('mobile/touxiang/'.($l->user_advar ? $l->user_advar : 'default.png').'?v=version')?>" class="" title="<?php print $l->user_name?$l->user_name:$l->admin_user_name; ?>"></div>
	    <div class="cont">
	      <div class="ut">
	        <span class="uname text-overflow ">
	        	<?php if($l->user_name) { 
	        		echo $l->user_name;
	        	}elseif($l->admin_user_name) {
	        		echo $l->admin_user_name;
	        	}else{ 
	        		echo '匿名';
	        	} ?>
	        </span>
	        <span class="date"><?php print $l->comment_date ?></span>
	      </div>
	      <?php if ($l->at_comment):?>
	        <div class="quote">
	          		
	              <div class="uname">@&nbsp;<span><?php echo isset($l->at_comment->user_name) ? $l->at_comment->user_name : '匿名';?></span></div>
	              <div class="qct"><?php echo isset($l->at_comment->comment_content) ? $l->at_comment->comment_content : '此处省略一万字';?></div>
	          
	        </div>
	      <?php endif;?>
	      
	      <div class="ct"><?php print $l->comment_content ?></div>
	      
	      <!-- <div class="tb"><a href="#" at_comment_id="<?php echo $l->comment_id;?>">回复</a></div> -->
	      
	    </div>
	  </li>

	

<?php endforeach ?>



<?php if (!$list): ?>
暂时没有反馈。
<?php endif ?>
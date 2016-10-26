<?php include APPPATH."views/common/user_header.php"; ?>
<script src="<?= static_style_url('pc/js/comm_tool.js?v=version')?>"></script>







               
               <div class="personal-center-right">
                   
                     <h1 class="order-details-bt">回复提醒</h1>
                     <div class="reply-remind">
                          <div class="reply-tit clearfix">
                               <div class="pingjianr fl-left" style="width:50%">回复内容</div>
                               <div class="article-name fl-left" style="width:50%">回复主题</div>
                          </div>
                          
                          <ul class="reply-remind-lb">
                          <?php foreach($my_response as $k => $v):?>
                          <li>
                             <div class="reply-remind-list clearfix">
                                  <div style="width:50%" class="replay-remind-left fl-left evaluation">
                                       <span title="<?=$v->comment_content;?>"><?= $v->comment_content2;?></span>
                                       <p>
                                       		<span style="font-size:12px">
                                       			<?= $v->comment_date;?>&nbsp;<?= $v->user_name;?>&nbsp;<span title="<?=$v->at_comment->comment_content?>">回复我的评论<?=cutstr($v->at_comment->comment_content, 0, 10)?></span>
                                       		</span>
                                       		
                                       		
                                       	</p>
                                  </div>
                                  <div title="<?=$v->product_name;?>" class="replay-remind-right wenzhangmingchen fl-left" style="padding-left:0">
                                  	<a href="<?=$v->link;?>">
                                  		<?=$v->product_name2;?>
                                  	</a>
                                  </div>
                                  
                             </div>
                          
                          </li>
                          <?php endforeach;?>
                          </ul>
                     </div>
            </div>
          </div>
     </div>
</div>


<script>
	
	$(function(){

		$('.liuyan-del').on('click', function(e){
			if (!confirm('确定删除吗')) {return;};
			var cid = $(this).find('a').eq(0).attr('comment_id');
			$.ajax({
				url:'/liuyan/delete_liuyan',
				dataType:'json',
				data:{
					cid:cid
				},
				type:'POST',
				success:function(res){
					alert(res.msg);
					if (res.res == 0) {
						location.href = location.href;
					};
				},
				error:function() {
					alert('未知错误');
				}				
			});
	
	});
	});
</script>


















<?php include APPPATH . "views/common/footer.php";?>
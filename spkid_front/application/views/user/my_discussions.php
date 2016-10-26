<?php include APPPATH."views/common/user_header.php"; ?>
<script src="<?= static_style_url('pc/js/comm_tool.js?v=version')?>"></script>







               
               <div class="personal-center-right">
                   
                     <h1 class="order-details-bt">我的讨论</h1>
                     <div class="reply-remind">
                          <div class="reply-tit clearfix">
                               <div class="pingjianr fl-left">评论内容</div>
                               <div class="article-name fl-left">文章名称</div>
                               <div class="zhuangtai fl-left">状态</div>
                               <div class="caozuo fl-left">操作</div>
                          </div>
                          
                          <ul class="reply-remind-lb">
                          <?php foreach($my_discussions as $k => $v):?>
                          <li>
                             <div class="reply-remind-list clearfix">
                                  <div title="<?=$v->comment_content;?>" class="replay-remind-left fl-left evaluation">
                                       <span><?= $v->comment_content2;?></span>
                                       <p><span><?= $v->comment_date;?></span></p>
                                  </div>
                                  <div title="<?=$v->product_name;?>" class="replay-remind-right wenzhangmingchen fl-left">
                                  	<a href="<?=$v->link;?>">
                                  		<?=$v->product_name2;?>
                                  	</a>
                                  </div>
                                  <div class="approved fl-left">
                                  	<?php echo $v->is_audit ? '审核通过' : '未审核'?>
                                  </div>
                                  <div class="delete-hu fl-left liuyan-del"><a comment_id="<?=$v->comment_id;?>" href="javascript:void(0)">删除</a></div>
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
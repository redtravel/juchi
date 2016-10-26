<?php include APPPATH."views/mobile/header.php"; ?>
<div class="view view-main" data-page="index" >
    <div class="pages">
        <div data-page="index" class="page article-bg no-toolbar">
        	<!--navbar start-->
		    <div class="navbar">
			    <div class="navbar-inner">
				  	<div class="left"><a href="#" class="link icon-only history-back"><i class="icon back"></i></a></div>
			        <div class="center">收货地址</div>
                    <div class="right"><a href="/" class="external"><i class="home-ico"></i></a></div>
			    </div>
		    </div>
		 	<!--navbar end-->	

		    <div class="yywtoolbar">
		        <div class="toolbar-inner row no-gutter">
		            <div class="col-100 xinzeng"><a class="link external" href="/address/address_add">新建地址</a></div>
		        </div>
		    </div>
		  		
		    <div class="page-content">
		        <div class="content-block wrap" style="padding-bottom:50px;">
			  		<ul class="receiving-address">
			        <?php foreach ($address as $add_val) { ?>                    	
			        	<li class="<?php if($add_val->is_used) echo 'default-address';?>" >			        		
							<div class="receiving-address-list ">
							    <div class="juli-plick clearfix">
					                <div class="receiving-lb receiving-lb2">
							            <span class="dizhi-user"><?php echo $add_val->consignee;?></span>
								    	<span class="address-tel"><?php echo $add_val->mobile;?></span>
								    	<div class="receiving-dizhi"><?php echo $add_val->country.$add_val->province.'  '.$add_val->city.$add_val->district.$add_val->address;?></div>
							       	</div>
					                <div class="default-address">
                                        <div class="default-address-left" <?php echo $add_val->is_used ? ' ' : 'onclick="return address_setdefault('.$add_val->address_id.');"'?> ><span class="<?php echo $add_val->is_used ? 'default-address-ico' : 'default-address-ico2';?>"></span>默认地址</div>
                                        <div class="default-address-yb">
                                            <a href="/address/address_editor/<?php echo $add_val->address_id;?>" class="external"><span class="default-editor">编辑</span></a>
                                            <a onclick="address_delete(<?php echo $add_val->address_id;?>);"><span class="default-delete">删除</span></a>                                       
                                        </div>
                                    </div>
							    </div>    
						    </div>							
			        	</li>
					<?php } ?>
			      	</ul>
			  	</div>
	     	</div>
        </div>
    </div>
</div>
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/address.js?v=version')?>"></script>
<?php include APPPATH."views/mobile/footer.php"?>


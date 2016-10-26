<div class="view view-main" data-page="index">
     <div class="pages">
          <div data-page="index" class="page article-bg no-toolbar">
    <div class="yywtoolbar">
        <div class="yywtoolbar-inner row no-gutter">
            <div class="col-100 xinzeng"><a class="link external" href="/address/address_add">新增收货地址</a></div>
        </div>
    </div>
		  <!--navbar start-->
		      <div class="navbar">
			    <div class="navbar-inner">
				  <div class="left"><a href="#" class="link icon-only back"> <i class="icon back"></i></a></div>
			          <div class="center">收货地址</div>
			    </div>
		     </div>
		 <!--navbar end-->		
	     <div class="page-content">
	          <div class="content-block wrap">
		  
		        <ul class="receiving-address">
		        <?php foreach ($address as $add_val) { ?>                    	
		        <li class="<?php if($add_val->is_used) echo '';?>" >
		        <a href="/address/address_editor/<?php echo $add_val->address_id;?>">
				 <div class="receiving-address-list ">
				     <div class="juli-plick clearfix">
		                       <div class="receiving-lb <?php if($add_val->is_used) echo '';?>">
				            <span class="dizhi-user"><?php echo $add_val->consignee;?></span>
					    <span class="address-tel"><?php echo $add_val->mobile;?></span>
					    <div class="receiving-dizhi"><?php echo $add_val->country.$add_val->province.'  '.$add_val->city.$add_val->district.$add_val->address;?></div>
				       </div>
		                      <div class="address-returned"></div>
				     </div>  
				     
			        </div>
			</a>
		        </li>
			
			<?php } ?>
		      </ul>
		  </div>
	     </div>
         </div>
    </div>
</div>



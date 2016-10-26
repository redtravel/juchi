<?php include APPPATH."views/mobile/header.php"; ?>
<div class="view view-main" data-page="index">
    <div class="pages">
        <div data-page="index" class="page article-bg no-toolbar">
	        <!--navbar start-->
		    <div class="navbar">
			    <div class="navbar-inner">
				  	<div class="left"><a href="#" class="link icon-only history-back"> <i class="icon back"></i></a></div>
			        <div class="center">新建地址</div>
			    </div>
		    </div>
	        <!--navbar end-->
	        <!--yywtoolbar start-->
	        <div class="yywtoolbar">
		        <div class="toolbar-inner row no-gutter">
			       	<div class="col-100"><a class="button form-ad-json preservation-hu" href="#">保存</a></div>
			    </div>
		    </div>
	        <!--yywtoolbar end-->
	       
	        <div class="page-content">
	            <div class="content-block wrap2">
		           	<form id="my-form">
		           		<input type="hidden" name="v_url_address" value="<?php echo $_SERVER['HTTP_REFERER'];?>">
			       		<ul class="receiving-address">
			           		<li>
							    <div class="edit-list">
									<div class="edit-user"><input type="text" name="consignee" placeholder="收件人姓名"></div>
							    </div>
			            	</li>
						    <li>
						    	<a href="#" class="item-link smart-select" data-searchbar="true" data-searchbar-placeholder="Search province" data-back-on-select="true">
							        <select name="province" id="province" onchange="return change_region(1,this.value,'city')">
							        	<option value="" selected="selected">请选择...</option>
							        	<?php foreach ($province as $pro_val) { ?>
							        	<option value="<?php echo $pro_val->region_id;?>"><?php echo $pro_val->region_name;?></option>
							        	<?php } ?>
							        </select>
								    <div class="item-content cities">
									 	<div class="item-inner icon-dizhi">
									      	<div class="item-title  cits-shi">所在省/市</div>
									 	</div>
								    </div>
						    	</a>
						    </li>		           				    
						    <li>
						    	<a href="#" class="item-link smart-select" data-searchbar="true" data-searchbar-placeholder="Search city" data-back-on-select="true">
									<select name="city" id="city"  onchange="return change_region(2,this.value,'district')">
					        			<option value="" selected="selected">请选择...</option>
					        		</select>
						    		<div class="item-content cities">
									 	<div class="item-inner icon-dizhi">
									      	<div class="item-title  cits-shi">所在市/区</div>
									 	</div>
								    </div>
						   		</a>
						  	</li>
						  	<li>
								<a href="#" class="item-link smart-select" data-searchbar="true" data-searchbar-placeholder="Search district" data-back-on-select="true">
							        <select name="district" id="district" >
							        	<option value="" selected="selected">请选择...</option>
							        </select>
								  	<div class="item-content cities">
									 	<div class="item-inner icon-dizhi">
									      	<div class="item-title  cits-shi">所在县/区</div>
									 	</div>
								  	</div>
							    </a>
						    </li>
						    <li>
						    	<div class="edit-list">
									<div class="edit-user xxdz-hu2">
										<div class="item-input item-input-field-noheight">
											<input type="text" name="address" placeholder="详细地址" value="">
										</div>
									</div>
						    	</div>
						   	</li>
						   	<li>
							   <div class="edit-list">
									<div class="edit-user xxdz-hu Phone-number-hu2">
										<div class="item-input item-input-field-noheight">
											<input type="text" name="mobile" placeholder="手机号码" value="">
										</div>
								    </div>
							    </div>
						   	</li>
						   	<li>
							   	<div class="default-address-hu clearfix">
									<span class="default-address-tit">本地址设为默认地址</span>
									<div class="default-address-anniu">
									  	<label class="label-switch">
									     	<input type="checkbox" name="is_used">
									     	<div class="checkbox"></div>
									  	</label>
								    </div>
								</div>
						   	</li>
			       		</ul>   
			 		</form> 
		    	</div> 
	       	</div>
	  	</div>
    </div>
</div>
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/address.js?v=version')?>"></script>
<?php include APPPATH."views/mobile/footer.php"; ?>
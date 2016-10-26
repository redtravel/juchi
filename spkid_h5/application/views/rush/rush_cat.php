<div class="plistSearch">
    <div class="plistSearchbar">
	<div class="bytype">
	    <span id="select_type" class="select_type"><?php if(isset($type_name ) && !empty($type_name ) ){ print $type_name; }else{ echo "选择品类"; } ?></span>
	    <div id="hide_type" class="hide_type" style="display:none;">
		<?php
		 if (isset($cat_content->cat) ) {?>
		    <h1><a href="/rush-<?= $args['rush_id'] ?>---<?= $args['size_id'] ?>.html">全部</a></h1>
		     <?php
		    foreach ($cat_content->cat as $key1 => $item1) {
			?>
	    		<div id="type_area" class="sec_area">
			    <?php
			    foreach ($item1 as $key2 => $item2) {
				if ($key2 == "name") {
				    ?>
			    		    <h2><a href="/rush-<?= $args['rush_id'] ?>-<?= $key1 ?>-<?= $args['sex_id'] ?>-<?= $args['size_id'] ?>--.html"><?= $item2 ?></a></h2>
					<?php
				    } else {?>
					<dl>
					<?php
					foreach ($item2 as $key3 => $item3) {
					    if ($key3 == "name") {
						?>
					    			<dt><a href="/rush-<?= $args['rush_id'] ?>-<?= $key2 ?>-<?= $args['sex_id'] ?>-<?= $args['size_id'] ?>--.html"><?= $item3 ?></a></dt>
						<?php
					    } else {
						foreach ($item3 as $key4 => $item4) {
						    if ($key4 == "name") {
							?>
							    			<dd><a href="/rush-<?= $args['rush_id'] ?>-<?= $key3 ?>-<?= $args['sex_id'] ?>-<?= $args['size_id'] ?>--.html"><?= $item4 ?></a></dd>
							<?php
						    }
						}
					    }
					}?>
					</dl>				
					<?php
				    }
				}
				?>
	    		</div>
			<?php
		    }
		}
		?>
	    </div>
	</div>
	<div class="bytype">
	    <span id="select_type" class="select_type"><?php if(isset($size_name ) && !empty($size_name ) ){ print $size_name; }else{ echo "选择尺码"; } ?></span>
	    <?php
	    if (isset($cat_content->size->data ) ) {
		?>
    	    <div id="hide_type" class="hide_type footage" style="display:none;">
		<h1><a href="/rush-<?=$args['rush_id']?>-<?=$args['type_id']?>.html">全部</a></h1>
                <?php if (isset($cat_content->size->data->male)) {?>
    		<div id="type_area" class="sec_area">
    		    <h2>男童</h2>
    		    <ul>
			    <?php
			    if (isset($cat_content->size->data->male)) {//$args["type_id"]
				$idx = 0;
				$cnt = count(get_class_vars(get_class($cat_content->size->data->male)));
				foreach ($cat_content->size->data->male as $maleid => $maleval) {
				    ?>
	    			<li><a href="/rush-<?=$args['rush_id']?>-<?=$args['type_id']?>-<?=$args['sex_id']?>-<?=$maleid?>--.html" <?= ++$idx == $cnt ? "class=\"noBg\"" : "" ?> ><?=$maleval?></a></li>
				    <?php
				}
			    }
			    ?>
    		    </ul>
    		</div>
                <?php } 
                    if (isset($cat_content->size->data->famale)) {
                ?>
    		<div id="type_area" class="sec_area">
    		    <h2>女童</h2>
    		    <ul>
			    <?php
			    if (isset($cat_content->size->data->famale)) {//$args["type_id"]
				$idx = 0;
				$cnt = count(get_class_vars(get_class($cat_content->size->data->famale) ));
				foreach ($cat_content->size->data->famale as $famaleid => $famaleval) {
				    ?>
	    			<li><a href="/rush-<?=$args['rush_id']?>-<?=$args['type_id']?>-<?=$args['sex_id']?>-<?=$famaleid?>--.html" <?= ++$idx == $cnt ? "class=\"noBg\"" : "" ?> ><?=$famaleval?></a></li>
				    <?php
				}
			    }
			    ?>
    		    </ul>
    		</div>
                <?php } ?>
    	    </div>
	    <?php } ?>
	</div>

	<div class="priceWidth">
	    	    <span style="float:left">价格</span>
	    	    <a href="/rush-<?=$args['rush_id']?>-<?=$args['type_id']?>-<?=$args['sex_id']?>-<?=$args['size_id']?>-2-.html">
	    	    	<span id="select_priceUp"  <?php if( isset($args['sort'] ) &&  $args['sort'] == 2 ){ echo 'class="jiageUp"'; }//2价格低到高 ?> ></span>
	    	    </a>
                <a href="/rush-<?=$args['rush_id']?>-<?=$args['type_id']?>-<?=$args['sex_id']?>-<?=$args['size_id']?>-1-.html">
                	<span id="select_priceDown" <?php if( isset($args['sort'] ) &&  $args['sort'] == 1 ){ echo 'class="jiageDown"'; }//1价格高到低 ?>></span>
                </a>
	</div>

    </div>
</div>
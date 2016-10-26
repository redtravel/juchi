<?php include APPPATH."views/mobile/header.php"; ?>
<div class="page cached no-toolbar">
    <!--navbar start-->
    <div class="navbar menu">
        <div class="navbar-inner">
            <div class="left"><a class="link icon-only back" href="#"> <i class="icon back"></i></a></div>
            <div class="center">品牌区</div>
        </div>
    </div>
    <!--navbar end-->
    <div class="page-content infinite-scroll public-bg">
        <div class="content-block v-brand-bottom">
            <!--brand ad 1-->
            <?php if( !empty($brand_1) )foreach($brand_1 as $bra_1){
                echo adjust_path($bra_1->ad_code);
            }?>
            <!--brand ad 2-->
            <?php if( !empty($brand_2) )foreach($brand_2 as $bra_2){
                echo adjust_path($bra_2->ad_code);
            }?>
            <!--brand ad 3-->
            <?php if( !empty($brand_3) )foreach($brand_3 as $bra_3){
                echo adjust_path($bra_3->ad_code);
            }?>
            <!--brand list-->
            <div class="edu-box1 brandlist-lb "> 
                <div class="listb03 ">
                    <ul class="brand-lb-v">
                    <?php foreach ($brand_list as $key => $b_val) { ?>
                        <li>
                           
                        <h3><?php echo $key;?></h3>
                        <div class="brand-search">
                            <?php for ($i=0; $i < count($b_val) ; $i++) { ?>
                            <a class="v_brand_radius external" href="/brand/brand_product/<?php echo explode("=",$b_val[$i])[0];?>"><?php echo explode("=",$b_val[$i])[1];?></a>
                            <?php } ?>
                        </div>
                            
                        </li>
                    <?php } ?>
                    </ul>
                    <script>
                        $$("ul li:last-child").addClass('v-brand-noline')
                    </script>
                </div>
            </div> 
        </div>
    </div>
</div>
<?php include APPPATH."views/mobile/footer.php"; ?>

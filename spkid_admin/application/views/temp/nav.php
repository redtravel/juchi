<ul id="mainMenuUl">
    <li  id="menu0" class="mainMenuOn" >
        <h3 onclick="javascript:location.href='<?= str_replace("[front]", WWW_PATH, $cats[0]["nav_url"])?>';"><?= $cats[0]["nav_name"] ?></h3>
        <div class="now"></div>
    </li>
<?php
    foreach ($cats as $key => $item) {
        if ($key == 0)            continue;
        if (isset($item["nav_name"])) {
?>
    <li id="menu<?= ($key + 0x0001) ?>" xname="<?=$item['nav_id']?>">
        <h3 <?php if (isset($item["nav_url"]) && strlen($item["nav_url"]) > 0) { ?> onclick="javascript:location.href='<?= str_replace("[front]", WWW_PATH, $item["nav_url"])?>';" <?php } ?>><?= $item["nav_name"] ?></h3>
		<?php if ($item["category_ids"]>0) { ?>
        <div id="mainMenuBox<?= ($key + 0x0001) ?>" style="display:none" class="menuSubBox">
            <div class="menuSubBoxs">
                <div class="menuSubBoxLeft">
                    <h4>正在进行的抢购</h4>
                    <ul id="menuOnNow">
<?php 
    foreach ($item["rush_data"] as $rush) {
?>
                        <li>
                            <p class="iconRedFont"> <s class="" onclick="location.href='<?= WWW_PATH . "rush-" . $rush["rush_id"] . ".html" ?>'; ">[<?= $rush["rush_tag"] ?>]</s> <a href="<?= WWW_PATH . "rush-" . $rush["rush_id"] . ".html" ?>" class="mainBrandNameA main_black" target="_self"><?= $rush["rush_index"] ?></a> </p>
                        <a class="bgSubMenuInfo" href="<?= WWW_PATH . "rush-" . $rush["rush_id"] . ".html" ?>" target="_self" ><?= $rush["rush_desc"] ?></a> 
                        </li>
<?php
    }
?>
                    </ul>
                </div>
                <div class="menuSubBoxRight">
                    <h4>分类</h4>
                    <ul id="menuOnNowKinds">
                        <li><a href="<?= isset($item["nav_url"]) && strlen($item["nav_url"]) > 0 && false  ? str_replace("[front]", WWW_PATH, $item["nav_url"]) : (WWW_PATH . "category-" . $item["category_ids"] . ".html") ?>" target="_self" class="main_darkgray_black" title="[全部]">[全部]</a></li>
<?php 
    foreach ($item["data"] as $val) {
?>
                        <li><a href="<?= WWW_PATH . "category-" . $val["type_id"] . ".html" ?>" target="_self" class="main_darkgray_black" title="<?= $val["type_name"]?>"><?= $val["type_name"]?></a></li>
<?php
    }
?>
                    </ul>
                </div>
            </div>
        </div>
		<?PHP } ?>
    </li>
<?php
        } else {
?>
    <li id="menu<?= ($key + 0x0001) ?>">
        <h3 onclick="javascript:;">按尺码选购</h3>
        <div id="sizeChoseBox" style="display:none;">
            <dl id="forMen" class="sizeSubBox">
                <dt>男童</dt>
<?php 
    foreach ($item["male_data"] as $male) {
?>
               <dd><a href="<?= WWW_PATH . "category-" . $male["parent_id"] . "-1-0-" . $male["size_id"] . ".html" ?>" target="_self" class="sizeChoseA"><?= $male["size_name"] ?></a></dd>
<?php
    }
?>
            </dl>
            <dl id="forWomen" class="sizeSubBox">
                <dt>女童</dt>
<?php 
    foreach ($item["famale_data"] as $famale) {
?>
               <dd><a href="<?= WWW_PATH . "category-" . $famale["parent_id"] . "-2-0-" . $famale["size_id"] . ".html" ?>" target="_self" class="sizeChoseA"><?= $famale["size_name"] ?></a></dd>
<?php
    }
?>
            </dl>    
        </div>
    </li>
<?php
        }
    }
?>
</ul>
<!-- SUCCESS -->
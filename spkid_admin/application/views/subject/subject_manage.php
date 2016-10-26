<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
            var module_type = $("[name=module_type]").val();
            if (module_type <= 0) {
                alert("请选择添加的模块类型！");
                return false;
            } else {
                return true;
            }
	}
	//]]>
</script>
<?php 
    $module_type_ary = array(
        4 => "自定义内容",
        6 => "单品",
        11 => "正在抢购活动",
    );
?>

<div class="main">
    <div class="main_title"><span class="l">活动专题管理 >> 管理 </span><a href="subject/index" class="return r">返回列表</a></div>
    <div class="blank5"></div>
    <div class="search_row">
        <?php print form_open_multipart('subject/proc_add_module',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
            添加模块：<select name="module_type">
                <option value="0">选择模块</option>
                <option value='4'><?=$module_type_ary[4];?></option>
                <option value='6'><?=$module_type_ary[6];?></option>
                <option value='11'><?=$module_type_ary[11];?></option>
            </select>
            位置：<select name="module_location">
                <option value="t">头</option>
                <option value='l'>左</option>
                <option value='r'>右</option>
                <option value='b'>底</option>
            </select>
            排序值：<input type="text" class="ts" name="sort_order" value="0" style="width:70px;" />
            <input type="hidden" name="subject_id" value="<?php print($row->subject_id);?>" />
            <input type="submit" class="am-btn am-btn-primary" value="添加" />
            <input type="button" class="am-btn am-btn-primary" value="生成文件" onclick="" />
        </form>
    </div>

    <div id="listDiv">
        <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
            <tr>
                <td colspan="4" class="topTd"> </td>
            </tr>
            
            <?php foreach($module_list as $module): ?>
            <tr class="row">
                <td colspan="2" style="width:50%;text-align:right;">
                    <?php print($module->module_title);?> [ <?php print $module_type_ary[$module->module_type]; ?> ] &nbsp;
                </td>
                <td colspan="2" style="width:50%;text-align:left;">
                    <a class="edit" href="subject/edit_module/<?php print $module->module_id; ?>" title="编辑"></a>
                    <a class="del" href="subject/delete_module/<?php print $module->module_id; ?>" title="删除"></a>
                    排序值：<?php print($module->sort_order);?>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <tr>
                <td colspan="4" class="bottomTd"> </td>
            </tr>
        </table>
    </div>
    <div class="blank5"></div>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
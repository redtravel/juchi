<style type="text/css">
    .input_color,.input_size{width:90px; border:0; border-bottom: solid red 1px;}
    .input_size{border-bottom: solid green 1px;}
</style>
<form id="item_fm" method="post" novalidate>
    <table>
        <tr>
            <td>商品名称:</td>
            <td colspan="3">
                <input class="easyui-validatebox" type="text" name="title" data-options="required:true" value="<?php print $item->title;?>" size="60"></input>
                <a href="http://detail.tmall.com/item.htm?&id=<?php print $item->num_iid;?>" target="_blank">天猫链接</a>;
            </td>
        </tr>
        <tr>
            <td>天猫成本价:</td>
            <td>
                <input class="easyui-validatebox" type="text" name="tmall_price" data-options="required:true" value="<?php print $item->tmall_price;?>" size="20" onblur="calc_shop_price();"/>
            </td>
            <td>专柜价:</td>
            <td>
                <input class="easyui-validatebox" type="text" name="reserve_price" data-options="required:true" value="<?php print $item->reserve_price;?>" size="20"></input>
            </td>
        </tr>
        <tr>
            <td>站内售价:</td>
            <td>
                <input class="easyui-validatebox" type="text" name="shop_price" data-options="required:true" value="<?php print $item->shop_price;?>" size="20"></input>
                建议零售价：<span style="color:blue; cursor: pointer;" id="sugguest_price" onclick="set_shop_price();">计算中...</span>
            </td>
            <td></td>
            <td>
            </td>
        </tr>
        <tr>
            <td>供应商:</td>
            <td colspan="2">
                <?php print form_dropdown('provider_id', get_pair($all_provider,'provider_id','provider_name'),$item->provider_id);?>
                (卖家昵称：<?php print $item->sync_data['nick']?>)
            </td>
            <td rowspan="5">
                <image src="<?php print $item->image?>_180x180.jpg"/>
            </td>
        </tr>
        <tr>
            <td>品牌:</td>
            <td colspan="2">
                <?php print form_dropdown('brand_id', get_pair($all_brand,'brand_id','brand_name'),$item->brand_id);?>
                (天猫品牌：<?php print $item->sync_data['brand_name']?>)
            </td>
        </tr>
        <tr>
            <td>前台分类:</td>
            <td colspan="2">
                <?php print form_product_type('category_id', $all_type, $item->category_id, '', array(''=>'请选择'));?>
                (天猫分类：<?php print $item->sync_data['category']?>)
            </td>
        </tr>
        <tr>
            <td>性别:</td>
            <td colspan="2">
                <label><?php print form_radio('sex', 1, $item->sex==1)?>男</label>
                <label><?php print form_radio('sex', 2, $item->sex==2)?>女</label>
                <label><?php print form_radio('sex', 3, $item->sex==3)?>中性</label>
            </td>
        </tr>
        <tr>
            <td>SKU:</td>
            <td colspan="2" id="td_sku">
                <input type="hidden" name="sku_alias"/>
                <input type="hidden" name="sku_del"/>
                <script type="text/javascript">
                    var sku_alias = <?php print json_encode((object)$item->sku_alias);?>;
                    var sku_del = <?php print json_encode($item->sku_del);?>;
                    $(function(){
                        calc_shop_price();
                    });
                    /**
                     * 删除颜色
                     * @param {type} color_code
                     * @returns {undefined}
                     */
                    function del_color(color_code)
                    {
                        var container = $('div[class=color][id="'+color_code+'"]');
                        $('.sku', container).each(function(i){
                            var sku_id = $(this).attr('id');
                            del_sku(sku_id)
                        });
                        container.remove();
                    }
                    
                    /**
                     * 删除尺码
                     * @param {type} sku_id
                     * @returns {undefined}
                     */
                    function del_size(sku_id)
                    {
                        var container = $('div[class=sku][id="'+sku_id+'"]');
                        var parent_container = container.parent();
                        del_sku(sku_id);
                        container.remove();
                        if(parent_container.find('.sku').length==0){
                            parent_container.remove();
                        }
                    }
                    
                    /**
                     * 删除SKU
                     * @param {type} sku_id
                     * @returns {undefined}
                     */
                    function del_sku(sku_id)
                    {
                        var deleted = false;
                        for(i in sku_del){
                            if(sku_del[i]==sku_id){
                                deleted=true;
                            }
                        }
                        if(!deleted){
                            sku_del.push(sku_id);
                        }
                    }
                    
                    function alias_color(obj)
                    {
                        var exists = false;
                        var obj = $(obj);
                        var color_name = 'color_'+obj.attr('data');
                        var color_alias = $.trim(obj.val());
                        for(i in sku_alias){console.log(i);
                            if(i==color_name){
                                sku_alias[i] = color_alias;
                                exists = true;
                                break;
                            }
                        }console.log(color_name);
                        if(!exists){
                            sku_alias[color_name] = color_alias;
                        }
                    }
                    
                    function alias_size(obj)
                    {
                        var exists = false;
                        var obj = $(obj);
                        var size_name = 'size_'+obj.attr('data');
                        var size_alias = $.trim(obj.val());
                        for(i in sku_alias){
                            if(i==size_name){
                                sku_alias[i] = size_alias;
                                exists = true;
                                break;
                            }
                        }
                        if(!exists){
                            sku_alias[size_name] = size_alias;
                        }
                        $('input.input_size', $('#td_sku')).each(function(i){
                            if('size_'+ $(this).attr('data') ==size_name ){
                                $(this).val(size_alias);
                            }
                        });
                    }
                    
                    /**
                     * 计算本店售价
                     * @returns {undefined}
                     */
                    function calc_shop_price()
                    {
                        var tmall_price = parseFloat($('input[name=tmall_price]').val())||0;
                        var shop_price = Math.min(Math.round(tmall_price*1.25), Math.round(tmall_price+40));
                        $('#sugguest_price').html(shop_price);
                    }
                    
                    /**
                     * 设置本店售价
                     * @returns {undefined}
                     */
                    function set_shop_price()
                    {
                        $('input[name=shop_price]').val($('#sugguest_price').html());
                    }
                </script>
                <?php foreach ($item->skus as $color): ?>
                    <div class="color" id="<?php print $color['color_code']; ?>">
                        <input type="text" data="<?php print $color['color_name']; ?>" value="<?php print $color['color_alias']; ?>" class="input_color" onblur="alias_color(this);"/>
                        <?php if ($color['color_img']): ?>
                            <img style="display: inline-block" src="<?php print $color['color_img'] ?>_30x30.jpg"/>
                        <?php endif; ?>
                            <span style="cursor: pointer" onclick="javascript:del_color('<?php print $color['color_code']; ?>');">删除</span>
                            <br/>
                        <?php foreach ($color['size_list'] as $size): ?>
                            <div style="margin-right:8px; display: inline-block" class="sku" id="<?php print $size['sku_id'];?>">
                                <input type="text" data="<?php print $size['size_name']; ?>" value="<?php print $size['size_alias'];?>" class="input_size" onblur="alias_size(this);" />
                                <span style="color:#00A707">[<?php print $size['stock'] ?>]</span>
                                <span style="cursor: pointer" onclick="javascript:del_size('<?php print $size['sku_id'];?>');">删除</span>
                            </div>
                        <?php endforeach; ?>

                    </div>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <td colspan="4">商品描述:</td>
        </tr>
        <tr>
            <td colspan="4">
                <?php print $this->ckeditor->editor('desc',$item->desc); ?>
            </td>
    </table>
</form>
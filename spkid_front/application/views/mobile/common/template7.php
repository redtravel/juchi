
<!-- template user's profile -->
<script type="text/template7" id="userTemplate">

<div class="personal-center clearfix">      
      <a href="/user/customers_center" class="customer-service external"></a>
      <a href="" class="personal-center-xx open-popover" data-popover=".popover-advar"><img src="<?php echo static_url('mobile/touxiang/{{advar}}')?>"></a>
      <a href="/user/profile" class="hu-bianji external"></a>
</div>
<div style="text-align:center; padding-top:10px;">{{user_name}}</div>

</script>

<!-- product -->
<script type="text/template7" id="infiniteProductTemplate">
{{#each data}}
<li>
    <div class="products-list clearfix">
    <a href="/pdetail-{{product_id}}.html" class="external">
    <div class="img_sbox"><img class="lazy lazy-fadeIn" data-src="{{../img_domain}}/{{img_url}}.418x418.jpg"></div>
    <div class="prod_name">{{brand_name}}&nbsp;{{product_name}}</div> 
    <div class="bline clearfix">
    <div class="favoheart">{{pv_num}}</div>
    <div class="price_bar {{#js_compare "this.price_show=='1'"}}xunjia_product{{/js_compare}}"><span class="prod_pprice">{{#js_compare "this.price_show=='1'"}}询价{{/js_compare}}{{#js_compare "this.price_show=='0'"}}{{shop_price}}{{/js_compare}}</span></div>
    </div>
    </a>
    {{#js_compare "this.is_hot=='1'"}} <div class="mark mark_sale">热品</div> {{/js_compare}}
    {{#js_compare "this.is_new=='1'"}} <div class="mark mark_new">新品</div> {{/js_compare}}
    {{#js_compare "this.is_offcode=='1'"}} <div class="mark mark_offcode">促销</div> {{/js_compare}}
    {{#js_compare "this.is_zhanpin=='1'"}} <div class="mark mark_show">展品</div> {{/js_compare}}
    </div>
</li>
{{/each}}
</script>

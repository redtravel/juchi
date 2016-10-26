
<!-- template user's profile -->
<script type="text/template7" id="userTemplate">

<div class="personal-center clearfix">      
      <a href="/user/customers_center" class="customer-service external"></a>
      <a href="" class="personal-center-xx open-popover" data-popover=".popover-advar"><img src="<?php echo static_url('mobile/touxiang/{{advar}}')?>"></a>
      <a href="/user/profile" class="hu-bianji external"></a>
</div>
<div style="text-align:center; padding-top:10px; color:#fff;">{{user_name}}</div>

</script>

<!-- product -->
<script type="text/template7" id="infiniteProductTemplate">
{{#each data}}
<li class="bdr-r">
    <div class="products-list clearfix">
    <a href="/pdetail-{{product_id}}.html" class="external">
    <div class="img_sbox"><img class="lazy" data-src="{{../img_domain}}/{{img_url}}.418x418.jpg"></div>
    <div class="prod_name"><span>{{brand_name}}</span>{{product_name}}</div> 
    <div class="bline clearfix">
    <div class="favoheart">{{pv_num}}关注</div>
    <div class="price_bar {{#js_compare "this.price_show=='1'"}}xunjia_product{{/js_compare}}">
    <span class="prod_pprice">{{#js_compare "this.price_show=='1'"}}询价{{/js_compare}}{{#js_compare "this.price_show=='0'"}}{{shop_price}}{{/js_compare}}</span>
    </div>
    </div>
    </a>
   
    </div>
</li>
{{/each}}
</script>

<!-- product -->
<script type="text/template7" id="infiniteSearchTemplate">
{{#each data}}
<a class="external" href="/pdetail-{{product_id}}">
    <dl class="search-list-half clearfix">
        <dt>
            <img class="lazy lazy-fadeIn" data-src="{{../img_domain}}/{{img_url}}.418x418.jpg">
        </dt>
        <dd>
        <div class="product-name-box">
             <div class="product-search-name">{{brand_name}}&nbsp;{{product_name}}</div>
        </div>
        <span class="product-price">¥
            {{#js_compare "this.price_show=='1'"}}
            <span class="big-price">询价</span>
            {{/js_compare}}
                    
            {{#js_compare "this.price_show=='0'"}}
            <span class="big-price">{{product_price1}}</span><span class="small-price">.{{product_price2}}</span>
            {{/js_compare}}
        </span>
        <div class="search-list-praise"><span class="haoping">浏览量{{pv_num}}<em>{{pj_real_num}}条评价</em></span></div>
        </dd>
    </dl>
</a>
{{/each}}
</script>

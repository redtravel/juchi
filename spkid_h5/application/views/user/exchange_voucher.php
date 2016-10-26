<?php include APPPATH . "views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<script type="text/javascript">

    function exchange(release_id) {
        if (!confirm('积分兑换后不可逆，确认兑换此面值？'))
            return false;
        $.ajax({
            url: '/user/proc_exchange_voucher',
            data: {release_id: release_id, rnd: new Date().getTime(0)},
            dataType: 'json',
            type: 'POST',
            success: function(result) {
                if (result.msg)
                    alert(result.msg);
                if (result.err)
                    return false;

                if (result.voucher_sn) {
                    alert('兑换成功，券号：' + result.voucher_sn +"，请到\"我的现金券\"查看！");
                }
            }
        });
    }
</script>
<div id="content">
    <div class="now_pos">
        <a href="/">首 页</a>
        >
        <a href="/user">会员中心</a>
        >
        <a class="now">积分兑换</a>
    </div>
    <div class="ucenter_left">
        <?php include APPPATH . "views/user/left.php"; ?>
    </div>
    <div class="ucenter_main">
        <div class="switch_block" id="listdiv" style="margin-top:15px;">			
            <div class="tip_top">
                <div class="bold">当前积分:</div>
                <div class="cred_b"><?php print $user->pay_points;?>分</div>
            </div>
            <div class="switch_block_title">
                <ul>
                    <li class="sel">积分兑换</li>

                </ul>
            </div>
            <div class="switch_block_content">
                <table id="tbSwitch" width="748" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <th width="25%">券值</th>
                            <th width="25%">所需积分</th>
                            <th width="25%">使用范围</th>
                            <th width="25%">操作</th>

                        </tr>
                        <tr>
                            <td width="25%">满100立减5元</td>
                            <td width="25%" class="levelPoint">600积分</td>
                            <td width="25%">全场通用</td>
                            <td width="25%"><b class="btn_g_75" onclick="exchange(1)">兑换</b></td>
                        </tr>
                        <tr>
                            <td width="25%">满180立减10元</td>
                            <td width="25%" class="levelPoint">1000积分</td>
                            <td width="25%">全场通用</td>
                            <td width="25%"><b class="btn_g_75" onclick="exchange(2)">兑换</b></td>
                        </tr>
                        <tr>
                            <td width="25%">满350立减20元</td>
                            <td width="25%" class="levelPoint">1800积分</td>
                            <td width="25%">全场通用</td>
                            <td width="25%"><b class="btn_g_75" onclick="exchange(3)">兑换</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="integral_rules">
                <h2>【送积分】完善您的个人资料、手机或邮箱验证，立即获赠更多积分 <a class="und" href="/user/profile" target="_blank">[ 点击进入 ]</a></h2>
                积分兑换现金券使用规则：<br>
                1.会员积分兑换成现金券后，该现金券直接绑定会员账户，现金券不可退回为积分。<br>
                2.会员积分在兑换后减少，积分减少不影响会员等级。<br>
                3.购物确认订单时，选择使用现金券，立即享受抵用优惠！<br>
                4.单笔订单限使用一张现金券，不可累积使用。现金券不兑现，不找零。<br>
                5.现金券抵用部分的金额不开具发票（若使用现金券后，实付金额为0元，则不开具发票）。<br>
                6.积分现金券的适用范围请详见该券说明（部分特殊商品不可使用现金券）。
            </div>
        </div>

    </div>
</div>
<?php include APPPATH . 'views/common/footer.php'; ?>

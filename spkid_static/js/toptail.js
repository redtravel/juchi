// 购物车弹窗显示

FS.hover(FS('#mainCartLink')[0],function(){
    if ('' == $("#mainCartBox").html()) {
        $.ajax({
            url: '/cart/info',
            type: 'POST',
            dataType: 'json',
            success: function(result) {
                if(result.msg) alert(result.msg);
                if(result.err) return false;
                $("#mainCartBox").html(result.html);
                FS.show(FS('#mainCartBox')[0]);
            }
        });
    } else {
	FS.show(FS('#mainCartBox')[0]);
    }
},function(){
	FS.hide(FS('#mainCartBox')[0]);
})
//FS.show(FS('#mainCartBox')[0]);
FS.addEvent(FS('#mainCartLink')[0],'mouseenter',function(){
	//FS.hide(FS('#mainCartBoxNull')[0]);
	FS('#mainCartBoxNull')[0].style.display='none';
});
//已登录会员用户中心弹窗显示
FS.addEvent(FS('#userInfobox')[0], 'mouseenter',function(){
	FS.show(FS('.infoPersonalBox')[0]);
});
FS.addEvent(FS('#userInfobox')[0], 'mouseleave',function(){
	FS.hide(FS('.infoPersonalBox')[0]);
});
//导航栏
FS.nav({
            activeTab: 'mainMenuOn',
            activeDiv: 'now',
            parentNode: '#mainMenuUl'
        })
//文本框内容交互
FS.toogleText('#msgOrderInputBoxText');
try{
	FS.addEvent(FS('#mainFOrder')[0], 'click', function(){
	alert('open window');

	})
}
catch(ex){
	alert(ex);
}

//邮件短信订阅内容
/*底部订阅信息 ajax方法*/
    FS.addEvent(FS('#msgOrderInputBoxBtn')[0], 'click', function() {
        var inputText = FS('#msgOrderInputBoxText')[0].value;
        if (inputText.isMobile() || inputText.isEmail()) {
            var params = '-1';
            if (inputText != '' && inputText.indexOf('@') == -1) params += '/mobile/' + inputText;
            if (inputText != '' && inputText.indexOf('@') != -1) params += '/email/' + inputText;
            FS.ajax({
                method: 'POST',
                url: "/rush/notice/rushNotice/" + params,
                async: true,
                timeout: 30000,
                success: function(result) {
                    FS.popUp({
                        popUpId: '#orderMainSucMsg',
                        closeId: '#closeMainSucMsgWindow',
                        maskId: '#maskid',
                        clickFlug: false,
                        targetFun: function() {
                            if (result > 0 && result < 6) {
                                FS.successPropTips({
                                    title: '订阅成功',
                                    msg: '成功订阅每日限抢信息！',
                                    tip: '温馨提示：您已经成功订阅了开场通知，我们将在活动开场前通知您。',
                                    className: 'iconMsgSuc',
                                    noFixed: true
                                })
                            } else if (result == 6) {
                                FS.successPropTips({
                                    title: '订阅成功',
                                    msg: '成功订阅每日限抢信息！',
                                    tip: '温馨提示：您已经成功订阅了开场通知，无须重复订阅。',
                                    className: 'iconMsgSuc',
                                    noFixed: true
                                })
                            } else {
                                FS.successPropTips({
                                    title: '订阅失败',
                                    msg: '请在线咨询客服人员。',
                                    tip: '',
                                    className: 'iconMsgFal',
                                    noFixed: true
                                })
                            }

                        }
                    })
                }
            });
        } else {
            FS.popUp({
                popUpId: '#orderMainSucMsg',
                closeId: '#closeMainSucMsgWindow',
                maskId: '#maskid',
                clickFlug: false,
                targetFun: function() {
                    FS.successPropTips({
                        title: '提示',
                        msg: '请输入正确的手机号或者邮箱地址!',
                        tip: '',
                        className: 'iconMsgFal',
                        noFixed: true
                    })
                }
            });
            return false;
        }
    });

    /*底部取消订阅信息 ajax方法*/
    FS.addEvent(FS('#mainBtnCancel')[0], 'click', function() {
        var textMobile = FS('.cancelOrderInput')[0].value;
        var textEmail = FS('.cancelOrderInput')[1].value;
        var params = '-1';
        var emailFlag = textEmail.isEmail();
        var mobileFlag = textMobile.isMobile();
        FS.popUp({
            popUpId: '#orderMainSucMsg',
            closeId: '#closeMainSucMsgWindow',
            maskId: '#maskid',
            clickFlug: false,
            noPopup: true,
            beforeFun: function() {
                FS.hide(FS("#order_msg")[0]);
            },
            targetFun: function() {
                if (emailFlag == false && mobileFlag == false) {
                    FS.successPropTips({
                        title: '提示',
                        msg: '请输入正确的手机号或邮箱地址!',
                        tip: '',
                        className: 'mainIconMsgWarm',
                        noFixed: true
                    });
                    return false;
                } else {
                    if (mobileFlag == false && textMobile != "请输入手机号码") {
                        FS.successPropTips({
                            title: '提示',
                            msg: '请输入正确的手机号或邮箱地址!',
                            tip: '',
                            className: 'mainIconMsgWarm',
                            noFixed: true
                        });
                        return false;
                    }
                    if (emailFlag == false && textEmail != "请输入您的邮箱") {
                        FS.successPropTips({
                            title: '提示',
                            msg: '请输入正确的手机号或邮箱地址!',
                            tip: '',
                            className: 'mainIconMsgWarm',
                            noFixed: true
                        });
                        return false;
                    }
                    params += '/mobile/' + textMobile + '/email/' + textEmail;
                }
                FS.ajax({
                    method: 'POST',
                    url: "/rush/notice/cancelrushNotice/" + params,
                    async: true,
                    success: function(result) {
                        if (result == 1) {
                            FS.successPropTips({
                                title: '取消订阅',
                                msg: '成功取消订阅!',
                                tip: '温馨提示：如果需要重新订阅请在订阅处留下手机或邮箱。',
                                className: 'iconMsgSuc',
                                noFixed: true
                            });
                        }
                    }
                });
            }
        })


    }); /*关闭弹出层*/
    FS.addEvent(FS.query("#closeMainSucMsgWindow"), 'click', function() {
        FS.hide(FS('#orderMainSucMsg')[0]);
        var bool = FS.getStyle(FS('#maskid')[0], 'display');
        if (bool !== 'none') FS.hide(FS('#maskid')[0]);
    });
    /*信息订阅弹窗
    
   /*点击取消订阅 弹出弹窗*/
    if (FS.query('#order_msg') !== null && FS.query('#order_cancel') !== null) {
        FS.popUp({
            eventTarget: '#order_cancel',
            popUpId: '#order_msg',
            closeId: '#closeOrderMsgWindow',
            maskId: '#maskid'
        });
    }

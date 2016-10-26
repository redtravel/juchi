/** 
 * FSL (Feishang Javascript Library) 
 * Copyright (c) 2012, All rights reserved.
 *
 * @fileOverview Feishang Javascript Library components
 * @version 1.0
 * @author <a href="mailto:zeng.xianghu@hotmail.com">Hoogle</a>、<a href="http://weibo.com/yinuoba">新浪微博(一诺吧)</a>)
 * 
 * @description tab选项卡
 */

FS.extend(FS, function () {
    /**
     * @extends tab(options) tab选项卡
     * @description tab选项卡功能
     * @param {Object} options 参数对象
     * @param {String} options.divActive 激活状态下tab对应内容的class样式
     * @param {String} options.active 激活状态下tab项的class样式
     * @param {String} options.tabParent tab项的父节点的选择器
     * @param {String} options.contentParent tab项对应的内容div的父节点选择器
     * @param {String} options.eventType 让tab工作的事件类型，默认是'mouseenter'
     * @param {String} options.eventType 让tab工作的事件类型，默认是'mouseenter'
     * @param {Function} [options.enterFun] 鼠标移到tab上时的回调函数，参数为当前tab对应的content及当前tab
     * 
     * @example FS.tab({
             divActive : 'nowNews',
             active : 'dynamic_active',
             tabParent : '#dynamic_btn',
             contentParent : '#msg_lists',
             eventType : 'click',
             enterFun: function(a,b){
                // a：当前tab对应的content
                // b: 当前tab
                console.info(a,b);
             }
        });
     *
     */
    var tab = function (options) {
        // tab的事件类型，主要考虑到有的tab是mouseenter触发，有的是click出发
        var tabParent = options.tabParent,
            contentParent = options.contentParent,
            eventType = options.eventType,
            enterFun = options.enterFun;

        // 取得tab项及其对应的内容项节点
        var tabList = FS.getChildNodes(FS(tabParent)[0]),
            tabContents = FS.getChildNodes(FS(contentParent)[0]);
        // 取得tab的个数，tab的个数与其对应的内容div的个数相等
        var length = tabList.length;
        if(length>1){
            for (var i = 0; i < length; i++) {
                // 如果定义了tab的事件类型，则按指定的事件类型出发
                if (eventType !== undefined) {
                    FS.addEvent(tabList[i], eventType, tabCore.bind(this, {
                        divActive: options.divActive,
                        active: options.active,
                        tabParent: options.tabParent,
                        contentParent: options.contentParent,
                        currentNode: tabList[i],
                        currentDiv: tabContents[i],
                        dynamic_hover: options.dynamic_hover,
                        enterFun: options.enterFun
                    }));
                } else {
                    // 默认是mouseenter事件触发tab
                    FS.addEvent(tabList[i], 'mouseenter', tabCore.bind(this, {
                        divActive: options.divActive,
                        active: options.active,
                        tabParent: options.tabParent,
                        contentParent: options.contentParent,
                        currentNode: tabList[i],
                        currentDiv: tabContents[i],
                        enterFun: options.enterFun
                    }))
                }
            }
        }
    };

    /**
     * 处理tab的逻辑
     */
    function tabCore(options) {
        // 将参数定义为局部变量
        var currentNode = options.currentNode,
            currentDiv = options.currentDiv,
            currentClass = options.active,
            divActiveClass = options.divActive,
            tabParent = options.tabParent,
            dynamic_hover = options.dynamic_hover,
            enterFun = options.enterFun;

        // 如果当前tab不处于激活状态
        if (!FS.hasClass(currentNode, currentClass)) {
            // 找出出于激活状态的节点并且remove掉
			
            FS.removeClass(FS('.' + currentClass)[0], currentClass);
			//alert(FS('.' + divActiveClass)[0]);
			//alert(divActiveClass);
            FS.removeClass(FS('.' + divActiveClass)[0], divActiveClass);
            FS.addClass(currentNode, currentClass);
            FS.addClass(currentDiv, divActiveClass);
            var pic=FS('img', FS.query(currentDiv));
            for(var i=0; i<pic.length; i++){
                if(FS.attr(pic[i],'psrc')){
                    pic[i].setAttribute('src',FS.attr(pic[i],'psrc'));
                    
                }
            }
            // 鼠标放到tab上，执行回调函数,参数为当前tab对应的content及当前tab
            enterFun(currentDiv,currentNode);
        } else {
            // 鼠标放到tab上，执行回调函数，参数为当前tab对应的content及当前tab
            enterFun(currentDiv,currentNode);
            // 如果当前tab就已经出于激活状态
            return
        }
    };
    

    return {
        tab: tab
    }
}());
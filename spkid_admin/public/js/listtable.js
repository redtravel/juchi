var listTable = new Object;

listTable.query = "query";
listTable.filter = new Object;
listTable.url = '';

/**
 * 切换排序方式
 */
listTable.sort = function(sort_by, sort_order)
{
    var args = {
        is_ajax : 1,
        sort_by:sort_by,
        rnd : new Date().getTime()
    };      
    if (this.filter.sort_by == sort_by)
    {
        args['sort_order'] = this.filter.sort_order == "DESC" ? "ASC" : "DESC";
    }
    else
    {
        args['sort_order'] = "DESC";
    }

    for (var i in this.filter)
    {
        if (typeof(this.filter[i]) != "function" && i != "sort_order" && i != "sort_by")
        {
            args[i] = this.filter[i];
        }
    }

    this.filter['page_size'] = this.getPageSize();
    $.ajax({
        url: this.url,
        data:args,
        dataType:'json',
        type:'POST',
        success:this.listCallback
    });
}

/**
 * 翻页
 */
listTable.gotoPage = function(page)
{
    if(listTable.pager_confirm){
        if(!confirm(listTable.pager_confirm)){
            return false;
        }
    }
    $('span#pager_status').css('display','');
    if (page != null) this.filter['page'] = page;

    if (this.filter['page'] > this.filter.page_count) this.filter['page'] = 1;

    this.filter['page_size'] = this.getPageSize();

    this.loadList();
}

/**
 * 载入列表
 */
listTable.loadList = function()
{
    if(window.parent.window.frames['top-frame'] != undefined){
        var ajax_loader = $(window.parent.window.frames['top-frame'].document).find('#ajax-loader');
        if(ajax_loader.length>0){
            ajax_loader.css('display', '');
        }
    }
    
    var args = {
        is_ajax:1,
        rnd : new Date().getTime()
    };
    args = this.compileFilter(args);
    $.ajax({
        url: this.url,
        data: args,
        dataType: 'json',
        type: 'POST',
        success: this.listCallback
    });
}


listTable.gotoPageFirst = function()
{
    if (this.filter.page > 1)
    {
        listTable.gotoPage(1);
    }
}

listTable.gotoPagePrev = function()
{
    if (this.filter.page > 1)
    {
        listTable.gotoPage(this.filter.page - 1);
    }
}

listTable.gotoPageNext = function()
{
    if (this.filter.page < listTable.filter.page_count)
    {
        listTable.gotoPage(parseInt(this.filter.page) + 1);
    }
}

listTable.gotoPageLast = function()
{
    if (this.filter.page < listTable.filter.page_count)
    {
        listTable.gotoPage(listTable.filter.page_count);
    }
}

listTable.changePageSize = function(e)
{
    var evt = ((typeof e == "undefined") ? window.event : e);
    if (evt.keyCode == 13)
    {
        listTable.gotoPage();
        return false;
    };
}

listTable.listCallback = function(result)
{
    if(window.parent.window.frames['top-frame'] != undefined){
        var ajax_loader = $(window.parent.window.frames['top-frame'].document).find('#ajax-loader');
        if(ajax_loader.length>0){
            ajax_loader.css('display', 'none');
        }
    }
    
    if (result.error > 0)
    {
        alert(result.message);
    }
    else
    {
        try
        {
            document.getElementById('listDiv').innerHTML = result.content;

            if (typeof result.filter == "object")
            {
                listTable.filter = result.filter;
            }

            if (typeof listTable.func == "function")
            {
                listTable.func();
            }
        }
        catch (e)
        {
            alert(e.message);
        }
    }
}

listTable.selectAll = function(obj, chk)
{
    if (chk == null)
    {
        chk = 'checkboxes';
    }

    var elems = obj.form.getElementsByTagName("INPUT");

    for (var i=0; i < elems.length; i++)
    {
        if (elems[i].name == chk || elems[i].name == chk + "[]")
        {
            elems[i].checked = obj.checked;
        }
    }
}

listTable.compileFilter = function(args)
{
    for (var i in this.filter)
    {
        if (typeof(this.filter[i]) != "function" && typeof(this.filter[i]) != "undefined" && this.filter[i]!='')
        {
            args[i] = this.filter[i];
        }
    }

    return args;
}

listTable.getPageSize = function()
{
    var ps = 20;

    var pageSize = document.getElementById("pageSize");

    if (pageSize)
    {
        ps = /\D+/.test(pageSize.value) ? 20 : pageSize.value ;
    }
    return ps;
}

listTable.toggle = function(obj,act,field,id,yes_exp,no_exp)
{
    //if (!confirm('确定执行该操作？')) {return false;};
    var args = {
        is_ajax:1,
        field : field,
        id : id,
        yes_exp : yes_exp,
        no_exp : no_exp,
        rnd : new Date().getTime()
    };
    args = this.compileFilter(args);
    $.ajax({
        url: act,
        data: args,
        dataType: 'json',
        type: 'POST',
        success: function(result){
            if (result.msg) {alert(result.msg)};
            if (result.err == 0) {$(obj).html(result.content)};
//	    改为样式显示By Rock
//			if (result.err == 0) {$(obj).html('<span class="'+result.content+'"></span>')};
        }
    });
}

listTable.edit = function(obj, act, field, id)
{
  var tag = obj.firstChild.tagName;

  if (typeof(tag) != "undefined" && tag.toLowerCase() == "input")
  {
    return;
  }


  var org = obj.innerHTML;
  var val = Browser.isIE ? obj.innerText : obj.textContent;


  var txt = document.createElement("INPUT");
  txt.value = (val == 'N/A') ? '' : val;
  txt.style.width = (obj.offsetWidth + 12) + "px" ;


  obj.innerHTML = "";
  obj.appendChild(txt);
  txt.focus();


  txt.onkeypress = function(e)
  {
    var evt = Utils.fixEvent(e);
    var obj = Utils.srcElement(e);

    if (evt.keyCode == 13)
    {
      obj.blur();

      return false;
    }

    if (evt.keyCode == 27)
    {
      obj.parentNode.innerHTML = org;
    }
  }


  txt.onblur = function(e)
  {
    if (true|Utils.trim(txt.value).length > 0)
    {
        var args = {
            is_ajax:1,
            field : field,
            id : id,
            val : Utils.trim(txt.value),
            rnd : new Date().getTime()
        };
        args = listTable.compileFilter(args);
        $.ajax({
            url: act,
            data: args,
            dataType: 'json',
            type: 'POST',
            success: function(result){
                if (result.msg) {alert(result.msg)};
                obj.innerHTML = (result.err == 0) ? result.content : org;
            }
        }); 
    }
    else
    {
      obj.innerHTML = org;
    }
  }
}

$(function () {
    $('.dataTable tr').hover(function () {
        $(this).addClass('bgHover');
    },function () {
        $(this).removeClass('bgHover');
    });
});
/* $Id : region.js 4865 2007-01-31 14:04:10Z paulgao $ */

var region = new Object();

region.loadRegions = function(parent, target)
{
  $.ajax({
    url:'region/search',
    data:{parent:parent,target:target,rnd:new Date().getTime()},
    dataType:'json',
    type:'POST',
    success:region.response
  });
}


/* *
 * 处理下拉列表改变的函数
 *
 * @obj     object  下拉列表
 * @type    integer 类型
 * @selName string  目标列表框的名称
 */
region.changed = function(obj, selName)
{
  var parent = obj.options[obj.selectedIndex].value;
  if(parent==0){
    region.response({target:selName,regions:{}});
    return false;
  }
  region.loadRegions(parent, selName);
}

region.response = function(result)
{
  var sel = document.getElementById(result.target);

  sel.length = 1;
  sel.selectedIndex = 0;
  
  if (document.all)
  {
    sel.fireEvent("onchange");
  }
  else
  {
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent('change', true, true);
    sel.dispatchEvent(evt);
  }

  if (result.regions)
  {
    for (i = 0; i < result.regions.length; i ++ )
    {
      var opt = document.createElement("OPTION");
      opt.value = result.regions[i].region_id;
      opt.text  = result.regions[i].region_name;

      sel.options.add(opt);
    }
  }
}


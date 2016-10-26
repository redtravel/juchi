<?php include(APPPATH.'views/common/header.php'); ?>
<script>
//var   moOBJ   =   dialogArguments;
var v_target = '<?=$target?>';
function gotoLogin()
{
    var packetsel = document.getElementById("packetsel");
    var packetselArr = packetsel.value.split("::::");
    //moOBJ.pass=true;
    //moOBJ.packet_id=packetselArr[0];
    //moOBJ.packet_name=packetselArr[1];
    //window.opener.document.getElementById('packet_id').value = packetselArr[0];
    window.opener.document.getElementById(v_target).value = packetselArr[1];
    window.close();
}
</script>
	<div style="padding-left: 25px">
  <?php print form_dropdown('packetsel',$location_list,'',' id="packetsel" size="15" style="width:130px;" ondblclick="javascript:gotoLogin();"');?>
  </div><BR><BR><BR>
  <div   align=center>
  <input type="button"  value="确定"  class="am-btn am-btn-secondary" onclick="gotoLogin();" />
  <input type="button"  value="取消"  class="am-btn am-btn-secondary" onclick="window.close();" />

  </div>
</body>
</html>

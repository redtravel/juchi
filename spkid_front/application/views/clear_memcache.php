<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function check(){
    window.location.href = '/clear/todo/1/' + document.getElementById('key').value;
}
function del(){
    window.location.href = '/clear/todo/2/' + document.getElementById('key').value;
}
</script>
MemCache key: 
<select id="key" name="key">
    <? foreach($options as $key => $val){ ?>
    <? if($getkey == $val){?>
    <option value=<?=$val;?> selected><?=$val;?></option>
    <? }else{ ?>
    <option value=<?=$val;?>><?=$val;?></option>
    <? }} ?>
</select>
<input type="submit" name="act" onclick="check()" value="check"/>
<input type="submit" name="act" onclick="del()" value="del"/>

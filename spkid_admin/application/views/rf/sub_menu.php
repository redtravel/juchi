<? 
$i=0;
foreach($sub_menu['action_list'] as $action):?>
<label class="half_row">
<? echo(++$i).'.';?> <a href="/<?=$action['url']?>" target="_self"><?=$action['name']?></a>
</label>
<?endforeach;?>

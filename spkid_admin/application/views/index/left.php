<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php print base_url();?>"></base>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="public/style/style.css">
<script type="text/javascript" src="public/js/jquery.js"></script>
<script type="text/javascript" src="public/js/left.js"></script>
<title></title>
</head>

<body style="scrollbar-face-color:#F0F9E8;scrollbar-highlight-color:#F2F2F0;scrollbar-3dlight-color:#EEF2E4;scrollbar-darkshadow-color:#EEF2E4;
scrollbar-Shadow-color:#BCBEBB;scrollbar-arrow-color:#BDBBBE;scrollbar-track-color:#F0F9E8;">
<div class="left">
	<?php foreach ($admin_menu as $group): ?>
		<dl>
          <dt class="navt_u" title="<?php echo $group->menu_name.'['.$group->action_id.']';?>"><?php print $group->menu_name; ?></dt>
	      <?php foreach ($group->sub_items as $item): ?>
	      <dd><a href="<?php print $item->url; ?>" target="<?php if(in_array($item->action_code, array('menu_purchasebox_scanning','menu_exchange_location'))):?>_blank<?php else:?>mainFrame<?php endif;?>"><?php print $item->menu_name; ?></a></dd>
	      <?php endforeach ?>
	   </dl>
	<?php endforeach ?>
</div>
</body>
</html>

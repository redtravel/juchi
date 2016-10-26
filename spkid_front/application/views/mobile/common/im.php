<script type="text/javascript" src="http://im.test.com/server/get.php?l=zh-cn&t=js&g=mini.xml"></script>
<script type="text/javascript">
JappixMini.launch({
        connection: {
			//user: 'wei',
            //password: '369',
           //domain: "im.test.com",.popup-Ixunjia
		   domain: "anonymous.im"
        },
		container: '.popup-Ixunjia',

        application: {
           network: {
              autoconnect: true,
           },
           interface: {
              showpane: true,
              animate: true,
           },
		   chat: {
              open: ['service']
           },

           user: {
			   <?php if ($user_name):?>
				   random_nickname: false,			  
			       nickname: <?php echo $user_name?>
			   <?php else:?>
			       random_nickname: true
			   <?php endif;?>
               
           },
           groupchat: {
			open: ["yueya"],
           },
        }
})
</script>

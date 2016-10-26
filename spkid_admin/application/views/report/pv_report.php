
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/assets/js/ichart.1.2.1.min.js"></script>
	<link rel="stylesheet" href="../../../public/assets/css/ichart.css">

	<script type="text/javascript">
	/******************************************************
	/*最近48小时pv量js
	/*******************************************************/

	$(function(){
		var data = [
		         	{
		         		name : '产品时pv量',
		         		value:[<?php echo $hourly_pv['product_pv_list']?>],
		         		color:'#de9972'
		         	},
		         	{
		         		name : '文章时pv量',
		         		value:[<?php echo $hourly_pv['article_pv_list']?>],
		         		color:'#28847f'
		         	},
		         	{
		         		name : '教育时pv量',
		         		value:[<?php echo $hourly_pv['course_pv_list']?>],
		         		color:'#98847f'
		         	}
		         ];
		var chart = new iChart.ColumnMulti3D({
				render : 'hour_canvasDiv',
				data: data,
				labels:[<?php echo $hourly_pv['hour_list']?>],
				title : {
					text : '<?php echo $hourly_pv['min_hour'] . "至" . $hourly_pv['max_hour'] . "pv量统计（单位：时）"?>',
					color : '#3e576f'
				},
				footnote : {
					text : '',
					color : '#909090',
					fontsize : 11,
					padding : '0 44'
				},
				width : $(window).width(),
				height : 400,
				background_color : '#ffffff',
				legend:{
					enable:true,
					background_color : null,
					align : 'center',
					valign : 'bottom',
					row:1,
					column:'max',
					border : {
						enable : false
					}
				},
				column_width : 8,//柱形宽度
				zScale:8,//z轴深度倍数
				xAngle : 50,
				bottom_scale:1.1,
				label:{
					color:'#4c4f48'
				},
				sub_option:{
					label :false
				},
				tip:{
					enable :true
				},
				text_space : 16,//坐标系下方的label距离坐标系的距离。
				coordinate:{
					background_color : '#d7d7d5',
					grid_color : '#a4a4a2',
					color_factor : 0.24,
					board_deep:10,
					offsety:-10,
					pedestal_height:10,
					left_board:false,//取消左侧面板
					width:$(window).width() - 200,
					height:240,        				
					scale:[{
						 position:'left',	
						 start_scale:0,
						 end_scale:<?php echo $hourly_pv['max_scale']?>,
						 scale_space:3000,
						 scale_enable : false,
						 label:{
							color:'#4c4f48'
						 }
					}]
				}
		});

		//利用自定义组件构造左侧说明文本
		chart.plugin(new iChart.Custom({
				drawFn:function(){
					//计算位置
					var coo = chart.getCoordinate(),
						x = coo.get('originx'),
						y = coo.get('originy');
					//在左上侧的位置，渲染一个单位的文字
					chart.target.textAlign('start')
					.textBaseline('bottom')
					.textFont('600 11px Verdana')
					.fillText('时pv量(次)',x-40,y-28,false,'#6d869f');
				}
		}));
		chart.draw();	
	});

	$(function(){
		var product_pv=[<?php echo $hourly_pv['product_pv_list']?>],
		article_pv=[<?php echo $hourly_pv['article_pv_list']?>],
		course_pv=[<?php echo $hourly_pv['course_pv_list']?>],t;
		
		var data = [
		         	{
		         		name : '产品时PV量',
		         		value:product_pv,
		         		color:'#0d8ecf',
		         		line_width:2
		         	},
		         	{
		         		name : '文章时pv量',
		         		value:article_pv,
		         		color:'#ef7707',
		         		line_width:2
		         	},
		         	{
		         		name : '课程时pv量',
		         		value:course_pv,
		         		color:'#af7707',
		         		line_width:2
		         	}
		         ];
	     
		var labels = [<?php echo $hourly_pv['hour_list']?>];
		var line = new iChart.LineBasic2D({
			render : 'hour_canvasDiv_line',
			data: data,
			align:'center',
			title : '',
			subtitle : '',
			footnote : '',
			width : $(window).width(),
			height : 400,
			tip:{
				enable:true,
				shadow:true
			},
			legend : {
				enable : true,
				row:1,//设置在一行上显示，与column配合使用
				column : 'max',
				valign:'top',
				sign:'bar',
				background_color:null,//设置透明背景
				offsetx:-80,//设置x轴偏移，满足位置需要
				border : true
			},
			crosshair:{
				enable:true,
				line_color:'#62bce9'
			},
			sub_option : {
				label:false,
				point_hollow : false
			},
			coordinate:{
				width:$(window).width() - 200,
				height:240,
				axis:{
					color:'#9f9f9f',
					width:[0,0,2,2]
				},
				grids:{
					vertical:{
						way:'share_alike',
				 		value:5
					}
				},
				scale:[{
					 position:'left',	
					 start_scale:0,
					 end_scale:<?php echo $hourly_pv['max_scale']?>,
					 scale_space:3000,
					 scale_size:2,
					 scale_color:'#9f9f9f'
				},{
					 position:'bottom',	
					 labels:labels
				}]
			}
		});

	//开始画图
	line.draw();
	});

	//最近30日pv量
	$(function(){
		var data = [
		         	{
		         		name : '产品日pv量',
		         		value:[<?php echo $daily_pv['product_pv_list']?>],
		         		color:'#de9972'
		         	},
		         	{
		         		name : '文章日pv量',
		         		value:[<?php echo $daily_pv['article_pv_list']?>],
		         		color:'#28847f'
		         	},
		         	{
		         		name : '教育日pv量',
		         		value:[<?php echo $daily_pv['course_pv_list']?>],
		         		color:'#98847f'
		         	}
		         ];
		var chart = new iChart.ColumnMulti3D({
				render : 'canvasDiv',
				data: data,
				labels:[<?php echo $daily_pv['day_list']?>],
				title : {
					text : '<?php echo $daily_pv['min_day'] . "至" . $daily_pv['max_day'] . "pv量统计（单位：日）"?>',
					color : '#3e576f'
				},
				footnote : {
					text : '',
					color : '#909090',
					fontsize : 11,
					padding : '0 44'
				},
				width : $(window).width(),
				height : 400,
				background_color : '#ffffff',
				legend:{
					enable:true,
					background_color : null,
					align : 'center',
					valign : 'bottom',
					row:1,
					column:'max',
					border : {
						enable : false
					}
				},
				column_width : 8,//柱形宽度
				zScale:8,//z轴深度倍数
				xAngle : 50,
				bottom_scale:1.1,
				label:{
					color:'#4c4f48'
				},
				sub_option:{
					label :false
				},
				tip:{
					enable :true
				},
				text_space : 16,//坐标系下方的label距离坐标系的距离。
				coordinate:{
					background_color : '#d7d7d5',
					grid_color : '#a4a4a2',
					color_factor : 0.24,
					board_deep:10,
					offsety:-10,
					pedestal_height:10,
					left_board:false,//取消左侧面板
					width:$(window).width() - 200,
					height:240,        				
					scale:[{
						 position:'left',	
						 start_scale:0,
						 end_scale:<?php echo $daily_pv['max_scale']?>,
						 scale_space:1000,
						 scale_enable : false,
						 label:{
							color:'#4c4f48'
						 }
					}]
				}
		});

		//利用自定义组件构造左侧说明文本
		chart.plugin(new iChart.Custom({
				drawFn:function(){
					//计算位置
					var coo = chart.getCoordinate(),
						x = coo.get('originx'),
						y = coo.get('originy');
					//在左上侧的位置，渲染一个单位的文字
					chart.target.textAlign('start')
					.textBaseline('bottom')
					.textFont('600 11px Verdana')
					.fillText('日pv量(次)',x-40,y-28,false,'#6d869f');
				}
		}));
		chart.draw();	
	});

	$(function(){
		var product_pv=[<?php echo $daily_pv['product_pv_list']?>],
		article_pv=[<?php echo $daily_pv['article_pv_list']?>],
		course_pv=[<?php echo $daily_pv['course_pv_list']?>],t;
		
		var data = [
		         	{
		         		name : '产品日PV量',
		         		value:product_pv,
		         		color:'#0d8ecf',
		         		line_width:2
		         	},
		         	{
		         		name : '文章日pv量',
		         		value:article_pv,
		         		color:'#ef7707',
		         		line_width:2
		         	},
		         	{
		         		name : '课程日pv量',
		         		value:course_pv,
		         		color:'#af7707',
		         		line_width:2
		         	}
		         ];
	     
		var labels = [<?php echo $daily_pv['day_list']?>];
		var line = new iChart.LineBasic2D({
			render : 'canvasDiv_line',
			data: data,
			align:'center',
			title : '',
			subtitle : '',
			footnote : '',
			width : $(window).width(),
			height : 400,
			tip:{
				enable:true,
				shadow:true
			},
			legend : {
				enable : true,
				row:1,//设置在一行上显示，与column配合使用
				column : 'max',
				valign:'top',
				sign:'bar',
				background_color:null,//设置透明背景
				offsetx:-80,//设置x轴偏移，满足位置需要
				border : true
			},
			crosshair:{
				enable:true,
				line_color:'#62bce9'
			},
			sub_option : {
				label:false,
				point_hollow : false
			},
			coordinate:{
				width:$(window).width() - 200,
				height:240,
				axis:{
					color:'#9f9f9f',
					width:[0,0,2,2]
				},
				grids:{
					vertical:{
						way:'share_alike',
				 		value:5
					}
				},
				scale:[{
					 position:'left',	
					 start_scale:0,
					 end_scale:<?php echo $daily_pv['max_scale']?>,
					 scale_space:1000,
					 scale_size:2,
					 scale_color:'#9f9f9f'
				},{
					 position:'bottom',	
					 labels:labels
				}]
			}
		});

	//开始画图
	line.draw();
	});

	/******************************************************
	/*周pv量js
	/*******************************************************/
	//最近30周pv量
	$(function(){
		var data = [
		         	{
		         		name : '产品周pv量',
		         		value:[<?php echo $weekly_pv['product_pv_list']?>],
		         		color:'#de9972'
		         	},
		         	{
		         		name : '文章周pv量',
		         		value:[<?php echo $weekly_pv['article_pv_list']?>],
		         		color:'#28847f'
		         	},
		         	{
		         		name : '教育周pv量',
		         		value:[<?php echo $weekly_pv['course_pv_list']?>],
		         		color:'#98847f'
		         	}
		         ];
		var chart = new iChart.ColumnMulti3D({
				render : 'week_canvasDiv',
				data: data,
				labels:[<?php echo $weekly_pv['week_list']?>],
				title : {
					text : '<?php echo $weekly_pv['min_week'] . "至" . $weekly_pv['max_week'] . "pv量统计（单位：周）"?>',
					color : '#3e576f'
				},
				footnote : {
					text : '',
					color : '#909090',
					fontsize : 11,
					padding : '0 44'
				},
				width : $(window).width(),
				height : 400,
				background_color : '#ffffff',
				legend:{
					enable:true,
					background_color : null,
					align : 'center',
					valign : 'bottom',
					row:1,
					column:'max',
					border : {
						enable : false
					}
				},
				column_width : 8,//柱形宽度
				zScale:8,//z轴深度倍数
				xAngle : 50,
				bottom_scale:1.1,
				label:{
					color:'#4c4f48'
				},
				sub_option:{
					label :false
				},
				tip:{
					enable :true
				},
				text_space : 16,//坐标系下方的label距离坐标系的距离。
				coordinate:{
					background_color : '#d7d7d5',
					grid_color : '#a4a4a2',
					color_factor : 0.24,
					board_deep:10,
					offsety:-10,
					pedestal_height:10,
					left_board:false,//取消左侧面板
					width:$(window).width() - 200,
					height:240,        				
					scale:[{
						 position:'left',	
						 start_scale:0,
						 end_scale:<?php echo $weekly_pv['max_scale']?>,
						 scale_space:3000,
						 scale_enable : false,
						 label:{
							color:'#4c4f48'
						 }
					}]
				}
		});

		//利用自定义组件构造左侧说明文本
		chart.plugin(new iChart.Custom({
				drawFn:function(){
					//计算位置
					var coo = chart.getCoordinate(),
						x = coo.get('originx'),
						y = coo.get('originy');
					//在左上侧的位置，渲染一个单位的文字
					chart.target.textAlign('start')
					.textBaseline('bottom')
					.textFont('600 11px Verdana')
					.fillText('周pv量(次)',x-40,y-28,false,'#6d869f');
				}
		}));
		chart.draw();	
	});

	$(function(){
		var product_pv=[<?php echo $weekly_pv['product_pv_list']?>],
		article_pv=[<?php echo $weekly_pv['article_pv_list']?>],
		course_pv=[<?php echo $weekly_pv['course_pv_list']?>],t;
		
		var data = [
		         	{
		         		name : '产品周PV量',
		         		value:product_pv,
		         		color:'#0d8ecf',
		         		line_width:2
		         	},
		         	{
		         		name : '文章周pv量',
		         		value:article_pv,
		         		color:'#ef7707',
		         		line_width:2
		         	},
		         	{
		         		name : '课程周pv量',
		         		value:course_pv,
		         		color:'#af7707',
		         		line_width:2
		         	}
		         ];
	     
		var labels = [<?php echo $weekly_pv['week_list']?>];
		var line = new iChart.LineBasic2D({
			render : 'week_canvasDiv_line',
			data: data,
			align:'center',
			title : '',
			subtitle : '',
			footnote : '',
			width : $(window).width(),
			height : 400,
			tip:{
				enable:true,
				shadow:true
			},
			legend : {
				enable : true,
				row:1,//设置在一行上显示，与column配合使用
				column : 'max',
				valign:'top',
				sign:'bar',
				background_color:null,//设置透明背景
				offsetx:-80,//设置x轴偏移，满足位置需要
				border : true
			},
			crosshair:{
				enable:true,
				line_color:'#62bce9'
			},
			sub_option : {
				label:false,
				point_hollow : false
			},
			coordinate:{
				width:$(window).width() - 200,
				height:240,
				axis:{
					color:'#9f9f9f',
					width:[0,0,2,2]
				},
				grids:{
					vertical:{
						way:'share_alike',
				 		value:5
					}
				},
				scale:[{
					 position:'left',	
					 start_scale:0,
					 end_scale:<?php echo $weekly_pv['max_scale']?>,
					 scale_space:3000,
					 scale_size:2,
					 scale_color:'#9f9f9f'
				},{
					 position:'bottom',	
					 labels:labels
				}]
			}
		});

	//开始画图
	line.draw();
	});

	/******************************************************
	/*月pv量js
	/*******************************************************/
	//最近12月pv量
	$(function(){
		var data = [
		         	{
		         		name : '产品月pv量',
		         		value:[<?php echo $monthly_pv['product_pv_list']?>],
		         		color:'#de9972'
		         	},
		         	{
		         		name : '文章月pv量',
		         		value:[<?php echo $monthly_pv['article_pv_list']?>],
		         		color:'#28847f'
		         	},
		         	{
		         		name : '教育月pv量',
		         		value:[<?php echo $monthly_pv['course_pv_list']?>],
		         		color:'#98847f'
		         	}
		         ];
		var chart = new iChart.ColumnMulti3D({
				render : 'month_canvasDiv',
				data: data,
				labels:[<?php echo $monthly_pv['month_list']?>],
				title : {
					text : '<?php echo $monthly_pv['min_month'] . "至" . $monthly_pv['max_month'] . "pv量统计（单位：月）"?>',
					color : '#3e576f'
				},
				footnote : {
					text : '',
					color : '#909090',
					fontsize : 11,
					padding : '0 44'
				},
				width : $(window).width(),
				height : 400,
				background_color : '#ffffff',
				legend:{
					enable:true,
					background_color : null,
					align : 'center',
					valign : 'bottom',
					row:1,
					column:'max',
					border : {
						enable : false
					}
				},
				column_width : 8,//柱形宽度
				zScale:8,//z轴深度倍数
				xAngle : 50,
				bottom_scale:1.1,
				label:{
					color:'#4c4f48'
				},
				sub_option:{
					label :false
				},
				tip:{
					enable :true
				},
				text_space : 16,//坐标系下方的label距离坐标系的距离。
				coordinate:{
					background_color : '#d7d7d5',
					grid_color : '#a4a4a2',
					color_factor : 0.24,
					board_deep:10,
					offsety:-10,
					pedestal_height:10,
					left_board:false,//取消左侧面板
					width:$(window).width() - 200,
					height:240,        				
					scale:[{
						 position:'left',	
						 start_scale:0,
						 end_scale:<?php echo $monthly_pv['max_scale']?>,
						 scale_space:5000,
						 scale_enable : false,
						 label:{
							color:'#4c4f48'
						 }
					}]
				}
		});

		//利用自定义组件构造左侧说明文本
		chart.plugin(new iChart.Custom({
				drawFn:function(){
					//计算位置
					var coo = chart.getCoordinate(),
						x = coo.get('originx'),
						y = coo.get('originy');
					//在左上侧的位置，渲染一个单位的文字
					chart.target.textAlign('start')
					.textBaseline('bottom')
					.textFont('600 11px Verdana')
					.fillText('月pv量(次)',x-40,y-28,false,'#6d869f');
				}
		}));
		chart.draw();	
	});

	$(function(){
		var product_pv=[<?php echo $monthly_pv['product_pv_list']?>],
		article_pv=[<?php echo $monthly_pv['article_pv_list']?>],
		course_pv=[<?php echo $monthly_pv['course_pv_list']?>],t;
		
		var data = [
		         	{
		         		name : '产品月PV量',
		         		value:product_pv,
		         		color:'#0d8ecf',
		         		line_width:2
		         	},
		         	{
		         		name : '文章月pv量',
		         		value:article_pv,
		         		color:'#ef7707',
		         		line_width:2
		         	},
		         	{
		         		name : '课程月pv量',
		         		value:course_pv,
		         		color:'#af7707',
		         		line_width:2
		         	}
		         ];
	     
		var labels = [<?php echo $monthly_pv['month_list']?>];
		var line = new iChart.LineBasic2D({
			render : 'month_canvasDiv_line',
			data: data,
			align:'center',
			title : '',
			subtitle : '',
			footnote : '',
			width : $(window).width(),
			height : 400,
			tip:{
				enable:true,
				shadow:true
			},
			legend : {
				enable : true,
				row:1,//设置在一行上显示，与column配合使用
				column : 'max',
				valign:'top',
				sign:'bar',
				background_color:null,//设置透明背景
				offsetx:-80,//设置x轴偏移，满足位置需要
				border : true
			},
			crosshair:{
				enable:true,
				line_color:'#62bce9'
			},
			sub_option : {
				label:false,
				point_hollow : false
			},
			coordinate:{
				width:$(window).width() - 200,
				height:240,
				axis:{
					color:'#9f9f9f',
					width:[0,0,2,2]
				},
				grids:{
					vertical:{
						way:'share_alike',
				 		value:5
					}
				},
				scale:[{
					 position:'left',	
					 start_scale:0,
					 end_scale:<?php echo $monthly_pv['max_scale']?>,
					 scale_space:5000,
					 scale_size:2,
					 scale_color:'#9f9f9f'
				},{
					 position:'bottom',	
					 labels:labels
				}]
			}
		});

	//开始画图
	line.draw();
	});
	</script>
	<div class="main">
    <div class="main_title"><span class="l">报表管理 >> pv报表</span> </div>
    <!-- 小时pv量统计 -->
    <div class="blank5"></div>
	<div id='hour_canvasDiv'></div>
	<div class="blank5"></div>
	<div id='hour_canvasDiv_line'></div>
    <!-- 日pv量统计 -->
    <div class="blank5"></div>
	<div id='canvasDiv'></div>
	<div class="blank5"></div>
	<div id='canvasDiv_line'></div>
	<!-- 周pv量统计 -->
	<div class="blank5"></div>
	<div id='week_canvasDiv'></div>
	<div class="blank5"></div>
	<div id='week_canvasDiv_line'></div>
	<!-- 月pv量统计 -->
	<div class="blank5"></div>
	<div id='month_canvasDiv'></div>
	<div class="blank5"></div>
	<div id='month_canvasDiv_line'></div>

				


		</div>
	<?php include_once(APPPATH.'views/common/footer.php'); ?>

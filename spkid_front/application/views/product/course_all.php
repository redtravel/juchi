<?php include_once(APPPATH . 'views/common/header.php')?>

<!-- wdCalendar js&css files start-->

<link href="<?php echo static_style_url('pc/wdCalendar/css/dailog.css?v=version')?>" rel="stylesheet" type="text/css" />
<link href="<?php echo static_style_url('pc/wdCalendar/css/calendar.css?v=version')?>" rel="stylesheet" type="text/css" /> 
<link href="<?php echo static_style_url('pc/wdCalendar/css/dp.css?v=version')?>" rel="stylesheet" type="text/css" />   
<link href="<?php echo static_style_url('pc/wdCalendar/css/alert.css?v=version')?>" rel="stylesheet" type="text/css" /> 
<link href="<?php echo static_style_url('pc/wdCalendar/css/main.css?v=version')?>" rel="stylesheet" type="text/css" /> 


<script src="<?php echo static_style_url('pc/wdCalendar/src/jquery.js?v=version')?>" type="text/javascript"></script>  

<script src="<?php echo static_style_url('pc/wdCalendar/src/Plugins/Common.js?v=version')?>" type="text/javascript"></script>    
<script src="<?php echo static_style_url('pc/wdCalendar/src/Plugins/datepicker_lang_US.js?v=version')?>" type="text/javascript"></script>     
<script src="<?php echo static_style_url('pc/wdCalendar/src/Plugins/jquery.datepicker.js?v=version')?>" type="text/javascript"></script>

<script src="<?php echo static_style_url('pc/wdCalendar/src/Plugins/jquery.alert.js?v=version')?>" type="text/javascript"></script>    
<script src="<?php echo static_style_url('pc/wdCalendar/src/Plugins/jquery.ifrmdailog.js?v=version')?>" defer="defer" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/wdCalendar/src/Plugins/wdCalendar_lang_US.js?v=version')?>" type="text/javascript"></script>    
<script src="<?php echo static_style_url('pc/wdCalendar/src/Plugins/jquery.calendar.js?v=version')?>" type="text/javascript"></script>

<!-- wdCalendar js&css files ends-->

<style>
    .tool-col-2 {
        position:absolute;
        left:45%;
    }
    .tool-col-3 {
        position:absolute;
        left:97%;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        var defaultDate = '<?php echo date('Y-m');?>';     
       var view="month";          
       
        var DATA_FEED_URL = "/product/course_data";
        var op = {
            view: view,
            theme:3,
            showday: new Date(),
            EditCmdhandler:Edit,
            DeleteCmdhandler:Delete,
            ViewCmdhandler:View,    
            onWeekOrMonthToDay:wtd,
            onBeforeRequestData: cal_beforerequest,
            onAfterRequestData: cal_afterrequest,
            onRequestDataError: cal_onerror, 
            autoload:true,
            url: DATA_FEED_URL + "?method=list",  
            quickAddUrl: DATA_FEED_URL + "?method=add", 
            quickUpdateUrl: DATA_FEED_URL + "?method=update",
            quickDeleteUrl: DATA_FEED_URL + "?method=remove"        
        };
        var $dv = $("#calhead");
        var _MH = document.documentElement.clientHeight;
        var dvH = $dv.height() + 2;
        op.height = _MH - dvH;
        op.eventItems =[];

        var p = $("#gridcontainer").bcalendar(op).BcalGetOp();
        if (p && p.datestrshow) {
            $("#txtdatetimeshow").text(p.datestrshow);
        } else {
            $("#txtdatetimeshow").text(defaultDate);
        }
        $("#caltoolbar").noSelect();
        
        $("#hdtxtshow").datepicker({ picker: "#txtdatetimeshow", showtarget: $("#txtdatetimeshow"),
        onReturn:function(r){                          
                        var p = $("#gridcontainer").gotoDate(r).BcalGetOp();
                        if (p && p.datestrshow) {
                            $("#txtdatetimeshow").text(p.datestrshow);
                        }
                 } 
        });
        function cal_beforerequest(type)
        {
            var t="Loading data...";
            switch(type)
            {
                case 1:
                    t="Loading data...";
                    break;
                case 2:                      
                case 3:  
                case 4:    
                    t="The request is being processed ...";                                   
                    break;
            }
            $("#errorpannel").hide();
            $("#loadingpannel").html(t).show();    
        }
        function cal_afterrequest(type)
        {
            switch(type)
            {
                case 1:
                    $("#loadingpannel").hide();
                    break;
                case 2:
                case 3:
                case 4:
                    $("#loadingpannel").html("Success!");
                    window.setTimeout(function(){ $("#loadingpannel").hide();},2000);
                break;
            }              
           
        }
        function cal_onerror(type,data)
        {
            $("#errorpannel").show();
        }
        function Edit(data)
        {
           var eurl="edit.php?id={0}&start={2}&end={3}&isallday={4}&title={1}";   
            if(data)
            {
                var url = StrFormat(eurl,data);
                OpenModelWindow(url,{ width: 600, height: 400, caption:"Manage  The Calendar",onclose:function(){
                   $("#gridcontainer").reload();
                }});
            }
        }    
        function View(data)
        {
            var str = "";
            $.each(data, function(i, item){
                str += "[" + i + "]: " + item + "\n";
            });
            alert(str);               
        }    
        function Delete(data,callback)
        {           
            
            $.alerts.okButton="Ok";  
            $.alerts.cancelButton="Cancel";  
            hiConfirm("Are You Sure to Delete this Event", 'Confirm',function(r){ r && callback(0);});           
        }
        function wtd(p)
        {
           if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
            }
            $("#caltoolbar div.fcurrent").each(function() {
                $(this).removeClass("fcurrent");
            })
            $("#showdaybtn").addClass("fcurrent");
        }
        //to show day view
        $("#showdaybtn").click(function(e) {
            //document.location.href="#day";
            $("#caltoolbar div.fcurrent").each(function() {
                $(this).removeClass("fcurrent");
            })
            $(this).addClass("fcurrent");
            var p = $("#gridcontainer").swtichView("day").BcalGetOp();
            if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
            }
        });
        //to show week view
        $("#showweekbtn").click(function(e) {
            //document.location.href="#week";
            $("#caltoolbar div.fcurrent").each(function() {
                $(this).removeClass("fcurrent");
            })
            $(this).addClass("fcurrent");
            var p = $("#gridcontainer").swtichView("week").BcalGetOp();
            if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
            }

        });
        //to show month view
        $("#showmonthbtn").click(function(e) {
            //document.location.href="#month";
            $("#caltoolbar div.fcurrent").each(function() {
                $(this).removeClass("fcurrent");
            })
            $(this).addClass("fcurrent");
            var p = $("#gridcontainer").swtichView("month").BcalGetOp();
            if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
            }
        });
        
        $("#showreflashbtn").click(function(e){
            $("#gridcontainer").reload();
        });
        
        //Add a new event
        $("#faddbtn").click(function(e) {
            var url ="edit.php";
            OpenModelWindow(url,{ width: 500, height: 400, caption: "Create New Calendar"});
        });
        //go to today
        $("#showtodaybtn").click(function(e) {
            var p = $("#gridcontainer").gotoDate().BcalGetOp();
            if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
            }


        });
        //previous date range
        $("#sfprevbtn").click(function(e) {
            var p = $("#gridcontainer").previousRange().BcalGetOp();
            if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
            }

        });
        //next date range
        $("#sfnextbtn").click(function(e) {
            var p = $("#gridcontainer").nextRange().BcalGetOp();
            if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
            }
        });
        
    });
</script> 

<style>

    .main {
        width:100%;
    text-align:center;
    }
  .main .course_nav {
    width:50%;
    
    display:inline-block;
  }

  .main .course_nav ul {    
    position:relative;
    color:black;
    font-weight:bold;
  }

  .main .course_nav ul li {
    padding:20px 0px;
    display:inline-block;
    margin-right:5%;
  }

  .main .course_nav ul li:hover {    
    border-bottom:2px rgb(0, 162, 232) solid;
  }

  .main .course_tabs {
    width:80%;
    
    position:relative;
    display:inline-block;
  }

  .main .course_tabs ul {
    list-style:none;
  }

  .all_courses_content {
    position:relative;
  }
  .course_filter {
    text-align:right;    
  }
  .one_month {
    margin-right:-10px;
  }

.calenda {
  position:relative;
  width:100%;
  border:1px solid red;
}
.cla_header ul li {
  display:inline-block;
  width:10%;
  border:1px solid gray;
  margin-right:-10px;
}

.caltoolbar_center {
    -margin:2px auto;
    -width:50%;
    -padding-left: 80%;    
}

</style>

<div class="course-bar">
     <ul class="category-container">
     <li><a href="/course_all.html"  class="course-active">全部课程</a></li>
     <li><a href="/index/course">热门课程</a></li>
     <li><a href="/index/medical">医考技考</a></li>
    </ul>
</div>
<div class="main">
    
  <div class="course_tabs">
    <ul>
      <li class="all_courses">
        <div class="all_courses_content">
        	  <div>

        	    <div id="calhead" style="padding-left:1px;padding-right:1px;">          
        	          
        	          <div id="loadingpannel" class="ptogtitle loadicon" style="display: none;">Loading data...</div>
        	           <div id="errorpannel" class="ptogtitle loaderror" style="display: none;">Sorry, could not load your data, please try again later</div>
        	          </div>          
        	          
        	          <div id="caltoolbar" class="ctoolbar">
                        <div class="caltoolbar_center">
        	            <div id="faddbtn" class="fbutton">
        	              
        	               </div>
        	            
        	            
        	          
        	          <div class="tool-col-1">
            	          <div id="sfprevbtn" title="Prev"  class="fbutton">
            	            <span class="fprev"></span>

            	          </div>

            	          <div id="sfnextbtn" title="Next" class="fbutton">
            	              <span class="fnext"></span>
            	          </div>
                      </div>   
                      <div class="tool-col-2">
            	          <div class="fshowdatep fbutton">
            	                  <div>
            	                      <input type="hidden" name="txtshow" id="hdtxtshow" />
            	                      <span id="txtdatetimeshow">Loading</span>

            	                  </div>
            	          </div>
                    </div>
                      
                      <div class="tool-col-3">
            	          <div  id="showmonthbtn" class="fbutton">
                              <div><span title='Month' class="showmonthview">月</span></div>
                          </div>
                      </div>
        	          <div class="clear"></div>
                        </div>
        	          </div>
        	    </div>
        	    <div style="padding:1px;">

        	      <div class="t1 chromeColor">
        	          &nbsp;</div>
        	      <div class="t2 chromeColor">
        	          &nbsp;</div>
        	      <div id="dvCalMain" class="calmain printborder">
        	          <div id="gridcontainer" style="overflow-y: visible;">
        	          </div>
        	      </div>
        	      <div class="t2 chromeColor">

        	          &nbsp;</div>
        	      <div class="t1 chromeColor">
        	          &nbsp;
        	      </div>   
        	      </div>
        	   
        	</div>  

        </div>
      </li>
      <li class="hot_courses" style="display:none"></li>
      <li class="examinations" style="display:none"></li>
      <li class="activities" style="display:none"></li>
    </ul>
  </div>
</div>
<?php include_once(APPPATH . 'views/common/footer.php')?>

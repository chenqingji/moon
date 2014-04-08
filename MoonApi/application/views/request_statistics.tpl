<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <title>API 请求统计</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>        
        
        <style>
            .line {
                clear:both;
            }
            .labelDiv {
                float:left;
            }
        </style>
        <script>
            $(function() {
                $( "#datepicker1" ).datepicker({
                  maxDate:0,
                  showOtherMonths: true,
                  selectOtherMonths: true,
                  onClose: function( selectedDate ) {
                    $( "#datepicker2" ).datepicker( "option", "minDate", selectedDate );
                  }                  
                });       
                $( "#datepicker2" ).datepicker({
                  maxDate:0,
                  showOtherMonths: true,
                  selectOtherMonths: true,
                  onClose: function( selectedDate ) {
                    $( "#datepicker1" ).datepicker( "option", "maxDate", selectedDate );
                  }                  
                });    

                {$scripts}
//                newProgressbar("progressbar1",1000);
            });
            function newProgressbar(id,width){
                $("#"+id).progressbar({
                    value: 100
                });
                $("#"+id).css({ "width":width,'height':20,"float":'left'});
                $("#"+id).children('div').css({
                  "background": '#' + Math.floor( Math.random() * 16777215 ).toString( 16 )
                });            
            }
        </script>        
    </head>
    <body>
        <h2>API HTTP请求统计</h2>实际统计日期：{$startdate} ~ {$enddate} 
        <form name='form1' method="post" action=''>
        <p>Date: 
            <input type="text" id="datepicker1" name="startdate"/> ~ 
            <input type="text" id="datepicker2" name="enddate"/>&nbsp;&nbsp;
            <button type="submit" id='submitBtn'>查询</button>
        </p>
        </form>
<!--        <div class="line"><div id="progressbar1" title="123/xxx"></div><div class="lableDiv">&nbsp;&nbsp;12/xxx</div></div>-->
    {if $divhtml}
        {$divhtml}
    {else}
        <p><em>{$startdate} ~ {$enddate} 日志空空如也~~</em></p>
    {/if}
    </body>
</html>

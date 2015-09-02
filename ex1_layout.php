<?php
/*------------------------------------------------------------------------
# edit.php - Quan Ly Khach Hang Component
# ------------------------------------------------------------------------
# author    Tam Nguyễn
# copyright Copyright (C) 2014. All Rights Reserved
# license   GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
# website   www.haiausolution.com
-------------------------------------------------------------------------*/

?>
<style link="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js" />
<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
</div>
<script type="text/javascript">
jQuery(document).ready(function ($){
    $( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: "yy-mm-dd",
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: "yy-mm-dd",
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  </script>
<div class="">
    <form action="ex1.php" method="post" name="adminForm" id="adminForm">
        <table>
            <tr><td><label for="from">From</label></td>
                <td><input type="text" id="from" name="from" value="<?php echo $this->filter->dateFrom?>"></td>
                <td><label for="to">to</label></td>
                <td><input type="text" id="to" name="to" value="<?php echo $this->filter->dateTo?>"></td>
                <td style="vertical-align: top;"><div class="btn-wrapper" id="toolbar-edit">
                        <button class="btn btn-small" type="submit">
                        <span class="icon-edit"></span>
                        View</button>
                </div></td>
            </tr>
        </table>
   </form>
</div>
<div id="j-main-container" class="span10">   
    <?php $rp->drawChart();?>
     <?php $rp->drawTable(); ?>
</div>

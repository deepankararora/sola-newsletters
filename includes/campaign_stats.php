<?php $camp_id = $_GET['camp_id'];
 
$result = sola_nl_get_camp_details($camp_id);
$links_array = sola_nl_camp_links($camp_id);
$lists = sola_nl_get_camp_lists($camp_id);

global $sola_nl_camp_subs_tbl;
global $wpdb;
        
$sql = "SELECT COUNT(`id`) as `total` FROM `$sola_nl_camp_subs_tbl` WHERE `camp_id` = '$camp_id'";
$sdf = $wpdb->get_row($sql);
$total_subscribers = $sdf->total;
$sql = "SELECT COUNT(`id`) as `total` FROM `$sola_nl_camp_subs_tbl` WHERE `camp_id` = '$camp_id' AND `status` >= 1";
$sdf = $wpdb->get_row($sql);
$total_sent = $sdf->total;
$sent_perc = round((($total_sent / $total_subscribers)*100),1);

?>

<style>
    
tr.even td.sorting_1 {
    background-color: #ffe1db;
}    
tr.even {
    background-color: #ffe1db;
}    
</style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h1><?php _e("Campaign Stats","sola") ?> - <?php echo stripslashes($result->subject) ?></h1>
                <ul>
                    <?php foreach($lists as $list){
                        echo "<li><a title='".$list->list_name."'>".$list->list_name."</a></li>";
                    }?>
                </ul>
                <p class="text-muted small"><?php _e("Date Sent","sola"); ?>: <?php echo date('l jS \of F Y h:i:s A',strtotime($result->date_sent)) ?></p>
                
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if ($result->status == 3) { ?>
                <h3><?php _e("Status","sola"); ?>: <span style="color: #EC6851;"><?php _e("Send in progress","sola"); ?> <strong>(<?php echo $sent_perc."%"; ?>)</strong></span></h3>
                <ul>
                    <li><?php _e("Subscribers:","sola"); ?> <?php echo number_format($total_subscribers); ?></li>
                    <li><?php _e("Successfully sent:","sola"); ?> <?php echo number_format($total_sent); ?></li>
                </ul>
                <?php } else { ?>
                <h3><?php _e("Status","sola"); ?>: <span style="color: #EC6851;"><?php _e("Send complete","sola"); ?> <strong>(<?php echo $sent_perc."%"; ?>)</strong></span></h3>
                <ul>
                    <li><?php _e("Subscribers:","sola"); ?> <?php echo number_format($total_subscribers); ?></li>
                    <li><?php _e("Successfully sent:","sola"); ?> <?php echo number_format($total_sent); ?></li>
                </ul>
                <?php } ?>
            </div>
        </div>
        
        
        
        <hr />
        <div class="row">
            <div class="col-sm-4 ">
                <h2 class="text-center">
                    <?php _e("Total Emails Sent", "sola"); ?>
                    <span class="label label-success">
                        <?php echo sola_nl_get_camp_stats($camp_id,'', true); ?>
                    </span>
                </h2>  
                <hr/>
                <table class="table sola_emails table-striped">
                    <thead>
                        <tr>
                            <td>
                                <?php _e("E-mail","sola"); ?>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $subs = sola_nl_get_camp_subs($camp_id, true);
                       foreach($subs as $sub){
                           $result = sola_nl_get_subscriber($sub->sub_id);?>
                            <tr>
                                <td>
                                    <?php echo $result->sub_email ?>
                                </td>
                            </tr>
                       <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-4 ">
                <?php if (function_exists("sola_nl_register_pro_version")){ ?>
                    <h2 class="text-center">
                        <?php _e("Total Opens", "sola"); ?>
                        <span class="label label-success">
                            <?php echo sola_nl_get_total_opens($camp_id, ''); ?>
                        </span>
                    </h2>
                    <hr/>
                    <table class="table sola_opens table-striped">
                        <thead>
                            <tr>
                                <td>
                                   <?php _e("E-mail","sola"); ?>
                                </td>
                                <td class="text-center">
                                    <?php _e("Opens","sola"); ?>
                                </td>
                                <td class="text-right">
                                    <?php _e("Last Date Opened","sola"); ?> 
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $subs = sola_nl_get_camp_subs($camp_id, true);
                           foreach($subs as $sub){
                               $result = sola_nl_get_subscriber($sub->sub_id); ?>
                                <tr>
                                    <td>
                                        <?php echo $result->sub_email; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo sola_nl_get_total_opens($camp_id, $sub->sub_id); ?>
                                    </td>
                                    <td class="text-right" >
                                        <?php if ($sub->date_open == "0000-00-00 00:00:00") { } else { echo $sub->date_open; } ?>
                                    </td>
                                </tr>
                                <?php
                           }              
                           ?> 
                        </tbody>
                    </table>
                <?php } else { ?>
                    <h2 class="text-center"><?php _e("Total Opens","sola"); ?></h2>
                    <hr/>
                    <h3 class="text-center" ><?php _e('Go','sola')?> <a target='_BLANK' href='http://solaplugins.com/plugins/sola-newsletters/?utm_source=plugin&utm_medium=link&utm_campaign=stats_opens' style='color:#EC6851;'><?php _e('Premium','sola')?> </a><?php _e('to get these stats and more!','sola')?></h3>
                <?php } ?>
            </div>
            <div class="col-sm-4 ">
                <?php if (function_exists("sola_nl_register_pro_version")){ ?>
                    <h2 class="text-center">
                        <?php _e("Total Clicks", "sola"); ?>
                        <span class="label label-success">
                            <?php echo sola_nl_get_link_clcks('',$camp_id); ?>
                        </span>
                    </h2>
                    <hr/>
                    <table class="table sola_clicks table-striped">
                        <thead>
                            <tr>
                                <td><?php _e("Link", "sola") ?></td>
                                <td class="text-center"><?php _e("Clicks" , "sola"); ?></td>
                            </tr>
                        </thead>
                        <tbody class="table-hover">
                            <?php foreach($links_array as $link){?>
                                <tr >
                                    <td>
                                        <?php echo $link->link_name ?>
                                    </td>
                                    <td align="center">
                                        <?php echo sola_nl_get_link_clcks($link->link_name, $camp_id) ?>
                                    </td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                        <h2 class="text-center"><?php _e("Total Clicks","sola"); ?></h2>
                        <hr/>
                        <h3 class="text-center" ><?php _e('Go','sola')?> <a target='_BLANK' href='http://solaplugins.com/plugins/sola-newsletters/?utm_source=plugin&utm_medium=link&utm_campaign=stats_clicks' style='color:#EC6851;'><?php _e('Premium','sola')?> </a><?php _e('to get these stats and more!','sola')?></h3>
                    <?php } ?>
                </div>

            </div>
                
    </div>    
    
    
    
    
  <?php /*<div class="wrap">  
    
    
    <div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
    
    <div>
        <h3>Total Sent: <?php echo sola_nl_get_camp_stats($camp_id,''); ?></h3>
        <h3>Total Opened: <?php echo sola_nl_get_total_opens($camp_id); ?></h3>
        <h3>Campaign Send Date: <?php echo $result->date_sent ?></h3>
        <div>
            <div style="width:49%; display: inline-block; ">
                <table class="wp-list-table widefat fixed">
                    <thead>
                        <tr>
                            <th>
                                Subscriber
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                Date Opened
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php
                       $subs = sola_nl_get_camp_subs($camp_id);
                       foreach($subs as $sub){
                           $result = sola_nl_get_subscriber($sub->sub_id);?>
                            <tr>
                                <td>
                                    <a title="Detailed Subscriber Stats" href="admin.php?page=sola-nl-menu&action=detailed_stats&camp_id=<?php echo $camp_id ?>&sub_id=<?php echo $sub->id?>"><?php echo $result->sub_email ?></a>
                                </td>
                                <td>
                                    <?php if($sub->status == 1) echo "Sent";
                                    else if($sub->status == 2) echo "Opened";
                                    ?>
                                </td>
                                <td>
                                    <?php echo $sub->date_open ?>
                                </td>
                            </tr>
                            <?php
                       }              
                       ?> 
                    </tbody>
                </table>
            </div>
            <div style="width:49%; float: right; ">
                <table class="wp-list-table widefat fixed">
                    <thead>
                        <tr>
                            <th>
                                Link
                            </th>
                            <th width="100px" style="text-align: center;">
                               Total Clicks
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach($links_array as $link){ ?>
                            <tr <?php if($i++ % 2 == 0){echo "class='alternate'";}?>>
                                <td>
                                    <?php echo $link->link_name ?>
                                </td>
                                <td align="center">
                                    <?php echo sola_nl_get_link_clcks($link->link_name, $camp_id) ?>
                                </td>
                            </tr>                       
                        <?php }              
                        ?> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; */?> 

<script type="text/javascript">
    jQuery(document).ready(function(){
                solaTable1 = jQuery('.sola_emails').dataTable({
                    "bProcessing": true
                });
                solaTable2 = jQuery('.sola_opens').dataTable({
                    "bProcessing": true,
                    "aaSorting": [[ 1, "desc" ]]
                });
                solaTable3 = jQuery('.sola_clicks').dataTable({
                    "bProcessing": true,
                    "aaSorting": [[ 1, "desc" ]]
                });
    });    
    
</script>
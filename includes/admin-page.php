<?php  
$camp_id = $_GET['camp_id'];
$current_page = 1;
$order = "DESC";

$orderBy = "date_created";
$limit = 10;
if(isset($_GET["p"])){
    $current_page = $_GET["p"];
}
if(isset($_GET["order"])){
    $order = $_GET["order"];
}
if(isset($_GET["orderBy"])){
    $orderBy = $_GET["orderBy"];
}
if($order == "DESC"){
    $orderswop = "ASC";
} else {
    $orderswop = "DESC";
}
$lc_order = strtolower($order);

if(isset($_GET['campaigns'])){
    $campaign_name_search = $_GET['campaigns'];
    $camps = sola_nl_get_camps($limit, $current_page, $order, $orderBy, $campaign_name_search);
} else{
    $campaign_name_search = "";
    $camps = sola_nl_get_camps($limit, $current_page, $order, $orderBy, $campaign_name_search);
}
$order_url = "&order=".$order."&orderBy=".$orderBy;

$total_rows = sola_nl_total_camps();
$total_pages = ceil($total_rows/$limit);

global $sola_nl_camp_subs_tbl;
global $wpdb;

?>

<div class="wrap">   
    <div id="icon-edit" class="icon32 icon32-posts-post">
        <br>
    </div>
    <h2>
        <?php _e("My Newsletter Campaigns","sola") ?>
        <a href="?page=sola-nl-menu&action=new_campaign" class="add-new-h2">
            <?php _e("New Campaign","sola") ?>
        </a>
    </h2>
    <form method="get" action="">
        <p class="search-box">   
            <input type="hidden" id="sola_nl_search_input" name="page" value="sola-nl-menu">
            <input type="search" id="sola_nl_search_input" name="campaigns" value="">
            <input type="submit" name="" id="search-submit" class="button" value="Search Campaigns">
        </p>        
    </form>
    <form id="sola_nl_camp_form" method="post">
        <div class="tablenav top">
            <div class="alignleft">
                <button value="delete_multi_camps" name="action" class="button-primary">Delete</button>
            </div>
            <div class="tablenav-pages">
                <span class="displaying-num"><?php echo $total_rows ?><?php _e("items", "sola") ?></span>
                <span class="pagination-links">
                    <a class="first-page <?php if($current_page == 1){echo "disabled";} ?>" title="Go to the first page" <?php if($current_page != 1) { ?>href="<?php echo $_SERVER['PHP_SELF'];?>?page=sola-nl-menu&p=<?php echo "1"; echo $order_url; ?>"<?php } ?>>«</a>
                    <a class="prev-page <?php if($current_page == 1){echo "disabled";} ?>" title="Go to the previous page" <?php if($current_page != 1) { ?>href="<?php echo $_SERVER['PHP_SELF'];?>?page=sola-nl-menu&p=<?php echo $current_page-1; echo $order_url; ?>"<?php } ?>>‹</a>
                    <span class="paging-input"><?php echo $current_page ?> of <span class="total-pages"><?php echo $total_pages ?></span></span>
                    <a class="next-page <?php if($current_page >= $total_pages){echo "disabled";} ?>" title="Go to the next page" <?php if($current_page < $total_pages) { ?>href="<?php echo $_SERVER['PHP_SELF'];?>?page=sola-nl-menu&p=<?php echo $current_page+1; echo $order_url; ?>"<?php } ?>>›</a>
                    <a class="last-page <?php if($current_page >= $total_pages){echo "disabled";} ?>" title="Go to the last page" <?php if($current_page < $total_pages) { ?>href="<?php echo $_SERVER['PHP_SELF'];?>?page=sola-nl-menu&p=<?php echo $total_pages; echo $order_url; ?>"<?php } ?>>»</a>
                </span>
            </div>
        </div>
        <div>
            <table class="wp-list-table widefat fixed">
                <thead>
                    <tr>
                        <th class="manage-column column-cb check-column">
                            <input id="sola_check_all" type="checkbox" >
                        </th>
                        <th class="manage-column column-title sorted <?php if($orderBy == "subject"){ echo $lc_order; } ?>">
                            <a href="<?php echo $_SERVER['PHP_SELF'];?>?page=sola-nl-menu&p=<?php echo $current_page; ?>&order=<?php echo $orderswop ?>&orderBy=subject">
                                <span><?php _e("Campaign Name","sola") ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th class="manage-column column-title sorted <?php if($orderBy == "status"){ echo $lc_order; } ?>">
                            <a href="<?php echo $_SERVER['PHP_SELF'];?>?page=sola-nl-menu&p=<?php echo $current_page; ?>&order=<?php echo $orderswop ?>&orderBy=status">
                                <span><?php _e("Campaign Status","sola") ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th class="manage-column column-title">
                             <span>
                                 <?php _e("Stats","sola") ?><br/> 
                             </span>
                        </th>
                        <th class="manage-column column-title">
                             <span><?php _e("Lists","sola") ?></span>
                        </th>
                        <th class="manage-column column-title sorted <?php if($orderBy == "date_created") { echo $lc_order; } ?>">
                            <a href="<?php echo $_SERVER['PHP_SELF'];?>?page=sola-nl-menu&p=<?php echo $current_page; ?>&order=<?php echo $orderswop ?>&orderBy=date_created">
                                <span><?php _e("Date Created","sola"); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $ii = 0;
                    foreach($camps as $camp){?>
                        <tr <?php if ($ii % 2 == 0){?>class="alternate"<?php }?>>
                            <?php 
                                $camp_id = $camp->camp_id; 
                                if(function_exists('sola_nl_register_pro_return_stats')){
                                    $stats = sola_nl_register_pro_return_stats($camp_id);
                                }
                                
                            ?>
                            <td>
                                <input type="checkbox" name="sola_camp_checkbox[]" value="<?php echo $camp->camp_id; ?>" class="sola-check-box">
                            </td>                            
                            <td>
                                <strong>
                                    <a class="row-title"<?php if ($camp->status == 0) echo "href=\"?page=sola-nl-menu&action=editor&camp_id=".$camp->camp_id."\"";
                                    else if($camp->status >= 1) echo "href=\"?page=sola-nl-menu&action=camp_stats&camp_id=".$camp->camp_id."\""?>>
                                        <?php echo $camp->subject ?>
                                    </a>
                                </strong>
                                <div class="row-actions">
                                    <?php if ($camp->email == "" || !$camp->email) { } else { ?>
                                    <span>
                                       <a target="_BLANK" href="<?php echo SITE_URL."/?action=sola_nl_browser&camp_id=".$camp->camp_id."&sub_id=0"; ?>"><?php _e("Preview","sola"); ?></a>
                                    </span>
                                    <span>|</span>
                                    <?php } ?>
                                    <?php if ($camp->status >= 1 && $camp->status < 9) { ?>
                                    <span>
                                       <a href="?page=sola-nl-menu&action=camp_stats&camp_id=<?php echo $camp->camp_id; ?>"><?php _e("View Stats","sola"); ?></a>
                                    </span>
                                    <?php } else { ?>
                                    <span>
                                       <a href="?page=sola-nl-menu&action=new_campaign&camp_id=<?php echo $camp->camp_id; ?>"><?php _e("Edit","sola"); ?></a>
                                    </span>
                                    <?php } ?>
                                    <?php if ($camp->email == "" || !$camp->email) { } else { ?>
                                    <span>|</span>
                                    <span>
                                       <a href="?page=sola-nl-menu&action=duplicate_campaign&camp_id=<?php echo $camp->camp_id; ?>"><?php _e("Duplicate","sola"); ?></a>
                                    </span>
                                    <?php } ?>
                                    <span>|</span>
                                    <span class="trash">
                                        <a href="?page=sola-nl-menu&action=delete_camp&camp_id=<?php echo $camp->camp_id; ?>" ><?php _e("Delete","sola"); ?></a>
                                    </span>
                                    <?php if ($camp->status == 2 || $camp->status == 3) { ?>
                                    <span>|</span>
                                    <span>
                                       <a href="?page=sola-nl-menu&action=pause_sending&camp_id=<?php echo $camp->camp_id; ?>"><?php _e("Pause Sending","sola"); ?></a>
                                    </span>
                                    <?php } ?>
                                    <?php if ($camp->status == 9) { ?>
                                    <span>|</span>
                                    <span>
                                       <a href="?page=sola-nl-menu&action=resume_sending&camp_id=<?php echo $camp->camp_id; ?>"><?php _e("Resume Sending","sola"); ?></a>
                                    </span>
                                    <?php } ?>
                                </div>
                            </td>
                            <td>
                               
                            <?php  
                                $current_date = date("Y-m-d H:i:s",current_time('timestamp'));       
                                $scheduled_time = $camp->schedule_date;
//                                echo $current_date;
                                if($current_date < $scheduled_time){ echo __('Scheduled to be sent on: <br/>'.$scheduled_time); }
                                else if($camp->status == 1) { echo __("Sent","Sola"); }
                                else if($camp->status == 0) {echo __("Not Sent","sola"); }
                                else if($camp->status == 9) {echo __("Sending Paused","sola"); }
                                else if($camp->status == 2 || $camp->status == 3) { 
                                    echo "Sending...<br />";
                                    echo '<div class="progressBar" id="progressBar_'.$camp->camp_id.'"><div style=""></div></div><div id="time_next_'.$camp->camp_id.'"><small>'.__("Waiting for other campaign to finish sending","sola").'</small></div>';
                                }
                                ?>
                            </td>
                            <?php if (function_exists('sola_nl_register_pro_version')){ ?>
                                <td>
                                    <?php 
                                    if(function_exists('sola_nl_register_pro_return_stats')){
                                        echo $stats['clicks']." ".__("clicks","sola")." <br />
                                            ".$stats['opens']." ".__("opens","sola")." <br />
                                            ".$stats['unsubscribes']." ".__("unsubscribes","sola")." <br />
                                            ".$stats['open_rate']."% ".__("open rate","sola")."";
                                    } else {
                                        echo "<small>".__("Please <a href='update-core.php'>upgrade</a> your premium version to view this.","sola")."</small>";
                                    }
                                    ?>
                                </td>
                            <?php } else {?>
                                <td>
                                    <?php echo "<a href='http://solaplugins.com/plugins/sola-newsletters/?utm_source=plugin&utm_medium=link&utm_campaign=campaign_stats_list' target='_BLANK'>".__("Pro only","sola")."</a>"; ?>
                                </td>
                            <?php } ?>
                            <td>
                                <?php
                                $lists = sola_nl_get_camp_lists($camp->camp_id);
                                $i = 0;
                                foreach($lists as $list){
                                    if($i > 0) {echo ", ";}?>
                                    <a href="?page=sola-nl-menu-subscribers&list_id=<?php echo $list->list_id ?>"><?php echo $list->list_name; ?></a>  
                                <?php } ?>
                            </td>
                            <td>
                                <?php echo $camp->date_created ?>
                            </td>
                        </tr>
                        <?php $ii++;
                    } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th><?php _e("Campaign Name","sola") ?></th>
                        <th><?php _e("Campaign Status","sola") ?></th>
                        <th><?php _e("Stats","sola") ?></th>
                        <th><?php _e("Lists","sola") ?></th>
                        <th><?php _e("Date Created","sola") ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
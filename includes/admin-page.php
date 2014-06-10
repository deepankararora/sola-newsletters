<?php  



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
    
$order_url = "&order=".$order."&orderBy=".$orderBy;
$camps = sola_nl_get_camps($limit, $current_page, $order, $orderBy);
$total_rows = sola_nl_total_camps();
$total_pages = ceil($total_rows/$limit);
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
                             <span><?php _e("Lists","sola") ?></span>
                        </th>
                        <th class="manage-column column-title sorted <?php if($orderBy == "date_created"){ echo $lc_order; } ?>">
                            <a href="<?php echo $_SERVER['PHP_SELF'];?>?page=sola-nl-menu&p=<?php echo $current_page; ?>&order=<?php echo $orderswop ?>&orderBy=date_created">
                                <span><?php _e("Date Created","sola") ?></span>
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
                            <td>
                                <input type="checkbox" name="sola_camp_checkbox[]" value="<?php echo $camp->camp_id ?>" class="sola-check-box">
                            </td>
                            <td>
                                <strong>
                                    <a class="row-title"<?php if ($camp->status == 0) echo "href=\"?page=sola-nl-menu&action=editor&camp_id=".$camp->camp_id."\"";
                                    else if($camp->status >= 1) echo "href=\"?page=sola-nl-menu&action=camp_stats&camp_id=".$camp->camp_id."\""?>>
                                        <?php echo $camp->subject ?>
                                    </a>
                                </strong>
                                <div class="row-actions">
                                    <?php if ($camp->status >= 1){?>
                                        <span>
                                           <a href="?page=sola-nl-menu&action=camp_stats&camp_id=<?php echo $camp->camp_id ?>">View Stats</a>
                                        </span>
                                    <?php } else { ?>
                                        <span>
                                           <a href="?page=sola-nl-menu&action=new_campaign&camp_id=<?php echo $camp->camp_id ?>">Edit</a>
                                        </span>
                                    <?php } ?>
                                    <span>|</span>
                                    <span class="trash">
                                        <a href="?page=sola-nl-menu&action=delete_camp&camp_id=<?php echo $camp->camp_id ?>" >Delete</a>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php if($camp->status == 1){ echo "Sent";}
                                else if($camp->status == 0){echo "Not Sent";}
                                else if($camp->status == 2 || $camp->status == 3){ 
                                    echo "Sending...<br />";
                                    echo '<div class="progressBar" id="progressBar_'.$camp->camp_id.'"><div style=""></div></div><div id="time_next_'.$camp->camp_id.'"><small>'.__("Waiting for other campaign to finish sending","sola").'</small></div>';
                                }
                                ?>
                            </td>
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
                        <th><?php _e("Lists","sola") ?></th>
                        <th><?php _e("Date Created","sola") ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
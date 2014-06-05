<?php 

if($_POST['sola_nl_cron_send']){
    $sola_nl_check = sola_nl_finish_camp(3);
    if ( is_wp_error($sola_nl_check) ) { 
        sola_return_error($sola_nl_check);  
    } else {
        echo "<div id=\"message\" class=\"updated\"><p>".__("Campaign Ready to Send","sola")."</p></div>";
    }
    sola_nl_add_subs_to_camp($_GET['camp_id']);
    header("location:admin.php?page=sola-nl-menu");
    

    
} else if($_POST['sola_nl_ajax_send']){
    $sola_nl_check = sola_nl_finish_camp(2);
        if ( is_wp_error($sola_nl_check) ) { 
        sola_return_error($sola_nl_check);  
    } else {
        echo "<div id=\"message\" class=\"updated\"><p>".__("Campaign Ready to Send","sola")."</p></div>";
    }
    sola_nl_add_subs_to_camp($_GET['camp_id']);

    header("location:admin.php?page=sola-nl-menu");
}



/* sola_nl_add_subs_to_camp($_GET['camp_id']); ?>
<div class="wrap">
        
    
    <div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
    <h2><?php _e("Send Campaign","sola") ?></h2>
    <?php
    $sola_nl_ajax_nonce = wp_create_nonce("sola_nl");
    $emails = sola_nl_camp_sub_emails($_GET['camp_id']);
    $subscribers = sola_nl_camp_subs($_GET['camp_id']);
    if($subscribers){
        ?>

        <script language="javascript">
            var subscribers = <?php echo json_encode($subscribers) ?>;
            var camp_id = '<?php echo $_GET['camp_id']; ?>';
            var index = 0;
            var sola_nl_nonce = '<?php echo $sola_nl_ajax_nonce; ?>';
            var total = subscribers.length;
            jQuery(document).ready( function() {
                sola_ajax_send_mail();
            });

        </script>
        <div>
            <div id="progressBar"><div></div></div>

        </div>
        <div>
            <a href="?page=sola-nl-menu">Go Home</a>
        </div>
        <div id="sola_send_results">

        </div>
         <?php } else {?>
        <h2>You Have no mails to send</h2>
        <div>
            <a href="?page=sola-nl-menu">Go Home</a>
        </div>
    <?php
    } ?>
    </div>
<?php include 'footer.php'; */ ?>
   
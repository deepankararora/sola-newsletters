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
<?php
/*
Plugin Name: Newsletters by Sola
Plugin URI: http://www.solaplugins.com
Description: Create beautiful email newsletters in a flash with Sola Newsletters.
Version: 1.01
Author: SolaPlugins
Author URI: http://www.solaplugins.com
*/

ob_start();
global $sola_nl_version;
global $sola_nl_p_version;
global $sola_nl_tblprfx;
global $sola_nl_subs_tbl;
global $sola_nl_list_tbl;
global $sola_nl_subs_list_tbl;
global $sola_nl_camp_tbl;
global $sola_nl_camp_list_tbl;
global $sola_nl_camp_subs_tbl;
global $sola_nl_success;
global $sola_nl_error;
global $sola_nl_style_table;
global $sola_nl_style_elements_table;
global $sola_nl_css_options_table;
global $sola_nl_link_tracking_table;
global $sola_nl_themes_table;

global $sola_global_subid;
global $sola_global_campid;

define("SOLA_PLUGIN_NAME","Sola Newsletters");

global $sola_nl_version;
global $sola_nl_version_string;
$sola_nl_version = "1.01";
$sola_nl_version_string = "beta";



global $wpdb;
$sola_nl_tblprfx = $wpdb->prefix."sola_nl_";
$sola_nl_subs_tbl = $sola_nl_tblprfx."subscribers";
$sola_nl_list_tbl =  $sola_nl_tblprfx."list";
$sola_nl_subs_list_tbl = $sola_nl_tblprfx."subscribers_list";
$sola_nl_camp_tbl = $sola_nl_tblprfx."campaigns";
$sola_nl_camp_list_tbl = $sola_nl_tblprfx."campaign_lists";
$sola_nl_camp_subs_tbl = $sola_nl_tblprfx."campaign_subscribers";
$sola_nl_style_table = $sola_nl_tblprfx."styles";
$sola_nl_style_elements_table = $sola_nl_tblprfx."style_elements";
$sola_nl_css_options_table = $sola_nl_tblprfx."css_options";
$sola_nl_link_tracking_table = $sola_nl_tblprfx."link_tracking";
$sola_nl_themes_table = $sola_nl_tblprfx."themes";


$plugin_url = ABSPATH.'wp-content/plugins';

define("SITE_URL", get_bloginfo('url'));
define("THEME_URL", '');
define("PLUGIN_URL", $plugin_url.'/sola-newsletters');
define("PLUGIN_DIR", plugins_url().'/sola-newsletters');


include "modules/module_editor.php";
include "modules/module_sending.php";
include "modules/module_widget.php";
include "modules/module_activation.php";
include "modules/module_subscribers.php";

add_action('admin_bar_menu', 'sola_sending_mails_tool_bar_name', 998);
add_action( 'admin_bar_menu', 'sola_sending_mails_tool_bar', 999 );
add_action( 'admin_bar_menu', 'sola_sending_mails_tool_bar_pending', 1000);
add_action('admin_head','sola_nl_wp_head');
add_action('wp_enqueue_scripts', 'sola_nl_add_user_stylesheet' );
add_action('admin_enqueue_scripts', 'sola_nl_add_admin_stylesheet');

add_action('init','sola_init');

add_action('admin_menu', 'sola_nl_admin_menu');
register_activation_hook( __FILE__, 'sola_nl_activate' );
register_deactivation_hook( __FILE__, 'sola_nl_deactivate' );

//front end includes
add_action('wp_head', 'sola_nl_wp_js');
add_action('wp_head', 'sola_nl_wp_post_data');

add_filter( 'cron_schedules', 'sola_cron_add_minutely' );    

add_action('wp_ajax_save_template', 'sola_nl_action_callback');
add_action('wp_ajax_preview_mail', 'sola_nl_action_callback');
add_action('wp_ajax_test_mail_2','sola_nl_action_callback');
add_action('wp_ajax_send_mail', 'sola_nl_action_callback');
add_action('wp_ajax_get_perc', 'sola_nl_action_callback');
add_action('wp_ajax_done_sending', 'sola_nl_action_callback');
add_action('wp_ajax_nopriv_sola_nl_sign_up_add_sub', 'sola_nl_action_callback');
add_action('wp_ajax_sola_nl_sign_up_add_sub', 'sola_nl_action_callback');
add_action('wp_ajax_sola_get_next_subs', 'sola_nl_action_callback');

// Shortcodes

add_action( 'sola_cron_send_hook', 'sola_cron_send' );

add_shortcode("confirm_link", "sola_nl_confirm_link");
add_shortcode("sub_name", "sola_nl_sub_name");
add_shortcode("unsubscribe_href", "sola_nl_unsubscribe_href");
add_shortcode("browser_view","sola_nl_view_browser");
add_shortcode("unsubscribe_text", "sola_nl_unsubscribe_text");

// init actions for tracker, links and view in browser

add_action('init', 'sola_nl_init_post_processing');

function sola_init() {
    /* add a "once every minute" cron job for the wp_cron. */
    $plugin_dir = basename(dirname(__FILE__))."/languages/";
    load_plugin_textdomain( 'sola', false, $plugin_dir );
    
    

    $camp_id = sola_check_send_mail_time(3); 
    $camp_id_2 = sola_check_if_currently_sending(3);
    if($camp_id){
        $_SESSION['camp_id'] = $camp_id;
        $_SESSION['no_send'] = false;  
        //sola_ajax_mail_send($camp_id);
        add_action('admin_print_scripts', 'sola_nl_send_js');
    } else if($camp_id_2){
        $_SESSION['camp_id'] = $camp_id_2;
        $_SESSION['no_send'] = true;            
        add_action('admin_print_scripts', 'sola_nl_send_js');
    }
    
    
}

//ajax function
function sola_nl_action_callback() {

    global $wpdb;
    global $sola_nl_camp_tbl;
    global $sola_nl_camp_subs_tbl;
    $check = check_ajax_referer( 'sola_nl', 'security' );
    
    
    
    if ($check == 1) {
        
        if ($_POST['action'] == "get_perc") {
            if (isset($_POST['camp_id'])) { $camp_id = $_POST['camp_id']; } else { return false; }
            
            $sql = "SELECT COUNT(`id`) as `total` FROM `$sola_nl_camp_subs_tbl` WHERE `camp_id` = '$camp_id'";
            $sdf = $wpdb->get_row($sql);
            $total_subscribers = $sdf->total;
            $sql = "SELECT COUNT(`id`) as `total` FROM `$sola_nl_camp_subs_tbl` WHERE `camp_id` = '$camp_id' AND `status` >= 1";
            $sdf = $wpdb->get_row($sql);
            $total_sent = $sdf->total;
            $sent_perc = round((($total_sent / $total_subscribers)*100),0);
            $temp_array[] = $sent_perc;
            
            $sql = "SELECT `last_sent` FROM `$sola_nl_camp_tbl` WHERE `camp_id` = '$camp_id' LIMIT 1";
            $sdf = $wpdb->get_row($sql);
            
            
            if ($sdf->last_sent == "0000-00-00 00:00:00") {
            
                $time_next = __("Pending first batch","sola");
            } else {
            
                $last_sent = strtotime($sdf->last_sent);
                $time_interval = get_option("sola_nl_send_limit_time");

                $time_next = ($last_sent + $time_interval) - time();
                if ($time_next <= 0) {
                    $time_next = __("Sending again in about ","sola").'0'.__(" minute(s)","sola");
                } else {
                    $time_next = __("Sending again in about ","sola").ceil(($time_next / 60)).__(" minute(s)","sola");
                }
            }
            
            
            $temp_array[] = $time_next;
            
            if ($sent_perc == 100) {
                $next_camp_id = sola_check_send_mail_time(3);
                if (!$next_camp_id) { $temp_array[] = "0";  } else { $temp_array[] = $next_camp_id; }
            } else {
                $temp_array[] = "0";
            }
            echo json_encode($temp_array);
            
            
            
            
        }
        
        if ($_POST['action'] == "save_template") {
            
            //var_dump($_POST);
            $sola_html = $_POST['sola_html'];
            $camp_id = $_POST['camp_id'];
            
//            $doc = new DOMDocument();
//            $doc->loadHTML($sola_html);
//            $xpath = new DOMXPath($doc);
//            $nodeList = $xpath->query('//a/@href');
//            for ($i = 0; $i < $nodeList->length; $i++) {
//                # Xpath query for attributes gives a NodeList containing DOMAttr objects.
//                # http://php.net/manual/en/class.domattr.php
//                echo $nodeList->item($i)->value . "<br/>\n";
//            }
            
            
            
//            $regex = '/<a\s+(?:[^"'>]+|"[^"]*"|'[^']*')*href=("[^"]+"|'[^']+'|[^<>\s]+)/i';
//            preg_match_all('/<a\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*href=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $sola_html, $matches);
//            var_dump($matches);

            
            $wpdb->update( 
                $sola_nl_camp_tbl, 
                array( 
                    'email' => $sola_html,
                    'last_save' => date("Y-m-d H:i:s")
                ), 
                array( 'camp_id' => $camp_id ), 
                array( 
                   '%s'	
                ), 
                array( '%d' ) 
            );
            
            sola_nl_save_style($_POST["styles"]);
            
            echo $sola_html;
        }
        if($_POST['action'] == "preview_mail"){
            sola_nl_preview_mail();
        }
        
        if($_POST['action'] == "sola_nl_sign_up_add_sub"){
            global $wpdb;
            global $sola_nl_subs_tbl;
            if(sola_nl_add_single_subscriber(2)){
                $sub_email = $_POST["sub_email"];
                $sql = "SELECT * FROM `$sola_nl_subs_tbl` WHERE `sub_email` = '$sub_email'";
                $result = $wpdb->get_row($sql);
                $sub_key =  $result->sub_key;
                $page_url = get_permalink( get_option("sola_nl_confirm_page"));
                
                
                $body = do_shortcode(nl2br(get_option("sola_nl_confirm_mail")));
                
                sola_mail("", $_POST['sub_email'], "Confirmation Sign Up", $body);
                _e("Thank you for subscribing. You will recive a mail shortly to confirm your account","sola");
            } 
        }
        if($_POST['action'] == "test_mail_2"){
            sola_nl_test_mail_2();
        }
        if($_POST['action'] == "send_mail"){
            sola_nl_ajax_send($_POST['subscribers'], $_POST['camp_id']);
            //sola_nl_send_mail();
        }
        if($_POST['action'] == "done_sending"){
            sola_nl_done_sending_camp($_POST['camp_id']);
        }
        if($_POST['action'] == "sola_get_next_subs"){
            extract($_POST);
            $limit = sola_get_camp_limit($camp_id);
            if($limit){
                $subscribers = sola_nl_camp_subs($camp_id, $limit);
                echo json_encode($subscribers);
            } else {
                echo false;
            }
        }
    } 
    
    
    
    
    die(); // this is required to return a proper result
}


function sola_nl_admin_menu() {
    $sola_nl_mainpage = add_menu_page('Newsletter', __('Newsletter','sola_nl'), 'manage_options', 'sola-nl-menu', 'sola_nl_admin_menu_layout', plugin_dir_url( __FILE__ )."images/sola_logo.png");
    add_submenu_page('sola-nl-menu', __('Subscribers','sola'), __('Subscribers','sola'), 'manage_options' , 'sola-nl-menu-subscribers', 'sola_nl_subscribers_page');
    add_submenu_page('sola-nl-menu', __('Lists','sola'), __('Lists','sola'), 'manage_options' , 'sola-nl-menu-lists', 'sola_nl_lists_page');
    add_submenu_page('sola-nl-menu', __('Settings','sola'), __('Settings','sola'), 'manage_options' , 'sola-nl-menu-settings', 'sola_nl_admin_settings_layout');
    add_submenu_page('sola-nl-menu', __('Error Log','sola'), __('Error Log','sola'), 'manage_options' , 'sola-nl-menu-error-log', 'sola_nl_admin_error_log_layout');
}

function sola_nl_wp_head() {
    @session_start();
    // post data handling
    
   global $sola_nl_success;
   global $sola_nl_error;
   
   
     // check for apc-object-cache.php (godaddy)

    $checker = get_dropins();
    if (isset($checker['object-cache.php'])) {
	echo "<div id=\"message\" class=\"error\"><p>".__("Please note: <strong>Sola Newsletters will not function correctly while using APC Object Cache.</strong> We have found that GoDaddy hosting packages automatically include this with their WordPress hosting packages. Please email GoDaddy and ask them to remove the object-cache.php from your wp-content/ directory.","sola")."</p></div>";
    }
   
   if (isset($_POST['sola_nl_edit_subscriber'])) {
      $sola_nl_check = sola_nl_update_subscriber();
      
      if ( is_wp_error($sola_nl_check) ) sola_return_error($sola_nl_check);
      else echo "<div id=\"message\" class=\"updated\"><p>".__("Subscribers Updated","sola")."</p></div>";
   }
   if (isset($_POST['sola_nl_import_subscribers'])) {
      if (is_uploaded_file($_FILES['sub_import_file']['tmp_name'])) {
           /* check if correct file type */
           if (strpos($_FILES['sub_import_file']['type'], ".csv" !== false) ) { sola_return_error(new WP_Error( 'sola_error', __( 'Upload error','sola'), __("Please ensure you upload a CSV file. The file you are trying to upload is a ",'sola').$_FILES['sub_import_file']['type'].__(' type file','sola') )); }
           else {
               $arm_nl_check = sola_import_file_subscribers($_POST['sub_list']);
               if ( is_wp_error($arm_nl_check) ) sola_return_error($arm_nl_check);
                else {
                    $_SESSION['arm_nl_success'] = __("Subscribers Imported Successfully","sola");
                    wp_redirect("admin.php?page=sola-nl-menu-subscribers");
                    exit('Cannot redirect');
                }
               
           }
       } else {
       
            $arm_nl_check = sola_import_subscribers($_POST['sub_import_excel'],$_POST['sub_list']);
            if ( is_wp_error($arm_nl_check) ) sola_return_error($arm_nl_check);
            else {
                $_SESSION['arm_nl_success'] = __("Subscribers Imported Successfully","sola");
                wp_redirect("admin.php?page=sola-nl-menu-subscribers");
                exit('Cannot redirect');
            }
       }
      
   }
   if(isset($_POST['sola_nl_new_subscriber'])){
        if(isset($_POST['sub_list'])){
            $check = sola_nl_add_single_subscriber();
            if($check == true){?>
               <div class="updated">
                  <p>Subscriber Added</p>
               </div>
               <?php
            } else {
                echo $check;
            }
        } else { ?>
            <div class="error">
                <p>Please Select a List when adding the subscriber</p>
            </div>
            <?php
        }
   }
   if(isset($_GET['action']) && $_GET['action'] == "delete_subscriber"){
      $sola_nl_check = sola_nl_delete_subscriber();
      if ( is_wp_error($sola_nl_check) ) sola_return_error($sola_nl_check);
         else echo "<div id=\"message\" class=\"updated\"><p>".__("Subscribers Deleted","sola")."</p></div>";
   }
   if(isset($_GET['action']) && $_GET['action'] == "delete_camp"){
      $sola_nl_check = sola_nl_delete_camp();
      if ( is_wp_error($sola_nl_check) ) sola_return_error($sola_nl_check);
      else echo "<div id=\"message\" class=\"updated\"><p>".__("Campaign Deleted","sola")."</p></div>";
      
   }
   if(isset($_POST['sola_nl_new_list'])){
      sola_nl_add_list();
   }
   if(isset($_POST['sola_nl_edit_list'])){
      $sola_nl_check = sola_nl_update_list();
      if ( is_wp_error($sola_nl_check) ) sola_return_error($sola_nl_check);
      else echo "<div id=\"message\" class=\"updated\"><p>".__("List Updated","sola")."</p></div>";
   }
   if(isset($_GET['action']) && $_GET['action'] == "delete_list"){
      $sola_nl_check = sola_nl_delete_list();
      if ( is_wp_error($sola_nl_check) ) sola_return_error($sola_nl_check);
      else echo "<div id=\"message\" class=\"updated\"><p>".__("List Deleted","sola")."d</p></div>";
      
   }
   if(isset($_POST["sola_nl_save_settings"])){
      if(sola_nl_update_settings()) {
          echo "<div id=\"message\" class=\"updated\"><p>".__("Settings Saved","sola")."</p></div>";
      }
   }
   if(isset($_POST['sola_nl_new_camp'])){
      $sola_nl_check = sola_nl_add_camp();
      if ( is_wp_error($sola_nl_check) ) { sola_return_error($sola_nl_check); }
      else  {
          $template_page = site_url()."/wp-admin/admin.php?page=sola-nl-menu&action=theme&camp_id=$sola_nl_check";
         //$new_camp_page = site_url()."/wp-admin/admin.php?page=sola-nl-menu&action=editor&camp_id=$sola_nl_check";
         ob_end_clean();
         header("location:".$template_page);
         exit();
      }
   }
   if(isset($_POST["sola_nl_edit_camp"])){
       $sola_nl_check = sola_nl_update_camp();
      if ( is_wp_error($sola_nl_check) ) { sola_return_error($sola_nl_check); }
        else {
         $new_camp_page = site_url()."/wp-admin/admin.php?page=sola-nl-menu&action=editor&camp_id=".$_POST["camp_id"];
         ob_end_clean();
         header("location:".$new_camp_page);
         exit();
      }
   }
//   if($_POST["sola_nl_finish_campaign"]){
//       
//       $sola_nl_check = sola_nl_finish_camp();
//      if ( is_wp_error($sola_nl_check) ) { sola_return_error($sola_nl_check); }
//      else {
//           echo "<div id=\"message\" class=\"updated\"><p>".__("Campaign Ready to Send","sola")."</p></div>";
//       }
//   }
    if(isset($_POST['action']) && $_POST["action"] == "sola-delete-subs" && isset($_POST['sola_check_subs']) && $_POST['sola_check_subs']){
        foreach($_POST['sola_check_subs'] as $sub_id){
            $sola_nl_check = sola_nl_delete_subscriber($sub_id);
            if ( is_wp_error($sola_nl_check) ) { 
                sola_return_error($sola_nl_check); 
                $check = false;
                break; 
            } else {
                $check = true;
            }
        }
        if($check == true){
            echo "<div id=\"message\" class=\"updated\"><p>".__("Subscribers Deleted","sola")."</p></div>";
        }
    }
    
    if(isset($_POST['action']) && $_POST["action"] == "delete_multi_camps" && isset($_POST["sola_camp_checkbox"]) && $_POST["sola_camp_checkbox"]){
        foreach($_POST["sola_camp_checkbox"] as $camp_id){
            $sola_nl_check = sola_nl_delete_camp($camp_id);
            if ( is_wp_error($sola_nl_check) ) { 
                sola_return_error($sola_nl_check);
                $check = false;
                break; 
            }
            else {
                $check = true;
            }
        }
        if($check == true){
                echo "<div id=\"message\" class=\"updated\"><p>".__("Campaigns Deleted","sola")."</p></div>";
            }
    }
    if(isset($_POST['sola_set_theme'])){
        
        $sola_nl_check = sola_set_theme($_POST['theme_id'], $_POST['camp_id']);
        if(is_wp_error($sola_nl_check)){
            sola_return_error($sola_nl_check);
        } else {
            $editor = site_url()."/wp-admin/admin.php?page=sola-nl-menu&action=editor&camp_id=".$_POST["camp_id"];
            header('location:'.$editor);
            exit();
        }
        
    }
    if (isset($_POST['action']) && $_POST['action'] == 'sola_submit_find_us') {
        sola_nl_feedback_head();
        echo "<div class=\"updated\"><p>". __("Thank You for your feedback!","sola")."</p></div>";        
    }
}

function sola_nl_admin_javascript() {
    // any additional JS goes here
}



function sola_nl_admin_menu_layout() {
    global $sola_nl_success;
    global $sola_nl_error;


    if ($_GET['page'] == "sola-nl-menu" && !isset($_GET['action'])) { 
        
        if(get_option('solag_nl_first_time')){
            update_option('solag_nl_first_time', false);
            if (class_exists("APC_Object_Cache")) {
                /* do nothing here as this caches the "first time" option and the welcome page just loads over and over again. quite annoying really... */
                include('includes/admin-page.php');
            }  else { 
                include('includes/welcome_page.php');
            }
        } else {
            include('includes/admin-page.php');
        }
    } else if($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "delete_camp"){
        include('includes/admin-page.php');
    } else if ($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "new_campaign") {
        include('includes/new_campaign.php');
    }else if ($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "new_subscriber") {
        include('includes/new_subscriber.php');
    }else if ($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "import") {
        include('includes/import_subscribers.php');
    }else if ($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "new_list") {
        include('includes/new_list.php');
    }else if ($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "editor"){
       include('includes/editor.php');
       //Jarryd
    }else if ($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "preview"){
       include('includes/preview.php');
       //
    } else if($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "confirm_camp"){
        include('includes/confirm_campaign.php');
    } else if($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "send_campaign"){
        include ('includes/send_campaign.php');
    } else if($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "camp_stats"){
        include ('includes/campaign_stats.php');
    } else if ($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "theme"){
        if(function_exists('sola_nl_register_pro_version')){
            include (PLUGIN_URL_PRO.'/includes/campaign_theme_pro.php');
        } else {
            include('includes/campaign_theme.php');
        }
    }
}

function sola_nl_admin_menu_layout_display() {
        
}

/*------------------- MOVED ACTVATION FUNCTIONS TO module_activation.php --------------------------------- */


function sola_nl_add_user_stylesheet() {
    wp_register_style( 'sola_nl_styles', plugins_url('/css/style.css', __FILE__) );
    wp_enqueue_style( 'sola_nl_styles' );
    
}
function sola_nl_add_admin_stylesheet() {
    
    if(isset($_GET['page']) && isset($_GET['action'])){
        if($_GET['page'] == "sola-nl-menu" && $_GET['action'] == "camp_stats" ){
            wp_register_style( 'sola_nl_bootstrap_css', PLUGIN_DIR.'/css/bootstrap.min.css' );
            wp_enqueue_style( 'sola_nl_bootstrap_css' );
            wp_register_style( 'sola_nl_bootstrap_theme_css', PLUGIN_DIR.'/css/bootstrap-theme.min.css' );
            wp_enqueue_style( 'sola_nl_bootstrap_theme_css' );
            wp_register_style( 'sola_nl_datatables_css', PLUGIN_DIR.'/css/data_table.css' );
            wp_enqueue_style( 'sola_nl_datatables_css' );
        }
    }
    if(isset($_GET['page'])){
       if($_GET['page'] == "sola-nl-menu" || $_GET['page'] == "sola-nl-menu-settings"){
            wp_register_style( 'sola_nl_jquery_css', plugins_url('/css/jquery-ui.css', __FILE__) );
            wp_enqueue_style( 'sola_nl_jquery_css' );
        }     
    }
    if (is_rtl()) {
        wp_register_style( 'sola_nl_styles', plugins_url('/css/style_rtl.css', __FILE__) );
        wp_enqueue_style( 'sola_nl_styles' );
    } else { 
        wp_register_style( 'sola_nl_styles', plugins_url('/css/style.css', __FILE__) );
        wp_enqueue_style( 'sola_nl_styles' );
    }
    
}

add_action('admin_print_scripts', 'sola_nl_admin_scripts_basic');

function sola_nl_admin_scripts_basic() {
    wp_enqueue_script('jquery');
   
    if(isset($_GET['page']) && isset($_GET['action']) && $_GET['page'] == "sola-nl-menu" && $_GET['action'] == "camp_stats"){
        wp_register_script('sola_nl_bootstrap_js', PLUGIN_DIR."/js/bootstrap.min.js", false);
        wp_enqueue_script( 'sola_nl_bootstrap_js' );
        wp_register_script('sola_nl_datatables_js', PLUGIN_DIR."/js/jquery.dataTables.js", false);
        wp_enqueue_script( 'sola_nl_datatables_js' );
    }

    if (isset($_GET['page']) && ($_GET['page'] == "sola-nl-menu-settings" || $_GET['page'] == "sola-nl-menu" || $_GET['page'] == "sola-nl-menu-subscribers" || (isset($_GET['action']) && $_GET['action'] == "preview"))) {  
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script( 'jquery-ui-tabs');
        wp_register_script('sola-nl-tabs', plugins_url('js/sola_nl_tabs.js',__FILE__), array('jquery-ui-core'), '', true);
        wp_enqueue_script('sola-nl-tabs');
        wp_register_script('sola-nl-js', plugins_url('js/sola_nl.js',__FILE__), array('jquery'), '', false);
        wp_enqueue_script('sola-nl-js');
    }
}


function sola_nl_admin_settings_layout() {
    sola_nl_settings_page_basic();
}
function sola_nl_admin_error_log_layout() {
    include('includes/error-log-page.php');
}


function sola_nl_settings_page_basic() {
    include('includes/settings-page.php');
}
function sola_nl_subscribers_page(){
   include('includes/subscribers-page.php');
}
function sola_nl_lists_page (){
   include 'includes/lists-page.php';
}

function sola_nl_get_subscribers($list_id = false, $limit = false, $page = false, $order = false, $orderBy = false){
    
    global $wpdb;
    global $sola_nl_subs_tbl;
    global $sola_nl_subs_list_tbl;
    $where = "";
    $limit_sql = "";
    $order_sql = "";
    if($list_id){
       $where = "WHERE `$sola_nl_subs_list_tbl`.`list_id` = '$list_id'";
    }
    if($limit){
        if(!$page){
            $page = 1;
        }
        $from = ($page - 1)*$limit;
        $limit_sql = "LIMIT $from , $limit";
    }
    if($orderBy != false){
        if(!$order){
            $order = "ASC";
        }
        $order_sql = "ORDER BY `$sola_nl_subs_tbl`.`$orderBy` $order"; 
    }
    $sql = "SELECT  `$sola_nl_subs_tbl`.*
             FROM  `$sola_nl_subs_tbl` 
             LEFT JOIN  `$sola_nl_subs_list_tbl` ON  `$sola_nl_subs_tbl`.`sub_id` =  `$sola_nl_subs_list_tbl`.`sub_id`
            ".$where."
             GROUP BY `$sola_nl_subs_tbl`.`sub_id`
             ".$order_sql." ".$limit_sql;

    return $wpdb->get_results($sql);
}
function sola_nl_get_total_subs(){
    global $wpdb;
    global $sola_nl_subs_tbl;
    $sql = "SELECT * FROM `$sola_nl_subs_tbl`";
    $wpdb->query($sql);
    return $wpdb->num_rows;
}
function sola_nl_subscriber_status($status){
   if($status == 1){
      echo "Subscribed";
   } else if($status == 2){
       echo "Pending Confirmation";
   } else {
      echo "Un-subscribed";
   }
}
function sola_nl_add_single_subscriber($status = 1){
    global $wpdb;
    global $sola_nl_subs_tbl;
    if(sola_cts()){
        extract($_POST);
        if($sub_email){
           $sub_key = wp_hash_password( $sub_email );
           $sola_nl_sub_check = $wpdb->insert( $sola_nl_subs_tbl, array( 'sub_id' => '', 'sub_name' => $sub_name, 'sub_email' => $sub_email, 'sub_key' => $sub_key , "status" => $status ) ) ;
           sola_nl_add_sub_list($sub_list, $wpdb->insert_id);
           if($sola_nl_sub_check == false){ 
                    return new WP_Error( 'db_query_error', 
			__( 'Could not add subscriber' ), $wpdb->last_error );
           } else{
               return true;
           }
        } else {?>
           <div class="error">
              <p><?php _e("Please enter an E-mail Address","sola"); ?></p>
           </div>
           <?php
        }
    } else {
        echo sola_se();
    }
}

// adds subscriber to lists
function sola_nl_add_sub_list($list_ids, $sub_id){
   global $wpdb;
   global $sola_nl_subs_list_tbl;
   $wpdb->delete( $sola_nl_subs_list_tbl, array( 'sub_id' => $sub_id )); // Delete all assciated to this subscriber
   foreach($list_ids as $list_id){
      $wpdb->insert( $sola_nl_subs_list_tbl, array( 'id' => '', 'list_id' => $list_id, 'sub_id' => $sub_id)); // add each one again
   }
}
function sola_nl_add_list(){
   global $wpdb;
   global $sola_nl_list_tbl;
   extract($_POST);
   if($list_name){
      if($wpdb->insert( $sola_nl_list_tbl, array( 'list_id' => '', 'list_name' => $list_name, 'list_description' => $list_description) ) == false){ ?>
         <div class="error">
            <p>There Was an Error adding the List</p>
         </div>
         <?php
      } else{?>
         <div class="updated">
            <p>List Added</p>
         </div>
         <?php
      }
   } else {?>
      <div class="error">
         <p>Please enter a List Name</p>
      </div>
      <?php         
   }
}
function sola_nl_get_lists(){
   global $wpdb;
   global $sola_nl_list_tbl;
   $sql = "SELECT * FROM `$sola_nl_list_tbl`";
   return $wpdb->get_results($sql);
}
function sola_nl_total_list_subscribers($list_id){
   global $wpdb;
   global $sola_nl_subs_list_tbl;
   global $sola_nl_subs_tbl;
   $sql = "SELECT  `$sola_nl_subs_list_tbl`.*, COUNT(`list_id`) AS `total`
            FROM  `$sola_nl_subs_list_tbl` 
            LEFT JOIN  `$sola_nl_subs_tbl` ON  `$sola_nl_subs_list_tbl`.`sub_id` =  `$sola_nl_subs_tbl`.`sub_id`
            WHERE `$sola_nl_subs_list_tbl`.`list_id` = '$list_id' AND `$sola_nl_subs_tbl`.`status` = '1'";
   //$sql = "SELECT *, COUNT(`list_id`) AS `total` FROM `$sola_nl_subs_list_tbl` WHERE `list_id` = '$list_id' AND `status` = '1'";
   //echo $sql; //debug
   $result = $wpdb->get_row($sql);
   $total = $result->total;
   
   return $total;
}
function sola_nl_get_subscriber($sub_id){
   global $wpdb;
   global $sola_nl_subs_tbl;
   $sql = "SELECT * FROM `$sola_nl_subs_tbl` WHERE `sub_id` = '$sub_id'";
   return $wpdb->get_row($sql);
}

function sola_nl_update_subscriber(){
   global $wpdb;
   global $sola_nl_subs_tbl;
   sola_nl_add_sub_list($_POST['sub_list'], $_POST['sub_id']);
   if ($wpdb->update( 
      $sola_nl_subs_tbl, 
      array( 
         'sub_email' => $_POST['sub_email'],	
         'sub_name' => $_POST['sub_name']	
      ), 
      array( 'sub_id' => $_POST['sub_id'] ), 
      array( 
         '%s',	
         '%s'	
      ), 
      array( '%d' ) 
   ) === FALSE) {
      return new WP_Error( 'db_query_error', 
			__( 'Could not execute query' ), $wpdb->last_error );
   } else {
      return true;
   }
   
}
function sola_nl_delete_subscriber($sub_id = null) {
   global $wpdb;
   global $sola_nl_subs_tbl;
   global $sola_nl_subs_list_tbl;
   global $sola_nl_camp_subs_tbl;
   if(!$sub_id){
       extract($_GET);
   }
   if(($wpdb->delete( $sola_nl_subs_tbl, array( 'sub_id' => $sub_id )))){
      $wpdb->delete( $sola_nl_subs_list_tbl, array( 'sub_id' => $sub_id ));
      return true;
   } else {
      return new WP_Error( 'db_query_error', 
			__( 'Could not execute query' ), $wpdb->last_error );
   }
}
function sola_nl_get_list($list_id){
   global $wpdb;
   global $sola_nl_list_tbl;
   $sql = "SELECT * FROM `$sola_nl_list_tbl` WHERE `list_id` = '$list_id'";
   return $wpdb->get_row($sql);
}
function sola_nl_update_list(){
   global $wpdb;
   global $sola_nl_list_tbl;

   if ($wpdb->update( 
      $sola_nl_list_tbl, 
      array( 
         'list_name' => $_POST['list_name'],	
         'list_description' => $_POST['list_description']	
      ), 
      array( 'list_id' => $_POST['list_id'] ), 
      array( 
         '%s',	
         '%s'	
      ), 
      array( '%d' ) 
   ) === FALSE) {
      return new WP_Error( 'db_query_error', 
			__( 'Could not execute query' ), $wpdb->last_error );
   } else {
      return true;
   }
   
}
function sola_nl_delete_list(){
   global $wpdb;
   global $sola_nl_list_tbl;
   extract($_GET);
   if($wpdb->delete( $sola_nl_list_tbl, array( 'list_id' => $list_id ) )){
      return true;
   } else {
      return new WP_Error( 'db_query_error', 
			__( 'Could not execute query' ), $wpdb->last_error );
   }
}
function sola_nl_check_if_selected_list_sub($list_id, $sub_id){
   global $wpdb;
   global $sola_nl_subs_list_tbl;
   if($wpdb->get_row("SELECT * FROM `$sola_nl_subs_list_tbl` WHERE `list_id` = '$list_id' AND `sub_id` = '$sub_id'")){
      return true;
   } else {
      return false;
   }
}
function sola_nl_get_subscriber_list($sub_id){
   global $wpdb;
   global $sola_nl_list_tbl;
   global $sola_nl_subs_list_tbl;
   $sql = "SELECT `$sola_nl_list_tbl`.`list_name`, `$sola_nl_list_tbl`.`list_id`
            FROM  `$sola_nl_list_tbl` 
            LEFT JOIN  `$sola_nl_subs_list_tbl` ON  `$sola_nl_list_tbl`.`list_id` =  `$sola_nl_subs_list_tbl`.`list_id`
            WHERE `$sola_nl_subs_list_tbl`.`sub_id` = '$sub_id'";
   return $wpdb->get_results($sql);
}
function sola_nl_update_settings(){
    
   extract($_POST);
   //var_dump($_POST);
   if(!$sola_nl_unsubscribe){
       $sola_nl_unsubscribe = "Unsubscribe";
   }
   update_option("sola_nl_email_note", $sola_nl_email_note);
   update_option("sola_nl_notifications", $sola_nl_notifications);
   //Signiture needs to be added
   //update_option("sola_nl_sig",$sola_nl_sig);
   update_option("sola_nl_unsubscribe",$sola_nl_unsubscribe);
   update_option("sola_nl_sent_from", $sola_nl_sent_from);
   update_option("sola_nl_sent_from_name",$sola_nl_sent_from_name);
   update_option("sola_nl_reply_to",$sola_nl_reply_to);
   update_option("sola_nl_reply_to_name",$sola_nl_reply_to_name);
   update_option("sola_nl_send_method", $sola_nl_send_method);
   update_option("sola_nl_host",$sola_nl_host);
   update_option("sola_nl_username",$sola_nl_username);
   update_option("sola_nl_password",$sola_nl_password);
   update_option("sola_nl_port",$sola_nl_port);
   update_option("sola_nl_sign_up_title", $sola_nl_sign_up_title);
   update_option("sola_nl_sign_up_btn","$sola_nl_sign_up_btn");
   update_option("sola_nl_utm_source",$sola_nl_utm_source);
   update_option("sola_nl_utm_medium",$sola_nl_utm_medium);
   //var_dump($_POST['sola_nl_sign_up_sub_list']);
   update_option("sola_nl_sign_up_lists", serialize($_POST['sola_nl_sign_up_sub_list']));
   update_option("sola_nl_social_links", $social_links);
   update_option("sola_nl_encryption", $encryption);
   update_option("sola_nl_confirm_mail",$sola_nl_confirm_mail);
   update_option("sola_nl_hosting_provider", $sola_nl_hosting_provider);
   update_option("sola_nl_send_limit_qty", $sola_nl_send_limit_qty);
   update_option("sola_nl_send_limit_time", $sola_nl_send_limit_time);
   return true;
}
function sola_nl_add_camp_list($list_ids, $camp_id){
   global $wpdb;
   global $sola_nl_camp_list_tbl;
   $wpdb->delete( $sola_nl_camp_list_tbl, array( 'camp_id' => $camp_id )); // Delete all assciated to this campaign
   foreach($list_ids as $list_id){
      $wpdb->insert( $sola_nl_camp_list_tbl, array( 'id' => '', 'list_id' => $list_id, 'camp_id' => $camp_id)); // add each one again
   }
}
function sola_nl_add_camp(){
   global $wpdb;
   global $sola_nl_camp_tbl;
   extract($_POST);
   if(isset($subject) && $subject){
      if(isset($sub_list) && $sub_list){
         if($wpdb->insert( $sola_nl_camp_tbl, array( 'camp_id' => '', 'subject' => $subject))){
            $camp_id = $wpdb->insert_id;
            sola_nl_add_camp_list($sub_list, $camp_id);
            return $camp_id;
         } else {
            return new WP_Error( 'db_query_error', 
               __( 'Could not execute query' ), $wpdb->last_error );
         }
      } else {
         return new WP_Error( 'Subject Error', 
         __( 'Please Select at least one list to send to' ) );
      }
   } else {
      return new WP_Error( 'Subject Error', 
      __( 'Please Enter a Subject' ) );
   }
}

function sola_nl_get_camps($limit, $page, $order = "DESC", $orderBy = "date_created"){
    $from = ($page - 1)*$limit;
    global $wpdb;
    global $sola_nl_camp_tbl;
    $sql = "SELECT * FROM `$sola_nl_camp_tbl`  ORDER BY `$orderBy` $order LIMIT $from , $limit";
    //echo $sql;
    return $wpdb->get_results($sql);
}
function sola_nl_total_camps(){
    global $wpdb;
    global $sola_nl_camp_tbl;
    $sql = "SELECT * FROM `$sola_nl_camp_tbl`";
    $wpdb->query($sql);
    return $wpdb->num_rows;
}
function sola_nl_get_camp_lists($camp_id){
   global $wpdb;
   global $sola_nl_list_tbl;
   global $sola_nl_camp_list_tbl;
    $sql = "SELECT `$sola_nl_list_tbl`.`list_name`, `$sola_nl_list_tbl`.`list_id`
            FROM  `$sola_nl_list_tbl` 
            LEFT JOIN  `$sola_nl_camp_list_tbl` ON  `$sola_nl_list_tbl`.`list_id` =  `$sola_nl_camp_list_tbl`.`list_id`
            WHERE `$sola_nl_camp_list_tbl`.`camp_id` = '$camp_id'";
   return $wpdb->get_results($sql);
}
function sola_nl_delete_camp($camp_id = false){
    global $wpdb;
    global $sola_nl_camp_tbl;
    global $sola_nl_camp_list_tbl;
    if(!$camp_id){
        extract($_GET);
    }
    $wpdb->delete( $sola_nl_camp_list_tbl, array( 'camp_id' => $camp_id ));
    if($wpdb->delete( $sola_nl_camp_tbl, array( 'camp_id' => $camp_id ))){
       return true;
    } else {
       return new WP_Error( 'db_query_error', 
                         __( 'Could not execute query' ), $wpdb->last_error );
    }
}
function sola_nl_get_camp_details($camp_id){
    global $wpdb;
    global $sola_nl_camp_tbl;
    $sql = "SELECT * FROM `$sola_nl_camp_tbl` WHERE `camp_id` = '$camp_id'";
    return $wpdb->get_row($sql);
}
function sola_nl_check_if_selected_list_camp($list_id, $camp_id){
   global $wpdb;
   global $sola_nl_camp_list_tbl;
   if($wpdb->get_row("SELECT * FROM `$sola_nl_camp_list_tbl` WHERE `list_id` = '$list_id' AND `camp_id` = '$camp_id'")){
      return true;
   } else {
      return false;
   }
}
function sola_nl_update_camp(){
   global $wpdb;
   global $sola_nl_camp_tbl;
   extract($_POST);
   if($subject){
      if($sub_list){
          
         if ($wpdb->update( 
            $sola_nl_camp_tbl, 
            array( 
               'subject' => $_POST['subject']
            ), 
            array( 'camp_id' => $_POST['camp_id'] ), 
            array( 
               '%s'	
            ), 
            array( '%d' ) 
         ) === FALSE) {
            return new WP_Error( 'db_query_error', 
                              __( 'Could not execute query' ), $wpdb->last_error );
         } else {
            sola_nl_add_camp_list($sub_list, $camp_id);
            return true;
         }
      } else {
         return new WP_Error( 'Subject Error', 
         __( 'Please Select at least one list to send to' ) );
      }
   } else {
      return new WP_Error( 'Subject Error', 
      __( 'Please Enter a Subject' ) );
   }
}
function sola_nl_get_letter($camp_id, $theme_id = null){
    global $wpdb;
    global $sola_nl_camp_tbl;
    global $sola_nl_themes_table;
    
    $sql = "SELECT * FROM `$sola_nl_camp_tbl` WHERE `camp_id` = '$camp_id'";
    $result = $wpdb->get_row($sql);
    if($result->email){
        $letter = stripcslashes($result->email);
    } else {
        $sql = "SELECT * FROM `$sola_nl_themes_table` WHERE `theme_id` = '$theme_id'";
        
        $result = $wpdb->get_row($sql);
        $letter = stripcslashes($result->theme_html);
    }
    
    return $letter;
}

function sola_nl_finish_camp($type){
    global $wpdb;
    global $sola_nl_camp_tbl;
    $limit = get_option('sola_nl_send_limit_qty');
    if($wpdb->update( 
            $sola_nl_camp_tbl, 
            array( 
               'sent_from' => $_POST['sent_from'],
               'sent_from_name' => $_POST['sent_from_name'],
               'reply_to' => $_POST['reply_to'],
               'reply_to_name' => $_POST['reply_to_name'],
               'status' => $type,
               'time_frame_qty' => $limit,
                
            ), 
            array( 'camp_id' => $_POST['camp_id'] ), 
            array( 
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
            ), 
            array( '%d' ) 
         )=== FALSE) {
      return new WP_Error( 'db_query_error', 
			__( 'Could not execute query' ), $wpdb->last_error );
   } else {
      sola_nl_add_camp_list($_POST['sub_list'],$_POST['camp_id'] );
      return true;
   }
}
// ADD EMAIL ADDRSS FOR CAMPAIGN
function sola_nl_add_subs_to_camp($camp_id){
    global $wpdb;
    global $sola_nl_camp_subs_tbl;
    global $sola_nl_camp_list_tbl;
    global $sola_nl_subs_list_tbl;
    global $sola_nl_subs_tbl;
    $sql = "SELECT * FROM `$sola_nl_camp_subs_tbl` WHERE `camp_id` = '$camp_id'";
    $wpdb->query($sql);
    $check = $wpdb->num_rows;
    $values = "";
    if($check == false){
        //remove in pro
        $limit = sola_nl_ml();
        $sql = "SELECT `$sola_nl_subs_list_tbl`.`sub_id`
                FROM `$sola_nl_camp_list_tbl`
                LEFT JOIN `$sola_nl_subs_list_tbl`
                ON `$sola_nl_camp_list_tbl`.`list_id`= `$sola_nl_subs_list_tbl`.`list_id`
                    LEFT JOIN `$sola_nl_subs_tbl`
                    ON `$sola_nl_subs_tbl`.`sub_id` = `$sola_nl_subs_list_tbl`.`sub_id`
                WHERE `camp_id` = '$camp_id' AND `status` = 1
                            
                GROUP BY `sub_id`
                LIMIT $limit";
        $sub_ids = $wpdb->get_results($sql);
        $insertsql = "INSERT INTO `$sola_nl_camp_subs_tbl` (`id`, `camp_id`,`sub_id`, `status`) VALUES";
        $i = 1;
        foreach($sub_ids as $sub_id){
            if($i != 1){
                $values .= ",";
            }
            $values .= " ('', '$camp_id', '$sub_id->sub_id','0')";
            $i++;
        }
        $wpdb->query($insertsql.$values);
    }
}
function sola_nl_total_camp_subs($camp_id){
    global $wpdb;
    global $sola_nl_camp_subs_tbl;
    
    $sql = "SELECT COUNT(*) as `total`
        FROM `$sola_nl_camp_subs_tbl`
        WHERE `camp_id` = '$camp_id'";
    $result = $wpdb->get_row($sql);
    return $result->total;
}

//GET ALL EMAILS TO SEND LINKED TO CAMPAIGN
function sola_nl_camp_sub_emails($camp_id, $limit = false){
    global $wpdb;
    global $sola_nl_camp_subs_tbl;
    global $sola_nl_subs_tbl;
    $sql = "SELECT `$sola_nl_subs_tbl`.`sub_email`
            FROM `$sola_nl_camp_subs_tbl`
            LEFT JOIN `$sola_nl_subs_tbl`
            ON `$sola_nl_subs_tbl`.`sub_id` = `$sola_nl_camp_subs_tbl`.`sub_id`
            WHERE `$sola_nl_subs_tbl`.`status` = 1 
                AND `$sola_nl_camp_subs_tbl`.`camp_id` = '$camp_id'
                AND `$sola_nl_camp_subs_tbl`.`status` = 0
                ";
    if($limit){
        $sql.= $limit;
    }
    $results = $wpdb->get_results($sql);
    $emails = array();
    foreach($results as $result){
        $emails[] .= $result->sub_email;
    }
    return $emails;
}
// get all subscribers info linked to campaign
function sola_nl_camp_subs($camp_id, $limit = false){
    global $wpdb;
    global $sola_nl_camp_subs_tbl;
    global $sola_nl_subs_tbl;
    $sql = "SELECT `$sola_nl_subs_tbl`.`sub_email`, `$sola_nl_subs_tbl`.`sub_id`, `$sola_nl_subs_tbl`.`sub_key`, `$sola_nl_subs_tbl`.`sub_name`
            FROM `$sola_nl_camp_subs_tbl`
            LEFT JOIN `$sola_nl_subs_tbl`
            ON `$sola_nl_subs_tbl`.`sub_id` = `$sola_nl_camp_subs_tbl`.`sub_id`
            WHERE `$sola_nl_subs_tbl`.`status` = 1 
                AND `$sola_nl_camp_subs_tbl`.`camp_id` = '$camp_id'
                AND `$sola_nl_camp_subs_tbl`.`status` = 0
                ";
    if($limit){
        $sql.= " LIMIT ".$limit;
    }
    $results = $wpdb->get_results($sql);
    if($results){
        return $results;
    } else {
        return false;
    }
}

function sola_nl_send_notification($subject, $body){
    if(get_option("sola_nl_notifications") == 1){
        $to = get_option("sola_nl_email_note");
        wp_mail($to , $subject, $body );
    }
}
function sola_nl_get_camp_stats($camp_id, $status, $sent = false){
    global $wpdb;
    global $sola_nl_camp_subs_tbl;
    if($status) $status = "status = '$status' AND";
    $sql = "SELECT COUNT(*) as `total` FROM `$sola_nl_camp_subs_tbl` WHERE $status camp_id = '$camp_id'"; 
    if($sent) { 
        $sql.= " AND `status` <> 0";
    }
    $result = $wpdb->get_row($sql);
    return $result->total;
}
function sola_nl_get_camp_subs($camp_id, $sent = false){
    global $wpdb;
    global $sola_nl_camp_subs_tbl;
    $sql = "SELECT * FROM `$sola_nl_camp_subs_tbl` WHERE `camp_id` = '$camp_id'";
    if($sent){
        $sql.= " AND `status` <> 0";
    }
    return $wpdb->get_results($sql);
}
function sola_nl_create_page($slug , $title, $content){
    // Initialize the post ID to -1. This indicates no action has been taken.
    $post_id = -1;

    // Setup the author, slug, and title for the post
    $author_id = 1;
    
    $post_type = "page";

    // If the page doesn't already exist, then create it
    $sola_check_page = get_page_by_title( $title ,'',$post_type);
    if( $sola_check_page == null ) {

            // Set the page ID so that we know the page was created successfully
            $post_id = wp_insert_post(
                array(
                    'comment_status'	=>	'closed',
                    'ping_status'	=>	'closed',
                    'post_author'	=>	$author_id,
                    'post_name'		=>	$slug,
                    'post_title'	=>	$title,
                    'post_status'	=>	'publish',
                    'post_type'		=>	$post_type,
                    'post_content'      =>      $content
                )
            );
            return $post_id;

    // Otherwise, we'll stop and set a flag
    } else {

        // Arbitrarily use -2 to indicate that the page with the title already exists
        
        return $sola_check_page->ID;
        
        
        //$post_id = -2;

    } // end if
}
// getting link to page to approve account
function sola_nl_wp_post_data(){
    global $wpdb;
    global $sola_nl_subs_tbl;
    if(isset($_GET["action"]) && isset($_GET["sub_key"]) && $_GET["action"] == "sola_nl_confirmation" && $_GET["sub_key"]){
        $wpdb->update( $sola_nl_subs_tbl, array("status" => 1) , array( "sub_key" => $_GET["sub_key"]));
    }
    if(isset($_GET["action"]) && isset($_GET["sub_key"]) &&$_GET["action"] == "sola_nl_unsubscribe" && $_GET["sub_key"]){
        $wpdb->update( $sola_nl_subs_tbl, array("status" => 0) , array( "sub_key" => $_GET["sub_key"]));
    }
    if(isset($_GET["action"]) && $_GET["action"] == "sola_nl_redirect"){
        global $sola_nl_link_tracking_table;
        $link_id = $_GET["sola_link_id"];
        $row = $wpdb->get_row( 
            $wpdb->prepare("
		SELECT * FROM `$sola_nl_link_tracking_table` WHERE `id` = %d ", 
                $link_id
            ) 
        );
        $clicked = $row->clicked + 1;  
        
        $wpdb->update( $sola_nl_link_tracking_table, array('clicked'=>$clicked), array("id"=>$link_id), array('%d'), array('%d') );
        $link = $row->link;
        header('Location: '.$link);
        exit;
}
}

function sola_nl_save_style($styles) {
    global $wpdb;
    global $sola_nl_style_table;
    
    $sql = "";
    //var_dump($styles);
    foreach($styles as $style){
       
       $style_1 = $style['style'];
       if(stripos($style["css"], "color") || $style["css"] == "color"){
           $value = $style['value'];
       } else {
        $value = $style['the_value']; 
       }
       
       
       $id = $style['id'];
       $sql = "UPDATE `$sola_nl_style_table` SET `style` = '$style_1', `value` = '$value' WHERE `id` = '$id'; ";
       if($wpdb->query($sql)){
            echo true;
        } else {
            echo "There is a problem";
        }
    }
    
    
}
function sola_nl_get_font_family_id($font_family){
    global $wpdb;
    global $sola_nl_fonts_table;
    $sql = "SELECT * FROM `$sola_nl_fonts_table` WHERE `font_family` = '$font_family'";
    $result = $wpdb->get_row($sql);
    return $result->id;
}


//short code function confirm mail link 
function sola_nl_confirm_link($atr , $text = null){
    global $wpdb;
    global $sola_nl_subs_tbl;
    $sub_email = $_POST["sub_email"];
    $sql = "SELECT * FROM `$sola_nl_subs_tbl` WHERE `sub_email` = '$sub_email'";
    $result = $wpdb->get_row($sql);
    $sub_key =  $result->sub_key;
    $page_url = get_permalink( get_option("sola_nl_confirm_page"));
    
    if (stristr($page_url,"?") === FALSE) {
        return "<a href='$page_url?action=sola_nl_confirmation&sub_key=$sub_key'>$text</a>";
    } else {
        return "<a href='$page_url&action=sola_nl_confirmation&sub_key=$sub_key'>$text</a>";
    }    
}
//short code function to show name in confirm mail
function sola_nl_sub_name(){
    return $_POST['sub_name'];
}
//short code function - unsubscribe href
function sola_nl_unsubscribe_href(){
    global $sola_global_subid;
    
    if(isset($_POST['subscriber'])){
        extract($_POST['subscriber']);
    } 
    else if( isset($_GET['sub_id']) ) {
        $subscriber = sola_nl_get_subscriber($_GET['sub_id']);
        $sub_key = $subscriber->sub_key;
    } 
    else {
        $subscriber = sola_nl_get_subscriber($sola_global_subid);
        if (isset($subscriber->sub_key)) { $sub_key = $subscriber->sub_key; } else { $sub_key = ""; }
    }
    $page_url = get_permalink( get_option("sola_nl_unsubscribe_page"));

    if (stristr($page_url,"?") === FALSE) {
        return $page_url."?sub_key=".$sub_key."&action=sola_nl_unsubscribe";
    } else {
        return $page_url."&sub_key=".$sub_key."&action=sola_nl_unsubscribe";
    }    
    
    
    
}
function sola_nl_unsubscribe_text(){
    return get_option('sola_nl_unsubscribe');
}
//Short Code to view in browser
function sola_nl_view_browser(){
    global $sola_global_subid;
    global $sola_global_campid;

    if(isset($_POST['subscriber'])){
        extract($_POST['subscriber']);
        return SITE_URL."/?action=sola_nl_browser&camp_id=".$_POST['camp_id']."&sub_id=".$sub_id;
    } else if (isset($sola_global_subid)) {
        return SITE_URL."/?action=sola_nl_browser&camp_id=".$sola_global_campid."&sub_id=".$sola_global_subid;
    } else {
        return ;
    }
}
function sola_return_error($data) {
    echo "<div id=\"message\" class=\"error\"><p><strong>".$data->get_error_message()."</strong><blockquote>".$data->get_error_data()."</blockquote></p></div>";
    sola_write_to_error_log($data);
}
function sola_write_to_error_log($data) {
    if (sola_nl_error_directory()) {
        if (is_multisite()) {
            $content = "\r\n".date("Y-m-d H:i:s").": ".$data->get_error_message() . " -> ". $data->get_error_data();
            $fp = fopen($upload_dir['basedir'].'/sola'."/sola_error_log.txt","a+");
            fwrite($fp,$content);
        } else {
            $content = "\r\n".date("Y-m-d H:i:s").": ".$data->get_error_message() . " -> ". $data->get_error_data();
            $fp = fopen(ABSPATH.'wp-content/uploads/sola'."/sola_error_log.txt","a+");
            fwrite($fp,$content);
        }
    }
    
    error_log(date("Y-m-d H:i:s"). ": ".SOLA_PLUGIN_NAME . ": " . $data->get_error_message() . "->" . $data->get_error_data());
    
}
function sola_nl_error_directory() {
    $upload_dir = wp_upload_dir();
    
    if (is_multisite()) {
        if (!file_exists($upload_dir['basedir'].'/sola')) {
            wp_mkdir_p($upload_dir['basedir'].'/sola');
            $content = "Error log created";
            $fp = fopen($upload_dir['basedir'].'/sola'."/sola_error_log.txt","w+");
            fwrite($fp,$content);
        }
    } else {
        if (!file_exists(ABSPATH.'wp-content/uploads/sola')) {
            wp_mkdir_p(ABSPATH.'wp-content/uploads/sola');
            $content = "Error log created";
            $fp = fopen(ABSPATH.'wp-content/uploads/sola'."/sola_error_log.txt","w+");
            fwrite($fp,$content);
        }
        
    }
    return true;
    
}

function sola_nl_return_error_log() {
    $fh = @fopen(ABSPATH.'wp-content/uploads/sola'."/sola_error_log.txt","r");
    $ret = "";
    if ($fh) {
        for ($i=0;$i<10;$i++) {
            $visits = fread($fh,4096);
            $ret .= $visits;
        }
    } else {
        $ret .= __("No errors to report on","sola");
    }
    return $ret;
    
}
function sola_nl_camp_links($camp_id){
    global $wpdb;
    global $sola_nl_link_tracking_table;
    $results = $wpdb->get_results( 
	$wpdb->prepare( 
            " SELECT * FROM `$sola_nl_link_tracking_table`  WHERE `camp_id` = %d  GROUP BY `link_name` ORDER BY SUM(clicked) DESC",
            $camp_id
        )
    );
    return $results;
}


function sola_nl_ml(){
    
    $a = 16;
    $b = 19;
    return round(sqrt(pow($a,2)+pow($b, 2)))*100;
    
}


function sola_sending_mails_tool_bar( $wp_admin_bar ) {
    $args = array(
        'id'    => 'sola_progress_bar',
        'meta'  => array( 'class' => 'sending_mails_toolbar_progress' )
    );
    $wp_admin_bar->add_node( $args );
}
function sola_sending_mails_tool_bar_name($wp_admin_bar){
    $args = array(
        'id'    => 'sola_toolbar_label',
        'meta'  => array( 'class' => 'sola_progress_label' )
    );
    $wp_admin_bar->add_node( $args );
}
function sola_sending_mails_tool_bar_pending($wp_admin_bar){
    $args = array(
        'id'    => 'sola_toolbar_pending',
        'meta'  => array( 'class' => 'sola_progress_pending' )
    );
    $wp_admin_bar->add_node( $args );
}

function sola_cts(){
    if(function_exists('sola_nl_register_pro_version')){
        return true;
    }
    global $wpdb;
    global $sola_nl_subs_tbl;
    $sql = "SELECT * FROM `$sola_nl_subs_tbl`";
    $wpdb->query($sql);
    if($wpdb->num_rows >= sola_nl_ml()){
        return false;
    } else {
        return true;
    }
}
function sola_se(){
    //$data = "WW91IGNhbiBvbmx5IGhhdmUgYSBtYXhpbXVtIG9mIDI1MDAgc3Vic2NyaWJlcnMhIElmIHlvdSBwdXJjaGFzZSB0aGUgcHJvIHlvdSBjYW4gaGF2ZSB1bmxpbWl0dGVkIHN1YnNjcmliZXJzLiA= ";
    //return base64_decode($data);
    return __('You can only have a maximum of '.sola_nl_ml().' subscribers! Go','sola')."<a target='_BLANK' href='http://solaplugins.com/plugins/sola-newsletters/?utm_source=plugin&utm_medium=link&utm_campaign=subscriber_limit' style='color:#EC6851;'>".__('Premium','sola')."</a>".__('to get unlimited subscribers.','sola');
    //return "You can only have a maximum of ".sola_nl_ml()." subscribers! Go Premium to get unlimited subscribers.";
}
function sola_get_theme_basic(){
    global $sola_nl_themes_table;
    global $wpdb;
    $sql = "SELECT * FROM `$sola_nl_themes_table` WHERE `theme_id` = 1";
    $results = $wpdb->get_results($sql);
    return $results;
}
function sola_set_theme($theme_id, $camp_id){
    global $sola_nl_camp_tbl;
    echo $sola_nl_camp_tbl;
    global $wpdb;
    $check = $wpdb->query( 
	$wpdb->prepare( 
            "UPDATE `$sola_nl_camp_tbl` SET `theme_id` = %d WHERE `camp_id` = %d LIMIT 1",
            $theme_id, $camp_id
        )
    );
    if($check === false){
        return new WP_Error( 'db_query_error', __( 'Could not execute query' ), $wpdb->last_error );
    } else {
        return true;
    }
}
function sola_get_camp_theme_id($camp_id){
    global $wpdb;
    global $sola_nl_camp_tbl;
    $sql = "SELECT * FROM `$sola_nl_camp_tbl` WHERE `camp_id` = $camp_id";
    $row = $wpdb->get_row($sql);
    if($row){
        return $row->theme_id;
    } else {
        return 1;
    }
}
function sola_nl_feedback_head() {
    if (function_exists('curl_version')) {

        $request_url = "http://www.solaplugins.com/apif/rec.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

        curl_close($ch);
        
    } 
    return;
}
function sola_nl_init_post_processing(){
    if(isset($_GET['action'])){
        if ($_GET['action'] == "sola_csv_export") {
            global $wpdb;
            $fileName = $wpdb->prefix.'sola_nl_subscribers.csv';

            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename={$fileName}");
            header("Expires: 0");
            header("Pragma: public");
            $fh = @fopen( 'php://output', 'w' );
            $query = "SELECT * FROM `{$wpdb->prefix}sola_nl_subscribers`";
            $results = $wpdb->get_results( $query, ARRAY_A );
            $headerDisplayed = false;
            foreach ( $results as $data ) {
                // Add a header row if it hasn't been added yet
                if ( !$headerDisplayed ) {
                    // Use the keys from $data as the titles
                    fputcsv($fh, array_keys($data));
                    $headerDisplayed = true;
                }
                // Put the data into the stream
                fputcsv($fh, $data);
            }
            // Close the file
            fclose($fh);
            // Make sure nothing else is sent, our file is done
            die();
        }
        if ($_GET['action'] == 'sola_nl_tracker') {
            sola_nl_tracker($_GET['camp_id'], $_GET['sub_id']);
        }
        if ($_GET['action'] == 'sola_nl_browser') {
            $letter = sola_nl_get_letter($_GET['camp_id']);
            echo do_shortcode(sola_nl_mail_body($letter, $_GET['sub_id'], $_GET['camp_id']));
            die();
        }
    }
}
function sola_nl_tracker($camp_id, $sub_id){
    global $wpdb;
    global $sola_nl_camp_subs_tbl;
    $results = $wpdb->get_row( 
	$wpdb->prepare( 
            " SELECT * FROM `$sola_nl_camp_subs_tbl` WHERE `camp_id` = %d AND `sub_id` = %d",
            $camp_id, $sub_id, 0
        )
    );
    $opens = $results->opens + 1;
    $now = date("Y-m-d H:i:s");
    $wpdb->update( 
        $sola_nl_camp_subs_tbl, 
        array( 
            'status' => 2,
            'date_open' => $now,
            'opens'=>$opens
        ), 
        array( 
            'camp_id' => $camp_id,
            'sub_id' => $sub_id
            ), 
        array( 
            '%d',
            '%s',
            '%d'
        ), 
        array( '%d', '%d' )
    );  
    
    header('Content-Type: image/png');
    //returns 1x1px image
    
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
    die();
}
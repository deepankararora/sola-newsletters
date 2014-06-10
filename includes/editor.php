<?php $sola_nl_ajax_nonce = wp_create_nonce("sola_nl");
$theme_id = sola_get_camp_theme_id($_GET['camp_id']);

?>

<script language="javascript">
    var sola_nl_nonce = '<?php echo $sola_nl_ajax_nonce; ?>';
    var sola_is_editing = false;
    var sola_is_editing_id = null;
    var camp_id = <?php echo $_GET['camp_id'] ?>;
</script>



<div class="sola-content-fixed">
    <div class="sola-header-content ">
        <div class="sola-sidebar-header">
            <ul>
                <li class="active editor_options_header" did="editor-content"><?php _e("Content","sola"); ?></li>
                <li class="editor_options_header" did="editor-styles"><?php _e("Style","sola"); ?></li>
            </ul>
            
        </div>
        <div class="header-right">
            <div class="next-button">
                <a id="sola_nl_next_temp_btn" class='button-primary sola_nl_preview_btn' ><?php _e("Next","sola"); ?></a>
            </div>
            <div id='sola_nl_save_text' ><?php if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") { echo "<span style='color:red'>"; _e("You are currently using the plugin on your localhost. Various elements such as images will NOT show up in your test or final email as they reference your hard drive which is not accessible via the web to others"); echo "</span>"; } ?></div>
        </div>
    </div>
    <div class="sidebar ">
        
        <div id="editor_options">
            <div id="editor-content" class="content">
                <div class="add-box sola_addable_item" type="text">
                    <h3><?php _e("Text","sola"); ?></h3>
                    <i class="fa fa-5x fa-font"></i>
                </div>
                
                <div class="add-box sola_show_editior_div">
                    <div class="add-box-title">
                        <h3><?php _e("Button","sola"); ?></h3>
                        <i class="fa fa-5x fa-square"></i>
                    </div>
                    <div class="sola-extra-content" style="padding:0 20px 10px 10px;">
                        
                        <div class="form-group">
                            <label>
                                <?php _e("Button Text","sola"); ?>
                            </label>
                            <input type="text" class="form-control" id="sola_nl_btn_text" value="Put Your Text Here"/>
                        </div>
                        <div class="form-group">
                            <label>
                                <?php _e("Link","sola"); ?>
                            </label>
                            <input type="text" class="form-control" id="sola_nl_btn_link" placeholder="http://www.linkhere.com"/>
                        </div>
                        <div class="sola_addable_item" type="btn" ><a href="" style="text-decoration:none; display: block; " class="sola_nl_btn"  id="sola_nl_btn">Put Your Text Here</a></div>
                    </div>
                </div>
                
                <div class="add-box sola_show_editior_div">
                    <div class="add-box-title">
                        <h3><?php _e("Blog Post","sola"); ?></h3>
                        <i class="fa fa-5x fa-bullhorn"></i>   
                    </div>
                    <div class="sola-extra-content">
                        <?php

                        $i = 1;
                        $args = array( 'posts_per_page' => 10 );
                        global $post;
                        $myposts = get_posts( $args );
                        foreach ( $myposts as $post ) : 
                            setup_postdata($post);
                            $sola_feat_image_url = "";
                            if (has_post_thumbnail( $post->ID ) ): 
                                $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); 
                                $sola_feat_image_url = $image[0]; 
                            endif; ?>


                                <div id="sola_newsletter_addableitems " >
                                    
                                    <div class="sola_addable_item sola_sub_addable_item <?php if($i == 1) {?>first<?php } ?>" type="blog_post" 
                                         value="<?php echo get_the_excerpt(); ?>" 
                                         feat_image="<?php echo $sola_feat_image_url ?>" 
                                         title="<?php the_title(); ?>"
                                         post_url="<?php echo get_permalink() ?>">
                                             <?php the_title(); ?>
                                    </div>
                                    <?php $i++ ?>
                                </div>

                        <?php

                        endforeach;
                        ?>
                    </div>
                </div>
                
                <div class="add-box sola_show_editior_div">
                    <h3><?php _e("Images","sola"); ?></h3>
                    <i class="fa fa-5x fa-picture-o"></i>
                    <div class="sola-extra-content">
                        <center>
                            <label for="upload_image">
                                <input id="upload_image_button" class="button" type="button" value="Choose Image" />
                            </label>
                        </center>    
                        <hr/>
                        <div id="images"><!-- Images Returned from pop up box -->

                        </div>
                    </div>
                </div>
                
                <div class="add-box sola_show_editior_div">
                    <h3><?php _e("Divider","sola"); ?></h3>
                    <i class="fa fa-5x fa-bars"></i>
                    
                    <div class="sola-extra-content">
                            <div type="divider" truesrc="" thumbnail="" class="sola_addable_hr sola_sub_addable_item" align="center"><em><?php _e("Insert spacer","sola"); ?></em></div>
                        <?php 

                            $dir = PLUGIN_URL.'/images/hr';
                            $files = scandir($dir, 1);   
                            foreach($files as $file){
                                if($file != "." && $file != ".."){
                                    ?>
                            <div type="image_divider" truesrc="<?php echo PLUGIN_DIR ?>/images/hr/<?php echo $file ?>" thumbnail="<?php echo PLUGIN_DIR ?>/images/hr/<?php echo $file ?>" class="sola_addable_hr sola_sub_addable_item" >

                                <img src="<?php echo PLUGIN_DIR ?>/images/hr/<?php echo $file ?>"  width="100%" style="max-width:540px"/>
                            </div>
                            <?php
                                }
                            }

                        ?>
                    </div>

            
                </div>
                <div class="add-box sola_show_editior_div">
                    <h3><?php _e("Social","sola"); ?></h3>
                    <i class="fa fa-5x fa-thumbs-up"></i>
                    <div class="sola-extra-content">
                        <?php 
                        $i = 1;
                        
                        $dir = PLUGIN_URL.'/images/social-icons';
                        $folders = scandir($dir, 1);
                        $social_links = get_option("sola_nl_social_links");
                        ksort($social_links);
                        $i = 1;
                        // check if there are links
                        $check = 0;
                        foreach($social_links as $social_name=>$social_link){
                            if($social_link){
                                $check = 1;
                            }
                        }
                        if($check == 1){
                            foreach ($folders as $folder) {
                                if($folder != "." && $folder != ".."){
                                    if(is_dir($dir."/".$folder)){?>
                                        <div class="sola_addable_item sola_sub_addable_item <?php if($i == 1) {?>first<?php } ?>" type="social_icons" style="padding: 5px 0; text-align: center">
                                        <?php  $i++;
                                        foreach($social_links as $social_name=>$social_link){
                                            if($social_link != ""){ ?>
                                                <a href="<?php echo $social_link ?>" title="<?php echo $social_name ?>" target="_blank" style="display:inline-block;">
                                                    <img type="social_icon"  src="<?php echo PLUGIN_DIR ?>/images/social-icons/<?php echo $folder ?>/<?php echo $social_name ?>.png" />
                                                </a>
                                            <?php } 

                                        }  ?>
                                        </div>
                                <?php }

                                }

                            }
                        } else { ?>
                            <div style="padding-right: 10px">
                                <?php _e("Please go into Sola Settings and Add links to your social Networks","sola-newsletters"); ?>
                            </div> <?php
                        }
                           
                        ?>
                    </div>
                </div>
                <div style="clear: both"></div>
            </div>
            
            <div id="editor-styles" class="styles" style="display:none">
                <?php sola_get_style_editor($theme_id) ?>
            </div>
            
            
        </div>
        <div id="sola_nl_send_test">
            <input type="email" value="<?php echo get_option('admin_email')?>" class="sola-input" id="sola_nl_to_mail_test" />
            <button class="button-primary sola_send_preview"><?php _e("Send Test","sola"); ?></button>
        </div>
    </div>  
    
</div>

<div id="sola_newsletter_preview"> 
    
    
        <?php       
        
        $letter = sola_nl_get_letter($_GET['camp_id'], $theme_id);
        echo $letter;
        

        ?>
    
        

</div>



<?php /* 
 * <div style='display:block;  clear:both; width:602px;'>


        </div>



        <div id="sola_newsletter_wrapper"  camp_id="<?php echo $_GET['camp_id']?>">        
                
                ?>        
        </div>
 * 
*/ ?>


    

























<?php /*
?>

<div class="wrap" style='width:559px;' >    
    

    <div class="icon32" id="icon-edit-pages"><br /></div>
    <h2 style='padding-right: 0;'><?php _e("Sola Newsletter Creation","sola-newsletters"); ?>
    <button id="sola_nl_next_temp_btn" class='button-primary sola_nl_next_btn' style='float:right;'>Next</button>

    <!---       REMOVED SAVE BTN as editor now saves automatically
     
    <button id="sola_nl_save_temp_btn" class='btnSave button-primary sola_nl_save_temp_btn ' style='float:right; margin-right:20px;'>Save</button> -->
     </h2>  
</div>


<?php
$sola_nl_ajax_nonce = wp_create_nonce("sola_nl");
?>

<script language="javascript">
    var sola_nl_nonce = '<?php echo $sola_nl_ajax_nonce; ?>';
    var sola_is_editing = false;
    var sola_is_editing_id = null;

</script>

<div style='display:block;  clear:both; width:602px;'>

 <div id='sola_nl_save_text' style='float:right; margin-right:10px; font-weight:bold; padding-top:4px;'></div>
</div>



<div id="sola_newsletter_wrapper" style="float:left;" camp_id="<?php echo $_GET['camp_id']?>">        
        <?php 
        $letter = sola_nl_get_letter($_GET['camp_id']);
        if ($letter){
            echo $letter;
        } else {
            sola_nl_default_letter();
        }
        ?>        
</div>
<div style="clear: both; padding-top:20px;width: 602px;">

    <input type="email" value="<?php echo get_option('admin_email')?>" class="sola-input" id="sola_nl_to_mail_test" />
    <button class="button-primary sola_send_test_mail">Send Test</button>
    <button id="sola_nl_next_temp_btn" class='button-primary sola_nl_next_btn' style='float:right;'>Next</button>
</div>

<div id='sola_place_test'>

</div>

<div id='sola_main_box' style='display:block; position:fixed; top:20%; height:400px; left: 800px; width:450px; padding:10px;'>

    <div id="sola_nl_editor_tabs">
        <ul>
            <li><a href="#tabs-content" class="tabs-content"><?php _e("Content","sola"); ?></a></li>
            <li><a href="#tabs-hr" ><?php _e("Horizontal Lines","sola"); ?></a></li>
            <li><a href="#tabs-images" class="tabs-images"><?php _e("Images","sola"); ?></a></li>
            <li><a href="#tabs-styling" class="tabs-styling"><?php _e("Styling","sola"); ?></a></li>
        </ul>
        <div id="tabs-content" style="overflow:auto;">

            <div id="sola_newsletter_addableitems" style="float:left; width:380px;">
                <div class="sola_addable_item" type="text">Text or Title</div>
            </div>
            <div style="clear:both"></div>
            <div class="sola_nl_accordian">
                <h3>Blog Post</h3>
                <div>
                    <?php


                    $args = array( 'posts_per_page' => 10 );
                    global $post;
                    $myposts = get_posts( $args );
                    foreach ( $myposts as $post ) : 
                        setup_postdata($post);
                        $sola_feat_image_url = "";
                        if (has_post_thumbnail( $post->ID ) ): 
                            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); 
                            $sola_feat_image_url = $image[0]; 
                        endif; ?>


                            <div id="sola_newsletter_addableitems" style="float:left; width:380px;">
                                <div class="sola_addable_item" type="blog_post" 
                                     value="<?php echo get_the_excerpt(); ?>" 
                                     feat_image="<?php echo $sola_feat_image_url ?>" 
                                     title="<?php the_title(); ?>"
                                     post_url="<?php echo get_permalink() ?>">
                                         <?php the_title(); ?>
                                </div>
                            </div>

                    <?php

                    endforeach;
                    ?>
                </div>
            </div>
            <div  class="sola_nl_accordian">
                <h3>Social Icons</h3>
                <div>
                    <?php 
                    $dir = PLUGIN_URL.'/images/social-icons';
                        $folders = scandir($dir, 1);
                        foreach ($folders as $folder){
                            if($folder != "." && $folder != ".."){
                                if(is_dir($dir."/".$folder)){?>
                                <div class="sola_nl_accordian">
                                    <h3><?php echo $folder ?></h3>
                                    <div>
                                        <?php
                                        $new_dir = $dir."/".$folder;
                                        $files = scandir($new_dir, 1);
                                        foreach($files as $file){
                                            if($file != "." && $file != ".."){ ?>
                                                 <img src="<?php echo PLUGIN_DIR ?>/images/social-icons/<?php echo $folder ?>/<?php echo $file ?>" />
                                            <?php
                                            }
                                        }?>
                                    </div>
                                </div>
                                <?php
                                }
                            }
                        }
                    ?>
                    
                </div>
            </div>

        </div>
        <div id="tabs-hr" style="overflow:auto; height: 380px;">

            <div id="sola_newsletter_addableitems" style="float:left; width:370px;">    
                <?php 

                $dir = PLUGIN_URL.'/images/hr';
                $files = scandir($dir, 1);   
                foreach($files as $file){
                    if($file != "." && $file != ".."){
                        ?>
                <div type="image" truesrc="<?php echo PLUGIN_DIR ?>/images/hr/<?php echo $file ?>" thumbnail="<?php echo PLUGIN_DIR ?>/images/hr/<?php echo $file ?>" class="sola_addable_hr " >

                    <img src="<?php echo PLUGIN_DIR ?>/images/hr/<?php echo $file ?>"  width="100%" style="max-width:540px"/>
                </div>
                <?php
                    }
                }

                ?>

            </div>
        </div>
        <div id="tabs-images" style="overflow:auto;">

            <div style="float:left; width:380px;">
                <div style='display:block;  height:340px; overflow-y: auto;'>
                    <center>
                        <label for="upload_image">
                            <input id="upload_image_button" class="button" type="button" value="Choose Image" />
                        </label>
                    </center>    
                    <hr/>
                    <div id="images"><!-- Images Returned from pop up box -->

                    </div>

                    <?php
//                        $query_images_args = array(
//                        'post_type' => 'attachment', 'post_mime_type' =>'image', 'post_status' => 'inherit', 'posts_per_page' => 10,
//                        );
//                        $query_images = new WP_Query( $query_images_args );
//                        $images = array();
//                        foreach ( $query_images->posts as $image) {
//                            $images[]= wp_get_attachment_url( $image->ID );
//                        }
//                        #If no images show message and take to media center else show images
//                        if(!$images){
//                            
//                        } else{
//                            foreach ($images as $image) {
//                                $trueimage = $image;
//                                $image = PLUGIN_DIR."/includes/timthumb.php?src=".$image."&h=78&w=78&zc=1";
//                                echo $image;
//                                echo "<div type=\"image\" truesrc='".$trueimage."' class=\"sola_addable_image\" style=\"float:left; padding:3px; margin:3px;\"/><img src='".$image."' ></div>";
//                            }
//                        }

                    ?>

                </div>
            </div>
        </div>
        <div id="tabs-styling">
            <style>

                .colorpicker{
                    display: inline-block;
                    width: 20px;
                    height: 20px;
                    border:1px solid #ccc;
                }
                .style{
                    position: relative;
                }
                .style-name{
                    display: inline-block;
                    height: 22px;
                    position: relative;
                    bottom: 5px;
                    width: 100px;
                }
                .font{
                    display:inline;
                }
                .font select{
                    margin: 0;
                    padding: 0;
                    position: relative;
                    top: -7px;
                    height: 22px;
                }
                </style>
                <div>
                    <?php sola_get_style_editor() ?>

        </div>

    </div>
</div>

*/ ?>
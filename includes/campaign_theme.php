<?php $camp_id = $_GET['camp_id']; 
//$themes = sola_get_theme_basic();
?>
<div class="wrap">    
    <div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
    <h2><?php _e('Choose a Theme', "sola") ?></h2>

    
    <form method="post" action="admin.php?page=sola-nl-menu&action=theme&camp_id=<?php echo $camp_id ?>">
        <input type="hidden" value="<?php echo $camp_id ?>" name="camp_id">
        <div class="themes_wrapper">
              <?php sola_nl_theme_selection(); ?>
        </div> 
        
        <input type="submit" value="Next" class="button-primary button-large" name="sola_set_theme">
    </form>  

    
    <br /><br />
    <hr />
    <h3><?php _e("Upload a theme","sola"); ?></h3>
    <form method="POST" enctype="multipart/form-data" name="sola_theme_upload">
        <input type="file" name="sola_theme_file" />
        <input type="submit" value="<?php _e("Upload","sola"); ?>" class="button-primary button-large" name="sola_upload_theme_btn">
    </form>

    
    
    <br /><br />
    <hr />
    <h2 class="text-center" ><?php _e('Find','sola')?> <a target='_BLANK' href='http://solaplugins.com/product-category/newsletter-themes/?utm_source=plugin&utm_medium=link&utm_campaign=more_themes' style='color:#EC6851;'><?php _e('more themes','sola')?></a> on SolaPlugins.com</h2>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
</div>
<?php include 'footer.php'; ?>
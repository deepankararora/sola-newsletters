<?php $camp = sola_nl_get_camp_details($_GET['camp_id']); ?>

<div id="icon-options-general" class="icon32 icon32-posts-post"><br></div><h2><?php _e("Live Preview","sola") ?></h2>

<div class="preview_actions">
    <div class="return_editor">
        <a title="Return To Editor" class="button-primary" href="admin.php?page=sola-nl-menu&action=editor&camp_id=<?php  echo $_GET['camp_id'] ?>">Return To Editor</a>
    </div>
    <div class="confirm_camp">
        <a title="Confirm Campaign" class="button-primary sola_nl_next_btn" href="admin.php?page=sola-nl-menu&action=confirm_camp&camp_id=<?php  echo $_GET['camp_id'] ?>">Confirm Campaign</a>
    </div>
</div>
<div class="sola_nl_preview_container">    
    <div id="sola_tabs">
          <ul>
              <li><a href="javascript:void(0)" class="preview_button_button" id="preview_desktop" window_width="800px" add_class="preview_desktop">Desktop</a></li>
              <li><a href="javascript:void(0)" class="preview_button_button" id="preview_mobile" window_width="300px" add_class="preview_mobile">Mobile</a></li>
              <li><a href="javascript:void(0)" class="preview_button_button" id="preview_tablet" window_width="500px" add_class="preview_tablet">Tablet (iPad - Portrait)</a></li>
                <li><a href="javascript:void(0)" class="preview_button_button" id="preview_tablet_landscape" window_width="800px" add_class="preview_tablet_landscape">Tablet (Nexus 10 - Landscape)</a></li>
          </ul>
    </div>    
    <div id="sola_newsletter_preview" style="width: 800px;">            
        <div class="preview_desktop"  id="preview_container">
            <div class="preview_container">
                <?php        
                    $letter = sola_nl_get_letter($_GET['camp_id']);
                    if ($letter){
                        echo $letter;
                    } else {
                        sola_nl_default_letter();
                    }             
                ?>    
            </div>
        </div>
    </div>

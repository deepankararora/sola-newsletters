<?php


add_action('admin_print_scripts', 'sola_nl_admin_editor_scripts_basic');
add_action('admin_enqueue_scripts', 'sola_nl_add_admin_editor_stylesheet');



function sola_nl_admin_editor_scripts_basic() {
    if(isset($_GET['page']) && isset($_GET['action'])){
        if ($_GET['page'] == "sola-nl-menu" && $_GET['action'] == 'editor') {
            wp_enqueue_media();
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-widget' );
            wp_enqueue_script( 'jquery-effects-core' );
            wp_enqueue_script( 'jquery-effects-shake' );
            wp_enqueue_script( 'jquery-ui-mouse' );
            wp_enqueue_script( 'jquery-ui-tabs');
            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'jquery-ui-droppable' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'jquery-ui-accordion' );
            wp_register_script('sola_nl_tinymce', PLUGIN_DIR."/js/tinymce/tinymce.min.js", false);
            wp_enqueue_script( 'sola_nl_tinymce' );
            wp_register_script('sola_nl_editor', PLUGIN_DIR."/js/editor.js", false);
            wp_enqueue_script( 'sola_nl_editor' );
            wp_register_script('sola_nl_color', PLUGIN_DIR."/js/colpick.js", false);
            wp_enqueue_script( 'sola_nl_color' );
            wp_register_script('sola_nl_bootstrap_js', PLUGIN_DIR."/js/bootstrap.min.js", false);
            wp_enqueue_script( 'sola_nl_bootstrap_js' );
        }
    }
}

function sola_nl_add_admin_editor_stylesheet() {
    if(isset($_GET['page']) && isset($_GET['action'])){
        if ($_GET['page'] == "sola-nl-menu" && $_GET['action'] == 'editor') {
            //wp_register_style( 'sola_nl_jquery_ui', PLUGIN_DIR.'/js/themes/base/jquery.ui.all.css' );
            //wp_enqueue_style( 'sola_nl_jquery_ui' );

            wp_register_style( 'sola_nl_editor_color_style', PLUGIN_DIR.'/css/colpick.css' );
            wp_enqueue_style( 'sola_nl_editor_color_style' );
            wp_register_style( 'sola_nl_bootstrap_css', PLUGIN_DIR.'/css/bootstrap.min.css' );
            wp_enqueue_style( 'sola_nl_bootstrap_css' );
            wp_register_style( 'sola_nl_bootstrap_theme_css', PLUGIN_DIR.'/css/bootstrap-theme.min.css' );
            wp_enqueue_style( 'sola_nl_bootstrap_theme_css' );
            wp_register_style( 'sola_nl_font_awesome', PLUGIN_DIR.'/css/font-awesome.min.css' );
            wp_enqueue_style( 'sola_nl_font_awesome' );
            wp_register_style( 'sola_nl_editor_style', PLUGIN_DIR.'/css/editor.css' );
            wp_enqueue_style( 'sola_nl_editor_style' );
        }
    }
}



function sola_get_style_editor($theme_id) {
    global $wpdb;
    global $sola_nl_style_table;
    global $sola_nl_style_elements_table;
    global $sola_nl_css_options_table;
    if(empty($theme_id)){
        $theme_id = 1;
    }
    $sql = "SELECT * FROM `$sola_nl_style_elements_table` WHERE `theme_id` = '$theme_id' ORDER BY `element_name` ASC";
    $results = $wpdb->get_results($sql);
    foreach ($results as $style_element) {?>
        <div class="style">
            <div class="style-name">
                <h4><?php echo $style_element->element_name ?></h4>
            </div>
            <div style="display:none" class="style-options">
            <?php 
            $sql = "SELECT * FROM `$sola_nl_style_table` WHERE `element_id` = '$style_element->id' ORDER BY `label` ASC";
            $styles = $wpdb->get_results($sql);
            
            foreach ($styles as $style) { ?>
                <div class="form-group" style="<?php if($style->style == "display:none;"){ echo $style->style;}?>">
                    
                    <label><?php echo $style->label ?></label>
                    <<?php echo $style->type ?>
                        id="<?php echo $style->id ?>"
                        css="<?php echo $style->css ?>"
                        <?php if($style->type != "select"){ ?>
                            value="<?php echo $style->value ?>"
                        <?php } ?>
                        class="<?php echo $style->class ?>"
                        style="<?php echo $style->style ?>"
                        element="<?php echo $style_element->element ?>"
                    >
                    <?php if ($style->type == "select"){ 
                        $sql = "SELECT * FROM `$sola_nl_css_options_table` WHERE `css_name` = '$style->css' ORDER BY `name` ASC";
                        $options = $wpdb->get_results($sql);
                        
                        foreach ($options as $option){
                            ?>
                            <option value='<?php echo $option->value?>' <?php if ($option->value == $style->value) {?> selected <?php } ?>><?php echo $option->name ?></option>
                        <?php }
                        ?>
                        
                    <?php } ?>
                    </<?php echo $style->type ?>>
                    
                </div>

            <?php    } ?>
            </div>
        </div>
    <?php }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*foreach($results as $result){ 
        $style_array = unserialize($result->style);
        ?>
        <div class="style">
            <div class="style-name">
                <h4><?php echo $result->element_name ?></h4>
            </div>
            <?php foreach ($style_array as $label => $css_settings_array) {
                $i = 1;
                ?>
                <div class="form-group">
                    <label><?php echo $label ?></label>
                    <?php foreach($css_settings_array as $key=>$value){

                        if($key == "html" or $key == "close-html"){ 
                            echo $value;
                            if($i == 1){?>
                                element="<?php echo $result->element ?>"
                                label="<?php echo $label ?>"
                            <?php $i++;

                            }
                         } else if($key == "font_id") { 
                            sola_get_font_select($value , $result->element, $result->id);
                         } else { ?>
                            <?php echo $key ?>="<?php echo $value ?>" 
                     <?php } ?>

                    <?php } ?>
                </div>
        
    <?php } ?>
      </div>                   
    <?php } 
  /*  $sql = "SELECT * FROM `$sola_nl_style_table` WHERE `style_name` = '$style_name'";
    $results = $wpdb->get_results($sql);
    foreach($results as $result){?>
        <div class="style">
            <div class="style-name">
                <h4><?php echo $result->element_name ?></h4>
            </div>
            <?php if(!empty($result->background_color) or !empty($result->color)){ ?>
                <div class="form-group">
                    <label >Color</label>
                    <div id="<?php echo $result->id ?>"
                         class="colorpicker" 
                         style_name="<?php echo $result->style_name ?>"
                         element="<?php echo $result->element ?>" 
                         css="<?php if($result->color) echo "color";
                                    else echo "backgroundColor";?>" 
                         color="<?php if($result->color) echo $result->color;
                                else echo $result->background_color;?>" 
                         style="background-color: <?php if($result->color) echo $result->color;
                                                        else echo $result->background_color;?>" >

                    </div>
                </div>
            <?php } ?>
            <div class='font'>
                <?php           
                if ($result->font_id != 0){ 
                    sola_get_font_select($result->font_id , $result->element, $result->id);
                    sola_get_font_size_select($result->font_size, $result->element, $result->id);
                } ?>
            </div>
            <?php if(!empty($result->border_radius)){ ?>
            <div class="form-group">
                <label>Border Radius</label>
                <input type="text" 
                       class="form-control" 
                       style_name="<?php echo $result->style_name ?>"
                       element="<?php echo $result->element ?>"
                       css="border-radius"
                       value="<?php echo $result->border_radius ?>">
            </div>
                
            <?php } ?>
            
            
            
        </div>
    <?php
    }*/
    
}
function sola_get_font_select($font_id, $element , $id) {
    global $wpdb;
    global $sola_nl_fonts_table;
    $sql = "SELECT * FROM `$sola_nl_fonts_table` ORDER BY `font_name` ASC";
    $fonts = $wpdb->get_results($sql);
    ?>
    
            <select id ='<?php echo $id ?>' class='font-select form-control' element='<?php echo $element ?>' css='font-family' > 
                <?php foreach($fonts as $font){ ?>
                    <option value='<?php echo $font->font_family ?>' <?php if($font->id == $font_id) echo "selected" ?>><?php echo $font->font_name ?></option>
                <?php } ?>
            </select>
    
   <?php
}
function sola_get_font_size_select($font_size, $element, $id){
    
    ?>
    <div class="form-group">
        <label >Font SIze</label>
        <select id='<?php echo $id ?>' class='font-size form-control' element='<?php echo $element ?>' css='font-size'>
            <option value='8px' <?php if ($font_size == "8px") { echo "selected"; } ?>>8px</option>
            <option value='9px' <?php if ($font_size == "9px") { echo "selected"; } ?>>9px</option>
            <option value='10px'<?php if ($font_size == "10px") { echo "selected"; } ?>>10px</option>
            <option value='11px'<?php if ($font_size == "11px") { echo "selected"; } ?>>11px</option>
            <option value='12px'<?php if ($font_size == "12px") { echo "selected"; } ?>>12px</option>
            <option value='13px'<?php if ($font_size == "13px") { echo "selected"; } ?>>13px</option>
            <option value='14px'<?php if ($font_size == "14px") { echo "selected"; } ?>>14px</option>
            <option value='16px'<?php if ($font_size == "16px") { echo "selected"; } ?>>16px</option>
            <option value='18px'<?php if ($font_size == "18px") { echo "selected"; } ?>>18px</option>
            <option value='24px'<?php if ($font_size == "24px") { echo "selected"; } ?>>24px</option>
            <option value='36px'<?php if ($font_size == "36px") { echo "selected"; } ?>>36px</option>
            <option value='48px'<?php if ($font_size == "48px") { echo "selected"; } ?>>48px</option>
            <option value='72px'<?php if ($font_size == "72px") { echo "selected"; } ?>>72px</option>
        </select>
    </div>
    <?php
}
function wp_new_excerpt($text) {
    if ($text == '') {
        $text = get_the_content('');
        $text = strip_shortcodes( $text );
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]>', $text);
        $text = strip_tags($text);
        $text = nl2br($text);
        $excerpt_length = apply_filters('excerpt_length', 55);
        $words = explode(' ', $text, $excerpt_length + 1);
        if (count($words) > $excerpt_length) {
                array_pop($words);
                array_push($words, '');
                $text = implode(' ', $words);
        }
    }
    return $text;
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'wp_new_excerpt');
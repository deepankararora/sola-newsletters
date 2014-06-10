<?php
$camp = sola_nl_get_camp_details($_GET['camp_id']);
$mail_method = get_option("sola_nl_send_method");
if ($mail_method == "1") { $mail_method = "wp_mail"; }
else if ($mail_method == "3") { $mail_method = "Gmail"; }
else if ($mail_method == "2") { $mail_method = "SMTP"; }
$limit = get_option('sola_nl_send_limit_qty');
$limit_time = get_option('sola_nl_send_limit_time');
?>
<div class="wrap">        
    
    <div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
    <h2><?php _e("Confirm Campaign","sola") ?></h2>
    <div>
        <?php /* */ ?>
        <form id='sendform' action="?page=sola-nl-menu&action=send_campaign&camp_id=<?php echo $_GET['camp_id'] ?>"  method="POST">
            <input type="hidden" name="camp_id"   value="<?php echo $camp->camp_id ?>" />
            <table>
                            <tr>
               <td width="250px">
                  <label><h3>Subject</h3></label>
                  <p class="description"><?php _e("Give your campaign a subject line to make your subscribers take the bait!","sola"); ?></p>
               </td>
               <td>
                  <input type="text" class="sola-input-subject" name="subject" value="<?php if($camp){ echo $camp->subject; } ?>"/>
               </td>
            </tr>

                <tr>
                    <td class="sola-td-vert">
                        <label><?php _e("Select List","sola"); ?></label>
                        <p class="description"><?php _e("Select a list you want to send this campaign to.","sola"); ?></p>
                    </td>
                    <td>
                    <?php 
                  if (isset($camp->status) && $camp->status == 9) {
                   ?><p class="description" style="color:red;"><?php echo __("You cannot edit the list while the campaign is being sent, or the sending has been paused","sola"); ?></p>
                     <?php
                     $lists = sola_nl_get_lists();
                     foreach($lists as $list){?>
                     <input style='display:none;' type="checkbox" name="sub_list[]" <?php if($camp && sola_nl_check_if_selected_list_camp($list->list_id, $camp->camp_id)) echo "checked=checked";?> value="<?php echo $list->list_id ?>"/>
                        <label style='display:none;'><?php echo $list->list_name ?> (<?php echo sola_nl_total_list_subscribers($list->list_id) ?>)</label>
                        <p class="description" style='display:none;'><?php echo $list->list_description ?></p>
                  <?php }
                       
                       
                  } else {
                         $lists = sola_nl_get_lists();
                            foreach($lists as $list){?>
                                <input type="checkbox" name="sub_list[]" <?php if(sola_nl_check_if_selected_list_camp($list->list_id, $camp->camp_id)) echo "checked=checked";?> value="<?php echo $list->list_id ?>"/>
                                <label><?php echo $list->list_name ?> (<?php echo sola_nl_total_list_subscribers($list->list_id) ?>)</label>
                                <p class="description"><?php echo $list->list_description ?></p>
                        <?php
                  }}?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php _e("Send From","sola"); ?></label>
                        <p class="description"><?php _e("What email do you want to send from","sola"); ?></p>
                    </td>
                    <td>
                        <input type="email" name="sent_from" value="<?php echo get_option("sola_nl_sent_from") ?>" class='sola-input'/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php _e("Send From Name","sola"); ?></label>
                        <p class="description"><?php _e("Send From Name","sola"); ?></p>
                    </td>
                    <td>
                        <input type="text" name="sent_from_name" value="<?php echo stripslashes(get_option("sola_nl_sent_from_name")) ?>" class='sola-input' />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php _e("Reply To","sola"); ?></label>
                        <p class="description"><?php _e("Where Should your subscribers reply","sola"); ?></p>
                    </td>
                    <td>
                        <input type="email" name="reply_to" value="<?php echo get_option("sola_nl_reply_to") ?>" class="sola-input" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php _e("Reply To Name","sola"); ?></label>
                        <p class="description"><?php _e("Give your subscribers a name to reply to","sola"); ?></p>
                    </td>
                    <td>
                        <input type="text" name="reply_to_name" class="sola-input" value ="<?php echo stripslashes(get_option("sola_nl_reply_to_name")) ?>" />
                    </td>
                </tr>
                <tr style="height:20px;"><td></td><td></td></tr>
                <tr>
                    <td colspan="2">
                        <a title="<?php _e("Return To Editor","sola");?>" class="button" href="admin.php?page=sola-nl-menu&action=editor&camp_id=<?php  echo $_GET['camp_id'] ?>"><?php _e("Return To Editor","sola"); ?></a> 
                          or  
                        <input id="sola_nl_cron_send_btn" type="submit" class="button-primary" name="sola_nl_cron_send" value="<?php _e("Send Now","sola"); ?>"/> <br />
                        <p><em><?php echo __("Your mail will be sent via","sola")." <strong>$mail_method</strong> ($limit ".__("mails every","sola")." ".($limit_time/60)." ".__("minute(s)","sola"); ?>)</em> <small><a href='?page=sola-nl-menu-settings'><?php _e("Edit","sola"); ?></a></small></p>
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
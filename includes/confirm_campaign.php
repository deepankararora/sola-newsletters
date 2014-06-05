<script>
    //alert();
//    jQuery("#sola_nl_send_camp_btn").click( function() {
//        alert("chop");
//    });
//    jQuery( "#sendform" ).submit(function( event ) {
//        alert( "Handler for .submit() called." );
//        //event.preventDefault();
//      });
</script>
<?php $camp = sola_nl_get_camp_details($_GET['camp_id']); ?>
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
                  <p class="description">Give your campaign a subject line to make your subscribers take the bait!</p>
               </td>
               <td>
                  <input type="text" class="sola-input-subject" name="subject" value="<?php if($camp){ echo $camp->subject; } ?>"/>
               </td>
            </tr>

                <tr>
                    <td class="sola-td-vert">
                        <label>Select List</label>
                        <p class="description">Select a list you want to send this campaign to.</p>
                    </td>
                    <td>
                        <?php $lists = sola_nl_get_lists();
                            foreach($lists as $list){?>
                                <input type="checkbox" name="sub_list[]" <?php if(sola_nl_check_if_selected_list_camp($list->list_id, $camp->camp_id)) echo "checked=checked";?> value="<?php echo $list->list_id ?>"/>
                                <label><?php echo $list->list_name ?> (<?php echo sola_nl_total_list_subscribers($list->list_id) ?>)</label>
                                <p class="description"><?php echo $list->list_description ?></p>
                        <?php
                        }?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Send From</label>
                        <p class="description">What email do you want to send from</p>
                    </td>
                    <td>
                        <input type="email" name="sent_from" value="<?php echo get_option("sola_nl_sent_from") ?>" class='sola-input'/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Send From Name</label>
                        <p class="description">Send From Name</p>
                    </td>
                    <td>
                        <input type="text" name="sent_from_name" value="<?php echo stripcslashes(get_option("sola_nl_sent_from_name")) ?>" class='sola-input' />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Reply To</label>
                        <p class="description">Where Should your subscribers reply</p>
                    </td>
                    <td>
                        <input type="email" name="reply_to" value="<?php echo get_option("sola_nl_reply_to") ?>" class="sola-input" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Reply To Name</label>
                        <p class="description">Give your subscribers a name to reply to</p>
                    </td>
                    <td>
                        <input type="text" name="reply_to_name" class="sola-input" value ="<?php echo stripcslashes(get_option("sola_nl_reply_to_name")) ?>" />
                    </td>
                </tr>
                                <tr>
                    <td colspan="2">
                        
                        <a title="Return To Editor" class="button" href="admin.php?page=sola-nl-menu&action=editor&camp_id=<?php  echo $_GET['camp_id'] ?>">Return To Editor</a> 
                          or  
                        <input id="sola_nl_cron_send_btn" type="submit" class="button-primary" name="sola_nl_cron_send" value="Send Now"/> <br />
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
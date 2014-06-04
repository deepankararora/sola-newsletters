<br /><br />
<hr />
<div class="footer" style="padding:15px 7px;">
    <div id=foot-contents>
        <div class="support">
            <em><?php _e("Sola Newsletters is still in BETA. If you find any errors or if you have any suggestions","sola");?>, <a href="http://support.solaplugins.com" target="_BLANK"><?php _e("please get in touch with us","sola"); ?></a>.</em>
            
            <?php if (function_exists("sola_nl_register_pro_version")) { global $sola_nl_pro_version; global $sola_nl_pro_version_string; ?>
            
            <br />Sola Newsletter Premium Version: <a target='_BLANK' href="http://solaplugins.com/plugins/sola-newsletters/?utm_source=plugin&utm_medium=link&utm_campaign=version_premium"><?php echo $sola_nl_pro_version.$sola_nl_pro_version_string; ?></a> |
            <a target="_blank" href="http://support.solaplugins.com/">Support</a>
            <?php } else { global $sola_nl_version; global $sola_nl_version_string; ?>
            <br />Sola Newsletter Version: <a target='_BLANK' href="http://solaplugins.com/plugins/sola-newsletters/?utm_source=plugin&utm_medium=link&utm_campaign=version_free"><?php echo $sola_nl_version.$sola_nl_version_string; ?></a> |
            <a target="_blank" href="http://support.solaplugins.com/">Support</a> | 
            <a target="_blank" id="uppgrade" href="http://solaplugins.com/plugins/sola-newsletters/?utm_source=plugin&utm_medium=link&utm_campaign=footer" title="Premium Upgrade">Go Premium</a>
            <?php } ?>
            <!-- | Add your 
            <a target="_blank" href="">★★★★★</a> on <a target="_blank" href="">wordpress.org</a> -->
        </div>
    </div>
</div>

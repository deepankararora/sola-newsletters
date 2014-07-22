jQuery(document).ready(function() {
    
    jQuery("#datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
    
    var orig_smtp_host = jQuery("#sola_nl_host").val();
    var orig_smtp_port = jQuery("#sola_nl_port").val();
    
    
    sola_nl_show_div();
    jQuery('#radio_button_1').click(function (){
        sola_nl_show_div();
    });
    jQuery('#radio_button_2').click(function (){
        sola_nl_show_div();
    });
    jQuery('#radio_button_3').click(function (){
        sola_nl_show_div();
    });
    
    
    
    
    jQuery("body").on("change", "#sola_nl_hosting_provider", function(){      
        
        var sola_nl_host_select = jQuery('option:selected', this).attr('send_limit');
        var sola_nl_host_limiting = jQuery('option:selected', this).attr('mtype');
        
        jQuery("#sola_nl_send_limit_qty").val(sola_nl_host_select);
        jQuery("#sola_nl_send_limit_type").val(sola_nl_host_limiting);
        
    });

    jQuery(".schedule_send_block").hide();
    jQuery("#sola_nl_schedule_send_btn").click(function() {
        jQuery(".schedule_send_block").toggle();
    });
        
    
    
    //Send A Test Emial -test if mail is working
    jQuery("body").on("click", ".sola_send_test_mail", function(){      
        jQuery(this).prop( "disabled", true );
        var sola_nl_to = jQuery("#sola_nl_to_mail_test").val();
        
        var smtp_host = jQuery("#sola_nl_host").val();
        var smtp_port = jQuery("#sola_nl_port").val();
        var smtp_user = jQuery("#sola_nl_username").val();
        var smtp_pass = jQuery("#sola_nl_password").val();
        var smtp_encrypt = jQuery("input[name=encryption]:radio:checked").val();
        
        if (jQuery('#sola_nl_to_mail_test_debug').is(':checked')) { var smtp_debug = "on"; } else { var smtp_debug = false; }
        if (jQuery('#radio_button_1').is(':checked')) { var wpmail = 'wpmail'; } else { var wpmail = false; }
        
        var data = {
            action: 'test_mail_2',
            smtp_host: smtp_host,
            smtp_port: smtp_port,
            smtp_user: smtp_user,
            smtp_pass: smtp_pass,
            smtp_debug: smtp_debug,
            smtp_encrypt:smtp_encrypt,
            mail_type: wpmail,
            to: sola_nl_to,
            security: sola_nl_nonce
        };
        
        //alert(data);
        jQuery.post(ajaxurl, data, function(response) {
            alert(response);
            jQuery(".sola_send_test_mail").prop( "disabled", false );
            
        });
    });
    //send preview mail
    jQuery("body").on("click", ".sola_send_preview", function(){
        jQuery(this).prop("disabled", true);
        var data = {
            action: "preview_mail",
            to: jQuery("#sola_nl_to_mail_test").val(),
            security:sola_nl_nonce,
            body:jQuery("#sola_newsletter_preview").html(),
            camp_id:camp_id
        };
        jQuery.post(ajaxurl, data, function(response){
           alert(response);
           jQuery(".sola_send_preview").prop("disabled", false);
        });
    });
    
    jQuery( "#sendform" ).submit(function( event ) {
        if(!confirm("Are you sure you want to send your campaign")){
            event.preventDefault();
        }         
      });
    
    
    function sola_nl_show_div(){
        if(jQuery('#radio_button_2').is(':checked')) { 
            jQuery("#sola_nl_smtp").show();
            jQuery("#sola_nl_host").val(orig_smtp_host).attr("readonly", false);
            jQuery("#sola_nl_port").attr("readonly", false);
            jQuery("#sola_nl_hosting_provider").val("2");
            
            //jQuery("#sola_nl_port").val(orig_smtp_port).attr("disabled", false);
        } 
        else if (jQuery('#radio_button_1').is(':checked')){
            jQuery("#sola_nl_smtp").hide();
            jQuery("#sola_nl_port").attr("readonly", false);
            jQuery("#sola_nl_hosting_provider").val("0");
        }
        else if (jQuery('#radio_button_3').is(':checked')){
            jQuery("#sola_nl_smtp").show();
            jQuery("#sola_nl_host").val("smtp.gmail.com").attr("readonly", "readonly");
            jQuery("#sola_nl_port").val("465").attr("readonly", "readonly");
            jQuery("#sola_nl_hosting_provider").val("1");
            jQuery("#enc_ssl").prop('checked', true);


        }
    };
    
    jQuery("#sola_check_all").click(function(){
        if(jQuery(this).attr('checked')){
            jQuery(".sola-check-box").prop('checked', true);
        } else {
            jQuery(".sola-check-box").prop('checked', false);
        }
    });
    jQuery(".sola-check-box").click(function(){
        if(!jQuery(this).attr('checked')){
            jQuery("#sola_check_all").prop('checked', false);
        }
    });
    
    //Jarryd
    
    jQuery(".preview_button_button").click(function (){	
        
        var width = jQuery(this).attr('window_width');
        var add_class = jQuery(this).attr('add_class');

        jQuery('#sola_newsletter_preview').fadeOut(500, function(){
            jQuery("#preview_container").removeClass();                    
            jQuery("#preview_container").addClass(add_class);                        
            jQuery('#sola_newsletter_preview').css("width", width);
            jQuery("#sola_newsletter_preview").fadeIn();
        }); 
    });
//    jQuery("#preview_desktop").removeClass("active");
//    jQuery("#preview_desktop").click(function(){
//        jQuery("#preview_desktop").removeClass();
//        jQuery("#preview_mobile").removeClass();
//        jQuery("#preview_tablet").removeClass();
//        jQuery("#preview_tablet_landscape").removeClass();
//        jQuery("#preview_desktop").addClass("active");   
//    });
//    jQuery("#preview_mobile").click(function(){
//        jQuery("#preview_desktop").removeClass();
//        jQuery("#preview_mobile").removeClass();
//        jQuery("#preview_tablet").removeClass();
//        jQuery("#preview_tablet_landscape").removeClass();
//        jQuery("#preview_mobile").addClass("active");   
//    });
//    jQuery("#preview_tablet").click(function(){
//jQuery("#preview_desktop").removeClass();
//        jQuery("#preview_mobile").removeClass();
//        jQuery("#preview_tablet").removeClass();
//        jQuery("#preview_tablet_landscape").removeClass();
//        jQuery("#preview_tablet").addClass("active");   
//    });
//    jQuery("#preview_tablet_landscape").click(function(){
//        jQuery("#preview_desktop").removeClass();
//        jQuery("#preview_mobile").removeClass();
//        jQuery("#preview_tablet").removeClass();
//        jQuery("#preview_tablet_landscape").removeClass();
//        jQuery("#preview_tablet_landscape").addClass("active");   
//    });
    

    jQuery("#sola_newsletter_wrapper img").each(function(){
        
        var attribute_width = jQuery(this).attr('width'); 
        
        if(!attribute_width){
            attribute_width = jQuery(this).css('width');
        }
            if (attribute_width.indexOf("%", 0) === -1) {
                var stripped_width = attribute_width.replace("px", "");
                var attribute_width_new = parseInt(stripped_width);    
                var real_attr_width = ((attribute_width_new / 600) * 100);
                var real_real_attr_width = real_attr_width+"%";
                jQuery(this).attr('width',real_real_attr_width);            
            } 
        
    });
});


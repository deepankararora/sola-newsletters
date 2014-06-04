jQuery(function(){
    jQuery('.sola_nl_sub_form').submit(function(event) {
        event.preventDefault();
        var div = "#".concat(jQuery(this).parent().attr("id"));
        //alert(div);
        //
        var form = jQuery(this).serialize();
        console.log("here");
        console.log(form);
//        var data = {
//            action: 'sola_nl_sign_up_add_sub',
//            security: jQuery(this).attr('nonce'),
//            sub_email: jQuery('#sub_email').val(),
//            sub_name: jQuery('#sub_name').val(),
//            sub_list:jQuery('#sub_list').val()
//        };
        jQuery.ajax({
            url:ajaxurl,
            type:"POST",
            data: form,
            beforeSend: function(){
                //console.log(data);
                //alert(div);
                jQuery(div).empty().append('<p><img width="25px"  src="wp-content/plugins/sola-newsletters/images/loading.gif" /> Subscribing...</p>');
            },
            error: function(jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }
            },
            success: function(response){
                //console.log("Sent");
                jQuery(div).empty().append(response);
            }
        
        });
//        jQuery.post(ajaxurl, data, 
//        function(response) {
//            
//            jQuery('#sola_nl_sign_up_box').empty().append(response);
//        });
    });
});





//jQuery(document).ready( function() {
//    jQuery("body").on("click", "#add_sub_btn", function(){
        
//    });
//});
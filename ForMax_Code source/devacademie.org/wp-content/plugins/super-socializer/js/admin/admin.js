var theChampReferrer = null,
    theChampReferrerVal = "",
    theChampReferrerTabId = "";

function theChampEmailPopupOptions(e) {
    jQuery(e).is(":checked") ? jQuery("#the_champ_email_popup_options").css("display", "block") : jQuery("#the_champ_email_popup_options").css("display", "none")
}

function theChampCommentingOptions(e) {
    jQuery(e).is(":checked") ? jQuery("#the_champ_commenting_extra").css("display", "none") : jQuery("#the_champ_commenting_extra").css("display", "table-row-group")
}

function theChampSetReferrer(e) {
    jQuery(theChampReferrer).val(theChampReferrerVal.substring(0, theChampReferrerVal.indexOf("#") > 0 ? theChampReferrerVal.indexOf("#") : theChampReferrerVal.length) + e)
}
jQuery(document).ready(function() {
    jQuery("#tabs").tabs(), theChampReferrer = jQuery("input[name=_wp_http_referer]"), theChampReferrerVal = jQuery("input[name=_wp_http_referer]").val(), (theChampReferrerTabId = location.href.indexOf("#") > 0 ? location.href.substring(location.href.indexOf("#"), location.href.length) : "") && theChampSetReferrer(theChampReferrerTabId), jQuery("#tabs ul a").click(function() {
        theChampSetReferrer(jQuery(this).attr("href"))
    }),
    jQuery("div.theChampHorizontalSharingProviderContainer").find('input').click(function() {
        if(jQuery(this).val() == 'youtube' || jQuery(this).val() == 'google'){
            if(jQuery(this).is(":checked") && jQuery(this).val() == 'youtube'){
                jQuery("#the_champ_google_options, #the_champ_youtube_options").css("display", "table-row-group");
            }else if(jQuery(this).is(":checked") && jQuery(this).val() == 'google'){
                jQuery("#the_champ_google_options").css("display", "table-row-group");
            }else if((jQuery(this).val() == 'youtube' && jQuery("div.theChampHorizontalSharingProviderContainer").find('input[value=google]').is(":checked") === false) || (jQuery(this).val() == 'google' && jQuery("div.theChampHorizontalSharingProviderContainer").find('input[value=youtube]').is(":checked") === false)){
                jQuery("#the_champ_google_options, #the_champ_youtube_options").css("display", "none")
            }else if(jQuery(this).val() == 'youtube' && !jQuery(this).is(":checked")){
                jQuery("#the_champ_youtube_options").css("display", "none")
            }
        }else{
            jQuery(this).is(":checked") ? jQuery("#the_champ_" + jQuery(this).val() + "_options").css("display", "table-row-group") : jQuery("#the_champ_" + jQuery(this).val() + "_options").css("display", "none")
        }
    }),
    jQuery("#the_champ_sl_email_username").click(function() {
        if(jQuery(this).is(":checked")){jQuery("#the_champ_sl_username_email").prop('checked', false)}
    }),jQuery("#the_champ_sl_username_email").click(function() {
        if(jQuery(this).is(":checked")){jQuery("#the_champ_sl_email_username").prop('checked', false)}
    }),jQuery("#the_champ_gdpr_enable").click(function() {
        jQuery(this).is(":checked") ? jQuery("#the_champ_gdpr_options").css("display", "table-row-group") : jQuery("#the_champ_gdpr_options").css("display", "none")
    }), jQuery("#the_champ_login_redirection_column").find("input[type=radio]").click(function() {
        jQuery(this).attr("id") && "the_champ_login_redirection_custom" == jQuery(this).attr("id") ? jQuery("#the_champ_login_redirection_url").css("display", "block") : jQuery("#the_champ_login_redirection_url").css("display", "none")
    }), jQuery("#the_champ_login_redirection_custom").is(":checked") ? jQuery("#the_champ_login_redirection_url").css("display", "block") : jQuery("#the_champ_login_redirection_url").css("display", "none"), jQuery("#the_champ_register_redirection_column").find("input[type=radio]").click(function() {
        jQuery(this).attr("id") && "the_champ_register_redirection_custom" == jQuery(this).attr("id") ? jQuery("#the_champ_register_redirection_url").css("display", "block") : jQuery("#the_champ_register_redirection_url").css("display", "none")
    }), jQuery("#the_champ_register_redirection_custom").is(":checked") ? jQuery("#the_champ_register_redirection_url").css("display", "block") : jQuery("#the_champ_register_redirection_url").css("display", "none"), jQuery(".the_champ_help_bubble").attr("title", theChampHelpBubbleTitle), jQuery(".the_champ_help_bubble").click(function() {
        jQuery("#" + jQuery(this).attr("id") + "_cont").toggle(500)
    }), jQuery("#the_champ_fb_comment_switch_wp").keyup(function() {
        jQuery(this).prev("span").remove(), "" == jQuery(this).val().trim() ? jQuery(this).before('<span style="color:red">This cannot be blank</span>') : jQuery(this).val().trim() == jQuery("#the_champ_fb_comment_switch_fb").val().trim() && jQuery(this).before('<span style="color:red">This cannot be same as text on "Switch to Facebook Commenting" button</span>')
    }), jQuery("input#the_champ_enable_commenting, input#the_champ_login_enable, input#the_champ_counter_enable").click(function() {
        jQuery(this).is(":checked") ? jQuery("div#tabs").css("display", "block") : jQuery("div#tabs").css("display", "none")
    }), jQuery("#the_champ_fb_comment_switch_fb").keyup(function() {
        jQuery(this).prev("span").remove(), "" == jQuery(this).val().trim() ? jQuery(this).before('<span style="color:red">This cannot be blank</span>') : jQuery(this).val().trim() == jQuery("#the_champ_fb_comment_switch_wp").val().trim() && jQuery(this).before('<span style="color:red">This cannot be same as text on "Switch to WordPress Commenting" button</span>')
    })
}), jQuery("html, body").animate({
    scrollTop: 0
});
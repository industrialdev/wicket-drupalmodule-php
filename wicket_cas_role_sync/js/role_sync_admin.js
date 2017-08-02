/**
 * Disable the individual role options if "Sync all roles" is checked
 */
(function($){
  // on click
  $("input[name=wicket_cas_role_sync_admin_settings_sync_all_roles]").click( function(){
     if($(this).is(':checked')){
       $('input[name*=wicket_cas_role_sync_admin_settings_whitelisted_roles').prop('disabled', true);
     }else{
       $('input[name*=wicket_cas_role_sync_admin_settings_whitelisted_roles').prop('disabled', false);
     }
  });
  // on load
  if($("input[name=wicket_cas_role_sync_admin_settings_sync_all_roles]").is(':checked')){
    $('input[name*=wicket_cas_role_sync_admin_settings_whitelisted_roles').prop('disabled', true);
  }else{
    $('input[name*=wicket_cas_role_sync_admin_settings_whitelisted_roles').prop('disabled', false);
  }
})(jQuery);

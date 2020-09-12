jQuery( document ).ready( function( $ ) {
  let togglers = [ '.toggler #post_icons_plugin_enable' ],
      toggled  = [ '.toggled' ],
      speed    = 400;
  function _init_toggle() {
    for( toggler in togglers ) {
      var $toggler = $( togglers[toggler] ),
      $toggled = $( toggled[toggler] ),
      $checked = !!$( togglers[toggler]+':checked' ).length;
      $toggled.hide();
      if ( $checked ) {
        $toggled.show();
      }
      $toggler.on( 'click', '', $toggled, function( e ) {
        e.data.slideToggle( speed );
      });
    }
  }
  _init_toggle();
});
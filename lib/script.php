<?php
function add_contact_css(){
  wp_enqueue_style( "oja_contact_admin.css", plugins_url( '/style/options.css', dirname(__FILE__)  ) );
}
add_action('wp_enqueue_scripts','add_contact_css');
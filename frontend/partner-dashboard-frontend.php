<?php

add_filter( 'template_include', 'my_plugin_templates' );

function my_plugin_templates( $template ) {

  if ( is_singular( 'partner' ) && locate_template( array( 'single-partner.php' ) ) !== $template ) {
      $template = plugin_dir_path( __FILE__ ) . 'partner-dashboard-template.php';
  }

  return $template;

}

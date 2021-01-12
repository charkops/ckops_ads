<?php
  /*
    Plugin Name: Ckops Ads
    Plugin URI: https://github.com/charkops/ckops_ads
    description: A Plugin that inserts a Google Test Add after 2nd paragraph of each post
    Version: 1.0
    Author: Charis Kopsacheilis
    Author URI: https://github.com/charkops
    License: GPL2
  */

  // Enqueues necessary gpt script to load in header 
  function enqueue_scripts() {
    wp_enqueue_script('gpt_script', 'https://securepubads.g.doubleclick.net/tag/js/gpt.js');
  }
  add_action('wp_enqueue_scripts', 'enqueue_scripts');

  // NOTE (@charkops): This is sloppy
  // Run script to setup googletag before </head> in order to be able to use it later for ad loading
  function setup_googletag() {
    echo '<script>
            window.googletag = window.googletag || {cmd: []};
            googletag.cmd.push(function() {
              googletag
                  .defineSlot(
                      "/6355419/Travel/Europe/France/Paris", [300, 250], "banner-ad")
                  .addService(googletag.pubads());
              googletag.enableServices();
            });
          </script>';
  }
  add_action('wp_head', 'setup_googletag');

  // Insert ads after second paragraph of single post content.
  function prefix_insert_post_ads( $content ) {
    $ad_code = '<div id="banner-ad" style="width: 300px; height: 250px;">
                <script>
                  googletag.cmd.push(function() {
                    googletag.display("banner-ad");
                  });
                </script>
              </div>';
    if ( is_single() && !is_admin() ) {
      return prefix_insert_after_paragraph( $ad_code, 2, $content );
    }
    return $content;
  }
  add_filter( 'the_content', 'prefix_insert_post_ads' );

  // Parent function that makes the magic happen
  function prefix_insert_after_paragraph( $insertion, $paragraph_id, $content ) {
    $closing_p = '</p>';
    $paragraphs = explode( $closing_p, $content );
    foreach ($paragraphs as $index => $paragraph) {

      if ( trim( $paragraph ) ) {
        $paragraphs[$index] .= $closing_p;
      }

      if ( $paragraph_id == $index + 1 ) {
        $paragraphs[$index] .= $insertion;
      }
    }
    
    return implode( '', $paragraphs );
  }

?>
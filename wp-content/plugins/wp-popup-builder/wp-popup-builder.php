<?php
if (!defined('ABSPATH')) exit;
/*
  Plugin Name: WP Popup Builder
  Description: WP Popup Builder is a powerfull tool to create amazing popup form for your site. Its drag and drop feature helps to create form in very easy step without having knowledge of coding. And also you can easily design and edit your form using easy to use interface. It has ready to use "Pre Built Popup" to give a quick start for your form, Also you can create your own design by choosing three different "Layouts" available. Images, Heading, Text, Button and even external form can be added to it.
  Version: 1.2.4
  Author: ThemeHunk
  Author URI: http://www.themehunk.com/
  Text Domain: wppb
 */
if (!function_exists('wppb_loaded_pro')) {
  define('WPPB_URL', plugin_dir_url(__FILE__));
  define('WPPB_PATH', plugin_dir_path(__FILE__));
  define("WPPB_PAGE_URL", admin_url('admin.php?page=wppb'));
  include_once(WPPB_PATH . 'admin/inc.php');
  include_once(WPPB_PATH . 'front/shortcode.php');
  include_once(WPPB_PATH . 'front/load.php');
  add_action('plugins_loaded', 'wppb_loaded');
  function wppb_loaded()
  {
    $instance  = wppb::get();
    $load_Files =  wppb::load_file();
    foreach ($load_Files as $value) {
      include_once(WPPB_PATH . 'admin/' . $value . '.php');
    }
    wppb_shortcode::get();
    wppb_load::get();
  }
  // show notify
  include_once(plugin_dir_path(__FILE__) . 'notify/notify.php');
}

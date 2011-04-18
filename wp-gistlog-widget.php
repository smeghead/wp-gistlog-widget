<?php
/*
Plugin Name: WP-Gistlog-Widget
Plugin URI: 
Description: This plugin allows you to display the gistlog.org histories.
Version: 1.1.0
Author: smeghead, fukata
*/

// wpgw_options_page() displays the page content for the Test Options submenu
function wpgw_options_page() {
  // Read in existing option value from database
  $widget_title = get_option('wpgw_widget_title');
  $user_id = get_option('wpgw_user_id' );
  $type = get_option('wpgw_type' );
  $display_gist_num = get_option('wpgw_display_gist_num' );

  // See if the user has posted us some information
  // If they did, this hidden field will be set to 'Y'
  if (isset($_POST['is_submit'])) {
    $widget_title = $_POST['wpgw_widget_title'];
    $user_id = $_POST['wpgw_user_id'];
    $type = $_POST['wpgw_type'];
    $display_gist_num = $_POST['wpgw_display_gist_num'];
    update_option('wpgw_widget_title', $widget_title);
    update_option('wpgw_user_id', $user_id);
    update_option('wpgw_type', $type);
    update_option('wpgw_display_gist_num', $display_gist_num);
  }
?>

  <input type="hidden" name="is_submit" value="true">
  <p><?php _e("WP Gistlog Widget Widget Title", 'mt_trans_domain' ); ?> 
    <input type="text" name="wpgw_widget_title" value="<?php echo $widget_title; ?>" size="40">
  </p>
  <p><?php _e("WP Gistlog User Id:", 'mt_trans_domain' ); ?> 
    <input type="text" name="wpgw_user_id" value="<?php echo $user_id; ?>" size="40">
  </p>
  <p><?php _e("WP Gistlog Widget Category:", 'mt_trans_domain' ); ?> 
    <select name="wpgw_type">
      <option value="recent">recent</options>
    </select>
  </p>
  <p><?php _e("Display Gist Num:", 'mt_trans_domain') ?><input type="text" name="wpgw_display_gist_num" value="<?php echo $display_gist_num; ?>" size="4" maxlength="3"/></p>
<?php
}

function get_widget_url() {
  $user_id = get_option('wpgw_user_id'); 
  $type = get_option('wpgw_type');
  $display_num = get_option('wpgw_display_gist_num');
  $display_num = $display_num ? "&num={$display_num}" : '';
  return "http://www.gistlog.org/{$user_id}/widget.js?type={$type}{$display_num}";
}
function wpgw_show_widget($args) {
  $widget_title = get_option('wpgw_widget_title'); 

  if (empty($widget_title)) {
    $widget_title = 'Gistlog.org';
    update_option('wpgw_widget_title', $widget_title);
  }

  echo $args['before_title'] . $widget_title . $args['after_title'] . $args['before_widget'];

  $widget_url = get_widget_url();
  $contents = file_get_contents($widget_url);
  preg_match_all('/<a href="([^"]*)"[^>]*>([^>]*)<\/a>/', $contents, $matches, PREG_PATTERN_ORDER);
  echo '<ul>';
  for ($i = 0; $i < count($matches[0]); $i++) {
    echo "<li><a href=\"{$matches[1][$i]}\">{$matches[2][$i]}</a></li>\n";
  }
  echo '</ul>';
?>
  <?php
  echo $args['after_widget'];
}

function wpgw_init_widget() {
  register_sidebar_widget('WP Gistlog Widget', 'wpgw_show_widget');
  register_widget_control('WP Gistlog Widget', 'wpgw_options_page', 250, 200 );
}
add_action("plugins_loaded", "wpgw_init_widget");

?>

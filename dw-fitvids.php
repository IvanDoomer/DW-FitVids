<?php
/*
Plugin Name: DW FitVids
Plugin URI: http://www.ti.blog.br/plugins/dw-fitvids/
Description: This plugin makes videos responsive using the FitVids jQuery plugin on WordPress.
Version: 1.2
License: GPL
Tags: videos, fitvids, responsive
Author: Ivan Carlos
Author URI: http://www.ti.blog.br/
*/


// protect yourself
if ( !function_exists( 'add_action') ) {
	echo "Hi there! Nice try. Come again.";
	exit;
}

class fitvids_wp {
	// when object is created
	function __construct() {
		add_action('admin_menu', array($this, 'menu')); // add item to menu
		add_action('wp_enqueue_scripts', array($this, 'fitvids_scripts')); // add fit vids to site
	}

	// make menu
	function menu() {
		add_submenu_page('themes.php', 'DW FitVids', 'FitVids', 'switch_themes', __FILE__,array($this, 'settings_page'), '', '');
	}

	// create page for output and input
	function settings_page() {
		?>
	    <div class="icon32" id="icon-themes"><br></div>
	    <div id="fitvids-wp-page" class="wrap">
	    
	    <h2>DW FitVids</h2>
	    
	    <?php
	    // $_POST needs to be sanitized by version 1.0
	   	if( isset($_POST['submit']) && check_admin_referer('fitvids_action','fitvids_ref') ) {
			  $fitvids_wp_message = '';

	   		update_option('fitvids_wp_jq', addslashes($_POST['fitvids_wp_jq']));
	   		update_option('fitvids_wp_selector', esc_js(trim($_POST['fitvids_wp_selector'])));
			  update_option('fitvids_wp_custom_selector',  esc_js(trim($_POST['fitvids_wp_custom_selector'])));
	   		
	   		if($_POST['fitvids_wp_jq'] != '') { $fitvids_wp_message .= 'You have enabled jQuery for your theme.'; }
	   		echo '<div id="message" class="updated below-h2"><p>FitVids is updated. ', $fitvids_wp_message ,'</p></div>';
	   	}
	    ?>
	    
	    <form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
		  <?php
		  wp_nonce_field('fitvids_action','fitvids_ref');
		  $checked = '';
	    if(get_option('fitvids_wp_jq') == 'true') { $checked = 'checked="checked"'; }
	    ?>

      <table class="form-table">
	    <tbody>
	    <tr>
		  <td>

		    <h3 style="font-weight: bold;">jQuery</h3>
				<p>If you are already running jQuery 1.7+ you will not need to check the box. Note that some plugins require different versions of jQuery and may have conflicts with FitVids.</p>
			  <label><input 	id="fitvids_wp_jq"
			          value="true"
			          name="fitvids_wp_jq"
			          type="checkbox"
			          <?php if(isset($checked)) echo $checked; ?>
			      > Add jQuery 1.7.2 from Google CDN</label>

		  </td>
	    </tr>
	    <tr>
		  <td>

			<h3 style="font-weight: bold;"><label for="fitvids_wp_selector">Enter jQuery Selector</label></h3>
			<p>Add a CSS selector for FitVids to work. <a href="http://www.w3schools.com/jquery/jquery_selectors.asp" target="_blank"> Need help?</a></p>
			<p><em>jQuery(" <input id="fitvids_wp_selector" value="<?php echo get_option('fitvids_wp_selector'); ?>" name="fitvids_wp_selector" type="text"> ").fitVids();</em></p>

			<h3 style="font-weight: bold;"><label for="fitvids_wp_custom_selector">Enter FitVids Custom Selector</label></h3>
			<p>Add a custom selector for FitVids if you are using videos that are not supported by default. <a href="https://github.com/davatron5000/FitVids.js#add-your-own-video-vendor" target="_blank"> Need help?</a></p>
			<p><em>jQuery().fitVids({ customSelector: " <input id="fitvids_wp_custom_selector" value="<?php echo stripslashes(get_option('fitvids_wp_custom_selector')); ?>" name="fitvids_wp_custom_selector" type="text"> "});</em></p>


			<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>

		  </td>
	    </tr>
	    </tbody>
      </table>
	    </form>
	    
	    </div>
	    
	    <?php }
    
    // add FitVids to site
    function fitvids_scripts() {
    	if(get_option('fitvids_wp_jq') == 'true') {
    	wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', '1.0');
			wp_enqueue_script( 'jquery' );
    	}
    	
    	// add fitvids
    	wp_register_script( 'fitvids', plugins_url('/jquery.fitvids.js', __FILE__), array('jquery'), '1.0', true);    	
    	wp_enqueue_script( 'fitvids');
    	add_action('wp_print_footer_scripts', array($this, 'add_fitthem'));
    } // end fitvids_scripts

    // slecetor script
    function add_fitthem() { ?>
    	<script type="text/javascript">
    	jQuery(document).ready(function() {
    		jQuery('<?php echo get_option('fitvids_wp_selector'); ?>').fitVids({ customSelector: "<?php echo stripslashes(get_option('fitvids_wp_custom_selector')); ?>"});
    	});
    	</script><?php
    }    
} // end fitvids_wp obj

new fitvids_wp();

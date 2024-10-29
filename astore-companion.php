<?php
/*
	Plugin Name: AStore Companion
	Description: AStore theme options.
	Author: VelaThemes
	Author URI: https://velathemes.com/
	Version: 1.0.5
	Text Domain: astore-companion
	Domain Path: /languages
	License: GPL v2 or later
*/

defined('ABSPATH') or die("No script kiddies please!");

require_once 'inc/widget-recent-posts.php';
require_once 'inc/pageMetabox/options.php';
require_once 'inc/AStore_Taxonomy_Images.php';
require_once 'inc/templates-importer/templates-importer.php';
require_once 'inc/elementor-widgets/elementor-widgets.php';


if (!class_exists('AStoreCompanion')){

	class AStoreCompanion{	
		
		public function __construct($atts = NULL)
		{

			register_activation_hook( __FILE__, array(&$this ,'plugin_activate') );
			add_action( 'plugins_loaded', array(&$this, 'init' ) );
			add_action( 'admin_menu', array(&$this ,'plugin_menu') );
			add_action( 'switch_theme', array(&$this ,'plugin_activate') );
			add_action( 'wp_enqueue_scripts',  array(&$this , 'enqueue_scripts' ));
			add_action( 'admin_enqueue_scripts',  array(&$this , 'enqueue_admin_scripts' ));
			add_action( 'wp_footer', array( $this, 'gridlist_set_default_view' ) );
			
			add_filter( 'astore_page_sidebar_layout', array(&$this ,'page_sidebar_layout'), 20,1 );
			add_action( 'astore_before_sidebar', array( $this, 'before_sidebar' ) );
			add_action( 'astore_after_sidebar', array( $this, 'after_sidebar' ) );

			
			//add_action( 'customize_controls_init', array( &$this,'customize_controls_enqueue') );

		}
	
	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	function customize_controls_enqueue(){
		//wp_enqueue_script( 'astore_companion_customizer_controls',  plugins_url('/assets/js/customizer.js', __FILE__), '', '1.0.0', true );
			
	}	


	function plugin_activate( $network_wide ) {
			
			 if ( is_plugin_active('cactus-companion/cactus-companion.php') ) {
    			deactivate_plugins('cactus-companion/cactus-companion.php');    
    		}
					
			}
		
	public static function init() {
		
		load_plugin_textdomain( 'astore-companion', false,  basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	/**
	 * Enqueue admin scripts
	*/
	function enqueue_admin_scripts()
	{
		wp_enqueue_style( 'wp-color-picker' );
		
		$theme = get_option('stylesheet');
		
		if(isset($_GET['page']) && $_GET['page']=='astore-template'){
			wp_enqueue_script( 'plugin-install' );
			wp_enqueue_script( 'updates' );
		}
		wp_enqueue_style( 'astore-companion-admin', plugins_url('assets/css/admin.css', __FILE__));
		wp_enqueue_script( 'astore-companion-admin', plugins_url('assets/js/admin.js', __FILE__),array('jquery','wp-color-picker' ),'',true);
		wp_localize_script( 'astore-companion-admin', 'astore_companion_admin',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce( 'wp_rest' ),
					'i18n' =>array('t1'=> __( 'Install and Import', 'astore-companion' ),'t2'=> __( 'Import', 'astore-companion' ) ),
				) );

	if( $theme == 'astore-pro' ){
		$custom_css = '.astore-free{ display:none;}';
		wp_add_inline_style( 'astore-companion-admin', wp_filter_nohtml_kses($custom_css) );
	}
	}
	
	/**
	 * Enqueue front scripts
	*/
	
	function enqueue_scripts()
	{
	
		global $post;
		$custom_css = '';
		$postid = isset($post->ID)?$post->ID:0;
		if(is_home()){
			$postid = get_option( 'page_for_posts' );
			}
			
		if($postid>0){
			$this->slider = get_post_meta($postid, '_acmb_slideshow', true);
			$bg_color = get_post_meta($postid, '_acmb_bg_color', true);
			$bg_image = get_post_meta($postid, '_acmb_bg_image', true);
			
			if($bg_color!=''){
				$custom_css .= '.page-id-'.$postid.' .page-wrap,.postid-'.$postid.' .page-wrap{background-color:'.$bg_color.';}';
				if( !is_page_template('template-sections.php') )
					$custom_css .= '.page-id-'.$postid.' .page-inner, .postid-'.$postid.' .page-inner{padding-top: 30px;}';
				}
			if($bg_image!=''){
				$custom_css .= '.page-id-'.$postid.' .page-wrap, .postid-'.$postid.' .page-wrap{background-image:url('.$bg_image.');}';
				if( !is_page_template('template-sections.php') )
					$custom_css .= '.page-id-'.$postid.' .page-inner, .postid-'.$postid.' .page-inner{padding-top: 30px;}';
				
				}
				
		}
		if(!empty($this->slider) && is_array($this->slider)){
			$custom_css .= '.page-id-'.$postid.' .page-wrap, .blog .page-wrap{padding-top: 0;}.page-id-'.$postid.' .page-inner, .blog .page-inner{padding-top:30px;}';
		}
		
		if (is_category()) {
			  $category = get_category(get_query_var('cat'));
			  $cat_id = $category->cat_ID;
			  if($cat_id>0){
					$category_meta = get_term_meta($cat_id);
					$category_meta = isset($category_meta['cactus_category_meta'])?unserialize($category_meta['cactus_category_meta'][0]):null;
					
					if(isset($category_meta[$cat_id]['_acmb_bg_color'])){
						$custom_css .= ".category-".$cat_id." .page-wrap{background-color:".$category_meta[$cat_id]['_acmb_bg_color'].";}";
						$custom_css .= ".category-".$cat_id." .page-inner, .category-".$cat_id." .page-inner{padding-top: 30px;}";
						}
					if(isset($category_meta[$cat_id]['bg_img'])){
						$image = wp_get_attachment_image_url( $category_meta[ $cat_id ]['bg_img'], 'full');
						
						$custom_css .= ".category-".$cat_id." .page-wrap{background-image:url(".$image.");}";
						$custom_css .= ".category-".$cat_id." .page-inner, .category-".$cat_id." .page-inner{padding-top: 30px;}";
						}
						
				  }
		  }

		$i18n = array();
		wp_enqueue_script( 'jquery-cookie', plugins_url('assets/js/jquery.cookie.min.js', __FILE__), array( 'jquery' ), null, true);
		wp_enqueue_style( 'astore-companion-front', plugins_url('assets/css/front.css', __FILE__));
		wp_enqueue_script( 'astore-companion-front', plugins_url('assets/js/front.js', __FILE__),array('jquery'),'',true);
		
		if($custom_css!='')
			wp_add_inline_style( 'astore-companion-front', wp_filter_nohtml_kses($custom_css) );

	}
	
	
	public static function replaceStar($str, $start, $length = 0)
{
  $i = 0;
  $star = '';
  if($start >= 0) {
   if($length > 0) {
    $str_len = strlen($str);
    $count = $length;
    if($start >= $str_len) {
     $count = 0;
    }
   }elseif($length < 0){
    $str_len = strlen($str);
    $count = abs($length);
    if($start >= $str_len) {
     $start = $str_len - 1;
    }
    $offset = $start - $count + 1;
    $count = $offset >= 0 ? abs($length) : ($start + 1);
    $start = $offset >= 0 ? $offset : 0;
   }else {
    $str_len = strlen($str);
    $count = $str_len - $start;
   }
  }else {
   if($length > 0) {
    $offset = abs($start);
    $count = $offset >= $length ? $length : $offset;
   }elseif($length < 0){
    $str_len = strlen($str);
    $end = $str_len + $start;
    $offset = abs($start + $length) - 1;
    $start = $str_len - $offset;
    $start = $start >= 0 ? $start : 0;
    $count = $end - $start + 1;
   }else {
    $str_len = strlen($str);
    $count = $str_len + $start + 1;
    $start = 0;
   }
  }
 
  while ($i < $count) {
   $star .= '*';
   $i++;
  }
 
  return substr_replace($str, $star, $start, $count);
}
	/**
	 * Admin menu
	*/
	function plugin_menu() {
		add_menu_page( 'AStore Companion', 'AStore Companion', 'manage_options', 'astore-companion', array($this , 'plugin_options') );
		add_submenu_page(
			'astore-companion', __( 'AStore Template Directory', 'astore-companion' ), __( 'Template Directory', 'astore-companion' ), 'manage_options', 'astore-template',
			array( 'astoreTemplater', 'render_admin_page' )
		);
		
		add_submenu_page(
			'astore-companion', __( 'AStore Theme License', 'astore-companion' ), __( 'AStore Theme License', 'astore-companion' ), 'manage_options', 'astore-license',
			array( 'AStoreCompanion', 'license' )
		);
		add_action( 'admin_init', array(&$this,'register_mysettings') );
	}
	
	/**
	 * Register settings
	*/
	function register_mysettings() {
		register_setting( 'astore-settings-group', 'astore_companion_options', array(&$this,'text_validate') );
	}
	
	static function license(){
		
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		?>
		
        <form method="post" class="astore-license-box" action="<?php echo admin_url('options.php');?>">

		<?php
			settings_fields( 'astore-settings-group' );
			$options     = get_option('astore_companion_options',AStoreCompanion::default_options());
			$astore_companion_options = wp_parse_args($options,AStoreCompanion::default_options());
			
			
		?>
		<div class="wrap">

          <div class="license">
          
          <?php if($astore_companion_options['license_key'] == '' ):?>
		<p><?php _e( 'AStore License Key', 'astore-companion' );?>: <input size="50" name="astore_companion_options[license_key]" value="<?php echo $astore_companion_options['license_key'];?>" type="text" /></p>
		<p></p>
        <?php
		
		else:
		$astore_companion_options['license_key'] = AStoreCompanion::replaceStar($astore_companion_options['license_key'],10,8);
		?>
        <p><?php _e( 'AStore License Key', 'astore-companion' );?>: <input size="50" disabled="disabled" name="astore_companion_options[license_key_hide]" value="<?php echo $astore_companion_options['license_key'];?>" type="text" /><input size="50" type="hidden" name="astore_companion_options[license_key]" value="" type="text" /></p>
		<p></p>
        
        <?php endif;?>
		 
		   </div>
			<p class="submit">
            <?php if($astore_companion_options['license_key'] == '' ):?>
			<input type="submit" class="button-primary" value="<?php _e('Active','astore-companion');?>" />
            <?php	else:?>
            <input type="submit" class="button-primary" value="<?php _e('Deactivate','astore-companion');?>" />
		 <?php endif;?>
			</p>
		</div>
        </form>
		
	<?php	}
	
	/**
	 * Options form
	*/
	function plugin_options() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'astore-companion' ) );
		}
		
		echo '<div class="updated"><h2>'.__( 'About AStore', 'astore-companion' ).'</h2><div class="astore-info-wrap">
	<p>'. __( 'AStore is the best choice for building online store since it\'s fully compatible with WooCommerce, the most popular ecommerce plugin. Using Elementor page builder plugin, you could simply edit your site using just drag &amp; drop.', 'astore-companion' ).'</p>
	</div></div>';
	
	
		
	}
	
	function gridlist_set_default_view() {
				
				$default = apply_filters( 'astore_glt_default','grid' );
				
				?>
					<script>
					jQuery(document).ready(function($) {
						if ($.cookie( 'gridcookie' ) == null) {
					    	$( '.archive .post-wrap ul.products' ).addClass( '<?php echo $default; ?>' );
					    	$( '.gridlist-toggle #<?php echo $default; ?>' ).addClass( 'active' );
					    }
					});
					</script>
				<?php
			}
			
	
	function before_sidebar(){
		global $post;
		
		$postid = isset($post->ID)?$post->ID:0;
		if( is_singular() ){
				
				$before_sidebar = get_post_meta($postid , '_acmb_before_sidebar', true);
				if( $before_sidebar != '' ){
					echo '<div class="astore-before-sidebar">';
					echo wp_kses_post($before_sidebar);
					echo '</div>';
				}
				
		}
		
	}
	
	
	function after_sidebar(){
		global $post;
		
		$postid = isset($post->ID)?$post->ID:0;
		if( is_singular() ){
				
				$after_sidebar = get_post_meta($postid , '_acmb_after_sidebar', true);
				if( $after_sidebar != '' ){
					echo '<div class="astore-after-sidebar">';
					echo wp_kses_post($after_sidebar);
					echo '</div>';
				}
				
		}
		
	}
	
	/**
	 * Get sidebar layout
	*/

	function page_sidebar_layout( $content ){
		
			global $post;
			
			$postid = isset($post->ID)?$post->ID:0;
			if(is_home()){
				$postid = get_option( 'page_for_posts' );
			}
			
			if((is_singular()||is_home()) && $postid>0){
				
				$sidebar_layout = get_post_meta($postid , '_acmb_sidebar', true);
				
				if( $sidebar_layout != '' )
					return $sidebar_layout;
				}
			
			if (is_category()) {
			  $category = get_category(get_query_var('cat'));
			  $cat_id = $category->cat_ID;
			  if($cat_id>0){
					$category_meta = get_term_meta($cat_id);
					$category_meta = isset($category_meta['astore_category_meta'])?unserialize($category_meta['astore_category_meta'][0]):null;
					
					if(isset($category_meta[$cat_id]['_acmb_sidebar'])){
						$sidebar_layout = $category_meta[$cat_id]['_acmb_sidebar'];
						if( $sidebar_layout != '' )
							return $sidebar_layout;
						}
				}
			}
				
				return $content;
			
			}
	
	/**
	 * Set default options
	*/
	
	public static function default_options(){

		$return = array(
			'license_key' => '',

		);
		
		return $return;
		
		}

		
		}
	
	new AStoreCompanion;
}
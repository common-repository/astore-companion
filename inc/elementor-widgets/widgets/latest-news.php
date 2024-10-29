<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AStore_Widget_Latest_News extends Widget_Base {

   public function get_name() {
      return 'astore-latest-news-1';
   }

   public function get_title() {
      return __( 'AStore: Latest News', 'astore-companion' );
   }

   public function get_icon() { 
        return 'eicon-wordpress';
   }

   protected function _register_controls() {

      $this->add_control(
         'section_blog_posts',
         [
            'label' => __( 'Blog Posts', 'astore-companion' ),
            'type' => Controls_Manager::SECTION,
         ]
      );

      $this->add_control(
         'posts_per_page',
         [
            'label' => __( 'Number of Posts', 'astore-companion' ),
            'type' => Controls_Manager::SELECT,
            'default' => 6,
            'section' => 'section_blog_posts',
            'options' => [
               2 => __( '2', 'astore-companion' ),
               3 => __( '3', 'astore-companion' ),
               4 => __( '4', 'astore-companion' ),
               5 => __( '5', 'astore-companion' ),
			   6 => __( '6', 'astore-companion' ),
			   7 => __( '7', 'astore-companion' ),
			   8 => __( '8', 'astore-companion' ),
			   9 => __( '9', 'astore-companion' ),
			   10 => __( '10', 'astore-companion' ),
			   11 => __( '11', 'astore-companion' ),
			   12 => __( '12', 'astore-companion' ),
            ]
         ]
      );
	  
	   $this->add_control(
         'columns',
         [
            'label' => __( 'Columns', 'astore-companion' ),
            'type' => Controls_Manager::SELECT,
            'default' => 3,
            'section' => 'section_blog_posts',
            'options' => [
               2 => __( '2', 'astore-companion' ),
               3 => __( '3', 'astore-companion' ),
               4 => __( '4', 'astore-companion' ),
              
            ]
         ]
      );

   }

   protected function render( $instance = [] ) {

      // get our input from the widget settings.

		$settings = $this->get_settings();
		$post_count = ! empty( $settings['posts_per_page'] ) ? (int)$settings['posts_per_page'] : 6;
		$columns  = ! empty( $settings['columns'] ) ? $settings['columns'] : '3';
		$class_column = 'col-md-4'; 
		
		switch( $columns ){
			case "1":
			$class_column = 'col-md-12';
			break;
			case "2":
			$class_column = 'col-md-6';
			break;
			case "3":
			$class_column = 'col-md-4';
			break;
			case "4":
			$class_column = 'col-md-3';
			break;
		}
		if( !is_numeric($columns) || $columns<=0 )
		$columns = 3;

      ?>


<div class="astore-section">
      <div class="astore-section-content">
          <div class="astore-section-container">
              <div class="row">
              
<?php 
// the query
$args = array(
  'post_type'=>'post',
  'posts_per_page' => $post_count,
  'ignore_sticky_posts' => 1,
  'post_status' => array( 'publish' ),
);
$the_query = new \WP_Query( $args ); ?>

<?php if ( $the_query->have_posts() ) : ?>

	<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		
        <div class="<?php echo $class_column;?>">
                      <div class="entry-box">
                          <?php
						  if( has_post_thumbnail() ){
							  $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
							  ?>
                              <a href="<?php the_permalink();?>"><img src="<?php echo $featured_img_url;?>" alt="<?php the_title(); ?>" style="width: 100px;float:left;margin-right: 20px;"></a>
                              <?php
						  }
						  ?>
                          <h3 class="entry-title" style="clear:none;font-size: 18px;"><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h3>
                          <div class="entry-description"><?php the_excerpt(); ?></div>
                      </div>
                  </div>
        
	<?php endwhile; ?>

	<?php wp_reset_postdata(); ?>

<?php endif; ?>
                  
              </div>
          </div>
      </div>
  </div>

<?php

   }

   protected function content_template() {}

   public function render_plain_content( $instance = [] ) {}

}
Plugin::instance()->widgets_manager->register_widget_type( new AStore_Widget_Latest_News );

<?php

$preview_url = add_query_arg( 'astore_templates', '', home_url() );

$html = '';

if ( is_array( $templates_array ) ) {
	$html .= '<div class="astore-template-dir wrap">';
	$html .= '<h1 class="wp-heading-inline">' . __( 'AStore Template Directory', 'astore-companion' ) . '</h1>';
	$html .= '<div class="astore-template-browser">';

	foreach ( $templates_array as $template => $properties ) {
		$html .= '<div class="astore-template">';
		$html .= '<div class="more-details astore-preview-template" data-demo-url="' . esc_url( $properties['demo_url'] ) . '" data-template-slug="' . esc_attr( $template ) . '" ><span>' . __( 'More Details', 'astore-companion' ) . '</span></div>';
		$html .= '<div class="astore-template-screenshot">';
		$html .= '<img src="' . esc_url( $properties['screenshot'] ) . '" alt="' . esc_html( $properties['title'] ) . '" >';
		$html .= '</div>'; // .astore-template-screenshot
		$html .= '<h2 class="template-name template-header">' . esc_html( $properties['title'] ) . (isset($properties['pro'])? apply_filters('astore_after_template_title','<span class="pro-template">Pro</span>'):'').'</h2>';
		$html .= '<div class="astore-template-actions">';

		if ( ! empty( $properties['demo_url'] ) ) {
			$html .= '<a class="button astore-preview-template" data-demo-url="' . esc_url( $properties['demo_url'] ) . '" data-template-slug="' . esc_attr( $template ) . '" >' . __( 'Preview', 'astore-companion' ) . '</a>';
		}
		$html .= '</div>'; // .astore-template-actions
		$html .= '</div>'; // .astore-template
	}
	$html .= '</div>'; // .astore-template-browser
	$html .= '</div>'; // .astore-template-dir
	$html .= '<div class="wp-clearfix clearfix"></div>';
}// End if().

echo $html;
?>

<div class="astore-template-preview theme-install-overlay wp-full-overlay expanded" style="display: none;">
	<div class="wp-full-overlay-sidebar">
		<div class="wp-full-overlay-header">
			<button class="close-full-overlay"><span class="screen-reader-text"><?php _e( 'Close', 'astore-companion' );?></span></button>
			<div class="astore-next-prev">
				<button class="previous-theme"><span class="screen-reader-text"><?php _e( 'Previous', 'astore-companion' );?></span></button>
				<button class="next-theme"><span class="screen-reader-text"><?php _e( 'Next', 'astore-companion' );?></span></button>
			</div>
            
			<span class="astore-import-template button button-primary"><?php _e( 'Import', 'astore-companion' );?></span>
           
            <a target="_blank" class="cc-buy-now" href="<?php echo esc_url('https://velathemes.com/astore-pro-theme/');?>"><span class="button orange"><?php _e( 'Buy Now', 'astore-companion' );?></span></a>
            
		</div>
		<div class="wp-full-overlay-sidebar-content">
			<?php
			foreach ( $templates_array as $template => $properties ) {
			?>
				<div class="install-theme-info astore-theme-info <?php echo esc_attr( $template ); ?>"
					 data-demo-url="<?php echo esc_url( $properties['demo_url'] ); ?>"
					 data-template-file="<?php echo esc_url( $properties['import_file'] ); ?>"
					 data-template-title="<?php echo esc_attr( $properties['title'] ); ?>" 
                     data-template-slug="<?php echo esc_attr( $template ); ?>">
					<h3 class="theme-name"><?php echo esc_attr( $properties['title'] ); ?></h3>
					<img class="theme-screenshot" src="<?php echo esc_url( $properties['screenshot'] ); ?>" alt="<?php echo esc_attr( $properties['title'] ); ?>">
					<div class="theme-details">
						<?php
						 	echo wp_kses_post( $properties['description'] );
						 ?>
					</div>
					<?php
					if ( ! empty( $properties['required_plugins'] ) && is_array( $properties['required_plugins'] ) ) {
					?>
					<div class="astore-required-plugins">
						<p><?php _e( 'Required Plugins', 'astore-companion' );?></p>
						<?php
						foreach ( $properties['required_plugins'] as $plugin_slug => $details ) {
							$file_name = isset($details['file'])?$details['file']:'';
							
							if ( astoreTemplater::check_plugin_state( $plugin_slug,$file_name ) === 'install' ) {
								echo '<div class="astore-installable plugin-card-' . esc_attr( $plugin_slug ) . '">';
								echo '<span class="dashicons dashicons-no-alt"></span>';
								echo $details['title'];
								echo astoreTemplater::get_button_html( $plugin_slug,$file_name );
								echo '</div>';
							} elseif ( astoreTemplater::check_plugin_state( $plugin_slug,$file_name ) === 'activate' ) {
								echo '<div class="astore-activate plugin-card-' . esc_attr( $plugin_slug ) . '">';
								echo '<span class="dashicons dashicons-admin-plugins" style="color: #ffb227;"></span>';
								echo $details['title'];
								echo astoreTemplater::get_button_html( $plugin_slug,$file_name );
								echo '</div>';
							} else {
								echo '<div class="astore-installed plugin-card-' . esc_attr( $plugin_slug ) . '">';
								echo '<span class="dashicons dashicons-yes" style="color: #34a85e"></span>';
								echo $details['title'];
								echo '</div>';
							}
						}
						?>
					</div>
					<?php
					}
					?>
				</div><!-- /.install-theme-info -->
			<?php } ?>
		</div>

		<div class="wp-full-overlay-footer">
			<button type="button" class="collapse-sidebar button" aria-expanded="true" aria-label="Collapse Sidebar">
				<span class="collapse-sidebar-arrow"></span>
				<span class="collapse-sidebar-label"><?php _e( 'Collapse', 'astore-companion' ); ?></span>
			</button>
			<div class="devices-wrapper">
				<div class="devices astore-responsive-preview">
					<button type="button" class="preview-desktop active" aria-pressed="true" data-device="desktop">
						<span class="screen-reader-text"><?php _e( 'Enter desktop preview mode', 'astore-companion' ); ?></span>
					</button>
					<button type="button" class="preview-tablet" aria-pressed="false" data-device="tablet">
						<span class="screen-reader-text"><?php _e( 'Enter tablet preview mode', 'astore-companion' ); ?></span>
					</button>
					<button type="button" class="preview-mobile" aria-pressed="false" data-device="mobile">
						<span class="screen-reader-text"><?php _e( 'Enter mobile preview mode', 'astore-companion' ); ?></span>
					</button>
				</div>
			</div>

		</div>
	</div>
	<div class="wp-full-overlay-main astore-main-preview">
		<iframe src="" title="Preview" class="astore-template-frame"></iframe>
	</div>
</div>

(function( $ ) {
	
	// Add Color Picker to all inputs that have 'color-field' class
    $(function() {
        $('.wp-color-picker').wpColorPicker();
    });
	
	// Handle sidebar collapse in preview.
	$( '.astore-template-preview' ).on(
		'click', '.collapse-sidebar', function () {
			event.preventDefault();
			var overlay = $( '.astore-template-preview' );
			if ( overlay.hasClass( 'expanded' ) ) {
				overlay.removeClass( 'expanded' );
				overlay.addClass( 'collapsed' );
				return false;
			}

			if ( overlay.hasClass( 'collapsed' ) ) {
				overlay.removeClass( 'collapsed' );
				overlay.addClass( 'expanded' );
				return false;
			}
		}
	);

	// Handle responsive buttons.
	$( '.astore-responsive-preview' ).on(
		'click', 'button', function () {
			$( '.astore-template-preview' ).removeClass( 'preview-mobile preview-tablet preview-desktop' );
			var deviceClass = 'preview-' + $( this ).data( 'device' );
			$( '.astore-responsive-preview button' ).each(
				function () {
					$( this ).attr( 'aria-pressed', 'false' );
					$( this ).removeClass( 'active' );
				}
			);

			$( '.astore-responsive-preview' ).removeClass( $( this ).attr( 'class' ).split( ' ' ).pop() );
			$( '.astore-template-preview' ).addClass( deviceClass );
			$( this ).addClass( 'active' );
		}
	);

	// Hide preview.
	$( '.close-full-overlay' ).on(
		'click', function () {
			$( '.astore-template-preview .astore-theme-info.active' ).removeClass( 'active' );
			$( '.astore-template-preview' ).hide();
			$( '.astore-template-frame' ).attr( 'src', '' );
			$('body.astore-companion_page_astore-template').css({'overflow-y':'auto'});
		}
	);
			
	// Open preview routine.
	$( '.astore-preview-template' ).on(
		'click', function () {
			var templateSlug = $( this ).data( 'template-slug' );
			var previewUrl = $( this ).data( 'demo-url' );
			$( '.astore-template-frame' ).attr( 'src', previewUrl );
			$( '.astore-theme-info.' + templateSlug ).addClass( 'active' );
			setupImportButton();
			$( '.astore-template-preview' ).fadeIn();
			$('body.astore-companion_page_astore-template').css({'overflow-y':'hidden'});
		}
	);
	
	$( '.astore-next-prev .next-theme' ).on(
				'click', function () {
					var active = $( '.astore-theme-info.active' ).removeClass( 'active' );
					if ( active.next() && active.next().length ) {
						active.next().addClass( 'active' );
					} else {
						active.siblings( ':first' ).addClass( 'active' );
					}
					changePreviewSource();
					setupImportButton();
				}
			);
			$( '.astore-next-prev .previous-theme' ).on(
				'click', function () {
					var active = $( '.astore-theme-info.active' ).removeClass( 'active' );
					if ( active.prev() && active.prev().length ) {
						active.prev().addClass( 'active' );
					} else {
						active.siblings( ':last' ).addClass( 'active' );
					}
					changePreviewSource();
					setupImportButton();
				}
			);

			// Change preview source.
			function changePreviewSource() {
				var previewUrl = $( '.astore-theme-info.active' ).data( 'demo-url' );
				$( '.astore-template-frame' ).attr( 'src', previewUrl );
			}
	
	function setupImportButton() {
		var installable = $( '.active .astore-installable' );
		if ( installable.length > 0 ) {
			$( '.wp-full-overlay-header .astore-import-template' ).text( astore_companion_admin.i18n.t1 );
		} else {
			$( '.wp-full-overlay-header .astore-import-template' ).text( astore_companion_admin.i18n.t2 );
		}
		var activeTheme = $( '.astore-theme-info.active' );
		var button = $( '.wp-full-overlay-header .astore-import-template' );
		$( button ).attr( 'data-template-file', $( activeTheme ).data( 'template-file' ) );
		$( button ).attr( 'data-template-title', $( activeTheme ).data( 'template-title' ) );
		$( button ).attr( 'data-template-slug', $( activeTheme ).data( 'template-slug' ) );
		
		if($( activeTheme ).data( 'template-file' ) == '' ){
				$('.cc-buy-now').show();
				$('.astore-import-template').hide();
			}else{
				$('.cc-buy-now').hide();
				$('.astore-import-template').show();
				}
	}
	
	
	// Handle import click.
	$( '.wp-full-overlay-header' ).on(
		'click', '.astore-import-template', function () {
			$( this ).addClass( 'astore-import-queue updating-message astore-updating' ).html( '' );
			$( '.astore-template-preview .close-full-overlay, .astore-next-prev' ).remove();
			var template_url = $( this ).data( 'template-file' );
			var template_name = $( this ).data( 'template-title' );
			var template_slug = $( this ).data( 'template-slug' );
			
			if ( $( '.active .astore-installable' ).length || $( '.active .astore-activate' ).length ) {

				checkAndInstallPlugins();
			} else {
				$.ajax(
					{
						url: astore_companion_admin.ajaxurl,
						beforeSend: function ( xhr ) {
							$( '.astore-import-queue' ).addClass( 'astore-updating' ).html( '' );
							xhr.setRequestHeader( 'X-WP-Nonce', astore_companion_admin.nonce );
						},
						// async: false,
						data: {
							template_url: template_url,
							template_name: template_name,
							template_slug: template_slug,
							action: 'astore_import_elementor'
						},
						type: 'POST',
						success: function ( data ) {
							$( '.astore-updating' ).replaceWith( '<span class="astore-done-import"><i class="dashicons-yes dashicons"></i></span>' );
							var obj = $.parseJSON( data );
							
							location.href = obj.redirect_url;
						},
						error: function ( error ) {
							console.error( error );
						},
						complete: function() {
							$( '.astore-updating' ).replaceWith( '<span class="astore-done-import"><i class="dashicons-yes dashicons"></i></span>' );
						}
					}, 'json'
				);
			}
		}
	);

	function checkAndInstallPlugins() {
		var installable = $( '.active .astore-installable' );
		var toActivate = $( '.active .astore-activate' );
		if ( installable.length || toActivate.length ) {

			$( installable ).each(
				function () {
					var plugin = $( this );
					$( plugin ).removeClass( 'astore-installable' ).addClass( 'astore-installing' );
					$( plugin ).find( 'span.dashicons' ).replaceWith( '<span class="dashicons dashicons-update" style="-webkit-animation: rotation 2s infinite linear; animation: rotation 2s infinite linear; color: #ffb227 "></span>' );
					var slug = $( this ).find( '.astore-install-plugin' ).attr( 'data-slug' );
					wp.updates.installPlugin(
						{
							slug: slug,
							success: function ( response ) {
								activatePlugin( response.activateUrl, plugin );
							}
						}
					);
				}
			);

			$( toActivate ).each(
				function () {
						var plugin = $( this );
						var activateUrl = $( plugin ).find( '.activate-now' ).attr( 'href' );
					if (typeof activateUrl !== 'undefined') {
						activatePlugin( activateUrl, plugin );
					}
				}
			);
		}
	}

	function activatePlugin( activationUrl, plugin ) {
		$.ajax(
			{
				type: 'GET',
				url: activationUrl,
				beforeSend: function() {
					$( plugin ).removeClass( 'astore-activate' ).addClass( 'astore-installing' );
					$( plugin ).find( 'span.dashicons' ).replaceWith( '<span class="dashicons dashicons-update" style="-webkit-animation: rotation 2s infinite linear; animation: rotation 2s infinite linear; color: #ffb227 "></span>' );
				},
				success: function () {
					$( plugin ).find( '.dashicons' ).replaceWith( '<span class="dashicons dashicons-yes" style="color: #34a85e"></span>' );
					$( plugin ).removeClass( 'astore-installing' );
				},
				complete: function() {
					if ( $( '.active .astore-installing' ).length === 0 ) {
						$( '.astore-import-queue' ).trigger( 'click' );
					}
				}
			}
		);
	}
     
})( jQuery );
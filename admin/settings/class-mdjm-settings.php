<?php
/*
 * class-mdjm-settings.php
 * 03/06/2015
 * @since 1.2.1
 * The MDJM Settings class
 */
	defined( 'ABSPATH' ) or die( 'Direct access to this page is disabled!!!' );
	
	/* -- Build the MDJM Settings class -- */
	if( !class_exists( 'MDJM_Settings' ) )	{
		class MDJM_Settings	{
			
			function __construct()	{
				$this->settings_register();
				
				add_action( 'contextual_help', array( &$this, 'help_text' ), 10, 3 ); // Contextual help
				
			} // __construct
			
			/*
			 * Register the MDJM settings
			 *
			 *
			 *
			 */
			function settings_register()	{	
				global $mdjm_settings, $mdjm_debug;
							
				/* -- Get the array of settings -- */
				include( 'settings.php' );
				
				if( !class_exists( 'MDJM_PG' ) )
					unset( $all_settings['payment_gateway'] );
				
				$this->sections = apply_filters( 'mdjm_addon_sections', $all_sections );
				
				$this->settings = apply_filters( 'mdjm_addon_settings', $all_settings );
								
				$this->add_sections();
				$this->add_fields();
								
				/* -- Register the settings -- */
				register_setting( 'mdjm-settings', MDJM_SETTINGS_KEY );
				register_setting( 'mdjm-permissions', MDJM_PERMISSIONS_KEY );
				register_setting( 'mdjm-debugging-files', MDJM_DEBUG_SETTINGS_KEY );
				register_setting( 'mdjm-events', MDJM_EVENT_SETTINGS_KEY );
				register_setting( 'mdjm-playlists', MDJM_PLAYLIST_SETTINGS_KEY );
				register_setting( 'mdjm-email', MDJM_EMAIL_SETTINGS_KEY );
				register_setting( 'mdjm-email-templates', MDJM_TEMPLATES_SETTINGS_KEY );
				register_setting( 'mdjm-clientzone', MDJM_CLIENTZONE_SETTINGS_KEY );
				register_setting( 'mdjm-pages', MDJM_PAGES_KEY );
				register_setting( 'mdjm-availability', MDJM_AVAILABILITY_SETTINGS_KEY );
				register_setting( 'mdjm-client-text', MDJM_CUSTOM_TEXT_KEY );
				register_setting( 'mdjm-payments', MDJM_PAYMENTS_KEY );
				register_setting( 'mdjm-uninstall', MDJM_UNINST_SETTINGS_KEY );
				register_setting( 'mdjm-addons', MDJM_API_SETTINGS_KEY );
				
				do_action( 'register_mdjm_premium_settings' ); // Allows MDJM PG Settings to be registered
			} // settings_register
			
			/*
			 * Validate the settings field entries
			 *
			 * @param	arr		$input		array of all option values on the page
			 *
			 */
			function settings_validate( $input )	{
				$output = $input;
				
				print_r( $input );
				exit;
				
				foreach( $input as $key => $value )	{
					if( $input == 'system_email' )	{
						if( !is_email( $input ) )	{
							add_settings_error( $key, esc_attr( 'settings_updated' ), 'Not an email address', 'error' );	
						}
					}
				}
				
				return $output;
			} // settings_validate
			
			/*
			 * Add the settings sections
			 *
			 *
			 *
			 */
			function add_sections()	{
				if( empty( $this->sections ) )
					return;
					
				foreach( $this->sections as $name => $args )	{
					add_settings_section( $name, $args['title'], array( &$this, 'section_content' ), $args['page']  );	
				}
			} // add_sections
			
			/*
			 * Add section content if required
			 *
			 *
			 */
			function section_content( $args )	{
				if( $args['id'] == 'mdjm_debugging_settings' )	{
					echo '<p>' . __( 'The settings below enable the MDJM support team to identify any problems ' . 
						'you may be experiencing.', 'mobile-dj-manager' ) . '</p>';
					
					echo '<p>' . sprintf( __( 'With debugging enabled, much of the activity that is executed as you browse around ' . 
						'pages and utilise features within the MDJM application and the %s is logged ' . 
						'and this can lead to slightly slower load times for your pages. It is therefore recommended ' . 
						'that you leave debugging turned off unless you are experiencing an issue and the MDJM support ' . 
						'team have asked you to enable this setting to aid in identifying the problem.', 'mobile-dj-manager' ), 
						MDJM_APP ) . '</p>' . "\r\n";
				}
					
				if( $args['id'] == 'mdjm_debugging_files_settings' )
					echo '<p>' . __( 'The following settings only apply if debugging is enabled', 'mobile-dj-manager' ) . '</p>' . "\r\n";
					
				if( $args['id'] == 'mdjm_addon_settings' )
					echo '<p>' . __( 'These settings are important to ensure that your MDJM Premium plugins continue to be updated and remain enabled', 'mobile-dj-manager' ) . '</p>' . "\r\n";
			} // section_content
			
			/*
			 * Add the settings field
			 *
			 *
			 *
			 */
			function add_fields()	{
				if( empty( $this->settings ) )
					return;
									
				foreach( $this->settings as $setting => $options )	{
					add_settings_field(
									$setting, // The name of the setting
									$options['label'], // The field label
									array( &$this, 'display_field' ), // The content
									'mdjm-' . $options['page'], // Which settings page to display on
									'mdjm_' . $options['section'] . '_settings', // Which section on the page
									array(  // Additional args
										'field' => $setting,
										'label_for' => $setting,
										'key' => ( !empty( $options['key'] ) ? $options['key'] : '' ),
										'type' => $options['type'],
										'class' => ( !empty( $options['class'] ) ? $options['class'] : '' ),
										'value' => $options['value'],
										'text' => ( !empty( $options['text'] ) ? $options['text'] : '' ),
										'desc' => ( !empty( $options['desc'] ) ? $options['desc'] : '' ),
										'size' => ( !empty( $options['size'] ) ? $options['size'] : '' ),
										'readonly' => ( !empty( $options['readonly'] ) ? true : false ),
										'custom_args' => ( !empty( $options['custom_args'] ) ? $options['custom_args'] : '' ),
									) );
				}
					
			} // add_fields
			
			/*
			 * Determine the type of field to display and then call the
			 * appropriate method to display it
			 *
			 *
			 */
			function display_field( $args )	{
				switch( $args['type'] )	{
					/* -- Checkbox Field -- */
					case 'checkbox':
						$this->show_checkbox_field( $args );
					break;
					
					/* -- Radio Field -- */
					case 'radio':
						$this->show_radio_field( $args );
					break;
					
					/* -- Custom Dropdown Field -- */
					case 'custom_dropdown':
						$this->show_select_field( $args );
					break;
					
					/* -- Milti Select Field -- */
					case 'multiple_select':
						$this->show_select_field( $args );
					break;
					
					/* -- Text Field -- */
					case 'text':
						$this->show_text_field( $args );
					break;
					/* -- Password Field -- */
					case 'password':
						$this->show_text_field( $args );
					break;
					/* -- Email Field -- */
					case 'email':
						$this->show_text_field( $args );
					break;
					/* -- Textarea Field -- */
					case 'textarea':
						$this->show_textarea_field( $args );
					break;
					/* -- Textarea TinyMCE field -- */
					case 'mce_textarea':
						$this->show_mce_textarea_field( $args );
					break;
				} // Switch
					
				echo ( !empty( $args['text'] ) ? __( $args['text'] ) : '' ) . 
				( !empty( $args['desc'] ) ? '<p class="description">' . __( $args['desc'] ) . '</p>' : '' ) . "\r\n";
					
			} // display_field
					
			/*
			 * Define & declare the current location (tab/section)
			 *
			 *
			 *
			 */
			function set_loc()	{
				$this->current_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'general' );
				
				switch( $this->current_tab )	{
					case 'general':
						$this->current_section = ( isset( $_GET['section'] ) ? $_GET['section'] : 'mdjm_app_settings' );
					break;
					case 'events':
						$this->current_section = ( isset( $_GET['section'] ) ? $_GET['section'] : 'mdjm_events_settings' );
					break;
					case 'emails':
						$this->current_section = ( isset( $_GET['section'] ) ? $_GET['section'] : 'mdjm_email_settings' );
					break;
					case 'client-zone':
						$this->current_section = ( isset( $_GET['section'] ) ? $_GET['section'] : 'mdjm_app_general' );
					break;
					case 'payments':
						$this->current_section = ( isset( $_GET['section'] ) ? $_GET['section'] : 'mdjm_payment_settings' );
					break;
					case 'addons':
						$this->current_section = ( isset( $_GET['section'] ) ? $_GET['section'] : 'mdjm_addon_settings' );
					break;
				} // switch
			} // set_loc
			
			/*
			 * Start the page HTML and display the navigation tabs and links
			 *
			 * @param
			 *
			 */
			function page_header()	{
				
				$this->set_loc();
				
				echo '<div class="wrap">' . "\r\n" . 
				'<div id="icon-themes" class="icon32"></div>' . "\r\n";
				
				settings_errors(); // Prints the saved and error messages
				
				echo '<h2 class="nav-tab-wrapper">' . "\r\n";
				 
				echo '<a href="' . $this->tab_url( 'general' ) . '" class="nav-tab' . 
					$this->active_tab( 'general' ) . '">' . __( 'General', 'mobile-dj-manager' ) . '</a>' . "\r\n";
				
				echo '<a href="' . $this->tab_url( 'events' ) . '" class="nav-tab' . 
					$this->active_tab( 'events' ) . '">' . __( 'Events', 'mobile-dj-manager' ) . '</a>' . "\r\n"; 
					
				echo '<a href="' . $this->tab_url( 'emails' ) . '" class="nav-tab' . 
					$this->active_tab( 'emails' ) . '">' . sprintf( __( 'Email %s Template Settings', 'mobile-dj-manager' ), '&amp;' ) . '</a>' . "\r\n";
					
				echo '<a href="' . $this->tab_url( 'client-zone' ) . '" class="nav-tab' . 
					$this->active_tab( 'client-zone' ) . '">' . MDJM_APP . '</a>' . "\r\n";
					
				echo '<a href="' . $this->tab_url( 'payments' ) . '" class="nav-tab' . 
					$this->active_tab( 'payments' ) . '">' . __( 'Payment Settings', 'mobile-dj-manager' ) . '</a>' . "\r\n";
					
				echo '<a href="' . $this->tab_url( 'addons' ) . '" class="nav-tab' . 
					$this->active_tab( 'addons' ) . '">' . __( 'Premium Addons', 'mobile-dj-manager' ) . '</a>' . "\r\n";
										
				echo '</h2>' . "\r\n";
				
				$this->print_nav_links();
				
				$this->exclude = array( 'client_text', 'mdjm_client_field_settings', 'mdjm_db_backups' );
				
				if( !in_array( $this->current_section, $this->exclude ) )
					echo '<form method="post" action="options.php">' . "\r\n";
					
			} // page_header
			
			/*
			 * End the page
			 *
			 *
			 *
			 */
			function page_footer()	{
				global $mdjm, $mdjm_debug;
				
				/* -- Don't display the save button for aitomated tasks page -- */
				if( isset( $_GET['task_action'] ) )
					return;
				
				if( !in_array( $this->current_section, $this->exclude ) )	{	
					if( current_user_can( 'manage_options' ) )
						submit_button();
											
					echo '</form>' . "\r\n";
				}
				
				/* -- This is where we can display any additional fields. Will not be saved as options -- */
				if( $this->current_section == 'mdjm_app_debugging' )
					$mdjm_debug->submit_files_button();
				
				if( $this->current_section == 'mdjm_addon_settings' )
					$this->mdjm_premium_addons();
			
				/* -- End the wrap div -- */
				echo '</div>' . "\r\n";
			} // page_footer
			
			/*
			 * Determine if the given tab is active
			 * if so, echo the CSS style
			 *
			 * @param	str		$tab	Required: The tab to query
			 */
			function active_tab( $tab )	{
				if( $tab == $this->current_tab )
					return ' nav-tab-active';
			} // active_tab
			
			/*
			 * Determine the link for the given tab
			 * 
			 *
			 * @param	str		$tab	Required: The tab to query
			 */
			function tab_url( $tab, $section='' )	{
				$section = !empty( $section ) ? '&section=' . $section : '';
				
				return admin_url( 'admin.php?page=mdjm-settings&tab=' . $tab . $section );
			} // tab_url
			
			/*
			 * Print out the section links within each tab
			 *
			 *
			 *
			 */
			function print_nav_links()	{				
				$links = array(
							'general'		=> array(
												__( 'Application Settings', 'mobile-dj-manager' )   => 'mdjm_app_settings',
												__( 'Permissions', 'mobile-dj-manager' )			=> 'mdjm_app_permissions',
												__( 'Debugging', 'mobile-dj-manager' )			  => 'mdjm_app_debugging',
												__( 'Backups', 'mobile-dj-manager' )				=> 'mdjm_db_backups',
												__( 'Plugin Removal', 'mobile-dj-manager' )		 => 'mdjm_app_uninstall',
												),
							'events'		=> array(
												__( 'Event Settings', 'mobile-dj-manager' ) 		=> 'mdjm_events_settings',
												__( 'Playlist Settings', 'mobile-dj-manager' )	 => 'mdjm_playlist_settings',
												//__( 'Event Staff', 'mobile-dj-manager' )		   => 'mdjm_event_staff',
												),
							'emails'		=> array(
												__( 'General Email Settings', 'mobile-dj-manager' )	=> 'mdjm_email_settings',
												__( 'Event Templates', 'mobile-dj-manager' )		   => 'mdjm_email_templates_settings',
												
												),
							'client-zone'	=> array(
													MDJM_APP . ' ' . __( 'General Settings', 'mobile-dj-manager' ) => 'mdjm_app_general',
													__( 'Pages', 'mobile-dj-manager' )						=> 'mdjm_app_pages',
													__( 'Customised Text', 'mobile-dj-manager' )			  => 'mdjm_app_text',
													__( 'Client Fields', 'mobile-dj-manager' )				=> 'mdjm_client_field_settings',
													__( 'Availability Checker', 'mobile-dj-manager' )		 => 'mdjm_availability_settings',
													),
							'payments'	=> array(
													__( 'Payment Settings', 'mobile-dj-manager' ) 		=> 'mdjm_payment_settings' ),
							'addons'	=> array(
													__( 'Premium Addons', 'mobile-dj-manager' ) 		=> 'mdjm_addon_settings' )
							);
							
				// Run the filter for the MDJM Add ons to enable the settings links
				$links = apply_filters( 'mdjm_settings_links', $links );
							
				if( !array_key_exists( $this->current_tab, $links ) )
					return;
					
				echo '<ul class="subsubsub">' . "\r\n"; 
				
				$sections = count( $links[$this->current_tab] );
				$i = 1;
				
				$payment_gw = array( 'mdjm_paypal_settings', 'mdjm_payfast_settings' );
								
				foreach( $links[$this->current_tab] as $name => $slug )	{
					
					if( in_array( $slug, $payment_gw ) )	{
						if( MDJM_PAYMENTS == false || !class_exists( 'MDJM_PG' ) )
							continue;
							
						$sections = $sections - count( $payment_gw );

						if( MDJM_PAYMENT_GW != substr( $slug, 5, strlen( MDJM_PAYMENT_GW ) ) )
							continue;
							
						$sections = $sections - 1;
					}
					
					echo '<li><a href="' . $this->tab_url( $this->current_tab, $slug ) . '"' . 
					( $slug == $this->current_section ? ' class="current"' : '' ) . 
					'>' . $name . '</a>' . ( $i < $sections ? ' | ' : '' ) . '</li>' . "\r\n";
					
					$i++;
				}
				echo '</ul>' . "\r\n";
				echo '<br class="clear">' . "\r\n";
			} // tab_links
			
/* ---------------------------------------------------------
		This is where we display the settings fields
--------------------------------------------------------- */
			/*
			 * Display the setting field as a hidden input
			 * These are used to hide settings that should not be displayed
			 * witihn the select nav link, but share the same option key
			 * to maintain their values
			 */
			function show_hidden_field( $args )	{
				echo '<input type="hidden" name="' . ( !empty( $args['key'] ) ? $args['key'] . '[' . $args['field'] . ']' 
					: $args['field'] ) . '" id="' . $args['field'] . '" ' . 
				'value="' . $args['value'] . '" /> ' . "\r\n";
			} // show_hidden_field
			
			/*
			 * Display the setting field as a text input.
			 * Also applies for password fields
			 *
			 *
			 */
			function show_text_field( $args )	{
				echo '<input type="' . $args['type'] . '" name="' . ( !empty( $args['key'] ) ? $args['key'] . '[' . $args['field'] . ']' 
					: $args['field'] ) . '" id="' . $args['field'] . '" ' . 
				( !empty( $args['class'] ) ? 'class="' . $args['class'] . '" ' : '' ) . 
				'value="' . esc_attr( $args['value'] ) . '"' .
				( !empty( $args['readonly'] ) ? ' readonly="readonly"' : '' ) . ' /> ' . "\r\n";
			} // show_text_field
			
			/*
			 * Display the setting field as a select input
			 *
			 *
			 *
			 */
			function show_select_field( $args )	{
				global $mdjm_settings;
				
				if( $args['custom_args']['list_type'] == 'page' ) // Pages
					wp_dropdown_pages( $args['custom_args'] );
										
				else	{
					echo '<select name="' . ( !empty( $args['key'] ) ? $args['key'] . '[' . $args['field'] . ']' 
					: $args['field'] ) . ( $args['type'] == 'multiple_select' ? '[]' : '' ) . '" id="' . $args['field'] . '"' . 
					( !empty( $args['class'] ) ? ' class="' . $args['class'] . '"' : '' ) . 
					( $args['type'] == 'multiple_select' ? ' multiple="multiple"' : '' ) . 
					( !empty( $args['size'] ) ? ' size="' . $args['size'] . '"' : '' ) . 
					'>' . "\r\n";
					
					/* -- Select list with values passed via array -- */
					if( $args['custom_args']['list_type'] == 'defined' )	{
						foreach( $args['custom_args']['list_values'] as $key => $value )	{
							echo '<option value="' . $key . '"' . 
							selected( $args['value'], $key, false ) . 
							'>' . $value . '</option>' . "\n";
						}	
					}
					
					/* -- Shortcode Select List -- */
					elseif( $args['custom_args']['list_type'] == 'shortcode' )	{
						foreach( $args['custom_args']['list_values'] as $shortcode )	{
							echo '<option value="' . $shortcode . '"' . 
							( !empty( $mdjm_settings['permissions']['dj_disable_shortcode'] ) && 
								in_array( $shortcode, $mdjm_settings['permissions']['dj_disable_shortcode'] ) ? ' selected="selected"' : '' ) . 
							'>' . $shortcode . '</option>' . "\r\n";
						}
					}
					
					/* -- Contract Select List -- */
					elseif( $args['custom_args']['list_type'] == 'contract' )	{										
						$template_args = array(
										'post_type' 	  => MDJM_CONTRACT_POSTS,
										'posts_per_page' => -1,
										'orderby' 		=> 'name',
										'order' 		  => $args['custom_args']['sort_order'],
										);
						$templates = get_posts( $template_args );
						if( $templates )	{							
							foreach( $templates as $template )	{
								echo '<option value="' . $template->ID . '"' . 
								selected( $args['value'], $template->ID, false ) . 
								'>' . get_the_title( $template->ID ) . '</option>' . "\n";
							}
						}
					}
					
					/* -- Email Template Select List -- */
					elseif( $args['custom_args']['list_type'] == 'email_template' )	{										
						$template_args = array(
										'post_type' 	  => MDJM_EMAIL_POSTS,
										'posts_per_page' => -1,
										'orderby' 		=> 'name',
										'order' 		  => $args['custom_args']['sort_order'],
										);
						$templates = get_posts( $template_args );
						if( !empty( $args['custom_args']['first_entry'] ) )	{
							echo '<option value="' . $args['custom_args']['first_entry'][0] . '"' . 
							selected( $args['value'], '0', false ) . 
							'>' . $args['custom_args']['first_entry'][1] . '</option>' . "\r\n";
						}
						if( $templates )	{							
							foreach( $templates as $template )	{
								echo '<option value="' . $template->ID . '"' . 
								selected( $args['value'], $template->ID, false ) . 
								'>' . get_the_title( $template->ID ) . '</option>' . "\n";
							}
						}
					}
					
					/* -- Template Select List -- */
					elseif( $args['custom_args']['list_type'] == 'templates' )	{
						$template_types = array(
										MDJM_EMAIL_POSTS	=> 'EMAIL TEMPLATES',
										MDJM_CONTRACT_POSTS => 'CONTRACT TEMPLATES',
										);
										
						foreach( $template_types as $template_type => $template_name )	{
							$template_args = array(
											'post_type' 	  => $template_type,
											'posts_per_page' => -1,
											'orderby' 		=> 'name',
											'order' 		  => 'ASC',
											);
							$templates = get_posts( $template_args );
							if( $templates )	{
								echo '<option value="--- ' . $template_type . ' ---" disabled>--- ' . $template_name . ' ---</option>' . "\r\n";
								
								foreach( $templates as $template )	{
									echo '<option value="' . $template->ID . '"';
									if( !empty( $mdjm_settings['permissions']['dj_disable_template'] ) && 
										in_array( $template->ID, $mdjm_settings['permissions']['dj_disable_template'] ) )
										echo ' selected="selected"';
									echo '>' . get_the_title( $template->ID ) . '</option>' . "\n";
								}
							}
						}
					}
					
					echo '</select>' . "\r\n";
				}
			} // show_select_field
			
			/*
			 * Display the setting field as a checkbox input
			 *
			 *
			 *
			 */
			function show_checkbox_field( $args )	{
				global $mdjm;
								
				$true_vals = array(
								'show_dashboard', 'show_credits', 'enable', 'warn', 'auto_purge', 'employer', 
								'enable_packages', 'warn_unattended', 'journaling', 'track_client_emails', 'bcc_dj_to_client', 
								'bcc_admin_to_client', 'contract_to_client', 'booking_conf_to_client', 
								'booking_conf_to_dj', 'notify_profile', 'package_prices', 'status_notification',
								'update_event', 'custom_client_text', 'enable_tax',
								'enable_paypal', 'enable_sandbox', 'paypal_debug', 'enable_pf_sandbox', 'payfast_debug', 'dj_see_wp_dash', 'dj_add_client',
								'dj_add_event', 'dj_view_enquiry', 'dj_upload_music', 'dj_add_venue', 'dj_see_deposit', 'upload_playlists',
								'enable_music_library', 'music_library_only', 'uninst_remove_db', 'uninst_remove_mdjm_posts', 
								'uninst_remove_mdjm_pages', 'uninst_remove_mdjm_templates', 'uninst_remove_mdjm_users',
								'gmail_enquiry', 'gmail_dj', 'gcal_full_sync'
								);
				
				$value = ( in_array( $args['field'], $true_vals ) ? '1' : 'Y' );
				
				echo '<input type="checkbox" name="' . ( !empty( $args['key'] ) ? $args['key'] . '[' . $args['field'] . ']' 
					: $args['field'] ) . '" id="' . $args['field'] . '"' . 
				checked( $args['value'], $value, false ) . 
				' value="' . $value . '"' . 
				
				/* -- The setting for a client to update an event is not yet active so disable is -- */
				( $args['field'] == 'update_event' ? ' disabled="disabled"' : '' ) . ' />';
					
			} // show_checkbox_field
						
			/*
			 * Display the setting field as a radio input
			 *
			 *
			 *
			 */
			function show_radio_field( $args )	{
				global $mdjm_settings;											
				$i = 0;
				foreach( $args['custom_args']['values'] as $radio )	{
					echo '<label>' . "\n";
					echo '<input type="radio" name="' . $args['key'] . '[' . $args['field'] . ']" value="' . $radio . '" id="' . $radio . '" ' . 
					checked( $args['value'], $radio, false ) . ' />' . "\n";
					if( $radio == 'html' )
						echo __( 'Use standard HTML submit button with text', 'mobile-dj-manager' ) . 
							'&nbsp;<input type="text" name="' . $args['key'] . '[button_text]" id="button_text" style="max-width: 100px;" value="' . 
							( !empty( $mdjm_settings[MDJM_PAYMENT_GW]['button_text'] ) ? $mdjm_settings[MDJM_PAYMENT_GW]['button_text'] : 
							__( 'Pay Now', 'mobile-dj-manager' ) ) . '">';
						
					else	{
						if( MDJM_PAYMENT_GW == 'paypal' )
							echo '<img src="https://www.paypalobjects.com/en_GB/i/btn/' . $radio . '">';
							
						elseif( MDJM_PAYMENT_GW == 'payfast' )
							echo '<img src="https://www.payfast.co.za/images/buttons/' . $radio . '" style="alignment-baseline: baseline;">';
					}
						
					echo '</label>' . "\n";
					$i++;
					if( $i != count( $args['custom_args']['values'] ) )	{
						echo '<br />' . "\n";	
					}
				}
			} // show_radio_field
			
			/*
			 * Display the setting field as a textarea input
			 *
			 *
			 *
			 */
			function show_textarea_field( $args )	{
				echo '<textarea name="' . ( !empty( $args['key'] ) ? $args['key'] . '[' . $args['field'] . ']' 
					: $args['field'] ) . '" id="' . $args['field'] . '" ' . 
				'cols="80" rows="6" ' . 
				( !empty( $args['class'] ) ? 'class="' . $args['class'] . '" ' : '' ) . 
				'>' . $args['value'] . '</textarea>' . "\r\n";
			} // show_textarea_field
			
			/*
			 * Display the setting field as a textarea input with tinyMCE
			 *
			 *
			 *
			 *
			 */
			function show_mce_textarea_field( $args )	{
				wp_editor( $args['value'], $args['field'], $args['custom_args']['mce_settings'] );
			} // show_mce_textarea_field
			
			/*
			 * Help text for settings pages
			 *
			 *
			 *
			 */
			function help_text( $contextual_help, $screen_id, $screen )	{
				$current = ( isset( $_GET['section'] ) ? $_GET['section'] : '' );
				
				switch( $current )	{
					case 'mdjm_client_field_settings':
						$contextual_help = 
						'<p>' . sprintf( __( 'By managing Client Fields, you can determine which information you capture and store for each of your clients. ' .
						'Each field listed below, whether default or create by you, will be displayed on the %s profile page ' . 
						'when visited by a client. As long as it is enabled.', 'mobile-dj-manager' ), MDJM_APP ) . '<br />' . 
						
						sprintf( __( 'For further assistance, refer to our %sUser Guides%s' .
						' or visit the %s ' . 
						'%sSupport Forums' . '%s', 'mobile-dj-manager' ),
						'<a href="' . mdjm_get_admin_page( 'user_guides' ) . '" target="_blank">',
						'</a>',
						'<a href="' . mdjm_get_admin_page( 'mydjplanner' ) . '" target="_blank">' . MDJM_NAME . '</a>',
						'<a href="' . mdjm_get_admin_page( 'mdjm_forums' ) . '" target="_blank">',
						'</a>' ) . 
						
						'</p>' . "\r\n";
					break;
					
					default:
						$contextual_help = 
						'<p>' . sprintf( __( 'For assistance, refer to our %sUser Guides%s' .
						' or visit the %s ' . 
						'%sSupport Forums%s', 'mobile-dj-manager' ),
						'<a href="' . mdjm_get_admin_page( 'user_guides' ) . '" target="_blank">',
						'</a>',
						'<a href="' . mdjm_get_admin_page( 'mydjplanner' ) . '" target="_blank">' . MDJM_NAME . '</a>',
						'<a href="' . mdjm_get_admin_page( 'mdjm_forums' ) . '" target="_blank">',
						'</a>' ) . 
						
						'</p>' . "\r\n";
					break;
					
				} // switch
				
				return $contextual_help;
			} // help_text
			
			/**
			 * Display our premium addons
			 *
			 *
			 *
			 *
			 */	
			function mdjm_premium_addons()	{
				?>
                <style>
				table { border-spacing: 0.5rem; }
				td {padding-left: 0.5rem; padding-right: 0.5rem; }
				</style>
				<h3><?php _e( 'Have you tried our Premium Plugins', 'mobile-dj-manager' ); ?>?</h3>
                <p><?php _e( 'Our Premium Plugins enhance the features of the MDJM Event Management Plugin. All premium plugins are provided with a full years updates and support', 'mobile-dj-manager' ); ?>.</p>
                <table>
                <tr>
                <td><a href="http://www.mydjplanner.co.uk/shop/mdjm-dynamic-contact-forms/" target="_blank"><img src="http://www.mydjplanner.co.uk/wp-content/uploads/2015/09/MDJM_DCF_Product.jpg" alt="MDJM Dynamic Contact Forms" title="MDJM Dynamic Contact Forms" /></a></td>
                <td><a href="http://www.mydjplanner.co.uk/shop/mdjm-payments/" target="_blank"><img src="http://www.mydjplanner.co.uk/wp-content/uploads/2015/10/MDJM_Payments_Product.jpg" alt="MDJM Google Calendar Sync" title="MDJM Google Calendar Sync" /></td></a>
                <td><a href="http://www.mydjplanner.co.uk/shop/mdjm-google-calendar-sync/" target="_blank"><img src="http://www.mydjplanner.co.uk/wp-content/uploads/2015/10/MDJM_Google_Cal_Product.jpg" alt="MDJM Google Calendar Sync" title="MDJM Google Calendar Sync" /></td></a>
                </tr>
                <tr>
                <td style="text-align:center"><a href="http://www.mydjplanner.co.uk/shop/mdjm-dynamic-contact-forms/" target="_blank" class="button secondary">Buy now</a><br>
                    <strong>&pound;35.00</strong>
                </td>
                <td style="text-align:center"><a href="http://www.mydjplanner.co.uk/shop/mdjm-payments/" target="_blank" class="button secondary">Buy now</a><br>
                    <strong>&pound;25.00</strong>
                </td>
                <td style="text-align:center"><a href="http://www.mydjplanner.co.uk/shop/mdjm-google-calendar-sync/" target="_blank" class="button secondary">Buy now</a><br>
                    <strong>&pound;25.00</strong>
                </td>
                </tr>
                </table>
				<?php
			} // mdjm_premium_addons	

			
		} // Class MDJM_Settings
	}	
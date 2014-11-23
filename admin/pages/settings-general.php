<?php
	defined('ABSPATH') or die("Direct access to this page is disabled!!!");
	if ( !current_user_can( 'manage_options' ) && !current_user_can( 'manage_mdjm' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	/* Check for plugin update */
	f_mdjm_has_updated();
	
	require_once WPMDJM_PLUGIN_DIR . '/includes/functions.php';
	require_once WPMDJM_PLUGIN_DIR . '/admin/includes/functions.php';
	
	function f_mdjm_display_general_settings_contents()	{
	?>
        <div class="wrap">
        <div id="icon-themes" class="icon32"></div>
        <?php 
		settings_errors();
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
        ?>
        <h2 class="nav-tab-wrapper">
            <a href="admin.php?page=mdjm-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
            <a href="admin.php?page=mdjm-settings&tab=pages" class="nav-tab <?php echo $active_tab == 'pages' ? 'nav-tab-active' : ''; ?>">Pages</a>
            <a href="admin.php?page=mdjm-settings&tab=permissions" class="nav-tab <?php echo $active_tab == 'permissions' ? 'nav-tab-active' : ''; ?>">Permissions</a>
            <a href="admin.php?page=mdjm-settings&tab=client_fields" class="nav-tab <?php echo $active_tab == 'client_fields' ? 'nav-tab-active' : ''; ?>">Client Fields</a>
            <a href="admin.php?page=mdjm-settings&tab=email_templates" class="nav-tab <?php echo $active_tab == 'email_templates' ? 'nav-tab-active' : ''; ?>">Email Templates</a>
            <a href="admin.php?page=mdjm-settings&tab=scheduler" class="nav-tab <?php echo $active_tab == 'scheduler' ? 'nav-tab-active' : ''; ?>">Scheduler</a>
        </h2>
             <?php
			$lic_info = do_reg_check( 'check' );
			if( $active_tab == 'general' ) {
				echo '<form method="post" action="options.php">';
				echo '<table class="form-table">';
				if( $lic_info && $lic_info[0] == 'XXXX' ) $class = ' class="form-invalid"';
				echo '<tr' . $class . '>';
				echo '<th scope="row">License Key:</th>';
				if( $lic_info )	{
					if( $lic_info[0] == 'XXXX' )	{
						echo '<td>Running in trial mode until ' . date( 'd/m/Y', strtotime( $lic_info[2] ) ) . '. Visit <a href="http://www.mydjplanner.co.uk" target="_blank">http://www.mydjplanner.co.uk</a> to purchase your license</td>';
					}
					else	{
						echo '<td>' . $lic_info[0] . ' (' . date( 'd/m/Y', strtotime( $lic_info[2] ) ) . ')</td>';
					}
				}
				else	{
					echo '<td class="form-error">UNLICENSED - Visit <a href="http://www.mydjplanner.co.uk" target="_blank">http://www.mydjplanner.co.uk</a> to purchase your license</td>';	
				}
				echo '</tr>';
				echo '</table>';
				settings_fields( 'mdjm-settings' );
				do_settings_sections( 'mdjm-settings' );
			}
			elseif( $active_tab == 'pages' )	{
				echo '<form method="post" action="options.php">';
				settings_fields( 'mdjm-pages' );
				do_settings_sections( 'mdjm-pages' );
			}
			elseif( $active_tab == 'permissions' )	{
				echo '<form method="post" action="options.php">';
				settings_fields( 'mdjm-permissions' );
				do_settings_sections( 'mdjm-permissions' );
			}
			elseif( $active_tab == 'client_fields' )	{
				include( WPMDJM_PLUGIN_DIR . '/admin/pages/settings-client-fields.php' );
			}
			elseif( $active_tab == 'email_templates' )	{
				include( WPMDJM_PLUGIN_DIR . '/admin/pages/settings-email-templates.php' );
			}
			elseif( $active_tab == 'scheduler' )	{
				include( WPMDJM_PLUGIN_DIR . '/admin/pages/settings-scheduler.php' );
			}
			else	{
				wp_die( 'You do not have the necessary permissions to view this page!' );
			}
			if( current_user_can( 'manage_options' ) 
				&& $lic_info // No license no save
				&& $active_tab != 'email_templates' // Email Templates use there own submit button
				&& !$_GET['task_action'] )	{ // If editing a task don't display
				submit_button(); 
			}
			 ?>
          </form>
        </div>
	<?php
	}
	f_mdjm_display_general_settings_contents();
?>
<?php

/*
 * Save the contact form field order
 *
 *
 */
	function save_mdjm_field_order()	{
		global $mdjm_posts;
		
		remove_action( 'save_post', array( $mdjm_posts, 'save_custom_post' ), 10, 2 );
		
		foreach( $_POST['fields'] as $order => $id )	{
			$menu = $order + 1;
			
			wp_update_post( array(
								'ID'			=> $id,
								'menu_order'	=> $menu,
								) );	
		}
		add_action( 'save_post', array( $mdjm_posts, 'save_custom_post' ), 10, 2 );
		die();
	} // save_mdjm_field_order
	add_action( 'wp_ajax_mdjm_update_field_order', 'save_mdjm_field_order' );
	
/*
 * Save the event transaction
 *
 *
 */
	function save_event_transaction()	{		
		if( !class_exists( 'MDJM_Transactions' ) )
			require_once( WPMDJM_PLUGIN_DIR . '/admin/includes/class/class-mdjm-transactions.php' );
		
		$mdjm_transactions = new MDJM_Transactions();
		
		$result = $mdjm_transactions->add_event_transaction();
		
		die();
	} // save_event_transaction
	add_action( 'wp_ajax_add_event_transaction', 'save_event_transaction' );
	
/*
 * Add a new event type
 * Initiated from the Event Post screen
 *
 */
	function add_event_type()	{
		global $mdjm;
		
		$mdjm->debug_logger( 'Adding ' . $_POST['type'] . ' new Event Type from Event Post form', true );
			
		$args = array( 
					'taxonomy'			=> 'event-types',
					'hide_empty' 		  => 0,
					'name' 				=> 'mdjm_event_type',
					'id' 				=> 'mdjm_event_type',
					'orderby' 			 => 'name',
					'hierarchical' 		=> 0,
					'show_option_none' 	=> __( 'Select Event Type' ),
					'class'			   => 'mdjm-meta required',
					'echo'				=> 0,
				);
				
		/* -- Validate that we have an Event Type to add -- */
		if( empty( $_POST['type'] ) )	{
			$result['type'] = 'Error';
			$result['msg'] = 'Please enter a name for the new Event Type';
		}
		/* -- Add the new Event Type (term) -- */
		else	{
			$term = wp_insert_term( $_POST['type'], 'event-types' );
			if( is_array( $term ) )	{
				$result['type'] = 'success';
			}
			else	{
				$result['type'] = 'error';
			}
		}
		
		$mdjm->debug_logger( 'Completed adding ' . $_POST['type'] . ' new Event Type from Event Post form', true );
		
		$args['selected'] = $result['type'] == 'success' ? $term['term_id'] : $_POST['current'];
		
		$result['event_types'] = wp_dropdown_categories( $args );
		
		$result = json_encode($result);
		echo $result;
		
		die();
	} // add_event_type
	add_action( 'wp_ajax_add_event_type', 'add_event_type' );
	
/*
 * Add a new transaction type
 * Initiated from the Transaction Post screen
 *
 */
	function add_transaction_type()	{
		global $mdjm;
		
		$mdjm->debug_logger( 'Adding ' . $_POST['type'] . ' new Transaction Type from Transaction Post form', true );
			
		$args = array( 
					'taxonomy'			=> 'transaction-types',
					'hide_empty' 		  => 0,
					'name' 				=> 'mdjm_transaction_type',
					'id' 				=> 'mdjm_transaction_type',
					'orderby' 			 => 'name',
					'hierarchical' 		=> 0,
					'show_option_none' 	=> __( 'Select Transaction Type' ),
					'class'			   => ' required',
					'echo'				=> 0,
				);
				
		/* -- Validate that we have a Transaction Type to add -- */
		if( empty( $_POST['type'] ) )	{
			$result['type'] = 'Error';
			$result['msg'] = 'Please enter a name for the new Transaction Type';
		}
		/* -- Add the new Event Type (term) -- */
		else	{
			$term = wp_insert_term( $_POST['type'], 'transaction-types' );
			if( is_array( $term ) )	{
				$result['type'] = 'success';
			}
			else	{
				$result['type'] = 'error';
			}
		}
		
		$mdjm->debug_logger( 'Completed adding ' . $_POST['type'] . ' new Transaction Type from Transaction Post form', true );
		
		$args['selected'] = $result['type'] == 'success' ? $term['term_id'] : $_POST['current'];
		
		$result['transaction_types'] = wp_dropdown_categories( $args );
		
		$result = json_encode($result);
		echo $result;
		
		die();
	} // add_transaction_type
	add_action( 'wp_ajax_add_transaction_type', 'add_transaction_type' );


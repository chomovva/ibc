<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; }



trait PublishingHouses {



	function get_publishing_house_link( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'action'      => 'table',
			'nonce'       => '',
			'id'          => '',
			'notice'      => '',
		) );
		if ( in_array( $args[ 'action' ], array( 'table', 'add', 'edit', 'delete' ) ) ) {
			if ( empty( $args[ 'nonce' ] ) ) {
				$args[ 'nonce' ] = wp_create_nonce( 'publishing_houses' );
			}
		} else {
			$args[ 'action' ] = 'table';
		}
		return add_query_arg(
			array(
				'page'    => 'publishing_houses',
				'action'  => $args[ 'action' ],
				'nonce'   => $args[ 'nonce' ],
				'id'      => $args[ 'id' ],
				'notice'  => urlencode( $args[ 'notice' ] ),
			),
			network_admin_url( 'admin.php?' )
		);
	}



	function the_publishing_house_link( $args = array() ) {
		echo $this->get_publishing_house_link( $args );
	}



	function get_publishing_houses() {
		$result = $this->db->get_results( "SELECT * FROM publishing_houses" );
		return ( is_array( $result ) ) ? $result : __return_empty_array();
		// return $result;
	}



	function get_publishing_house( $id ) {
		$sql = $this->db->prepare( "SELECT * FROM publishing_houses WHERE id = %d", $id );
		$result = $this->db->get_row( $sql, OBJECT );
		return ( is_null( $result ) ) ? new \WP_Error( 'ibcdb', $this->db->last_error ) : $result;
	}



	function delete_publishing_house( $id ) {
		$result = $this->db->delete( 'publishing_houses', array( 'ID' => $id ), array( '%d' ) );
		return ( 0 == $result ) ? new \WP_Error( 'ibcdb', __( 'Издательство не удалено', IBC_TEXTDOMAIN ) ) : true;
	}



	function add_publishing_house( $name ) {
		$result = $this->db->insert( 'publishing_houses', array( 'name' => $name ), array( '%s' ) );
		return ( is_numeric( $result ) && $result > 0 ) ? $this->db->insert_id : new \WP_Error( 'ibcdb', __( 'Издательство не добавлено', IBC_TEXTDOMAIN ) );
	}

	function update_publishing_house( $id, $name ) {
		$result = $this->db->update( 'publishing_houses', array( 'name' => $name ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
		return ( is_numeric( $result ) && $result > 0 ) ? $id : new \WP_Error( 'ibcdb', __( 'Издательство не обновлено', IBC_TEXTDOMAIN ) );
	}

}
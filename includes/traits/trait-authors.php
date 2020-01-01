<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; }



trait Authors {



	function get_author_link( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'action'      => 'table',
			'nonce'       => '',
			'id'          => '',
			'notice'      => '',
		) );
		if ( in_array( $args[ 'action' ], array( 'table', 'add', 'edit', 'delete' ) ) ) {
			if ( empty( $args[ 'nonce' ] ) ) {
				$args[ 'nonce' ] = wp_create_nonce( 'authors' );
			}
		} else {
			$args[ 'action' ] = 'table';
		}
		return add_query_arg(
			array(
				'page'    => 'authors',
				'action'  => $args[ 'action' ],
				'nonce'   => $args[ 'nonce' ],
				'id'      => $args[ 'id' ],
				'notice'  => urlencode( $args[ 'notice' ] ),
			),
			network_admin_url( 'admin.php?' )
		);
	}



	function the_author_link( $args = array() ) {
		echo $this->get_author_link( $args );
	}



	function get_authors() {
		$result = $this->db->get_results( "SELECT * FROM authors" );
		return ( is_array( $result ) ) ? $result : __return_empty_array();
	}



	function get_author( $id ) {
		$sql = $this->db->prepare( "SELECT * FROM authors WHERE id = %d", $id );
		$result = $this->db->get_row( $sql, OBJECT );
		return ( is_null( $result ) ) ? new \WP_Error( 'ibcdb', $this->db->last_error ) : $result;
	}



	function delete_author( $id ) {
		$result = $this->db->delete( 'authors', array( 'ID' => $id ), array( '%d' ) );
		return ( 0 == $result ) ? new \WP_Error( 'ibcdb', __( 'Автор не удалён', IBC_TEXTDOMAIN ) ) : true;
	}



	function add_author( $first_name, $last_name, $middle_name ) {
		$result = $this->db->insert(
			'authors',
			array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'middle_name' => $middle_name,
			),
			array( '%s', '%s', '%s' )
		);
		return ( is_numeric( $result ) && $result > 0 ) ? $this->db->insert_id : new \WP_Error( 'ibcdb', __( 'Новый автор не добавлен', IBC_TEXTDOMAIN ) );
	}

	function update_author( $id, $name ) {
		$result = $this->db->update( 'authors', array( 'name' => $name ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
		return ( is_numeric( $result ) && $result > 0 ) ? $id : new \WP_Error( 'ibcdb', __( 'Данные автора не обновлены', IBC_TEXTDOMAIN ) );
	}

}
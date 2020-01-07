<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; };



trait Genres {



	function get_genre_link( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'action'      => 'table',
			'nonce'       => '',
			'id'          => '',
			'notice'      => '',
		) );
		if ( in_array( $args[ 'action' ], array( 'table', 'add', 'edit', 'delete' ) ) ) {
			if ( empty( $args[ 'nonce' ] ) ) {
				$args[ 'nonce' ] = wp_create_nonce( 'genres' );
			}
		} else {
			$args[ 'action' ] = 'table';
		}
		return add_query_arg(
			array(
				'page'    => 'genres',
				'action'  => $args[ 'action' ],
				'nonce'   => $args[ 'nonce' ],
				'id'      => $args[ 'id' ],
				'notice'  => urlencode( $args[ 'notice' ] ),
			),
			network_admin_url( 'admin.php?' )
		);
	}



	function the_genre_link( $args = array() ) {
		echo $this->get_genre_link( $args );
	}



	function get_genres( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'parent'  => '',
			'exclude' => '',
		) );
		$where = __return_empty_array();
		$sql = "SELECT * FROM genres";
		foreach ( $args as $key => &$value ) {
			switch ( $key ) {
				case 'parent':
					$value = sanitize_key( $value );
					if ( ! empty( $value ) || '0' === ( string ) $value ) {
						$where[] = $this->db->prepare( 'parent = %d', $value );
					}
					break;
				case 'exclude':
					foreach ( wp_parse_id_list( $value ) as $id ) {
						$where[] = $this->db->prepare( 'id != %d', $id );
					}
					break;
			}
		}
		if ( ! empty( $where ) ) {
			$sql .= " WHERE " . implode( " AND ", $where );
		}
		$result = $this->db->get_results( $sql );
		return ( is_array( $result ) ) ? $result : __return_empty_array();
	}



	function get_genre( $id ) {
		$sql = $this->db->prepare( "SELECT * FROM genres WHERE id = %d", $id );
		$result = $this->db->get_row( $sql, OBJECT );
		return ( is_null( $result ) ) ? new \WP_Error( 'ibcdb', $this->db->last_error ) : $result;
	}



	function delete_genre( $id ) {
		$genres = $this->get_genres( array( 'parnt' => $id ) );
		if ( ! empty( $genres ) ) {
			foreach ( $genres as $genre ) {
				$this->update_genre( $genre->id, $genre->name, 0 );
			}
		}
		$result = $this->db->delete( 'genres', array( 'ID' => $id ), array( '%d' ) );
		return ( 0 == $result ) ? new \WP_Error( 'ibcdb', __( 'Жанр не удалён', IBC_TEXTDOMAIN ) ) : true;
	}



	function add_genre( $name, $parent ) {
		$result = $this->db->insert(
			'genres',
			array(
				'name'   => $name,
				'parent' => $parent,
			),
			array( '%s', '%d' )
		);
		return ( is_numeric( $result ) && $result > 0 ) ? $this->db->insert_id : new \WP_Error( 'ibcdb', __( 'Новый жанр не добавлен', IBC_TEXTDOMAIN ) );
	}



	function update_genre( $id, $name, $parent = 0 ) {
		$result = $this->db->update(
			'genres',
			array(
				'name'   => $name,
				'parent' => $parent,
			),
			array( 'id' => $id ), array( '%s', '%d' ), array( '%d' ) );
		return ( is_numeric( $result ) && $result > 0 ) ? $id : new \WP_Error( 'ibcdb', __( 'Данные автора не обновлены', IBC_TEXTDOMAIN ) );
	}



}
<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; }



trait Readers {



	function get_readers_table_name( $blog_id = false ) {
		global $wpdb;
		if ( ! $blog_id ) {
			$blog_id = get_current_blog_id();
		}
		return sprintf( '%1$s%2$s_%3$s', $wpdb->get_blog_prefix( get_current_blog_id() ), IBC_SLUG, 'readers' );
	}



	function get_readers( $args = array() ) {
		global $wpdb;
		$args = wp_parse_args( $args, array(
			// 'number_readers' => 0,
			// 'orderby'     => 'date',
			// 'order'       => 'DESC',
			// 'offset'      => 0,
			'exclude'      => '',
			'department'   => '',
		) );
		extract( $args );
		// if ( ! in_array( $order, array( 'DESC', 'ASC' ) ) ) { $order = 'DESC'; }
		// if ( ! in_array( $orderby, array( 'card_id', 'first_name', 'last_name' ) ) ) { $order = 'reader_date'; }
		$table_name = $this->get_readers_table_name();
		$sql = "SELECT * FROM $table_name";
		if ( ! empty( trim( $args[ 'department' ] ) ) ) {
			$sql = $wpdb->prepare( $sql . " WHERE department = %d", $args[ 'department' ] );
		}
		if ( ! empty( $args[ 'exclude' ] ) ) {
			if ( preg_match( '/WHERE/', $sql ) ) {
				$sql .= " WHERE";
			}
			$args[ 'exclude' ] = wp_parse_id_list( $args[ 'exclude' ] );
			for ( $i = 0; $i < count( $args[ 'exclude' ] ); $i++ ) {
				$sql = $wpdb->prepare( $sql . ( ( 0 != $i ) ? " AND" : "" ) . " id != %d", $args[ 'exclude' ][ $i ] );
			}
		}
		$result = $wpdb->get_results( $sql );
		return ( is_array( $result ) ) ? $result : __return_empty_array();
	}


	function get_reader( $reader_id ) {
		global $wpdb;
		$table_name = $this->get_readers_table_name();
		$sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE id=%s", $reader_id );
		$result = $wpdb->get_row( $sql, OBJECT );
		return $result;
	}


// SELECT SQL_CALC_FOUND_ROWS okjdv_posts.ID
// FROM okjdv_posts
// WHERE 1=1
// AND okjdv_posts.post_type = 'post'
// AND (okjdv_posts.post_status = 'publish'
// OR okjdv_posts.post_status = 'future'
// OR okjdv_posts.post_status = 'draft'
// OR okjdv_posts.post_status = 'pending'
// OR okjdv_posts.post_status = 'private')
// ORDER BY okjdv_posts.post_date DESC
// LIMIT 0, 75


	function add_reader( $first_name, $last_name, $sex, $department ) {
		global $wpdb;
		$table_name = $this->get_readers_table_name();
		$wpdb->insert(
			$table_name,
			array(
				'card_id'     => '',
				'first_name'  => $first_name,
				'last_name'   => $last_name,
				'sex'         => $sex,
				'date_added'  => date( 'Y-m-d G:i:s' ),
				'librarian'     => get_current_user_id(),
				'department'  => $department,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%d', '%d' )
		);
		$reader_id = $wpdb->insert_id;
		$card_id = sprintf(
			'%04d%04d',
			get_current_blog_id(),
			$reader_id
		);
		$wpdb->update(
			$table_name,
			array( 'card_id' => $card_id ),
			array( 'id' => $reader_id ),
			array( '%s' ),
			array( '%d' )
		);
		return $reader_id;
	}



	function update_reader( $reader_id, $first_name, $last_name, $sex, $department ) {
		global $wpdb;
		$result = $wpdb->update(
			$this->get_readers_table_name(),
			array(
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'sex'        => $sex,
				'department' => $department,
			),
			array( 'id' => $reader_id ),
			array( '%s', '%s', '%s', '%d' ),
			array( '%d' )
		);
		return $result;
	}


	function delete_reader( $reader_id ) {
		global $wpdb;
		$wpdb->delete(
			$this->get_readers_table_name(),
			array( 'id' => $reader_id ),
			array( '%d' )
		);
	}



	function wp_dropdown_readers( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'name' => 'reader',
			'selected' => '',
			'exclude' => '',
			'echo' => true,
			'show_option_none' => '',
		) );
		$readers = $this->get_readers();
		$result = __return_empty_array();
		if ( is_array( $readers ) ) {
			$result[] = '<option value="">' . $args[ 'show_option_none' ] . '</option>';
			foreach ( $readers as $reader ) {
				$result[] = sprintf(
					'<option value="%1$s">%2$s %3$s</option>',
					$reader->id,
					$reader->last_name,
					$reader->first_name,
					selected( $selected, $reader->id, false )
				);
			}
		}
		if ( $args[ 'echo' ] ) {
			echo implode( "\r\n", $result );
		} else {
			return implode( "\r\n", $result );
		}
	}



}
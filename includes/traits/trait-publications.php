<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; }



trait Publications {


	function publications_parse_args( $args ) {
		$defaults = array(
			'title' => '',
			'publishing_house_id' => 0,
			'annotation' => '',
			'isbn' => '',
			'year' => '',
		);
		$result = __return_empty_array();
		foreach ( $defaults as $key => $value ) {
			$result[ $key ] = ( isset( $args[ $key ] ) ) ? $args[ $key ] : $value;
		}
		return $result;
	}



	function get_publications( $args = array() ) {
		return __return_empty_array();
	}



	function add_publication( $args ) {
		$args = $this->publications_parse_args( $args );
		extract( $args );
		// $result = $this->db->insert( 'publications', array(
		// 	'title' => $title,
		// 	'publishing_house_id' => ( int ) $publishing_house_id,
		// 	'annotation' => $annotation,
		// 	'isbn' => $isbn,
		// 	'year' => $year,
		// ), array( '%s', '%d', '%s', '%s', '%s' ) );
		// echo "<pre>";
		// var_dump( $args );
		// echo "</pre>";
		$sql = $this->db->prepare( 'INSERT INTO publications VALUES ( %s, %d, %s, %s, %s );', $title, $publishing_house_id, $annotation, $isbn, $year );
		echo "<pre>";
		var_dump( $sql );
		echo "</pre>";
		$result = $this->db->query( $sql );
		return ( is_numeric( $result ) && $result > 0 ) ? $this->db->insert_id : new \WP_Error( 'ibcdb', __( 'Новая публикация не добавлена', IBC_TEXTDOMAIN ) );
	}



	function update_publication( $id, $args ) {
		$args = $this->publications_parse_args( $args );
		$result = $this->db->update( 'publications', $args, array( 'id' => $id ), array( '%s', '%d', '%s', '%s', '%s' ), array( '%d' ) );
		return ( is_numeric( $result ) && $result > 0 ) ? $id : new \WP_Error( 'ibcdb', __( 'Данные публикации не обновлены', IBC_TEXTDOMAIN ) );
	}



	function delete_publication() {

	}



}
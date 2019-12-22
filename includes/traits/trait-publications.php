<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; }



trait Publications {



	function save_publication_postdata( $post_id, $postdata ) {
		$slug = IBC_SLUG;
		foreach ( array(
			'isbn',
			'year',
			'genre',
			'publishing_houses',
			'authors',
		) as $key ) {
			switch ( $key ) {
				case 'publishing_houses':
				case 'authors':
				case 'genre':
					$terms = ( isset( $postdata[ $key ] ) && ! empty( $postdata[ $key ] ) ) ? wp_parse_id_list( $postdata[ $key ] ) : array();
					wp_set_object_terms( $post_id, $terms, "{$slug}_{$key}", false );
					break;
				default:
					if ( isset( $postdata[ $key ] ) && ! empty( $postdata[ $key ] ) ) {
						update_post_meta( $post_id, "{$slug}_{$key}", sanitize_text_field( $postdata[ $key ] ) );
					} else {
						delete_post_meta( $post_id, "{$slug}_{$key}" );
					}
					break;
			}
		}
	}




	function render_publication_metabox( $post, $meta ) {
		$slug = IBC_SLUG;
		$textdomain = IBC_TEXTDOMAIN;
		$isbn = get_post_meta( $post->ID, "{$slug}_isbn", true );
		$year = get_post_meta( $post->ID, "{$slug}_year", true );
		$authors_terms = get_terms( array( 'taxonomy' => "{$slug}_authors", 'hide_empty' => false ) );
		$authors = get_the_terms( $post, "{$slug}_authors" );
		$authors = ( is_array( $authors ) ) ? wp_list_pluck( $authors, 'term_id' ) : __return_empty_array();
		$genre_terms = get_terms( array( 'taxonomy' => "{$slug}_genre", 'hide_empty' => false ) );
		$genre_parents_terms = ( is_array( $genre_terms ) ) ? wp_list_filter( $genre_terms, array( 'parent' => 0 ), 'AND' ) : __return_empty_array();
		$genre = get_the_terms( $post, "{$slug}_genre" );
		$genre = ( is_array( $genre ) ) ? wp_list_pluck( $genre, 'term_id' ) : __return_empty_array();
		$publishing_houses_terms = get_terms( array( 'taxonomy' => "{$slug}_publishing_houses", 'hide_empty' => false ) );
		$publishing_houses = get_the_terms( $post, "{$slug}_publishing_houses" );
		$publishing_houses = ( is_array( $publishing_houses ) ) ? wp_list_pluck( $publishing_houses, 'term_id' ) : __return_empty_array();
		wp_add_inline_script( 'jquery.mask', "jQuery( document ).ready( function () { jQuery( '#isbn' ).mask( '0-000-00000-0' ); } );", 'after' );
		wp_add_inline_script( 'jquery.mask', "jQuery( document ).ready( function () { jQuery( '#year' ).mask( '0000' ); } );", 'after' );
		wp_add_inline_script( 'select2', "jQuery( document ).ready( function () { jQuery( '#authors' ).select2(); } );", 'after' );
		wp_add_inline_script( 'select2', "jQuery( document ).ready( function () { jQuery( '#publishing_house' ).select2(); } );", 'after' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'jquery.mask' );
		wp_enqueue_style( 'flexboxgrid' );
		wp_enqueue_style( 'select2' );
		require_once IBC_VIEWS . 'forms/publication.php';
	}



}
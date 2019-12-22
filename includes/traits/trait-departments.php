<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; }



trait Departments {



	function get_departments_table_name( $blog_id = false ) {
		global $wpdb;
		if ( ! $blog_id ) {
			$blog_id = get_current_blog_id();
		}
		return sprintf( '%1$s%2$s_%3$s', $wpdb->get_blog_prefix( get_current_blog_id() ), IBC_SLUG, 'departments' );
	}


	function get_departments( $args = array() ) {
		global $wpdb;
		$result = __return_empty_array();
		$args = wp_parse_args( $args, array(
			'parent'        => '',
			'exclude'       => '',
			'hierarchical'  => 1,
		) );
		$table_name = $this->get_departments_table_name();
		$sql = "SELECT * FROM $table_name";
		$where = __return_empty_array();
		if ( ! empty( $args[ 'parent' ] ) || '0' === ( string ) $args[ 'parent' ] ) {
			$where[] = $wpdb->prepare( "parent = %d", ( int ) $args[ 'parent' ] );
		}
		if ( ! empty( $args[ 'exclude' ] ) ) {
			$args[ 'exclude' ] = wp_parse_id_list( $args[ 'exclude' ] );
			for ( $i = 0; $i < count( $args[ 'exclude' ] ); $i++ ) {
				foreach ( wp_parse_id_list( $args[ 'exclude' ] ) as $exclude_id ) {
					$where[] = $wpdb->prepare( "id != %d", $exclude_id );
				}
			}
		}
		if ( ! empty( $where ) ) {
			$sql .= ' WHERE ' . implode( " AND ", $where );
		}
		$departments = $wpdb->get_results( $sql );
		if ( ! is_array( $departments ) || empty( $departments ) ) {
			$result = __return_empty_array();
		} elseif ( empty( $args[ 'hierarchical' ] ) ) {
			$result = $departments;
		} else {
			$parents = wp_list_filter( $departments, array( 'parent' => 0 ), 'AND' );
			foreach ( $parents as $parent ) {
				$result[] = $parent;
				$childs = wp_list_filter( $departments, array( 'parent' => $parent->id ), 'AND' );
				if ( ! empty( $childs ) ) {
					foreach ( $childs as $child ) {
						$child->name = '— ' . $child->name;
						$result[] = $child;
					}
				}
			}
		}
		return $result;
	}



	function get_department( $department_id ) {
		global $wpdb;
		$table_name = $this->get_departments_table_name();
		$sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $department_id );
		$result = $wpdb->get_row( $sql, OBJECT );
		return $result;
	}



	function add_department( $name, $parent ) {
		global $wpdb;
		$table_name = $this->get_departments_table_name();
		$wpdb->insert(
			$table_name,
			array(
				'name' => $name,
				'parent' => ( empty( $parent ) ) ? 0 : $parent,
			),
			array( '%s', '%d' )
		);
		$department_id = $wpdb->insert_id;
		return $department_id;
	}



	function update_department( $department_id, $name, $parent ) {
		global $wpdb;
		$result = $wpdb->update(
			$this->get_departments_table_name(),
			array(
				'name' => $name,
				'parent' => $parent,
			),
			array( 'id' => $department_id ),
			array( '%s', '%d' ),
			array( '%d' )
		);
		return $result;
	}


	function delete_department( $department_id ) {
		global $wpdb;
		$childs = $this->get_departments( array( 'parent' => $department_id ) );
		if ( ! empty( $childs ) ) {
			foreach ( $childs as $child ) {
				$this->delete_department( $child->id );
			}
		}
		$wpdb->delete(
			$this->get_departments_table_name(),
			array( 'id' => $department_id ),
			array( '%d' )
		);
	}




	function wp_dropdown_departments( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'name'         => 'department',
			'selected'     => '',
			'exclude'      => '',
			'hierarchical' => 0,
			'echo'         => true,
			'show_option_none' => '',
			'style'        => '',
			'parent'       => '',
		) );
		$departments = $this->get_departments( array(
			'exclude'       => $args[ 'exclude' ],
			'parent'        => $args[ 'parent' ],
			'hierarchical'  => $args[ 'hierarchical' ],
		) );
		$result = __return_empty_array();
		if ( is_array( $departments ) ) {
			$result[] = sprintf(
				'<select id="%1$s" name="%1$s" style="%3$s"><option>%2$s</option>',
				$args[ 'name' ],
				$args[ 'show_option_none' ],
				$args[ 'style' ]
			);
			foreach ( $departments as $department ) {
				$result[] = sprintf(
					'<option value="%1$s" %3$s>%2$s</option>',
					$department->id,
					$department->name,
					selected( $args[ 'selected' ], $department->id, false )
				);
			}
			$result[] = '</select>';
		}
		if ( $args[ 'echo' ] ) {
			echo implode( "\r\n", $result );
		} else {
			return implode( "\r\n", $result );
		}
	}




}
<?php

	$action = ( isset( $_REQUEST[ 'action' ] ) && in_array( $_REQUEST[ 'action' ], array( 'table', 'add', 'edit' ) ) ) ? $_REQUEST[ 'action' ] : 'table';
	$nonce = wp_create_nonce( 'publications' );

?>


<div class="wrap">

	<?php 
		if ( isset( $_REQUEST[ 'notice' ] ) && ! empty( $_REQUEST[ 'notice' ] ) ) {
			printf(
				'<div id="message" class="notice is-dismissible"><p>%1$s</p></div>',
				stripcslashes( $_REQUEST[ 'notice' ] )
			);
		}
	?>

	<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php network_admin_url( __( 'Вторичное меню', IBC_TEXTDOMAIN ) ); ?>">
		<a href="<?php $this->the_page_link( 'publications', array( 'nonce' => $nonce ) ); ?>" class="nav-tab <?php echo ( 'table' == $action ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Список', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php $this->the_page_link( 'publications', array( 'action' => 'add', 'nonce' => $nonce ) ); ?>" class="nav-tab <?php echo ( 'add' == $action ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Добавить', IBC_TEXTDOMAIN ); ?>
		</a>
		<?php if ( 'edit' == $action ) : ?><span class="nav-tab nav-tab-active"><?php _e( 'Редактирование', IBC_TEXTDOMAIN ); ?></span><?php endif; ?>
	</nav>

	<?php if ( 'table' == $action ) : ?>

		<form method="POST">
			<?php $GLOBALS[ 'Publications_List_Table' ]->display(); ?>
		</form>

	<?php else : ?>
		<?php
			$id = __return_empty_string();
			$title = __return_empty_string();
			$isbn = __return_empty_string();
			$annotation = __return_empty_string();
			$year = __return_empty_string();
			$publishing_house = __return_empty_string();
			if ( 'edit' == $action && isset( $_REQUEST[ 'id' ] ) ) {
				$publication = $this->get_publication( $_REQUEST[ 'id' ] );
				if ( ! is_wp_error( $publication ) ) {
					$id = $publication->id;
					$first_name = $publication->first_name;
					$last_name = $publication->last_name;
					$middle_name = $publication->middle_name;
				}
			}
		?>
		<form id="publications-form">
			<input type="hidden" name="page" value="publications">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
			<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>" required="required">
			<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>">
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<figure>
						<img src="<?php echo IBC_ASSETS . 'images/publication.svg' ?>" style="display: block; width: 100%;">
					</figure>
				</div>
				<div class="col-xs-12 col-sm-6 first-sm">
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="title"><?php _e( 'Название', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="title" type="text" name="query[title]" value="<?php echo esc_attr( $title ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="isbn"><?php _e( 'ISBN', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="isbn" type="text" name="query[isbn]" value="<?php echo esc_attr( $isbn ); ?>">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="publishing_house"><?php _e( 'Издательство', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<select style="width: 100%;" name="publishing_house">
								<option></option>
							</select>
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="year"><?php _e( 'Год издания', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="year" type="text" name="query[year]" value="<?php echo esc_attr( $year ); ?>">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="genre"><?php _e( 'Жанр', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<select id="genre" style="width: 100%;" name="query[genre]">
								<option></option>
							</select>
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12">
							<h4><label for="annotation"><?php _e( 'Анотация', IBC_TEXTDOMAIN ); ?></label></h4>
							<?php
								wp_editor( $annotation, 'annotation', array(
									'wpautop'       => 1,
									'media_buttons' => 0,
									'textarea_name' => 'query[annotation]',
									'textarea_rows' => 5,
									'tabindex'      => null,
									'editor_css'    => '',
									'editor_class'  => '',
									'teeny'         => 0,
									'dfw'           => 0,
									'tinymce'       => 1,
									'quicktags'     => 0,
									'drag_drop_upload' => false,
								) );
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<?php submit_button( ( 'edit' == $action ) ? __( 'Сохранить', IBC_TEXTDOMAIN ) : __( 'Добавить', IBC_TEXTDOMAIN ) ); ?>
						</div>
					</div>
				</div>
			</div>
		</form>

	<?php endif; ?>

</div>
<div class="wrap">

	<?php $tab = ( isset( $_REQUEST[ 'tab' ] ) && ! empty( $_REQUEST[ 'tab' ] ) ) ? $_REQUEST[ 'tab' ] : ''; ?>

	<h1 class="wp-heading-inline"><?php echo get_admin_page_title() ?></h1>

	<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Вторичное меню', IBC_TEXTDOMAIN ); ?>">
		<a href="<?php echo add_query_arg( array( 'page' => 'publications' ), network_admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( empty( $tab ) ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Список', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php echo add_query_arg( array( 'page' => 'publications', 'tab' => 'add' ), network_admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( $tab == 'edit' || $tab == 'add' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Добавить / редактировать', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php echo add_query_arg( array( 'page' => 'publications', 'tab' => 'import' ), network_admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( $tab == 'import' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Импорт', IBC_TEXTDOMAIN ); ?>
		</a>
	</nav>

	<?php if ( $tab == 'edit' || $tab == 'add' ) : ?>
		<?php
			$publication_id = __return_empty_string();
			$title = __return_empty_string();
			$isbn = __return_empty_string();
			$annotation = __return_empty_string();
			$year = __return_empty_string();
			$publishing_house = __return_empty_string();
		?>
		<form id="publications-form">
			<input type="hidden" name="<?php echo esc_attr( $publication_id ); ?>">
			<div class="row middle-xs" style="margin: .5em 0;">
				<div class="col-xs-12 col-sm-4">
					<label for="title"><?php _e( 'Название', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-4">
					<input style="width: 100%;" id="title" type="text" name="title" value="<?php echo esc_attr( $title ); ?>" required="required">
				</div>
			</div>
			<div class="row middle-xs" style="margin: .5em 0;">
				<div class="col-xs-12 col-sm-4">
					<label for="isbn"><?php _e( 'ISBN', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-4">
					<input style="width: 100%;" id="isbn" type="text" name="isbn" value="<?php echo esc_attr( $isbn ); ?>">
				</div>
			</div>
			<div class="row middle-xs" style="margin: .5em 0;">
				<div class="col-xs-12 col-sm-4">
					<label for="publishing_house"><?php _e( 'Издательство', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-4">
					<select style="width: 100%;" name="publishing_house">
						<option></option>
					</select>
				</div>
			</div>
			<div class="row middle-xs" style="margin: .5em 0;">
				<div class="col-xs-12 col-sm-4">
					<label for="year"><?php _e( 'Год издания', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-4">
					<input style="width: 100%;" id="year" type="text" name="year" value="<?php echo esc_attr( $year ); ?>">
				</div>
			</div>
			<div class="row middle-xs" style="margin: .5em 0;">
				<div class="col-xs-12 col-sm-4">
					<label for="genre"><?php _e( 'Жанр', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-4">
					<select id="genre" style="width: 100%;" name="genre">
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
							'media_buttons' => 1,
							'textarea_name' => '', //нужно указывать!
							'textarea_rows' => 5,
							'tabindex'      => null,
							'editor_css'    => '',
							'editor_class'  => '',
							'teeny'         => 0,
							'dfw'           => 0,
							'tinymce'       => 1,
							'quicktags'     => 1,
							'drag_drop_upload' => false,
						) );
					?>
				</div>
			</div>
		</form>
	<?php elseif ( $tab == 'import' ) : ?>
		<p>Импорт</p>
	<?php else : ?>
		<form method="POST">
			<?php $GLOBALS[ 'Publications_List_Table' ]->display(); ?>
		</form>
	<?php endif; ?>


</div>
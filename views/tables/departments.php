<div class="wrap">

	<?php $tab = ( isset( $_REQUEST[ 'tab' ] ) && ! empty( $_REQUEST[ 'tab' ] ) ) ? $_REQUEST[ 'tab' ] : ''; ?>

	<h1 class="wp-heading-inline"><?php echo get_admin_page_title() ?></h1>


	<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Вторичное меню', IBC_TEXTDOMAIN ); ?>">
		<a href="<?php echo add_query_arg( array( 'page' => 'departments' ), admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( empty( $tab ) ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Список', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php echo add_query_arg( array( 'page' => 'departments', 'tab' => 'add' ), admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( $tab == 'edit' || $tab == 'add' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Добавить / редактировать', IBC_TEXTDOMAIN ); ?>
		</a>
	</nav>


	<?php if ( $tab == 'edit' || $tab == 'add' ) : ?>
		<?php
			$department_id = __return_empty_string();
			$name = __return_empty_string();
			$parent = 0;
			if ( isset( $_REQUEST[ 'department_id' ] ) ) {
				$department = $this->get_department( $_REQUEST[ 'department_id' ] );
				if ( is_object( $department ) ) {
					$department_id = $department->id;
					$name = $department->name;
					$parent = $department->parent;
				}
			}
			
		?>
		<form id="department-form-edit">
			<input type="hidden" name="department_id" value="<?php echo $department_id; ?>">
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<figure>
						<img src="<?php echo IBC_ASSETS . 'images/departments.svg' ?>" style="display: block; width: 100%;">
					</figure>
				</div>
				<div class="col-xs-12 col-sm-6 first-sm">
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="name"><?php _e( 'Название', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="name" type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php esc_attr_e( 'Название', IBC_TEXTDOMAIN ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="parent"><?php _e( 'Родительское подразделение', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<?php
								$this->wp_dropdown_departments( array(
									'name'     => 'parent',
									'selected' => $parent,
									'exclude'  => $department_id,
									'parent'   => 0,
									'style'    => 'display: block; width: 100%;'
								) );
							?>
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-6">
							<?php submit_button( __( 'Сохранить', IBC_TEXTDOMAIN ) ); ?>
						</div>
					</div>
				</div>
			</div>
		</form>
		<script>
			jQuery( document ).ready( function () {
				var $form = jQuery( '#department-form-edit' );
				$form.submit( function ( event ) {
					event.preventDefault();
					jQuery.ajax( {
						type: 'GET',
						url: ajaxurl,
						data: {
							action: '<?php echo $action; ?>',
							security: '<?php echo $nonce; ?>',
							type: 'edit',
							request: $form.serialize(),
						},
						success: function ( answer ) {
							console.log( answer );
							if ( answer.success ) {
								window.location.reload( true );
							} else {
								alert( '<?php _e( 'Ошибка! Попробуйте позже (server)', IBC_TEXTDOMAIN ); ?>' );
							}
						},
						error: function ( answer ) {
							alert( '<?php _e( 'Ошибка! Попробуйте позже (client)', IBC_TEXTDOMAIN ); ?>' );
							console.log( answer );
						},
					} );
				} );
			} );
		</script>
	<?php else : ?>
		<form method="POST">
			<?php $GLOBALS[ 'Departments_List_Table' ]->display(); ?>
		</form>
	<?php endif; ?>
			
			
		<script>
			jQuery( document ).ready( function () {
				jQuery( '.department-action-delete[data-department]' ).click( function ( event ) {
					event.preventDefault();
					var $link = jQuery( this );
					var department_id = $link.attr( 'data-department' );
					jQuery.ajax( {
						type: 'GET',
						url: ajaxurl,
						data: {
							action: 'departments',
							security: '<?php echo $nonce; ?>',
							type: 'delete',
							request: department_id,
						},
						success: function ( answer ) {
							if ( answer.success ) {
								window.location.reload( true );
							} else {
								alert( '<?php _e( 'Ошибка! Попробуйте позже.', IBC_TEXTDOMAIN ); ?>' );	
							}
							console.log( answer );
						},
						error: function ( answer ) {
							alert( '<?php _e( 'Ошибка! Попробуйте позже.', IBC_TEXTDOMAIN ); ?>' );
							console.log( answer );
						},
					} );
				} );
			} );
		</script>



</div>
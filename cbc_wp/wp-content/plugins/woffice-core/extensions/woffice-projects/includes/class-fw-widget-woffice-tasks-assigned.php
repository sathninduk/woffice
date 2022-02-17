<?php 

class Widget_Woffice_Tasks_Assigned extends WP_Widget {

	/**
	 * @internal
	 */
	function __construct() {
		$this->projects = fw()->extensions->get( 'woffice-projects' );
		if ( is_null( $this->projects ) ) {
			return;
		}
		
		$widget_ops = array( 'description' => 'Woffice widget to display the user tasks.' );
		parent::__construct( false, __( '(Woffice) User tasks', 'woffice' ), $widget_ops );
	}
	/**
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
	
	
		$data = array(
			'before_widget' => $args['before_widget'],
			'after_widget'  => $args['after_widget'],
			'title'  		=> isset($instance['title']) ? $instance['title'] :  __( 'My Tasks', 'woffice'),
			'style_type'  => isset($instance['style_type']) ? $instance['style_type'] : 'style_1',
			'before_title'  => str_replace( 'class="', 'class="widget_assigned_tasks ', $args['before_title']),
			'after_title'   => $args['after_title'],
		);

		if(isset($instance['style_type']) && $instance['style_type'] == 'style_2'){
			echo fw_render_view($this->projects->locate_view_path( 'widget-assigned-layout-2' ), $data );
		} else {
			echo fw_render_view($this->projects->locate_view_path( 'widget-assigned' ), $data );
		}
		
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( 'My Tasks', 'woffice'), 'category' => 'all', 'current_user' => 0 ));

		?>
		<p>
            <label for="<?php echo esc_attr( $this->get_field_id('style_type') ); ?>"><?php _e('Layout Style:','woffice'); ?></label>
            <select class="widefat" name="<?php echo esc_attr( $this->get_field_name('style_type') ); ?>" id="<?php echo esc_attr( $this->get_field_id('style_type') ); ?>">
                <option value="style_1" <?php selected('style_1', (isset($instance['style_type']) ? $instance['style_type'] : null)); ?>><?php _e('Style 1','woffice'); ?></option>
				<option value="style_2" <?php selected('style_2', (isset($instance['style_type']) ? $instance['style_type'] : null)); ?>><?php _e('Style 2`', 'woffice'); ?></option>
            </select>
        </p>
		<?php if(isset($instance['style_type']) && $instance['style_type'] == 'style_2'){?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'woffice' ); ?> </label>
				<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
					value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat"
					id="<?php esc_attr( $this->get_field_id( 'title' ) ); ?>"/>
			</p>
	<?php
		}
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}

function fw_ext_woffice_projects_assigned_register_widget() {
	register_widget( 'Widget_Woffice_Tasks_Assigned' );
}
add_action( 'widgets_init', 'fw_ext_woffice_projects_assigned_register_widget' );


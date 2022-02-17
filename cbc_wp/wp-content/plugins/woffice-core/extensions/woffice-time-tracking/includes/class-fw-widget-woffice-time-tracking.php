<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

class Widget_Woffice_Time_Tracking extends WP_Widget {

	/**
	 * @internal
	 */
	function __construct() {

		$this->extension = fw()->extensions->get( 'woffice-time-tracking' );

		if ( is_null( $this->extension ) ) {
			return;
		}
		
		$widget_ops = array( 'description' => __('Woffice widget to display the time tracker. Options can be found in the Extension\'s settings.','woffice') );
		parent::__construct( false, __( '(Woffice) Time Tracking', 'woffice' ), $widget_ops );
	}
	/**
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		
		$data = array(
			'before_widget' => $args['before_widget'],
			'after_widget'  => $args['after_widget'],
			'before_title'  => str_replace( 'class="', 'class="widget-woffice-time-tracking ', $args['before_title']),
			'after_title'   => $args['after_title'],

			'title' => $instance['title'],
			'description' => $instance['description'],
		);

		echo fw_render_view($this->extension->locate_view_path( 'widget' ), $data );
	}

	function form ($instance) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'category' => 'all', 'current_user' => 0 ));

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'woffice' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			       value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat"
			       id="<?php esc_attr( $this->get_field_id( 'title' ) ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php _e( 'Description:', 'woffice' ); ?></label>
			<textarea type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"
			      id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"
		          class="widefat"
			><?php echo esc_attr( (isset($instance['description']) ? $instance['description'] : null) ); ?></textarea>

		</p>
		<?php

	}

	function update( $new_instance, $old_instance ) {

		//$instance = wp_parse_args( (array) $new_instance, $old_instance );
		return $new_instance;

	}
}

function fw_ext_woffice_time_tracking_register_widget() {
	register_widget( 'Widget_Woffice_Time_Tracking' );
}
add_action( 'widgets_init', 'fw_ext_woffice_time_tracking_register_widget' );


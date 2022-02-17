<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */
 
/* Roles array ready for options */
global $wp_roles;
$tt_roles = array();
foreach ($wp_roles->roles as $key=>$value){
$tt_roles[$key] = $value['name']; }
$tt_roles_tmp = array('nope' => __("Everyone","woffice")) + $tt_roles;
/* End */

/**
 * Filter to customize the default post options
 *
 * @param array
 */
$options = apply_filters('woffice_options_post', array(
	'post-box' => array(
		'title'   => __( 'Post Options', 'woffice' ),
		'type'    => 'box',
		'options' => array(
			'post_top_featured'    => array(
				'label'       => __( 'Top featured image (optional)', 'woffice' ),
				'desc'       => __( 'Change the image behind the page title, otherwise we are going to use the default one.', 'woffice' ),
				'type'        => 'upload',
				'images_only' => true
			),
			'exclude_roles' => array(
			    'type'  => 'select-multiple',
			    'label' => __('Exclude Roles', 'woffice'),
				'help'  => __('Help tip : Hold Ctrl to select multiple roles.', 'woffice'),
			    'desc'  => __('The excluded roles will not be able to see that blog post, they will receive an error message instead. ', 'woffice'),
			    'choices' => $tt_roles_tmp
			),
			'featured_height' => array(
				'label' => __( 'Featured image height', 'woffice' ),
				'type'  => 'short-text',
				'value' => '250',
				'desc' => __('Only a number please','woffice'),
			),
			'everyone_edit'    => array(
				'label'       => __( 'Everyone can edit ?', 'woffice' ),
				'desc'       => __( 'If this is checked, all loggedin members with editing permissions on posts will be able to edit this post, otherwise only the author of the post and the administrators will be able to do it (This affect only the frontend).', 'woffice' ),
				'type'        => 'checkbox',
				'value' => true
			),
		),
	),
));

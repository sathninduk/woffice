<?php if ( ! defined( 'FW' ) ) {
    die( 'Forbidden' );
}

/* Roles array ready for options */
global $wp_roles;
    $tt_roles_tmp = array();
foreach ($wp_roles->roles as $key=>$value){
    $tt_roles_tmp[$key] = $value['name'];
}

/* End */

/* Admin user array ready for options */
$users = get_users( array('role' => 'administrator') );
$user_list = array();

foreach ( $users as $user ) {
	$user_list[$user->ID] = ucfirst($user->data->user_nicename);
}

/* End */

$options = array(
    'permissions' => array(
        'title'   => __( 'Permissions', 'woffice' ),
        'type'    => 'tab',
        'options' => array(
            'general_permissions-box' => array(
                'title'   => __( 'General Options', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'public'    => array(
                        'label' => __( 'Do you want to make this website public ?', 'woffice' ),
                        'desc'  => __( 'As an Intranet, every visitor have to login to access to the content and the wbesite, with this option your Website will be free to be reached by everyone...', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'yep',
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'nope',
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => 'nope',
                    ),
                    'excluded_pages'    => array(
                        'label' => __( 'Excluded pages', 'woffice' ),
                        'desc'  => __( 'If the website is private you can still make some pages public with this option. That means that the pages selected will not be affected by the redirection to the login page. For Buddypress pages please see : Buddypress sections', 'woffice' ),
                        'type'         => 'multi-select',
                        'population' => 'posts',
                        'source' => 'page',
                        'prepopulation' => false
                    ),
                    'products_public'    => array(
                        'label' => __( 'Woocomerce products excluded ?', 'woffice' ),
                        'desc'  => __( 'IF your website is private and you want your WooCommerce products to be public, select YEP.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'yep',
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'nope',
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => 'nope',
                    ),
                    'feeds_private'    => array(
                        'label' => __( 'Do you want to make the feeds private?', 'woffice' ),
                        'desc'  => __( 'Some feeds of your site make still be available also if you make your site or some pages private. If you enable this option, all feeds will be turned off.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => true,
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => false,
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => true,
                    ),
                    'frontend_state' => array(
                        'label'   => __( 'Post Status on frontend creation', 'woffice' ),
                        'desc'  => __( 'When the post is submitted, you can choose if it is directly published or need you approval first.', 'woffice' ),
                        'type'    => 'select',
                        'choices' => array(
                            'draft' => __('Draft', 'woffice'),
                            'publish' => __('Publish', 'woffice'),
                            'pending' => __('Pending', 'woffice')
                        ),
                        'value' => 'publish'
                    ),
                    'privacy_post' => array(
	                    'label'   => __( 'Posts Privacy', 'woffice' ),
	                    'desc'  => __( 'The default value is the one set above for the whole site. Regarding single items.', 'woffice' ),
	                    'type'    => 'select',
	                    'choices' => array(
		                    'default' => __('Default', 'woffice'),
		                    'public' => __('Public', 'woffice'),
		                    'private' => __('Private', 'woffice')
	                    ),
	                    'value' => 'default'
                    ),
                    'privacy_project' => array(
	                    'label'   => __( 'Projects Privacy', 'woffice' ),
	                    'desc'  => __( 'The default value is the one set above for the whole site. Regarding single items.', 'woffice' ),
	                    'type'    => 'select',
	                    'choices' => array(
		                    'default' => __('Default', 'woffice'),
		                    'public' => __('Public', 'woffice'),
		                    'private' => __('Private', 'woffice')
	                    ),
	                    'value' => 'default'
                    ),
                    'privacy_wiki' => array(
	                    'label'   => __( 'Wiki articles Privacy', 'woffice' ),
	                    'desc'  => __( 'The default value is the one set above for the whole site. Regarding single items.', 'woffice' ),
	                    'type'    => 'select',
	                    'choices' => array(
		                    'default' => __('Default', 'woffice'),
		                    'public' => __('Public', 'woffice'),
		                    'private' => __('Private', 'woffice')
	                    ),
	                    'value' => 'default'
                    ),
                    'privacy_directory' => array(
	                    'label'   => __( 'Directory items Privacy', 'woffice' ),
	                    'desc'  => __( 'The default value is the one set above for the whole site. Regarding single items.', 'woffice' ),
	                    'type'    => 'select',
	                    'choices' => array(
		                    'default' => __('Default', 'woffice'),
		                    'public' => __('Public', 'woffice'),
		                    'private' => __('Private', 'woffice')
	                    ),
	                    'value' => 'default'
                    ),
                ),
            ),
            'buddy_roles' => array(
                'title'   => __( 'Buddypress Pages Options', 'woffice' ),
                'type'  => 'box',
                'options' => array(
                    'buddy_members_state'    => array(
                        'label' => __( 'Members restriction (redirection)', 'woffice' ),
                        'desc'  => __( 'Members directory & profile pages. If it is set as "private", the non-logged users will be redirected to the login page.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'private',
                            'label' => __( 'Private', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'public',
                            'label' => __( 'Public', 'woffice' )
                        ),
                        'value'        => 'private',
                    ),
                    'buddy_groups_state'    => array(
                        'label' => __( 'Groups restriction (redirection)', 'woffice' ),
                        'desc'  => __( 'Groups directory & single group pages. If it is set as "private", the non-logged users will be redirected to the login page.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'private',
                            'label' => __( 'Private', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'public',
                            'label' => __( 'Public', 'woffice' )
                        ),
                        'value'        => 'private',
                    ),
                    'buddy_activity_state'    => array(
                        'label' => __( 'Activity restriction (redirection)', 'woffice' ),
                        'desc'  => __( 'Activity page. If it is set as "private", the non-logged users will be redirected to the login page.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'private',
                            'label' => __( 'Private', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'public',
                            'label' => __( 'Public', 'woffice' )
                        ),
                        'value'        => 'private',
                    ),
                    'buddy_members_excluded'    => array(
                        'label' => __( 'Members restriction (excluded roles)', 'woffice' ),
                        'desc'  => __( 'Do you want to exclude some roles from the members pages (directory & profiles) ?', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('nope')
                    ),
                    'buddy_groups_excluded'    => array(
                        'label' => __( 'Groups restriction (excluded roles)', 'woffice' ),
                        'desc'  => __( 'Do you want to exclude some roles from the groups pages (directory & single) ?', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('nope')
                    ),
                    'buddy_activity_excluded'    => array(
                        'label' => __( 'Activity restriction (excluded roles)', 'woffice' ),
                        'desc'  => __( 'Do you want to exclude some roles from the Activity pages ?', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('nope')
                    ),
                    'buddy_manage_signups'    => array(
	                    'label' => __( 'User Approval', 'woffice' ),
	                    'desc'  => __( 'Select admins who are able to manage signups.', 'woffice' ),
	                    'type'         => 'select-multiple',
	                    'choices'      => $user_list,
	                    'value'        => array('nope')
                    ),
                )
            ),
            'permissions-wiki-box' => array(
                'title'   => __( 'Wiki Options', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'wiki_create' => array(
                        'label' => __( 'Who can create a Wiki article ?', 'woffice' ),
                        'desc'  => __( 'It is only affecting a front end button on the main wiki page. The roles with post edit capabilities will still be able to create Wiki from the backend.', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('administrator'),
                    ),
                    'wiki_edit' => array(
                        'label' => __( 'Who can edit a Wiki article ?', 'woffice' ),
                        'desc'  => __( 'The roles selected here will be able to edit and delete all wiki posts, also if they are not the author of that post (This only affect the frontend).', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('administrator'),
                    ),
                    'override_wiki_by_caps'    => array(
                        'label' => __( 'Use meta capabilities by frontend', 'woffice' ),
                        'desc'  => __( 'If you enable this option, the settings above will be overrided by more specific meta capabilities of the post. You have to use the plugin User Role Editor (or others) to change them.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => true,
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => false,
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => false,
                    ),
                ),
            ),
            'permissions-projects-box' => array(
                'title'   => __( 'Projects Options', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'projects_create' => array(
                        'label' => __( 'Who can create a project article ?', 'woffice' ),
                        'desc'  => __( 'It is only affecting a front end button on the main projects page. The roles with post edit capabilities will still be able to create Projects from the backend.', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('administrator'),
                    ),
                    'projects_public'    => array(
                        'label' => __( 'Are the projects public by default?', 'woffice' ),
                        'desc'  => __( 'If enabled, every users can view the projects but only the members assigned to the projects can edit them (this option can be overrided in every single project).', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'yep',
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'nope',
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => 'nope',
                    ),
                ),
            ),
            'permissions-blog-box' => array(
                'title'   => __( 'Blog Posts Options', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'post_create' => array(
                        'label' => __( 'Who can create a Blog article ?', 'woffice' ),
                        'desc'  => __( 'It is only affecting a front end button on the main Blog page. The roles with post edit capabilities will still be able to create Blog article from the backend.', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('administrator'),
                    ),
                    'post_edit' => array(
                        'label' => __( 'Who can edit a Blog article ?', 'woffice' ),
                        'desc'  => __( 'The roles selected here will be able to edit and delete all blog posts, also if they are not the author of that post (This only affect the frontend).', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('administrator'),
                    ),
                    'override_post_by_caps'    => array(
                        'label' => __( 'Use meta capabilities by frontend', 'woffice' ),
                        'desc'  => __( 'If you enable this option, the settings above will be overrided by more specific meta capabilities of the post. You have to use the plugin User Role Editor (or others) to change them.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => true,
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => false,
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => false,
                    ),
                ),
            ),
            'permissions-directory-box' => array(
                'title'   => __( 'Directory Options', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'directory_create' => array(
                        'label' => __( 'Who can create a directory item ?', 'woffice' ),
                        'desc'  => __( 'It is only affecting a front end button on the main directory page. The roles with post edit capabilities will still be able to create an item from the backend.', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('administrator'),
                    ),
                ),
            ),
            'permissions-event-box' => array(
                'title'   => __( 'Event Options', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'event_create' => array(
                        'label' => __( 'Who can create an event ?', 'woffice' ),
                        'desc'  => __( 'It is only affecting a front end button on the main event page. The roles with post edit capabilities will still be able to create an item from the backend.', 'woffice' ),
                        'type'         => 'select-multiple',
                        'choices'      => $tt_roles_tmp,
                        'value'        => array('administrator'),
                    ),
                ),
            ),
        )
    )
);

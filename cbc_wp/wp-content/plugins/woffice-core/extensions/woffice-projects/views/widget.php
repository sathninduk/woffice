<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo $before_widget;

echo $title;

?>

<!-- WIDGET -->
<ul class="list-styled list-projects">

    <?php

    if (!is_user_logged_in()) {
        echo '<div class="woffice-poll-ajax-reply">';
        echo '<i class="fa fa-lock"></i><p>' . __("Sorry ! It is only for logged users.", "woffice") . '</p>';
        echo '</div>';
    } else {

    $project_query_args = array(
        'post_type' => 'project',
        'posts_per_page' => '-1'
    );

    $projects = new WP_query($project_query_args);
    $excluded = array();
    $hide_projects_archived = woffice_get_settings_option( 'hide_projects_completed', true );

    while ( $projects->have_posts() ) : $projects->the_post();

        $hide = ( $hide_projects_archived ) ? (bool)woffice_get_post_option( get_the_ID(), 'project_completed', false) : false;

        if(!woffice_is_user_allowed_projects() || $hide) {
            array_push($excluded, get_the_ID());
        }
    endwhile;

    // QUERY $tax
    $query_args = array(
        'post_type' => 'project',
        'post__not_in' => $excluded,
        'posts_per_page' => -1,
    );

    if (!empty($category) && $category !== "all") {
        $the_tax = array(array(
            'taxonomy' => 'project-category',
            'terms' => array($category),
            'field' => 'slug',
        ));
        $query_args['tax_query'] = $the_tax;
    }

    if (!empty($status) && $status !== 'all') {
	    $query_args['meta_key']     = 'fw_option:project_current_status';
	    $query_args['meta_compare'] = 'LIKE';

	    if ($status == 'archived') {
		    $query_args['meta_value'] = 'archived';
	    } else if ($status === 'done') {
		    $query_args['meta_value'] = 'done';
	    } else if ($status === 'in_progress') {
		    $query_args['meta_value'] = 'in_progress';
	    } else if ($status === 'in_review') {
		    $query_args['meta_value'] = 'in_review';
	    } else if ($status === 'planned') {
		    $query_args['meta_value'] = 'planned';
	    }
    }

    /**
     * Filter the query args for the filter "(Woffice) Recent Projects"
     *
     * @param array $query_args
     */
    $query_args = apply_filters('woffice_widget_recent_projects_query_args', $query_args);
    $widget_projects_query = new WP_Query( $query_args );

    /**
     * Filter the maximum number of projects to display in the widget "(Woffice) Recent Projects"
     *
     * @param int
     */
    $widget_projects_max = apply_filters('woffice_widget_recent_projects_max', 8);

    $number_projects = 0;

    $user_id = ((bool)$current_user) ? get_current_user_id() : false;

    while ($widget_projects_query->have_posts()) : $widget_projects_query->the_post();

        if ($number_projects === $widget_projects_max)
            break;

        if ($user_id) {
            $project_members = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'project_members') : '';

            if (!empty($project_members) && !in_array($user_id, $project_members)) {
                continue;
            }
        }

        echo'<li>';
            echo '<a href="'. get_the_permalink() .'" rel="bookmark">'. get_the_title() .'</a>';
            woffice_project_progressbar();
        echo '</li>';

        $number_projects++;

    endwhile;

    if ($number_projects == 0)
        esc_html_e("Sorry you don't have any project yet.","woffice");

    wp_reset_postdata();
    }
    ?>

</ul>
<?php  echo $after_widget ?>
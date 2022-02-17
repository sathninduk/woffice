<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo $before_widget;

echo $title;
?>
	<!-- WIDGET -->
	<ul class="list-styled list-wiki">
		<?php

        $query_args = array(
            'post_type' => 'wiki',
            'showposts' => $show,
            'post_status' => array( 'publish', 'draft' ),
        );

        if (!empty($category) && $category != "all") {
            $the_tax = array(array(
                'taxonomy' => 'wiki-category',
                'terms' => array($category),
                'field' => 'slug',
            ));
            $query_args['tax_query'] = $the_tax;
        }

        // GET WIKI POSTS
        $widget_wiki_query = new WP_Query($query_args);
        while($widget_wiki_query->have_posts()) : $widget_wiki_query->the_post();

            if (woffice_is_user_allowed_wiki()){
                $likes = woffice_get_wiki_likes(get_the_ID());
                $likes_display = (!empty($likes)) ? $likes : '';
                echo'<li class="is-'.get_post_status().' pt-2"><a href="'. get_the_permalink() .'" rel="bookmark" data-post-id="'.get_the_ID().'">'. get_the_title() . $likes_display.'</a></li>';
            }

        endwhile;
        wp_reset_postdata();
		?>
	</ul>
<?php echo $after_widget ?>
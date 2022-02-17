<?php
if( ! class_exists( 'Woffice_Wiki_Display_Manager' ) ) {
    /**
     * Class Woffice_Wiki_Display_Manager
     * This class handles the rendering of the Wiki articles
     * @since 2.3.1
     * @author Xtendify
     */
	class Woffice_Wiki_Display_Manager {

        /**
         * Option to Sort Wikis by like
         *
         * @var null
         */
		static $optionSortByLike = null;

        /**
         * If Sorted Wikis by like in Page
         *
         * @var null
         */
		protected $sortedByLikeInPage = null;

        /**
         * If Accordion enabled
         *
         * @var bool|null
         */
		protected $accordionEnabled = null;

        /**
         * Stores the excluded categories
         * @var null
         */
		protected $excludedCategories = null;

        /**
         * Current posts handler
         * @var null
         */
		protected $posts = null;

        /**
         * Grouped posts handler
         * @var null
         */
		protected $groupedPosts = null;

		/**
         * Parent page
		 * @var int
		 */
		protected $parent;

		/**
         * Article count limit
		 * @var int
		 */
		protected $wiki_article_count_limit;

        /**
         * Woffice_Wiki_Display_Manager constructor.
         *
         * @param int $parent
         */
		public function __construct( $parent = 0 ) {

			$this->parent = $parent;

			static::$optionSortByLike = ( is_null( static::$optionSortByLike ) ) ? ( function_exists( 'fw_get_db_settings_option' ) ) ? fw_get_db_settings_option( 'wiki_sortbylike' ) : '' : false;
			$this->sortedByLikeInPage = ( static::$optionSortByLike == 'yep' && isset( $_GET['sortby'] ) && $_GET['sortby'] == 'like' ) ? true : false;

			$enable_wiki_accordion = woffice_get_settings_option( 'enable_wiki_accordion' );
			$this->accordionEnabled = ( $enable_wiki_accordion == 'yep' ) ? true : false;

			$wiki_excluded_categories = woffice_get_settings_option( 'wiki_excluded_categories' );
			$this->excludedCategories = ( ! empty( $wiki_excluded_categories ) ) ? $wiki_excluded_categories : array();

			/**
			 * Filter the article limit count for categories
			 *
			 * @param int
			 */
			$this->wiki_article_count_limit = apply_filters('woffice_wiki_article_count_limit', 10);

			$args = array(
				'post_type' => 'wiki',
				'showposts' => '-1',
				'orderby' => 'post_title',
				'order' => 'ASC',
				'post_status' => array( 'publish' ),
				'woffice_check_wiki_permission' => true,
			);

			if (!empty($this->excludedCategories)) {
				$args['tax_query'] = array(
					'taxonomy' => 'wiki-category',
                    'field' => 'id',
                    'terms' => $this->excludedCategories,
                    'operator' => 'NOT IN'
				);
			}

			/**
			 * Filter the args for the Wiki query
			 *
			 * @param array
			 */
			$args = apply_filters('woffice_wiki_items_query_args', $args);

			$posts_query = new WP_Query($args);

			$this->posts = $posts_query->posts;
			$this->loadTaxonomiesInPost();

		}

		/**
		 * Display all the categories for the instanced class
		 */
		public function displayCategories( ) {

			$categories = $this->getPrimaryCategories( $this->parent );
            /**
             * Display each category
             */
			$html_begin_row = '<div class="row">';
			$html_end_row   = '</div>';
			$h              = 0;

			/**
			 * Filter `woffice_wiki_columns_number`
			 *
			 * @param int
			 *
			 * @return int - must be 1, 2, 3, 4
			 */
			$columns_number = apply_filters('woffice_wiki_columns_number', 2);
			$columns_class  = 'col-md-6';

			if ($columns_number === 1) {
				$columns_class = 'col-md-12';
			} else if ($columns_number === 3) {
				$columns_class = 'col-md-4';
			} else if ($columns_number === 4) {
				$columns_class = 'col-md-3';
			}

            /**
             * We loop through all categories
             */
			foreach ( $categories as $category ) {

                /**
                 * We only take parent categories here
                 */
				if ( $category->parent != 0 )
					continue;

				$n_elements = $this->getNumberOfElements( $category->term_id );

				$wiki_list = $this->formatWikiStrings( $this->getGroupedPosts($category->term_id) );

                /**
                 * Create new rows
                 */
				if ( $h > 0 && ! is_float( $h / $columns_number ) ) {
					echo $html_end_row;
				}
				if ( ! is_float( $h / $columns_number ) ) {
					print $html_begin_row;
				}

                /**
                 * Create the column
                 */
				echo '<div class="'. $columns_class .' wiki-category-container">';

                /**
                 * Column Title
                 */
				if ( $n_elements > 0 ) {
					$category_title = apply_filters( 'woffice_wiki_category_title', '<i class="fa fa-folder text-light"></i> ' . esc_html( $category->name ) . ' <span class="wiki-category-count">(' . $n_elements . ')</span>', $category->name, $n_elements );
					echo '<div class="heading"><h2><a href="' . get_term_link( $category->slug, 'wiki-category' ) . '" class="text-body font-weight-bold">' . $category_title . '</a></h2></div>';
				}
                /**
                 * List of posts
                 */
				$accordion = ( $this->accordionEnabled ) ? 'collapsed-wiki' : '';
				echo '<ul class="list-styled list-wiki wiki-category-container ' . $accordion . '">';

                /**
                 * We display subcategories first
                 */
				$this->displaySubcategories( $category->term_id );

				 /**
                 * Display its elements
                 */
				$this->displaySingleElements( $wiki_list );

				wp_reset_postdata();

				echo '</ul>';

                if (!is_tax() && $n_elements > $this->wiki_article_count_limit)
					echo '<a href="'. esc_url(get_term_link( $category->slug, 'wiki-category' )) . '">' . esc_html__('See All', 'woffice') . '</a>';
				
				/**
                 * Let close the column
                 */
				echo '</div>';
				$h ++;				

			}

            /**
             * Closing the row
             */
			if ( $h > 0 ) {
				echo $html_end_row;
			}

		}

		/**
		 * Display recursively all the subcategories of a given tem_id parent
		 *
		 * @param $parent
		 */
		public function displaySubcategories( $parent ) {

			$categories = $this->getSubcategories( $parent );

			if( empty($categories) )
				return 0;

			foreach ($categories as $category_child) {

				if( $category_child->parent != $parent)
					continue;

				$n_elements = $this->getNumberOfElements( $category_child->term_id );

				$grouped_posts = $this->getGroupedPosts($category_child->term_id);
				$wiki_list = $this->formatWikiStrings( $grouped_posts );

				if($n_elements == 0)
					return;

                /**
                 * Create the list
                 */
				if ($this->accordionEnabled) {
					$wiki_subcategory_title = '<li class="sub-category"><span data-toggle="collapse" data-target="#' . $category_child->slug . '" expanded="false" aria-controls="' . $category_child->slug . '">' . esc_html($category_child->name) . '<span class="wiki-category-count">(' . $n_elements . ')</span></span>';
					echo apply_filters('woffice_wiki_subcategory_title', $wiki_subcategory_title, $category_child->name, $n_elements, $category_child->slug);
					echo '<ul id="' . $category_child->slug . '" class="list-styled list-wiki collapse" aria-expanded="false">';
				} else {
					echo '<li class="sub-category"><span>' . esc_html($category_child->name) . ' <span class="wiki-category-count">(' . $n_elements . ')</span></span>
                    <ul class="list-styled list-wiki ">';
				}

                /**
                 * Loop through the subcategories
                 */
				$this->displaySubcategories( $category_child->term_id);

                /**
                 * Display its elements
                 */
				$this->displaySingleElements( $wiki_list );

				wp_reset_postdata();

                /**
                 * Close the list
                 */
				echo '</ul></li>';
				
			}

		}


		/**
		 * Return the HTML (as stringo r as an array of stings) of the posts passed by parameter
		 *
		 * @param $posts
		 *
		 * @return array
		 * @internal param WP_Post[] $wiki_query
		 *
		 */
		protected function formatWikiStrings( $posts ) {

			$wiki_list = array(
				'array'      => array(),
				'string'     => '',
			);

			$wiki_article_count = 0;

			if(!is_array($posts))
			    return $wiki_list;

            /**
             * Get articles of the current category
             */
			foreach($posts as $post) {
				if (!isset($post->ID)) {
					continue;
				}

				$likes               = woffice_get_wiki_likes( $post->ID );
				$likes_display       = ( ! empty( $likes ) ) ? $likes : '';
				$featured_wiki       = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option( $post->ID, 'featured_wiki' ) : '';
				$featured_wiki_class = ( $featured_wiki ) ? 'featured' : '';

				if (!is_tax() && $wiki_article_count == $this->wiki_article_count_limit)
					break;

				if ( $this->sortedByLikeInPage ) {
					$like = woffice_get_string_between( $likes_display, '</i> ', '</span>' );
					array_push( $wiki_list['array'], array(
							'string' => '<li class="is-' . $post->post_status . '"><a href="' . get_the_permalink($post) . '" rel="bookmark" class="' . $featured_wiki_class . ' text-body" data-post-id="' . $post->ID . '">' . $post->post_title . $likes_display . '</a></li>',
							'likes'  => ( ! empty( $like ) ) ? (int) $like : 0
						)
					);
				} else {
					$wiki_list['string'] .= '<li class="is-' . $post->post_status . '"><a href="' . get_the_permalink($post) . '" rel="bookmark" class="' . $featured_wiki_class . ' text-body" data-post-id="' . $post->ID . '">' . $post->post_title . $likes_display . '</a></li>';
				}

				$wiki_article_count++;

			}

			return $wiki_list;

		}

		/**
		 * Displa the wikies previously formatted with the function formatWikiStrings
		 *
		 * @param $wiki_list
		 */
		protected function displaySingleElements( $wiki_list ) {

			$wiki_string = $wiki_list['string'];
			if ( $this->sortedByLikeInPage ) {
				$wiki_string = '';

				// Sort the wikis by like number
				usort( $wiki_list['array'], 'woffice_sort_objects_by_likes');

				foreach ( $wiki_list['array'] as $wiki ) {
					$wiki_string .= $wiki['string'];
				}
			}

			echo $wiki_string;

		}

		/**
		 * Get categories of the first level
		 *
		 * @param $include (If 0 includes all parent categories)
		 *
		 * @return array
		 */
		protected function getPrimaryCategories( $include) {

			$wiki_categories_args = apply_filters( 'woffice_wiki_get_primary_categories_args',  $this->getCategoriesArgs(0, $include) );

			return get_categories( $wiki_categories_args );

		}

		/**
		 * Get all subcategories of a given paernt
		 *
		 * @param $child_of
		 *
		 * @return array
		 */
		protected function getSubcategories( $child_of ) {

			$wiki_categories_args = apply_filters( 'woffice_wiki_get_primary_categories_args',  $this->getCategoriesArgs($child_of, 0) );

			return get_categories( $wiki_categories_args );

		}

		/**
		 * Set the args to retrieve the categories
		 *
		 * @param $child_of
		 * @param $include
		 *
		 * @return array
		 */
		protected function getCategoriesArgs( $child_of, $include) {

			/**
			 * Filter the args for the query of sub category wiki
			 *
			 * @param array
			 */
			return apply_filters('woffice_wiki_categories_query_args', array(
				'type'     => 'wiki',
				'orderby'  => 'name',
				'order'    => 'ASC',
				'number'   => '0',
				'taxonomy' => 'wiki-category',
				'include'  => $include,
				'child_of'  => $child_of,
				'hide_empty' => true,
				'exclude'  => $this->excludedCategories,
			));

		}

		/**
		 * Assign the right taxonomy to each post loaded
		 */
		protected function loadTaxonomiesInPost( ) {

			foreach ( $this->posts as &$post ) {
				$terms = get_the_terms( $post, 'wiki-category' );

				$post->taxonomy_id = array();

				if ( ! is_array( $terms ) )
					continue;

				$term_ids = array();
				foreach ( $terms as $term ) {
					array_push( $term_ids, $term->term_id );

				}
				//fw_print(get_the_terms( $post, 'wiki-category' ));
				$post->taxonomy_id = $term_ids;

			}

		}

		/**
         * Group posts by taxonomy
		 * @return array
		 */
		protected function groupPostsByTaxonomy() {

			$taxonomies = array();

			foreach ($this->posts as $post) {

				foreach($post->taxonomy_id as $term_ids) {
					if (!isset($taxonomies[$term_ids]))
						$taxonomies[$term_ids] = array();

					array_push($taxonomies[$term_ids], $post);
				}
			}

			return $taxonomies;

		}

		/**
		 * Return the grouped posts by all or a given term taxonomy
		 *
		 * @param int $term_id (if 0 returns all groups)
		 *
		 * @return array|mixed|null
		 */
		protected function getGroupedPosts($term_id = 0) {
			if (is_null($this->groupedPosts))
				$this->groupedPosts = $this->groupPostsByTaxonomy();

			if ($term_id !== 0 && isset($this->groupedPosts[$term_id]))
				return $this->groupedPosts[$term_id];

			return $this->groupedPosts;

		}

		/**
		 * Get the number of posts (recursively) of a given term
		 *
		 * @param $term_id
		 *
		 * @return int
		 */
		protected function getNumberOfElements( $term_id) {
			$n_elements = 0;
			$children = $this->getSubcategories( $term_id);

			foreach($children as $child_term) {
				$grouped_posts = $this->getGroupedPosts($child_term->term_id);
				$n_elements += count($grouped_posts);
			}

			$n_elements += count((array)$this->getGroupedPosts($term_id));

			return $n_elements;
		}

	}
}
<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) :
			the_post();

			do_action( 'idea_single_post_before' );

		    ?>


            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php
                // do_action( 'idea_single_post_top' );

                /**
                 * Functions hooked into storefront_single_post add_action
                 *
                 * @hooked storefront_post_header          - 10
                 * @hooked storefront_post_content         - 30
                 */
                do_action( 'idea_single_post' );

                /**
                 * Functions hooked in to storefront_single_post_bottom action
                 *
                 * @hooked storefront_post_nav         - 10
                 * @hooked storefront_display_comments - 20
                 */
                do_action( 'idea_single_post_bottom' );
                ?>

            </article><!-- #post-## -->

            <?php

			do_action( 'idea_single_post_after' );

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'idea_sidebar' );
get_footer();

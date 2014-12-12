<?php
/**
 * The template for displaying category pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */

get_header(); 
$author = get_query_var( 'author' );
?>
    <div id="primary" class="site-content span9">
        <div class="entry-author">
            <div class="author-info">
                <div class="author-avatar">
                <?php  
                    if ( function_exists( 'coauthors_posts_links' ) && get_the_post_thumbnail( get_the_author_meta( 'ID', $author ) ) ) {
                        echo get_the_post_thumbnail( get_the_author_meta( 'ID', $author ), array(90,90) );
                    } else {
                        echo get_avatar( $author, 90 );
                    }
                ?>
                </div><!-- .author-avatar -->
                <div class="author-description">
                    <h2><a href="<?php echo esc_url( get_author_posts_url( $author ) ); ?>" rel="author"><?php echo get_the_author_meta( 'display_name', $author ); ?></a></h2>

                    <p class="description"><?php the_author_meta( 'description', $author ); ?></p>
                    
                    <?php if ( function_exists( 'coauthors_posts_links' ) && get_the_author_meta( 'website', $author ) ) : ?>
                        <p class="user-url">
                            <span><?php _e('Website URL:','dw_focus') ?></span>
                            <a href="<?php the_author_meta( 'website', $author ); ?>" title="<?php the_author_meta( 'website', $author ); ?>">
                                <?php the_author_meta( 'website', $author ); ?>
                            </a> 
                        </p>
                    <?php elseif ( get_the_author_meta( 'user_url', $author ) ) : ?>
                        <p class="user-url">
                            <span><?php _e('Website URL:','dw_focus') ?></span>
                            <a href="<?php the_author_meta( 'user_url', $author ); ?>" title="<?php the_author_meta( 'user_url', $author ); ?>">
                                <?php the_author_meta( 'user_url', $author ); ?>
                            </a> 
                        </p>
                    <?php endif; ?>
    
                    <?php if ( get_the_author_meta( 'aim', $author ) ) : ?>
                    <p class="aim">
                        <span><?php _e('AIM:','dw_focus') ?></span> 
                        <a href="aim:goaim?screenname=<?php the_author_meta( 'aim', $author ); ?>" title="<?php the_author_meta( 'aim', $author ); ?>"><?php the_author_meta( 'aim', $author ); ?></a>   
                    </p>
                    <?php endif; ?>
                    
                    <?php if ( function_exists( 'coauthors_posts_links' ) && get_the_author_meta( 'yahooim', $author ) ) : ?>

                        <p class="yahoo">
                            <span><?php _e('Yahoo:','dw_focus') ?></span> 
                            <a href="ymsgr:sendim?<?php the_author_meta( 'yahooim', $author ); ?>" title="<?php the_author_meta( 'yahooim', $author ); ?>"><?php the_author_meta( 'yahooim', $author ); ?></a>   
                        </p>  
                    <?php elseif ( get_the_author_meta( 'yim', $author ) ) : ?>
                        <p class="yahoo">
                            <span><?php _e('Yahoo:','dw_focus') ?></span> 
                            <a href="ymsgr:sendim?<?php the_author_meta( 'yim', $author ); ?>" title="<?php the_author_meta( 'yim', $author ); ?>"><?php the_author_meta( 'yim', $author ); ?></a>   
                        </p>
                    <?php endif; ?>
                </div><!-- .author-description -->
            </div><!-- .author-info -->
        </div><!-- .entry-author -->

    	<div class="content-bar row-fluid">
           <h1 class="page-title author"><?php printf( __( 'Author Archives: %s', 'dw_focus' ), '<span class="vcard">'.get_the_author_meta( 'display_name', $author ) ); ?></h1>
            </h1>

            <div class="post-layout">
                <a class="layout-list <?php dw_active(apply_filters( 'cat_display_filter', dw_get_option('cat_display')),'list'); ?>" href="#"><i class="icon-th-list"></i></a>
                <a class="layout-grid <?php dw_active(apply_filters( 'cat_display_filter', dw_get_option('cat_display')),'grid'); ?>" href="#"><i class="icon-th"></i></a>
            </div>
        </div>

        <?php 
            $no_results = '';
            if( ! have_posts() ) {
                $no_results = 'no-results';
            }

        ?>
        <div class="content-inner <?php echo $no_results; echo 'layout-'.apply_filters( 'cat_display_filter', dw_get_option('cat_display'));?>">
		<?php if ( have_posts() ) : ?>
            <?php global $archive_i; $archive_i = 1; ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part('content', 'archive'); ?>
                <?php $archive_i++; ?>
			<?php endwhile; ?>
		<?php else : ?>

			<?php get_template_part( 'no-results', 'archive' ); ?>

		<?php endif; ?>
        </div>
        <?php 
            dw_focus_pagenavi();
            wp_reset_query();
        ?>
	</div>
<?php get_sidebar( ); ?>
<?php get_footer(); ?>
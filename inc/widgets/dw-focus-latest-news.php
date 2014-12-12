<?php  
class dw_focus_lastest_news extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'dw_focus_lastest_news', // Base ID
            __('DW Focus: Lastest news', 'dw_focus'), // Name
            array( 'description' => __( 'Display latest news', 'dw_focus' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $meta = isset($instance['meta']) ? (bool) $instance['meta'] : false;
        $date = isset($instance['date']) ? (bool) $instance['date'] : false;
        $author = isset($instance['author']) ? (bool) $instance['author'] : false;
        $cat = isset($instance['cat']) ? (bool) $instance['cat'] : false;
        $tag = isset($instance['tag']) ? (bool) $instance['tag'] : false;

        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];
            ?>
            <?php if ( have_posts() ) : ?>
                <div class="content-inner">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php
                            if( has_post_thumbnail( get_the_ID() ) ) {
                                $class = ' has-thumbnail';
                            }
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class($class); ?> >
                            <?php if( has_post_thumbnail() ) : ?>
                            <div class="entry-thumbnail">
                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'dw_focus' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">

                                    <?php if ( has_post_format('Video') || has_post_format('Audio') || has_post_format('Gallery')) : ?>
                                        <?php echo dw_focus_post_format_icons(); ?>    
                                    <?php endif ?>

                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            <div class="post-inner">
                                <header class="entry-header">
                                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'dw_focus' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

                                    <?php if ( $meta ): ?>
                                        <div class="entry-meta entry-meta-top">
                                            <?php if ( $author ): ?>
                                            <span class="author">
                                                <?php 
                                                    if ( function_exists( 'coauthors_posts_links' ) ) {
                                                        coauthors_posts_links();
                                                    } else {
                                                        the_author_posts_link();
                                                    }
                                                ?>
                                            </span>

                                            <?php endif ?>

                                            <?php 
                                                if ( ($author && $date) || ($author && $cat) ) {
                                                    _e(' - ','dw-focus'); 
                                                }
                                            ?>

                                            <?php if ( $date ) : ?>
                                                <?php echo dw_human_time(); ?>
                                            <?php endif; ?>

                                            <?php 
                                                if ($cat && $date) {
                                                    _e(' - ','dw-focus'); 
                                                }
                                            ?>
                                            
                                            <?php if ( $cat ): ?>
                                                <?php
                                                    $categories_list = get_the_category_list( __( ', ', 'dw_focus' ) );
                                                    if ( $categories_list && dw_focus_categorized_blog() ) :
                                                ?>

                                                <span class="cat-links">
                                                    <?php printf( '%1$s', $categories_list ); ?>
                                                </span>

                                                <?php endif; ?>
                                            <?php endif ?>
                                        </div>
                                    <?php endif ?>
                                </header>

                                <div class="entry-content">
                                    <?php the_excerpt(); ?>
                                </div><!-- .entry-content -->
                                
                                <?php if ( $tag ): ?>
                                    
                                <footer class="entry-meta entry-meta-bottom">
                                    <?php
                                        /* translators: used between list items, there is a space after the comma */
                                        $tags_list = get_the_tag_list( '', __( ', ', 'dw_focus' ) );
                                        if ( $tags_list ) :
                                    ?>
                                    <span class="tags-links">
                                        <?php printf( __( 'Tags: %1$s', 'dw_focus' ), $tags_list ); ?>
                                    </span>
                                    <?php endif; // End if $tags_list ?>
                                
                                </footer>
                                <?php endif ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php dw_focus_pagenavi(); ?>
            <?php else : ?>
                <?php get_template_part( 'no-results', 'archive' ); ?>
            <?php endif; ?>
            <?php
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'Lastest News', 'dw_focus' );
        }

        $meta = isset($instance['meta']) ? (bool) $instance['meta'] : false;
        $date = isset($instance['date']) ? (bool) $instance['date'] : false;
        $author = isset($instance['author']) ? (bool) $instance['author'] : false;
        $cat = isset($instance['cat']) ? (bool) $instance['cat'] : false;
        $tag = isset($instance['tag']) ? (bool) $instance['tag'] : false;

        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','dw_focus' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <!-- Meta info -->
        <div class="meta-info">
            <p>
                <label for="<?php echo $this->get_field_id('meta'); ?>" ><input type="checkbox" name="<?php echo $this->get_field_name('meta') ?>" id="<?php echo $this->get_field_id('meta'); ?>" <?php checked(true, $meta); ?> class="recent-post-meta-info" >&nbsp;&nbsp;<?php  _e('Show the meta infomation of post','dw-focus') ?> </label>
            </p>
            <p> --
                <label for="<?php echo $this->get_field_id('date'); ?>" ><input type="checkbox" <?php disabled( false,  $meta ); ?> name="<?php echo $this->get_field_name('date') ?>" id="<?php echo $this->get_field_id('date'); ?>" <?php checked(true, $date); ?> class="submeta-info" >&nbsp;&nbsp;<?php  _e('Show the date of post','dw-focus') ?> </label>
            </p>
            <p> --
                <label for="<?php echo $this->get_field_id('author'); ?>" ><input type="checkbox" <?php disabled( false, $meta ); ?> name="<?php echo $this->get_field_name('author') ?>" id="<?php echo $this->get_field_id('author'); ?>" <?php checked(true, $author); ?> class="submeta-info" >&nbsp;&nbsp;<?php  _e('Show the author info','dw-focus') ?> </label>
            </p>
            <p> --
                <label for="<?php echo $this->get_field_id('cat'); ?>" ><input type="checkbox" <?php disabled( false,  $meta ); ?> name="<?php echo $this->get_field_name('cat') ?>" id="<?php echo $this->get_field_id('cat'); ?>" <?php checked(true, $cat); ?> class="submeta-info" >&nbsp;&nbsp;<?php  _e('Show the category info','dw-focus') ?> </label>
            </p>
            <p> --
                <label for="<?php echo $this->get_field_id('tag'); ?>" ><input type="checkbox" <?php disabled( false,  $meta ); ?> name="<?php echo $this->get_field_name('tag') ?>" id="<?php echo $this->get_field_id('tag'); ?>" <?php checked(true, $tag); ?> class="submeta-info" >&nbsp;&nbsp;<?php  _e('Show the tag info','dw-focus') ?> </label>
            </p>
        </div>
        <?php 
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['meta'] = $new_instance['meta'] ? true : false;
        $instance['date'] = $new_instance['date'] ? true : false;
        $instance['author'] = $new_instance['author'] ? true : false;
        $instance['cat'] = $new_instance['cat'] ? true : false;
        $instance['tag'] = $new_instance['tag'] ? true : false;

        return $instance;
    }

}

// register dw_focus_lastest_news widget
function register_dw_focus_lastest_news() {
    register_widget( 'dw_focus_lastest_news' );
}
add_action( 'widgets_init', 'register_dw_focus_lastest_news' );
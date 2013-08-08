    <div>
        <h3><?php printf ( __( "%s's Posts", 'bp_pop' ),  bp_get_displayed_user_fullname() ); ?></h3>
        <div id="item-body">
            <?php

            global $more;
            $more = 0;

            $args = array(
                'post_type'         => array( 'news' ),
                'paged'             => bp_pop_cur_page(),
                'tax_query'         => array(
                    array(
                        'taxonomy' => 'author',
                        'field' => 'slug',
                        'terms' => bp_get_displayed_user_username(),
                    )
                ),
                'post_status'       => 'publish',
                'orderby'           => 'date',
                'order'             => 'DESC',
                'posts_per_page'    => 20,
            );

            $wp_query = new WP_query( $args );

            ?>
            <ul>
                <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

                            <li><span><?php the_date(); ?></span> <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>

                <?php endwhile; ?>
            </ul>
        </div>
    </div>
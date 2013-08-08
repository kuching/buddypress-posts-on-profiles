<?php

/**
 * Defines BuddyPress Posts on profile plugin textdomain
 * po/mo translations go into /languages/ dir
 */
function bp_pop_load_textdomain() {

    load_plugin_textdomain( 'bp_pop', dir( __FILE__) );

}
add_action( 'plugins_loaded', 'bp_pop_load_textdomain' );


/**
 * Defines the Posts on profile component slug
 * This will be used in URL slug and in file template names
 *
 * @todo perhaps allow the user to select this through an admin option screen?
 */
define ( 'BP_POSTSONPROFILE_SLUG', 'posts' );


// @todo wrap everything below in a nice class and extend BP_Component
// @see http://codex.buddypress.org/theme-compatibility/how-to-enjoy-bp-theme-compat-in-plugins/


/**
 * Setup globals to display posts on BuddyPress user profiles
 */
function bp_pop_setup_globals() {

    global $bp, $wpdb;

    $bp->postsonprofile = new stdClass();

    $bp->postsonprofile->id                             = 'postsonprofile';
    $bp->postsonprofile->table_name                     = $wpdb->base_prefix . 'bp_postsonprofile';
    $bp->postsonprofile->format_notification_function   = 'bp_postsonprofile_format_notifications';
    $bp->postsonprofile->slug                           = BP_POSTSONPROFILE_SLUG;

    $bp->active_components[$bp->postsonprofile->slug]   = $bp->postsonprofile->id;

}
add_action( 'wp', 'bp_pop_setup_globals', 2 );


/**
 * BuddyPress Posts on Profile current page number helper function
 * Grabs the current page number from pretty URL (BuddyPress requires pretty permalinks anyway)
 * If it fails, returns 1
 *
 * @return  string  the current page number as it appears on URL
 */
function bp_pop_cur_page() {

    $pageURL = 'http';
    if ( is_ssl() )
        $pageURL .= "s";
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80")
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    else
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

    $urlEnd = substr($pageURL, -3);
    $pageFound = intval ( str_replace("/", "", $urlEnd) );
    $pageNum = $pageFound > 0 ? $pageFound : 1;
    return $pageNum;
}


/**
 * Setup navigation tab for BuddyPress Posts on profile page
 * Adds a menu nav item if the user has authored any published posts
 */
function bp_pop_setup_nav() {

    global $bp, $wpdb;
    $postsonprofile_link = $bp->loggedin_user->domain . $bp->postsonprofile->slug . '/';

    if ( !bp_is_blog_page() ) {

            // only show menu item if user is author, collaborator, editor, admin, etc.
            if ( user_can( $bp->displayed_user->id, 'edit_posts' ) ) :

                    bp_core_new_nav_item( array(
                            'name'              => __( 'Posts', 'bp_pop' ),
                            'slug'              => $bp->postsonprofile->slug,
                            'position'          => 80,
                            'screen_function'   => 'bp_pop_main_screen',
                        )
                    );

                    if ( (int) bp_pop_cur_page() > 0 ) {

                            bp_core_new_subnav_item( array(
                                    'name'              => __( 'Page', 'bp_pop' ) . bp_pop_cur_page(),
                                    'slug'              => 'page',
                                    'parent_slug'       => $bp->postsonprofile->slug,
                                    'parent_url'        => $postsonprofile_link,
                                    'screen_function'   => 'bp_pop_main_screen',
                                    'position'          => 20,
                                )
                            );

                            bp_core_new_subnav_item( array(
                                    'name'              => __( 'Page', 'bp_pop' ) . bp_pop_cur_page(),
                                    'slug'              => bp_pop_cur_page(),
                                    'parent_slug'       => $bp->postsonprofile->slug . '/page',
                                    'parent_url'        => $postsonprofile_link . '/page/',
                                    'screen_function'   => 'bp_pop_main_screen',
                                    'position'          => 20,
                                )
                            );
                    }

            endif;

    }
}
add_action( 'wp', 'bp_pop_setup_nav', 2 );


/**
 * Load template filter for BuddyPress Posts on profile pages
 * Tells BuddyPress where to look for templates to display new component
 */
function bp_pop_load_template_filter( $found_template, $templates ) {

    global $bp;
    if ( $bp->current_component != $bp->postsonprofile->slug )
        return $found_template;

    /**
     * Attempts to look for a template in child-parent theme order
     * and in common buddypress template directories order
     */
    foreach ( (array) $templates as $template ) {
        if ( file_exists( TEMPLATEPATH . '/buddypress/members/single/' . $template ) )
            $filtered_templates[] = STYLESHEETPATH . '/buddypress/members/single/' . $template;
        elseif ( file_exists( STYLESHEETPATH . '/community/members/single/' . $template ) )
            $filtered_templates[] = STYLESHEETPATH . '/community/members/single/' . $template;
        elseif ( file_exists( STYLESHEETPATH . '/members/single/' . $template ) )
            $filtered_templates[] = STYLESHEETPATH . '/members/single/' . $template;
        elseif ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $filtered_templates[] = STYLESHEETPATH . '/' . $template;
        elseif ( file_exists( TEMPLATEPATH . '/buddypress/members/single/' . $template ) )
            $filtered_templates[] = TEMPLATEPATH . '/buddypress/members/single/' . $template;
        elseif ( file_exists( TEMPLATEPATH . '/community/members/single/' . $template ) )
            $filtered_templates[] = TEMPLATEPATH . '/community/members/single/' . $template;
        elseif ( file_exists( TEMPLATEPATH . '/members/single/' . $template ) )
            $filtered_templates[] = TEMPLATEPATH . '/members/single/' . $template;
        elseif ( file_exists( TEMPLATEPATH . '/' . $template ) )
            $filtered_templates[] = TEMPLATEPATH . '/' . $template;
        else
            $filtered_templates[] = plugin_dir_path( __FILE__ ) . 'templates/' . $template;
    }

    $found_template = $filtered_templates[0];

    return apply_filters( 'bp_pop_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_pop_load_template_filter', 10, 2 );


/**
 * Loads the template and action for BuddyPress Posts on profile page
 * Tells BuddyPress to load the corresponding template when visiting the new component page
 */
function bp_pop_main_screen() {

    global $bp;
    do_action( 'bp_pop_main_screen' );

    bp_core_load_template( apply_filters( 'bp_pop_main_screen', '/' . $bp->postsonprofile->slug ) );
}
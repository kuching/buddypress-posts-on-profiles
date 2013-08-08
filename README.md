BuddyPress Posts on Profiles
----------------------------
####BuddyPress Plugin

Simple and tiny BuddyPress plugin to create a new members menu item and a corresponding template file to show posts published/authored by the displayed user.

Tested up to 1.81 and works. But doesn't use yet BuddyPress Theme Compatibility features introduced with 1.7. Right now the plugin will look for a 'posts.php' plugin in the `members/single` directory of the active WordPress theme, otherwise fallback to a rather simple template inside the plugin directory `templates`.

You can edit a copy of `templates/posts.php` file and place it into your current theme BuddyPress template path. Basically you can do any sort of query. You can use `bp_pop_cur_page()` function to get the paged variable to eventually pass into a query.
 

* @todo: wrap code in a class
* @todo: update to support BP 1.7+ Theme Compatibility
* @todo: add admin option screen to select eg. posts to query  

<?php

/*
* Add your own functions here. You can also copy some of the theme functions into this file. 
* Wordpress will use those functions instead of the original functions then.
*/

// Display Favicon on WordPress Backend 
add_action('admin_head', 'show_favicon');
function show_favicon() {
echo '<link href="//respondiv.com/favicon.png" rel="icon" type="image/x-icon">';
}
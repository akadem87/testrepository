<?php

/*
  Plugin Name: Fxnow related traiders blogs
  Plugin URI: none
  Description: Get related traiders blogs enities from Fxnow.ru
  Author: Krasu
  Version: 0.1
  Author URI: none
 */

include dirname(__FILE__) . '/../../wp-load.php';
$fxnowdb = new wpdb($fxnow_db['dbuser'], $fxnow_db['dbpwd'], $fxnow_db['dbname'], $fxnow_db['dbhost']);

$count = 5;
$entries = $fxnowdb->get_results("
            SELECT blogentry_title, blogentry_id, user_username
            FROM `se_blogentries`, `se_users`
            WHERE `se_blogentries`.blogentry_user_id = `se_users`.user_id
            ORDER BY blogentry_date DESC LIMIT " . intval($count)
);

$output = array();
$url = 'http://fxnow.ru/blog.php?user=%s&blogentry_id=%s';

foreach ($entries as $entry) {
    $entry->url = sprintf($url, $entry->user_username, $entry->blogentry_id);
    $entry->title = $entry->blogentry_title;
    $output[] = $entry;
}

$memcache->set(md5('fxnow_related_traiders_blogs_list'), $output, 0, 2592*1000);

?>

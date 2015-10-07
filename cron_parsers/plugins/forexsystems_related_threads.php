<?php

include dirname(__FILE__) . '/../../wp-load.php';
$forexsystemsdb = new wpdb($forexSystems_db['dbuser'], $forexSystems_db['dbpwd'], $forexSystems_db['dbname'], $forexSystems_db['dbhost']);

$count = 5;
$entries = $forexsystemsdb->get_results("
            SELECT title, threadid 
            FROM `vb_thread` WHERE visible = 1
            ORDER BY `vb_thread`.`lastpost` DESC
            LIMIT " . intval($count)
);

$output = array();
$url = 'http://forexsystems.ru/showthread.php?t=%s';

foreach ((array) $entries as $entry) {
    $entry->url = sprintf($url, $entry->threadid);
    $output[] = $entry;
}

$memcache->set(md5('forexsystems_related_threads_list'), $output, 0, 2592 * 1000);
?>

<?php

include dirname(__FILE__) . '/../../wp-load.php';

$siteratingdb = new wpdb(
                $username,
                $password,
                $database,
                $hostname
);

$count = 5;

$entries = $siteratingdb->get_results("
        SELECT url, sitename, hits FROM sites
        WHERE status=0 and admin_status!=2 and rank>0
		ORDER BY rank asc LIMIT " . intval($count)
);

$memcache->set(md5('siterating_top_sites_list'), $entries, 0, 2592 * 1000);
?>
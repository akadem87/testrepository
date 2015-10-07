<?php

include dirname(__FILE__) . '/../../wp-load.php';

$fxnowdb = new wpdb($fxnow_db['dbuser'], $fxnow_db['dbpwd'], $fxnow_db['dbname'], $fxnow_db['dbhost']);

$count = 5;
$entries = $fxnowdb->get_results("
        SELECT
            se_brokersmon_company.CompNameComp,
            se_brokersmon_company.CompCompNameAlias,
            se_brokersmon_company.CompLinkComp,
            se_brokersmon_company.CompImgComp,
            se_brokersmon_company.CompLinkImg,
            se_brokersmon_company.sort,
            se_brokersmon_company.IdComp as idcomp
		FROM
            se_brokersmon_company
		ORDER BY sort desc LIMIT " . intval($count)
);

$output = array();
$url = 'http://fxnow.ru/rate_brokerstoplist_view.php?id=%s&name=%s';
$url_img = 'http://fxnow.ru/images/brokersmon/%s';

foreach ($entries as $entry) {
    $entry->url = sprintf($url, $entry->idcomp, $entry->CompNameComp);
    $entry->img = sprintf($url_img, $entry->CompImgComp);
    $output[] = $entry;
}

$memcache->set(md5('fxnow_top_brokers_list'), $output, 0, 2592*1000);
?>
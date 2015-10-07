<?php

include dirname(__FILE__) . '/../../wp-load.php';

$banks = array(
    "Bank of Canada", "Bank of England", "European Central Bank", "Federal Reserve", "Swiss National Bank", "The Reserve Bank of Australia", "Bank of Japan"
);

$fortraderdb = new wpdb(get_option('fortrader_dbuser'), get_option('fortrader_dbpwd'), get_option('fortrader_dbname'), get_option('fortrader_dbhost'));
$entries = $fortraderdb->get_results('
           SELECT name, interest_rate as rate FROM ft_interestrates WHERE type = "bank" AND actual = 1 ORDER BY date_added DESC, name ASC
        ');

$output = array();
foreach ($entries as $entry) {
    $entry->img_name = str_replace(' ', '_', strtolower($entry->name));
    $output[] = $entry;
}

$memcache->set(md5('world_bank_rates_list'), $output, 0, 2592 * 1000);
?>

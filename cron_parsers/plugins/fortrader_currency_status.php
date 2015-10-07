<?php

include dirname(__FILE__) . '/../../wp-load.php';
$fortraderdb = new wpdb($fortraderDrupal_db['dbuser'], $fortraderDrupal_db['dbpwd'], $fortraderDrupal_db['dbname'], $fortraderDrupal_db['dbhost']);

$entries = $fortraderdb->get_results("
            SELECT * FROM `ft_course` WHERE symbol IN (1,2) ORDER BY `ft_course`.`dateadd` DESC LIMIT  4"
);

if ($entries) {
    $output = array();
    foreach ($entries as $entry) {
        if (!isset($output[$entry->symbol])) {
            $output[$entry->symbol] = $entry;
            $output['date'] = explode('-', $entry->dateadd);
            $output['date'] = mktime(0, 0, 0, $output['date'][1], $output['date'][2], $output['date'][0]);
        } else {
            $output[$entry->symbol]->oldvalue = round($output[$entry->symbol]->currprice - $entry->currprice, 2);
            if ($output[$entry->symbol]->oldvalue > 0)
                $output[$entry->symbol]->oldvalue = '+' . $output[$entry->symbol]->oldvalue;
        }
        $output[$entry->symbol]->currprice = round($output[$entry->symbol]->currprice, 2);
    }

    $memcache->set(md5('fortrader_currency_status_list'), $output, 0, 2592 * 1000);
}
?>

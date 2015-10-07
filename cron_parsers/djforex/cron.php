<?php

include '../../wp-load.php';
include('./simple_html_dom.php');

$start = microtime(true);

$replace = getReplacements();
$html_file = dirname(__FILE__) . '/tmp.html';
$hash_file = dirname(__FILE__) . '/hash';

$parse_url = 'http://djforex.ru/news/weekly.asp';
$allowable_tags = 'p,a,div,img,pre';
$result = array();
$category = array(3432);

error_reporting(E_ERROR);

file_put_contents($html_file, file_get_contents($parse_url));
$doc = new DOMDocument();
$doc->loadHTMLFile($html_file);

//echo '<pre>';

$elements = $doc->getElementsByTagName('td');
for ($i = 0; $i < $elements->length; $i++) {
    $tmp = $elements->item($i);
    if ($tmp->attributes->getNamedItem('class')->nodeValue == 'block_date') {
        $date = $tmp->nodeValue;
        break;
    }
}

$date = explode(' ', $date, 3);
$date[2] = intval($date[2]);

$date[1] = str_replace(array(
            'янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'
                ), array(
            'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sept', 'oct', 'nov', 'dec'
                ), substr($date[1], 0, 6));

$date = date('Y-m-d', strtotime(implode(' ', $date)));

$limit = (!isset($_GET['n']) || !intval($_GET['n'])) ? 0 : intval($_GET['n']);

$hash = (!$limit) ? file_get_contents($hash_file) : 1;

if ($hash == sha1($date))
    exit('No new data'."\n");

$ids = array();
$elements = $doc->getElementsByTagName('tr');

for ($i = 0; $i < $elements->length; $i++) {
    $tmp = $elements->item($i);
    if ($tmp->attributes->getNamedItem('class')->nodeValue == 'news_header_nrm') {
        $ids[] = $i;
    }
}

$n = 0;
foreach ($ids as $i) {
    $tr = $elements->item($i)->getElementsByTagName('td');
    $time = explode(':', $tr->item(0)->nodeValue);
    $data = array(
        'time' => $date . ' ' . (int) $time[0] . ':' . (int) $time[1],
        'title' => preg_replace($replace['from'], $replace['to'], strip_tags($tr->item(1)->nodeValue)),
    );

    $a = $tr->item(1)->getElementsByTagName('a')->item(0);

    if ($a->attributes->getNamedItem('class')->nodeValue != 'news_header_imp') {
        $data['source'] = $a->attributes->getNamedItem('href')->nodeValue;
        $data['text'] = preg_replace($replace['from'], $replace['to'], getFullDescription($data['source'], $tr->item(1)->nodeValue));
    }

    $data['uniqid'] = sha1($data['time'] . $data['title']);
    if (!checkUnique($data['uniqid'])) {
        $new_post = array(
            'post_type' => 'post',
            'post_content' => $data['text'],
            'post_title' => $data['title'],
            'post_name' => sanitize_title_with_translit($data['title']),
            'post_author' => 1,
            'post_date' => $data['time'],
            'post_date_gmt' => get_gmt_from_date($data['time']),
            'post_category' => $category,
            'post_status' => 'publish',
            'comment_status' => 'open',
        );

        $id = wp_insert_post($new_post);
        if ($id) {
            add_post_meta($id, 'djforex_id', $data['uniqid']);
            add_post_meta($id, 'djforex_source', 'http://djforex.ru' . $data['source']);

            $n++;

            if ($limit && $n >= $limit) {
                break;
            }

        } else {
            print "ERROR: Can't add entry\n";
        }
    }
}

function getFullDescription($url, $title = '') {
    global $allowable_tags;

    $html = file_get_dom('http://djforex.ru/' . $url);
    $innerHTML = str_replace('<h3>' . $title . '</h3>', '', iconv('cp1251', 'utf-8', $html->find('td.infoBlock_text', 1)->find('td', 0)->innertext));

    $html->clear();
    unset($html);
    
    return strip_tags($innerHTML, $allowable_tags);
}

function getReplacements() {
    $fh = fopen(dirname(__FILE__) . '/replacements', 'a+');
    $return = array('from' => array(), 'to' => array());
    while (!feof($fh)) {
        $tmp = explode(' | ', fgets($fh));
        $return['from'][] = '@' . preg_quote($tmp[0]) . '@ui';
        $return['to'][] = preg_replace('/\n/ui', '', $tmp[1]);
    }
    return $return;
}

function checkUnique($uniq_id) {
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare('SELECT COUNT(post_id) FROM ' . $wpdb->postmeta . ' WHERE meta_value=%s AND meta_key="djforex_id"', array($uniq_id)));
}

file_put_contents($hash_file, sha1($date));

echo 'Added: ' . $n . "\n";
echo 'Execution time: ' . (microtime(true) - $start) . "\n";
?>

<?php

define('DB_HOSTNAME', 'flex.nl.streamlink.lv'); // Адрес сервера баз данных
define('DB_USERNAME', 'fortrader'); // Имя пользователя базы данных
define('DB_USER_PASSWORD', 'KtwZSZJ'); // Пароль пользователя базы данных
define('DB_NAME', 'fortrader_drupal'); // Имя базы данных
define('DB_WORK_TABLE', 'ft_interestrates'); // Имя таблицы для сохранения информации

$html_file = dirname(__FILE__) . '/tmp.html';
$hash_file = dirname(__FILE__) . '/hash.ini';
$parse_url = 'http://www.fxstreet.com/fundamental/interest-rates-table/';
$result = array();

$link = mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_USER_PASSWORD) or die('No db connect');
mysql_select_db(DB_NAME, $link) or die('No db');

file_put_contents($html_file, file_get_contents($parse_url));
$doc = new DOMDocument();
$doc->loadHTMLFile($html_file);

//echo '<pre>';
$elements = $doc->getElementsByTagName('table');
$hash = file_get_contents($hash_file);

if (!is_null($elements)) {
    $j = 0;
    foreach ($elements as $element) {
        if (!substr_count($element->getAttribute('class'), 'it-sortable'))
            continue;

        $nodes = $element->lastChild->childNodes;
        $region = trim($element->firstChild->nodeValue);

        foreach ($nodes as $node) {
            $fields = $node->childNodes;
            $data = array();

            for ($i = 0; $i < $fields->length; $i++) {
                if ($fields->item($i)->nodeName != 'td') {
                    $node->removeChild($fields->item($i));
                }
            }

            for ($i = 0; $i < $fields->length; $i++) {
                $td = $fields->item($i);

                if ($j == 0) {
                    if ($i == 2 || $i == 1) {
                        $data[(($i == 1) ? 'next_meeting' : 'last_change')] = date('Y-m-d', strtotime($td->nodeValue));
                    } elseif ($i == 3) {
                        $data['interest_rate'] = number_format(floatval($td->nodeValue), 2);
                    } elseif ($i == 0) {
                        $data['name'] = mysql_real_escape_string(strip_tags($td->nodeValue));
                    }
                } else {
                    if ($i == 3) {
                        $data['last_change'] = date('Y-m-d', strtotime($td->nodeValue));
                    } elseif ($i == 1) {
                        $data['interest_rate'] = number_format(floatval($td->nodeValue), 2);
                    } elseif ($i == 0) {
                        $data['name'] = mysql_real_escape_string(strip_tags($td->nodeValue));
                        $data['region'] = $region;
                    }
                }
            }

            if (!empty($data))
                $result[$data['name']] = $data;
        }

        $j++;
    }

    $new_hash = md5(json_encode($result));
    file_put_contents($hash_file, $new_hash);

    if ($hash != $new_hash) {
        write_data_to_db($result);
    } else {
        print "No new data";
    }
}

function write_data_to_db($data = array()) {
    $check_sql = 'SELECT name, next_meeting, last_change, interest_rate FROM ' . DB_WORK_TABLE . ' WHERE name IN ("%s") ORDER BY date_added DESC, name ASC LIMIT %s';
    $exists = array();

    function extract_names($item) {
        return $item['name'];
    }

    $names = array_map('extract_names', $data);

    if (!empty($names)) {
        $check_query = mysql_query(sprintf($check_sql, implode('", "', $names), count($names)));
        while ($row = mysql_fetch_assoc($check_query)) {
            $exists[$row['name']] = $row;
        }

        $sql = prepare_array($data, $exists);
        if ($sql) {
            mysql_query($sql['update']);
            if (mysql_query($sql['insert'])) {
                $added_rows_count = mysql_affected_rows ();
                print "Added " . $added_rows_count . " rows";
            } else {
                print "MYSQL ERROR: " . mysql_error();
            }
        } else {
            print "No changes";
        }
    } else {
        print "Data was empty";
    }
}

function prepare_array($new, $old) {
    $insert_keys = array();
    foreach ($new as $key => $value) {
        if (isset($old[$key])) {
            if (array_diff_assoc($new[$key], $old[$key])) {
                $insert_keys[] = $key;
            }
        } else {
            $insert_keys[] = $key;
        }
    }

    if (!empty($insert_keys)) {
        foreach ($insert_keys as $value) {
            $sql_parts[] =
                    '(
                    "' . $new[$value]['name'] . '",
                    "' . $new[$value]['next_meeting'] . '",
                    "' . $new[$value]['last_change'] . '",
                    "' . $new[$value]['interest_rate'] . '",
                    "' . (isset($new[$value]['region']) ? $new[$value]['region'] : '' ) . '",
                    "' . date('Y-m-d H:i:s') . '",
                    "' . (!isset($new[$value]['region']) ? 1 : 2 ) . '",
                    "1"
                    )';
            $update_sql_parts[] = '"' . $new[$value]['name'] . '"';
        }
    }

    $sql['insert'] = (empty($sql_parts)) ? '' : 'INSERT INTO ' . DB_WORK_TABLE . ' (name, next_meeting, last_change, interest_rate, region, date_added, type, actual) VALUES ' . implode(',', $sql_parts);
    $sql['update'] = (empty($sql_parts)) ? '' : 'UPDATE ' . DB_WORK_TABLE . ' SET actual=0 WHERE name IN (' . implode(',', $update_sql_parts) . ');';

    return $sql;
}

//echo '</pre>';
?>

<?php

$db2 = mysql_connect('192.168.1.4', 'finfile', 'KtwZS');
mysql_select_db('finfile_new', $db2);

include dirname(__FILE__) . '/../wp-load.php';
##################################
## Началоц раздела конфигурации ##
##################################
// Имя лога ошибок.
// define('ERRORS_LOG', false);		 // false - логирование ошибок отключено
define('ERRORS_LOG', 'journal_stat_errors.txt');

##################################
##  Конец раздела конфигурации  ##
##################################
// Получаем последнюю вставленную строку в таблицу статистики или false
$last_row = $wpdb->get_row('SELECT * FROM `ft_journalstat` ORDER BY `id` DESC LIMIT 1', ARRAY_A);

if ($wpdb->last_error) {
    ErrorsLogging('Не удалось выполнить запрос в базу данных 1.');
}

/* вторая бд */

//Подключаемся к базе с статистикой
// Получаем сумму из таблицы downloads
$summ = mysql_query('SELECT SUM(`downloads`) as `totalviews` FROM `file_stats` WHERE file_id IN (' . extract_ids() . ') AND `date`="' . date('Y-m-d') . '"', $db2);
$summ = mysql_fetch_assoc($summ);

if (mysql_errno ()) {
    ErrorsLogging('Не удалось подключиться к базе данных finfile_new.');
}
mysql_close($db2);

/* конец вторая бд */



if ($last_row) { // Таблица не пуста
    if ($last_row['date'] == date('Y-m-d')) { // В таблице существует запись текущего дня
        $wpdb->get_results('UPDATE `ft_journalstat` SET `curr`=' . $summ['totalviews'] . ' WHERE `id`=' . $last_row['id']);
    } else          // Начался новый день {
        $wpdb->get_results('INSERT INTO `ft_journalstat` SET `date`="' . date('Y-m-d') . '", `summ`=' . ($last_row['summ'] + $last_row['curr'] + $summ['totalviews']) . ', `last`=' . $last_row['curr'] . ', `curr`=' . $summ['totalviews'] . '');
} else {   // Таблица пуста, вставляем первую запись
    $wpdb->get_results('INSERT INTO `ft_journalstat` SET `date`="' . date('Y-m-d') . '", `summ`=' . $summ['totalviews'] . ', `last`=0, `curr`=0');
}

if ($wpdb->last_error) {
    ErrorsLogging('Не удалось выполнить запрос в базу данных 2: .' . 'INSERT INTO `ft_journalstat` SET `date`="' . date('Y-m-d') . '", `summ`=' . $summ['totalviews'] . ', `last`=0, `curr`=0');
}

// Логируем ошибки в файл
function ErrorsLogging($message) {
    if (ERRORS_LOG) { // Логирование включено
        $err_log = fopen(ERRORS_LOG, 'a+');
        fwrite($err_log, date('Y.m.d H:i:s') . "\t" . $message . "\n");
    }
    exit();
}

function extract_ids() {
    $entries = query_posts('nopaging=true&cat=1064');
    $ids = array();
    foreach ($entries as $entry) {
        $id = get_post_meta($entry->ID, 'pdf', true);
        if (!substr_count($id, '/files/')) {
            $id = mysql_real_escape_string(preg_replace('/(.*)?showfile\-([^\/]+)\/[^\n]+/i', '$2', $id));
        } else {
            $id = mysql_real_escape_string(preg_replace('/(.*)?files\/([^\/]+)\/[^\n]+/i', '$2', $id));
        }

        if ($id)
            $ids[] = '"' . $id . '"';
    }

    return implode(',', $ids);
}

?>
<?php

if (isset($_SERVER['SERVER_NAME']))
    exit();

include dirname(__FILE__) . '/../wp-load.php';
include ABSPATH . '/' . PLUGINDIR . '/exlines/creator.php';
include ABSPATH . '/' . PLUGINDIR . '/exlines/exlines.php';

// Report all PHP errors (bitwise 63 may be used in PHP 3)
//error_reporting(E_ALL);

// Same as error_reporting(E_ALL);
//ini_set('error_reporting', E_ALL);



$creator = new ExLineCreator();
$ExLines = new ExLines();

$lines = $ExLines->getAllLines();
$lines_count = count($lines);

$deleted = 0;
$errors = 0;

$fh = fopen(ABSPATH . PLUGINDIR . "/exlines/lines/debug.log", 'a+');

fwrite($fh, "\n\n" . str_repeat('=', 80) . "\n"
        . '[' . date('d.m.Y H:i:s') . '] Start update ' . "\n"
        . str_repeat('-', 80) . "\n"
);


for ($i = 0; $i < $lines_count; $i++) {
    if (!$lines[$i]->last_access && (time() - $lines[$i]->created) > 3600 * 24 * 7) { // if not accessed in last week
        $ExLines->deleteLine($lines[$i]->uid);
       /* fwrite($fh, '[' . date('d.m.Y H:i:s') . '] Deleted: '
                . ABSPATH . PLUGINDIR . "/exlines/lines/cache/" . $lines[$i]->uid
                . "\n"
        );*/
		fwrite($fh, '[' . date('d.m.Y H:i:s') . '] Deleted: '
                . "/var/www/files.fortrader.ru/public_html/exlines_cache/" . $lines[$i]->uid
                . "\n"
        );
        $deleted++;
        continue;
    }

    if ($lines[$i]->last_access) {
        $now = date_create(date('Y-m-d', time()));
        $last_access = date_create(date('Y-m-d', $lines[$i]->last_access));
        $interval = date_diff($now, $last_access);

        if ($interval->m > 3 && $interval->invert) {
            $ExLines->deleteLine($lines[$i]->uid);
			/*
            fwrite($fh, '[' . date('d.m.Y H:i:s') . '] Deleted: '
                    . ABSPATH . PLUGINDIR . "/exlines/lines/cache/" . $lines[$i]->uid
                    . "\n"
            );*/
			 fwrite($fh, '[' . date('d.m.Y H:i:s') . '] Deleted: '
                    . "/var/www/files.fortrader.ru/public_html/exlines_cache/" . $lines[$i]->uid
                    . "\n"
            );
            $deleted++;
            continue;
        }
    }

    $result = $creator->createImg(
                    $lines[$i]->template, array($lines[$i]->date, time(), 0),
                    $lines[$i]->text, $lines[$i]->uid
    );

    if (!$result) {
       /* fwrite($fh, '[' . date('d.m.Y H:i:s') . '] Can\'t update line file: '
                . ABSPATH . PLUGINDIR . "/exlines/lines/cache/" . $lines[$i]->uid
                . "\n"
        );*/
		 fwrite($fh, '[' . date('d.m.Y H:i:s') . '] Can\'t update line file: '
                . "/var/www/files.fortrader.ru/public_html/exlines_cache/" . $lines[$i]->uid
                . "\n"
        );
        $errors++;
    }
}

$updated = $lines_count - ($errors + $deleted);
$updated = ($updated > 0) ? $updated : 0;

fwrite($fh, str_repeat('-', 80) . "\n"
        . '[' . date('d.m.Y H:i:s') . '] Complete update '
        . '(updated: ' . $updated . ', deleted: ' . $deleted . ', errors: ' . $errors . ')'
        . "\n" . str_repeat('=', 80) . "\n\n"
);

fclose($fh);

if (!function_exists('date_diff'))
{
    function date_diff($date1, $date2) {
        $current = $date1;
        $datetime2 = date_create($date2);
        $count = 0;
        while(date_create($current) < $datetime2){
            $current = gmdate("Y-m-d", strtotime("+1 day", strtotime($current)));
            $count++;
        }
        return $count;
    } 
}


// this code generate previews
//$fill = array((time()+(60*60*24*300)*23),time(), 0);
////$fill = array((time()+(60*60*24*300)*1),time(), 0);
//$text = 'Я на Форексе';
//
//echo '<pre>';
//$creator->createImg('zero_406x34', $fill, $text);
//$creator->createImg('zero_400x82', $fill, $text);
//$creator->createImg('zero_300x40_b', $fill, $text);
//$creator->createImg('zero_300x40', $fill, $text);
//$creator->createImg('gold_400x82', $fill, $text);
//$creator->createImg('gold_406x34', $fill, $text);
//$creator->createImg('gold_300x40', $fill, $text);
?>

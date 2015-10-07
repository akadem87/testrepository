ff<?php
	
$base->name = 'fortrader';
$base->host = '192.168.1.4';
$base->user = 'fortrader_wp';
$base->pass = 'WTBXpNMZt34xXMMT';

require_once("simple_html_dom.php");

$vb = new vB_Content_Getter('parserovich', 'parser74615');
$data = $vb->get('http://forexsystems.ru/realtime.php');

$html = str_get_html($data);
$table = $html->getElementById('spy_table');

$tbody = $table->childNodes(1);

foreach ($tbody->find('tr') as $row)
{
    $item = $row->children(1);
    $link_item = $item->find('a', 0);

    $ln_rep1 = array('showthread.php?p=', '#post');
    $ln_rep2 = array('', '-post');

    if ($link_item)
    {
        $new_item['title'] = $link_item->plaintext;
        $new_item['link'] = 'http://forexsystems.ru/'.str_replace($ln_rep1, $ln_rep2, $link_item->href).'.html';

        //echo 'descr: '.$item->

        $items[] = $new_item;
    }
}


/*
foreach($m[1] as $k => $i)
{
    $url = 'http://forexsystems.ru/'.str_replace(array, , $m[1][$k]).'.html';

    preg_match('/<!-- message -->(.*?)<!-- \/ message -->/si', file_get_contents($url), $data);

    $data[$k] = array(
                'title' 		=> $m[2][$k],
                'short_descr' 	=> $m[3][$k],
                'full_descr'    => $data[1],
                'link_sourse' 	=> 'http://forexsystems.ru/'.$m[1][$k],
                'date_material' => date('Y-m-d H:i:s', strtotime($matches[1][$k]))
    );

    /*
    id | title | small_descr | full_descr | link_sourse | date_material(varchar) | date_add(varchar)

    title - ��������� ����, small_descr - ����� ���������, link_sourse - ������ �� ��������� �� ������, date_material - ���� ���������, date_add - ���� ���������� ������.
    */
/*
    mysql_query("INSERT INTO `ft_allmaterial` (`title`,`id_material`,`name_sourse`,`small_descr`,`full_descr`,`link_sourse`,`date_material`,`date_add`)
        VALUES ('".addslashes($data[$k]['title'])."',
                10,
                '����� ���������',
                '".notlishniee(addslashes($data[$k]['short_descr']))."',
                '".addslashes($data[$k]['full_descr'])."',
                '".addslashes($data[$k]['link_sourse'])."',
                '".addslashes($data[$k]['date_material'])."',
                '".date("Y-m-d H:i:s")."')
    ");

}
*/
	
function notlishniee($string){
    $string = strrev($string);
    $string = substr($string, strpos($string, '(')+3, 2048);
    return trim(strrev($string));
}


class vB_Content_Getter
{
    private $logged_in;
    private $user;
    private $pass;
    private $cookiefile;

    function __construct($user, $pass, $cookiepath = '/cookiefile.txt')
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->cookiefile = $_SERVER['DOCUMENT_ROOT'].$cookiepath;
    }

    function get($url)
    {
        if (!$this->logged_in) $this->login();

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    function login()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://forexsystems.ru/login.php?do=login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,    "vb_login_username=$this->user
                                                &vb_login_password=$this->pass
                                                &cookieuser=&s=&do=login");

        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiefile);
        curl_exec($ch);

        $this->logged_in = true;
    }
}
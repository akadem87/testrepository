<?php

include('simple_html_dom.php');
include dirname(__FILE__). '/../wp-config.php';
$config = $config['fortrader'];

mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('no connection');
mysql_select_db(DB_NAME);

class CurrencyChecker
{
    public $link;
    public $items = array();

    protected $data;
	protected $courseDate;
    protected $needleIds;

    public function loadData()
    {
        $xml = simplexml_load_file($this->link);

        foreach ($this->items as $item)
            $this->needleIds[] = $item['cbr_id'];

        foreach ($xml->Valute as $valute)
        {
            if (in_array($valute->NumCode, $this->needleIds))
                $this->data[(int)$valute->NumCode] = (float)str_replace(',', '.', $valute->Value);
        }
		
		foreach( $xml->attributes() as $a => $b) { 
			if( $a == 'Date' ){
				$this->courseDate = strtotime( str_replace( '/', '-', $b ) );
			}
		}
    }

    public function updateDb()
    {
		if( date('N', $this->courseDate ) == 2 ){
			$today = date('Y-m-d', $this->courseDate - 60*60*24*3 );
		}else{
			$today = date('Y-m-d', $this->courseDate - 60*60*24 );
		}
		
		$tomorrow = date("Y-m-d", $this->courseDate );

        $result = mysql_query("SELECT * FROM ft_course
                        WHERE curr_date = '".$today."'");

        while ($item = mysql_fetch_array($result)) {
            $currentItems[$item['cbr_id']] = $item['val'];
        }

        foreach ($this->data as $id => $value)
        {
            if (isset($currentItems[$id])) {
                $change = $value - $currentItems[$id];
            }
            else $change = 0;

			$query = "INSERT INTO ft_course(cbr_id, val, movement, curr_date)
                    VALUE('{$id}','{$value}','{$change}','{$tomorrow}')";

            mysql_query($query) or die(mysql_error());
        }
    }

    public function run()
    {
        $this->loadData();
        $this->updateDb();
    }
}

$checker = new CurrencyChecker();
$checker->items = array(
    array('curr_id'=>1, 'cbr_id'=>840, 'USD'),
    array('curr_id'=>2, 'cbr_id'=>978, 'EUR')
);
$checker->link = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=".date("d/m/Y", time() + 60*60*24 );
$checker->run();


/*-------------------------------

  // Функция загрузки курсов
  function course ($time) { 

    // Формирование ссылки запроса
    $link = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=".date("d/m/Y", $time); 
	
	

    // Загрузка HTML страницы
    $fd = fopen ($link, "r"); 
	
    // Чтение содержимого файла в переменную $text 
    if ($fd) while (!feof ($fd)) $text .= fgets($fd, 4096); 

    // Закрытие открытого файлового дескриптора
    fclose ($fd); 
	
    // Разбор содержимого при помощи регулярных выражений 
    $pattern = "#<Valute ID=\"([^\"]+)[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>([^<]+)#i"; 

    // Замена по регулярному выражению
    preg_match_all($pattern, $text, $out, PREG_SET_ORDER); 

    // Обработанные данные
    return $out; 
  } 


  // Поиск и добавление курсов в базу данных
  function course_base ($cur1, $cur2) {

    // Поиск сегодняшней даты в базе данных
    $res = mysql_query("SELECT COUNT(*) FROM ft_course WHERE currdate = '".date("Y.m.d")."' AND symbol = '".$cur1."'");
	
	 
    // Если запись не найдена
    if (mysql_result($res, 0) == 0) {
	
	  // Если курсы на сегодня не получены, то получаем
	  GLOBAL $course1; if (empty($course1)) $course1 = course(time());
	  
	  // Если курсы на завтра не получены, то получаем
	  GLOBAL $course2; if (empty($course2)) $course2 = course(time() + 24*60*60);
		
	  // Поиск необходимой валюты сегодня
	  for ($i = 0; $i < count($course1) + 1; $i++) 

	     // Если валюта найдена сегодня
	     if ($course1[$i][2] == $cur2) $course1_res = str_replace(",", ".", $course1[$i][4]);
		
	  // Поиск необходимой валюты завтра
	  for ($i = 0; $i < count($course2) + 1; $i++) 
	
	     // Если валюта найдена завтра
	     if ($course2[$i][2] == $cur2) $course2_res = str_replace(",", ".", $course2[$i][4]);
	
	  // Если курсы получены
	  if ((!empty($course1_res)) and (!empty($course2_res)))
	  
         // Добавление записи в базу данных
	     mysql_query("INSERT INTO ft_course SET ".
				
		   		     "dateadd   = '". date("Y.m.d")                     ."', ".  // Дата добавления записи
				     "symbol    = '". $cur1                             ."', ".  // Валюта (1 - USD; 2 - EUR)
				     "currdate  = '". date("Y.m.d")                     ."', ".  // Дата сегодня
				     "nextdate  = '". date("Y.m.d", time() + 24*60*60)  ."', ".  // Дата завтра
				     "currprice = '". $course1_res                      ."', ".  // Курс валюты сегодня
				     "nextprice = '". $course2_res                      ."'  "); // Курс валюты завтра
    }
  }


  // Переменные загруженных курсов
  $course1; $course2;
  
  // Поиск и добавление курсов в базу данных
  //course_base(1, 840);  // USD (1 - код валюты в базе данных; 840 - код валюты на сайте)
  //course_base(2, 978);  // EUR (2 - код валюты в базе данных; 978 - код валюты на сайте)
*/
?>
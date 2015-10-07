<?php 
	// Адрес сервера баз данных
	define('DB_HOSTNAME', '192.168.1.4');
	
	// Имя пользователя базы данных
	define('DB_USERNAME', 'informerbase');
	
	// Пароль пользователя базы данных
	define('DB_USER_PASSWORD', 'HcCF5MjB8TXJTDEa');
	
	// Имя базы данных
	define('DB_NAME', 'informerbase');
	
	// Имя таблицы для сохранения информации	
	define('DB_WORK_TABLE', 'ft_calendar');
	
	
	/*
	$feed = 'http://www.google.com/calendar/feeds/fxteam.ru@gmail.com/public/embed?singleevents=true&start-min='.urlencode(date('Y-m-d', time()-60*60*24*14)).'T00%3A00%3A00%2B03%3A00&start-max='.urlencode(date('Y-m-d', time()+60*60*24*14)).'T00%3A00%3A00%2B03%3A00';*/
	$feed = 'http://www.google.com/calendar/feeds/fxteam.ru@gmail.com/public/embed?singleevents=true&start-min='.urlencode(date('Y-m-d', time()-60*60*24*14)).'T00%3A00%3A00%2B03%3A00&start-max='.urlencode(date('Y-m-d', time()+60*60*24*14)).'T00%3A00%3A00%2B03%3A00&max-results=150&orderby=starttime';
	
	mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_USER_PASSWORD);
	mysql_select_db(DB_NAME);
	mysql_query('SET NAMES utf8');
	$mysql_lid = 0;
	
    $doc = new DOMDocument();
    $doc->load( $feed );
    $entries = $doc->getElementsByTagName('entry');
	
    $inserted = $updated = false;
    
    foreach ( $entries as $entry ) { 
        $status = $entry->getElementsByTagName( "eventStatus" ); 
        $eventStatus = $status->item(0)->getAttributeNode("value")->value;
    
        $titles = $entry->getElementsByTagName( "title" ); 
        $title = $titles->item(0)->nodeValue;

        $contents = $entry->getElementsByTagName('content');
        $content = $contents->item(0)->nodeValue;
        
        $links = $entry->getElementsByTagName( "link" ); 
        $link = $links->item(0)->getAttributeNode("href")->value;

        $places = $entry->getElementsByTagName( "where" ); 
        $where = $places->item(0)->getAttributeNode("valueString")->value;
        
        $times = $entry->getElementsByTagName( "when" ); 
        $startTime = $times->item(0)->getAttributeNode("startTime")->value;
        $when = date( "Y-m-d H:i:s", strtotime( $startTime ) );
        
        $countries = $entry->getElementsByTagName( "where" );
        $country = $countries->item(0)->getAttributeNode("valueString")->value;
        
        preg_match('(((?:<b>Предыдущее значение</b>:)\s*(.*?))\s*(?=$|<br))m', $content, $last);
        preg_match('(((?:<b>Прогноз</b>:)\s*(.*?))\s*(?=$|<br))m', $content, $fcast);
        preg_match('(((?:<b>Фактическое значение</b>:)\s*(.*?))\s*(?=$|<br))m', $content, $fact);
        
        $last = explode(', ', $last[2]);
        $fcast = explode(', ',$fcast[2]);
        $fact = explode(', ', $fact[2]);
        
        
        $fact = expandInfo($fact);
        $fcast = expandInfo($fcast);
        $last = expandInfo($last);
        
        $len = max(count($fact), count($last), count($fcast));
        for($i=0; $i < $len; $i++){
        	
        	$sql = 'INSERT INTO `'.DB_WORK_TABLE.'` SET `date`="'.$when.'", `country`="'.$country.'", `url`="'.$link.'", `name`="'.mysql_escape_string($title).'", ';
        	
        	if(isset($fact[$i][2]) && isset($fcast[$i][2]) && isset($last[$i][2]) && $last[$i][2]==$fcast[$i][2] && $last[$i][2]==$fact[$i][2]){
				$sql.='`lastvalue`="'.$last[$i][0].'", `forecastvalue`="'.$fcast[$i][0].'",	`factvalue`="'.$fact[$i][0].'", `dimension`="'.$fact[$i][1].'", `currency`="'.$fact[$i][2].'"';
				mysql_query($sql);
				if(mysql_insert_id() == $mysql_lid){
        			$sql = 'UPDATE `'.DB_WORK_TABLE.'` SET `factvalue`="'.$fcast[$i][0].'" WHERE `date`="'.$when.'" && `country`="'.$country.'" && `dimension`="'.$fcast[$i][1].'"';
        			mysql_query($sql);
				}
				else 
					$mysql_lid = mysql_insert_id();
        	}
        	elseif(isset($fcast[$i][2]) && isset($last[$i][2]) && $last[$i][2]==$fcast[$i][2]){
        		$sql.='`lastvalue`="'.$last[$i][0].'", `forecastvalue`="'.$fcast[$i][0].'",	`dimension`="'.$fcast[$i][1].'", `currency`="'.$fcast[$i][2].'"';
        		mysql_query($sql);
        	}
        	elseif (isset($fcast[$i][2])){
        		$sql.='`forecastvalue`="'.$fcast[$i][0].'",	`dimension`="'.$fcast[$i][1].'", `currency`="'.$fcast[$i][2].'"';
        		mysql_query($sql);
        	}
        	elseif (isset($last[$i][2])){
        		$sql.='`lastvalue`="'.$last[$i][0].'",	`dimension`="'.$last[$i][1].'", `currency`="'.$last[$i][2].'"';
        		mysql_query($sql);
        	}
        }
    }
        
    
	function expandInfo($data){
		$expdata = array();
		foreach ($data as $dat){
			$dat = explode('%', $dat);
			if(count($dat)==2){
				$t = array(trim($dat[0]), (trim($dat[1]) == '' ? '%' : trim($dat[1])));
				$t[] = getCurrency($t[0]);
				$t[0] = getValue($t[0]);
				$expdata[] = $t;
				continue;
			}
			elseif(mb_strpos($dat[0], 'B') !== false){
				$t = array(trim(mb_substr($dat[0], 0, (mb_strlen($dat[0])-1))), 'B');
				$t[] = getCurrency($t[0]);
				$t[0] = getValue($t[0]);
				$expdata[] = $t;
				continue;
			}
			elseif(mb_strpos($dat[0], 'K') !== false){
				$t = array(trim(mb_substr($dat[0], 0, (mb_strlen($dat[0])-1))), 'K');
				$t[] = getCurrency($t[0]);
				$t[0] = getValue($t[0]);
				$expdata[] = $t;
				continue;
			}
			elseif(mb_strpos($dat[0], 'М') !== false){
				$t = array(trim(mb_substr($dat[0], 0, (mb_strlen($dat[0])-2))), 'М');
				$t[] = getCurrency($t[0]);
				$t[0] = getValue($t[0]);
				$expdata[] = $t;
				continue;
			}
			elseif(mb_strpos($dat[0], 'M') !== false){
				$t = array(trim(mb_substr($dat[0], 0, (mb_strlen($dat[0])-1))), 'M');
				$t[] = getCurrency($t[0]);
				$t[0] = getValue($t[0]);
				$expdata[] = $t;
				continue;
			}
			else {
				if(trim($dat[0]) == '')
					continue;
				$t[0] = getValue($dat[0]);
				$t[1] = '';
				$t[2] = getCurrency($dat[0]);
				$expdata[] = $t;
				continue;				
			}
		}
		return $expdata;
    }
    
    function getCurrency($str){
		for($i=0; $i < strlen($str); $i++){
    		if(!in_array(mb_substr($str, $i, 1), array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '.')))
    			return mb_substr($str, $i, 2);
		}
		return '';
    }
    
    function getValue($str){
    	$result = '';
		for($i=0; $i < strlen($str); $i++){
    		if(in_array(mb_substr($str, $i, 1), array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '.')))
    			$result.=mb_substr($str, $i, 1);
		}
		return is_numeric($result)?$result:'0';
    }
?>
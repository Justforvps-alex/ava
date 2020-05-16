<?php
function Curl_avito($url,$time_sleep)
{
	$useragent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/'.rand(60,72).'.0.'.rand(1000,9999).'.121 Safari/537.36';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_URL,$url);
	if(isset($proxy))
	{
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	}
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
	$page = curl_exec($ch);
	$html=str_get_html($page);
	//echo $html;
	echo "<br><br>Вступавет проверочка<br>";
	sleep($time_sleep);
	curl_close($ch);
	$html=check_html($html,$url);
	//echo "<br><br><br><br>Верный хтмл только этот стоп<br><br>м".$html."<br><br><br>";
	return $html;
}
function check_html($html,$url)
{
    $string_proxy=1;
	$check_html=$html;
	//echo "<br>".$url."<br>";
	$check_1=strpos($check_html,'Объявления');
	$check_2=strpos($check_html,'user_unauth');
	$check_3=strpos($check_html,'image64');
	if($check_1!==false or $check_2!==false or $check_3!==false){echo "<br>Vse norm<br>"; $check_proxy_check=1;}
	else {echo "<br>Vse ploho<br>"; $check_proxy_check=0;}
	while($check_html=='' or $check_proxy_check==0)
	{
		$check_1=strpos($check_html,'Объявления');
		$check_2=strpos($check_html,'user_unauth');
		$check_3=strpos($check_html,'image64');
		if($check_html!='')
		{
		if($check_1!==false or $check_2!==false or $check_3!==false){echo "<br>Vse norm<br>"; $check_proxy_check=1; break;}
		else {echo "<br>Vse ploho<br>";}
		}
		else {echo "<br>Vse ploho<br>";}
		$useragent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/'.rand(60,72).'.0.'.rand(1000,9999).'.121 Safari/537.36';
		$show_info = file('socks5_proxies.txt');
		$proxy=$show_info[$string_proxy];
		echo "<br>Внутри swhile".$proxy."<br>";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		$page = curl_exec($ch);
		$check_html=str_get_html($page);
		curl_close($ch);
		//echo "<br><br>Плохой хтмл<br>".$check_html;
		$string_proxy++;
	}
	echo "<br>End of while<br>";
	sleep(4);
	return $check_html;
}
function find_phone_url($id,$phone_item_only0,$phone_item_only1,$phone_item_only2)
{
	$id_only=$id;
	//$phone_item_only0=$phone_item_only0;
	//$phone_item_only1=$phone_item_only1;
	//$phone_item_only2=$phone_item_only2;
	$array0 = str_split($phone_item_only0); 
	$array1 = str_split($phone_item_only1);
	$array2 = str_split($phone_item_only2);
	//function check_code($array0,$array1,$array2)
	$a=0;//a номера переменных
	$k=0; //Номер кода
	$code_key='';//Буква разделитель
	$finish=0; //Код для авершения
	while($array0[$a]!=NULL and $array1[$a]!=NULL and $array2[$a]!=NULL and $finish==0)
	{
		//Если все 3 совпадают
		if($array0[$a]==$array1[$a] and $array0[$a]==$array2[$a])
		{ $a=$a+3; }
		//Проверка когда 2 отличаются
		elseif($array0[$a]!=$array1[$a] and $array0[$a]!=$array2[$a] and $array1[$a]!=$array2[$a])
		{
			if($array0[$a+1]==$array1[$a+1]) { $k=0; $code_key=$array0[$a]; $finish=1; break;}
			elseif($array0[$a+1]==$array2[$a+1]) { $k=0; $code_key=$array0[$a]; $finish=1;break;} 
			elseif($array1[$a+1]==$array2[$a+1]) { $k=1; $code_key=$array1[$a]; $finish=1;break;}	
		}
		//Проверка когда 1 отличается
		elseif($array0[$a]==$array1[$a] and $array0[$a]!=$array2[$a])
		{
			$code_key=$array2[$a];
			if($array0[$a]==$array2[$a+1]) { $k=2; $finish=1;break;}	
		}
		elseif($array0[$a]==$array2[$a] and $array0[$a]!=$array1[$a])
		{
			$code_key=$array1[$a];
			if($array0[$a]==$array1[$a+1]) { $k=1; $finish=1;break;}	
		}
		elseif($array1[$a]==$array2[$a] and $array1[$a]!=$array0[$a])
		{
			$code_key=$array0[$a];
			if($array1[$a]==$array0[$a+1]) { $k=0; $finish=1;break;}	
		}
	}
	//Находим количество букв и вгоняем линию
	if($k==0) { $numer=count($array0); $crypted_line_array=$array0; }
	elseif($k==1) { $numer=count($array1); $crypted_line_array=$array1; }
	elseif($k==2) { $numer=count($array2); $crypted_line_array=$array2; }
	$pkey=''; //Код
	$i=0;
	while($i<$numer)
	{
		if($crypted_line_array[$i]==$code_key) {$i++;}
		$pkey.=$crypted_line_array[$i];
		$i=$i+3;
	}
	$phoneUrl="https://www.avito.ru/items/phone/".$id_only."?pkey=".$pkey."&vsrc=r";
	//echo "<br>Вывод из поиска".$phoneUrl."<br>";
	return $phoneUrl;
}
function file_force_download($file) {
  if (file_exists($file)) {
    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
    // если этого не сделать файл будет читаться в память полностью!
    if (ob_get_level()) {
      ob_end_clean();
    }
    // заставляем браузер показать окно сохранения файла
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    // читаем файл и отправляем его пользователю
    readfile($file);
    exit;
  }
}
function download_proxy($url)
{
	$fp = fopen('socks5_proxies.txt', 'wb'); // создаём и открываем файл для записи
	$ch = curl_init($url); // $url содержит прямую ссылку на видео
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FILE, $fp); // записать вывод в файл
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
}
?>

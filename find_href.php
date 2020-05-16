<?php
ini_set('max_execution_time', 30000000);
//ob_flush();
//flush();
echo "<br>";
echo (date("l dS of F Y h:I:s A"));
echo "<br>";
$main_url=$_GET['url'];
$number_of_phones=$_GET['n'];
require_once 'simple_html_dom.php';
require_once 'classes.php';
require_once 'functions.php';
//@header("Content-type: text/html; charset=UTF-8");
//$GLOBALS['sysMessages'] = "Нет системных сообщений";
//echo "<p style='color: darkgreen; font-size: 18px;'>".$sysMessages."</p>" ;
$url='https://api.proxyscrape.com/?request=getproxies&proxytype=socks5&timeout=2100&country=all';
download_proxy($url);
$phone_number=1; 
$page_number=1;
$max_pages=100;
//$number_of_phones=1500; //vvodim post
$url='';
//$main_url='https://www.avito.ru/nizhniy_tagil/bytovaya_elektronika';
require_once 'PHPExcel/Classes/PHPExcel.php'; //Подключаем библиотеку
$phpexcel = new PHPExcel(); //Создаем новый Excel файл
$page_excel = $phpexcel->setActiveSheetIndex(0); //Устанавливаем активный лист
$page_excel->setTitle("Phones"); //Записываем название 
$page_excel->setCellValue("A1", "Телефоны");
while($page_number<$max_pages && $phone_number<=$number_of_phones)
{
	$url=$main_url.'?p='.$page_number;
	$time_sleep=rand(1,2);
	$html=Curl_avito($url,$time_sleep);
	foreach($html->find('div.snippet-horizontal') as $href_div)
	{
		$id=$href_div->attr['data-item-id'];
		if($id%2!=0)
		{
			$array0[$id]=$href_div->attr['data-pkey'];
			if($array0[$id]!='')
			{
				$id_array[]=$id;
				echo $id.") ".$array0[$id]."<br>";
				foreach($href_div->find('a.snippet-link') as $href_to_check)
				{echo $id.") ".$href_to_check->href."<br>";}								//убрать
			}
		}
	}
	$html->clear(); // подчищаем за собой
    unset($html);
	$max_id=count($id_array);
    //echo "<br>vtoroy cicl<br>";
	$time_sleep=rand(1,2);
	$html=Curl_avito($url,$time_sleep);
	for($id_numer=0;$id_numer<$max_id; $id_numer++)
	{
		$id=$id_array[$id_numer];
		foreach($html->find("div[data-item-id=$id]") as $href_div)
		{
		        if(isset($href_div)){$array1[$id]=$href_div->attr['data-pkey']; echo $id.") ".$array1[$id]."<br>";}
		}
	}
	$html->clear(); // подчищаем за собой
    unset($html);
	//echo "<br>vtoroy cicl<br>";
	$checked_id=2;
	$time_sleep=rand(1,2);
	$html=Curl_avito($url,$time_sleep);
	for($id_numer=0;$id_numer<$max_id; $id_numer++)
	{
		$id=$id_array[$id_numer];
		foreach($html->find("div[data-item-id=$id]") as $href_div)
		{
		if(isset($href_div)){$array2[$id]=$href_div->attr['data-pkey']; echo $id.") ".$array2[$id]."<br>";}
		if($array0[$id]!='' && $array1[$id]!='' && $array2[$id]!='')
		{           
				    echo "<br>Номер айди)".$id."<br>";
	            	//echo $checked_id.") ".$array0[$id]."<br>".$array1[$id]."<br>".$array2[$id]."<br>";
            		$phone_item_only0=$array0[$id];
            		$phone_item_only1=$array1[$id];
            		$phone_item_only2=$array2[$id];
              		$url=find_phone_url($id,$phone_item_only0, $phone_item_only1, $phone_item_only2);
            		echo "<br>Фоне юрл".$checked_id.")".$url."<br>";
            		$time_sleep=rand(2,3);
            		$imgContent = Curl_avito($url,$time_sleep);
            		//echo "<br>".$imgContent."<br>";
            		$avitoContact = new AvitoContact;
            		$imgContent = explode('base64,', $imgContent)[1];
            		//echo "<br>".$imgContent."<br>";
                	$a = fopen('phone.png', 'wb');
                	fwrite($a, base64_decode($imgContent));
            		fclose($a);
	            	$image='phone.png';
	            	$result = $avitoContact->recognize('phone.png');
	            	if ($result) 
	            	{
	                 	echo "<br>Phone number: ".$result."<br>";
	                  	$page_excel->setCellValue("A$checked_id", $result);
	            	} 
	            	else 
                	{
                			echo '<h2 class="text-danger">Ничего не получилось</h2>';
	            	}
	            	$checked_id++;
	            	$phone_number++;
		}
		}
	}
	$html->clear(); // подчищаем за собой
    unset($html);
	echo "<br>number of page ".$page_number."<br>";
	//echo "Номер телефона ".$phone_number."<br>";
	//$id_array->clear(); // подчищаем за собой
    unset($id_array);
	$page_number++;
}


//$max_id_all=count($id_all_pages);
//echo "<br>Vst linki".$max_id_all."<br>";

$objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007'); //Формат
$objWriter->save("phones.xlsx"); //Сохраняем
echo 'File created successfuly'; //Сообщаем о создании файла
echo "<br>";
echo (date("l dS of F Y h:I:s A"));
echo "<br>";
$file="phones.xlsx";
file_force_download($file);
?>

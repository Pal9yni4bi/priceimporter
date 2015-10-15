<?
/*подключаем константы вордпресса*/
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-config.php');

/*разбираем переданные опции*/
if (isset($_POST['uploadfile'])){
	// Каталог, в который мы будем принимать файл:
	$uploaddir = '../../../';
	$uploadfilename = "price_".$_SERVER['SERVER_NAME'].".xls";
	$uploadfile = $uploaddir.$uploadfilename;
	// Копируем файл из каталога для временного хранения файлов:
	if (copy($_FILES['uploadfile']['tmp_name'], $uploadfile))
	{
	echo "<h3>Файл успешно загружен на сервер</h3>";
	}
	else { echo "<h3>Ошибка! Не удалось загрузить файл на сервер!</h3>"; exit; }
	// Выводим информацию о загруженном файле:
	echo "<h3>Информация о загруженном на сервер файле: </h3>";
	echo "<p><b>Оригинальное имя загруженного файла: ".$_FILES['uploadfile']['name']."</b></p>";
	echo "<p><b>Присвоенное имя загруженного файла: ".$uploadfilename."</b></p>";
	echo "<p><b>Mime-тип загруженного файла: ".$_FILES['uploadfile']['type']."</b></p>";
	echo "<p><b>Размер загруженного файла в байтах: ".$_FILES['uploadfile']['size']."</b></p>";
}
if (isset($_POST['uploadfilecopy'])){
	$uploaddircopy = '../../../';
	$uploadfilenamecopy = "price_".$_SERVER['SERVER_NAME']."_privatecopy.xls";
	$uploadfilecopy = $uploaddircopy.$uploadfilenamecopy;
	if (copy($_FILES['uploadfile']['tmp_name'], $uploadfilecopy))
	{
	echo "<h3>Файл для личного пользования успешно загружен на сервер с пометкой _privatecopy</h3>";
	}
	else { echo "<h3>Ошибка! Не удалось загрузить файл для личного пользования на сервер!</h3>";}
	echo "<p><b>Присвоенное имя загруженного файла для личного пользования: ".$uploadfilenamecopy."</b></p>";
}
	
if (isset($_POST['uploadmysql'])){
	include_once ("excel.class.php");
	$data = new Spreadsheet_Excel_Reader("$uploadfile");
	/*задает массив вида "порядковый номер вкладки экселя - хук для вызова */
                $sheets = array(
                  '0' => "price",
                  '1' => "eurovagonka",
                  '2' => "block-house",
                  '3' => "brus-imitation",
                  '4' => "doska-pola",
                  '5' => "brus",
                  '6' => "dveri",
                  '7' => "banya-olxa",
                  '8' => "banya-lipa",
                  '9' => "lestnica",
                  '10' => "nalichnick",
                  '11' => "plintys",
                  '12' => "ygolock",
                  '13' => "doska-strog",
                  '14' => "pogonazh",
				  '15' => "shit",
                  '16' => "mebel",
                  '17' => "fanera",
                  '18' => "osb",
                  '19' => "krepezh",
                  '20' => "propitki",
                  '21' => "akcii"
                );	
	/*задает массив вида "порядковый номер вкладки экселя - хук для вызова - конец */
		
	//подключение к БД 
	$host='localhost'; // имя хоста (уточняется у провайдера)
	$database=DB_NAME; // имя базы данных, которую вы должны создать
	$user=DB_USER; // заданное вами имя пользователя, либо определенное провайдером
	$pswd=DB_PASSWORD; // заданный вами пароль
	$dbh = mysql_connect($host, $user, $pswd) or die("Не могу соединиться с MySQL.");
	mysql_select_db($database) or die("Не могу подключиться к базе.");
mysql_query("SET names 'cp1251'");
	/* из массива $sheets выдергиваются айдишники и присваиваются $sid, значение для айди присваивается переменной $sheet*/
	foreach($sheets as $sid=>$sheet){
		/*разбираем файл через класс экселя*/
		$content = $data->dump(false,false,$sid,$sheet);
		$content = html_entity_decode($content,ENT_QUOTES);
		/*минифицируем излишки*/
		$content = decor($content);
		/*меняем кодировку для записи в БД*/
		$content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
		/*записываем в БД*/
		mysql_query("REPLACE priceimporter SET id='$sheet', content='$content'") or die("Не удалось осуществить запись в БД");
	}
	/*отключаемся от БД*/
	mysql_close($dbh);
}

function decor($txt){
	$arr_match = array(
		"<nobr>",
		"</nobr>",
		"font-size:8px",
		"font-size:9px",
	);
	$arr_repl = array(
		"",
		"",
		"font-size:90%",
		"",
	);
	$txt = str_replace($arr_match,$arr_repl,$txt);

	$pattern = array(
		"/font-family:[^;]*;/i",
		"/height:[^;]*;/i",
		"/border-[^:]*:[^;]*;/i",
	);
	$replacement = array(
		"",
		"",
		"",
	);
	$txt = preg_replace($pattern, $replacement, $txt);

	return $txt;
}
?>
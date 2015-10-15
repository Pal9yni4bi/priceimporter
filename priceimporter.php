<?php
/*если форма обрабатывается в том же файле, то загружаются только чуть больше сотни символов от контента в соответствующее поле
глюк не зависит от типа подключения к БД, от доступности функции декора
глюк возникает именно при записи в БД, т.к. через echo контент выводится целиком. Надо проверить, сможет ли он записать туда длинный контекнт, если он не в переменной*/
/*
Plugin Name: PriceImporter
Plugin URI: http://andrus.pro/?p=126
Description: Импортирует прайс-лист xls в inc и БД с разбивкой по страницам
Version: 0.7.4
Author: Andrew Kaltsou
Author URI: http://andrus.pro/
License: GPL2
*/

/*  Copyright YEAR  Andrew Kaltsou (email : pal9yni4bi at gmail.com)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//include_once ("settings.php");
	
function priceimporter_activate() {
	global $wpdb;
	$wpdb->query("CREATE TABLE IF NOT EXISTS priceimporter (id VARCHAR(255) NOT NULL COLLATE utf8_general_ci, content LONGTEXT NOT NULL COLLATE utf8_general_ci, PRIMARY KEY (id))") or die ("Could not query: . mysql_error()");
	$filename = '../priceimporter';
	if (!file_exists($filename)) {
		mkdir("../priceimporter",0755);
		clearstatcache();
	}
}
register_activation_hook( __FILE__, 'priceimporter_activate' );

add_action('admin_menu', 'CreatePriceImporterMenu'); 

/*задаём шорткод*/
function pricelist($id) {
	global $wpdb;
	$result = $wpdb->get_var("SELECT content FROM priceimporter WHERE id='$id[child]'", 0,0) or die ("Could not query: . mysql_error()");
	return $result;
}
 
add_shortcode( 'pricelist', 'pricelist' );

/*создаём меню настройки*/
function CreatePriceImporterMenu()
{
    if (function_exists('add_menu_page'))
    {
        add_menu_page ('Страница загрузки прайса', 'Загрузить прайс', 'manage_options', 'PriceImporterIdentifictor', 'PriceImporterOptions');
    }
}
 
function PriceImporterOptions()
{
	$filename = "price_".$_SERVER['SERVER_NAME'];
    echo "<h2>Загрузка прайса</h2>
		<form method=post action=../wp-content/plugins/price_importer/a_little_bit_of_magic.php enctype=multipart/form-data>
			<input type=file name=uploadfile id=upload_file>
			<input type=submit value=Загрузить>
			<p><label><input type=checkbox name=uploadfile checked>Загрузить файл .xls</label></p>
			<p><label><input type=checkbox name=uploadmysql checked>Загрузить данные в БД</label></p>
			<p><label><input type=checkbox name=uploadfilecopy>Загрузить копию файла в .xls</label></p>
		</form>";
	echo "Доступ к файлу можно будет получить по ссылке <b>http://".$_SERVER['SERVER_NAME']."/price_".$_SERVER['SERVER_NAME'].".xls</b>";
}
?>
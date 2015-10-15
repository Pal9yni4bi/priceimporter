<?
/*В каждом поле можно ввести желаемую зацепку,
справа пусть генериуется код для вставки на страницы (шорткод) для каждого имени (лень - 
двигатель чего-то). По умолчанию присваивать какую-нибудь хрень типа айди1, айди2 и т.п.
Для снижения нагрузки на БД хранить секцию полей в ассоциативном массиве (?).
(?) Сделать ссылку у каждого поля в секции "удалить закладку\шорткод\хзкакэтоназвать".
Сделать валидатор, которые запретит вводить не положительные значения числа полей.

Пока делаю автосоздание строк БД через массив, нужно переделать в создание массива
в строке БД, т.к. по теперешней много запросов к базе + будет некоректно удаляться
плагин.
*/
$priceimporter_name = "Плагин для загрузки прайсов PriceImporter";
/*для формирования страницы вытягиваем из БД значение параметра. Понадобится ещё в нескольких местах*/
$fieldscounter = get_option('priceimporter_number_of_fields');
     
function priceimporter_code_add_admin() {
  global $priceimporter_name;
  add_options_page(__('Settings').': '.$priceimporter_name, $priceimporter_name, 'edit_themes', basename(__FILE__), 'priceimporter_code_to_admin');
}
 
// Вид административной страницы и обработка-запоминание получаемых опций
 
function priceimporter_code_to_admin() {
  global $priceimporter_name;
?>
 
<div class="wrap">
<?php
screen_icon(); // Значок сгенерируется автоматически
echo '<h2>'.__('Settings').': '.$priceimporter_name.'</h2>'; // Заголовок
// Пошла обработка запроса
if (isset($_POST['save'])) {
    update_option('priceimporter_number_of_fields', stripslashes($_POST['number_of_fields']));
	for ($i = 1, $j = 1; $i <= get_option('priceimporter_number_of_fields') ; $i++, $j++) {
    update_option('priceimporter_textfield['.$i.']', stripslashes($_POST['priceimporter_textfield_'.$i]));
	}
    echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><b>'.__('Settings saved.').'</b></p></div>';
}
// Внешний вид формы		echo $fieldscounter
?>
	<form method="post">
		<table class="form-table">
		  <thead>
		  <tr valign="top">
			<th scope="row">Сколько таблиц необходимо импортировать?</th>
			<td>
				<input name="number_of_fields" class="regular-text" type="text" value="<?php echo get_option('priceimporter_number_of_fields');?>" >
			</td>
			<td>
				<-- Число листов в книге Excel
			</td>
		  </tr>
		  </thead>
			<?
			for ($i = 1, $j=1; $i <= get_option('priceimporter_number_of_fields') ; $i++, $j++) {
			/*массив будет состоять из пар id$j => name$j 
			при обработке пост-запроса сначала эти данные сформируются в массив или придут готовым массивом, а потом присвоятся опции БД
			(?)На время внедрения можно сохранять "в два потока", дублируя инфу*/
			  echo "<tr valign='top'>";
				echo "<th scope='row'>Название зацепки № ".$i.":</th>";
				echo "<td>";
					echo "<input name='priceimporter_textfield_".$i."' class='regular-text' type='text' value=" . get_option('priceimporter_textfield['.$i.']') . ">";
				echo "</td>";
				echo "<td>";
					echo "Для вставки листа № ".$i." книги Excel воспользуйтесь шорткодом <strong>[priceimporter child=".get_option('priceimporter_textfield['.$i.']')."]</strong>";
				echo "</td>";				
			  echo "</tr>";
			  }
			?>
		</table>		
  <div class="submit">
      <input name="save" type="submit" class="button-primary" value="<?php echo __('Save Draft'); ?>" />
  </div>
</form>
 
</div>
<?php
}
 
// Итоговые действия
 
add_action('admin_menu', 'priceimporter_code_add_admin');
 
// Деинсталяция
 
if (function_exists('register_uninstall_hook'))
  register_uninstall_hook(__FILE__, 'priceimporter_deinstall');
   
function priceimporter_deinstall() {
  delete_option('priceimporter_textfield');
}
?>
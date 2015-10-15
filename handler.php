<?
/*$option_name = 'priceimporter_field_1' ; 
$newvalue = $_POST['priceimporter_field_1'] ;
  if ( get_option($option_name) ) {
    update_option($option_name, $newvalue);
  } else {
    $deprecated=' ';
    $autoload='no';
    add_option($option_name, $newvalue, $deprecated, $autoload);
  }
*/  
update_option( 'priceimporter_field_1', $_POST['priceimporter_field_1'] );
update_option( priceimporter_field_2, $_POST['priceimporter_field_2'] );

header("Location:../../../wp-admin/options-general.php?page=priceimporter-options-page.php");
?>
<?php

@session_start();
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

include ENGINE_DIR.'/data/config.php';
require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';

if ($_COOKIE['dle_skin']) {
	if (@is_dir(ROOT_DIR.'/templates/'.$_COOKIE['dle_skin']))
		{
			$config['skin'] = $_COOKIE['dle_skin'];
		}
}

if ($config["lang_".$config['skin']]) {

     include_once ROOT_DIR.'/language/'.$config["lang_".$config['skin']].'/website.lng';

} else {

     include_once ROOT_DIR.'/language/'.$config['langs'].'/website.lng';

}
$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

$tip = intval($_POST['tip']);
if (!$tip) die ("error");


$buffer = "<select id=\"name_select\" name=\"n\" onChange=\"makeSelect(this.value)\"><option value=\"0\">выберите марку ТС</option>";
// в зависимости от значения формируется выводимый select
$names = $db->query("SELECT name_id, name FROM " . PREFIX . "_auto_models WHERE tip = '$tip' GROUP by name ASC");

while ($row = $db->get_row($names)) {
	$buffer.= "<option value=".$row["name_id"].">".$row["name"]."</option>";
}
$buffer.= "</select>";

@header("Content-type: text/css; charset=".$config['charset']);
echo $buffer; //"ответ сервера"

?>

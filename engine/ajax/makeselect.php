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

$id = intval($_POST['id']);
$tip = intval($_POST['tip']);
if (!$id || !$tip) die ("error");


$buffer = "<select id=\"model_select\" name=\"m\" onchange=\"getModel(this.value)\"><option value=\"0\">выберите модель</option>";

$models = $db->query("SELECT id, model FROM " . PREFIX . "_auto_models WHERE name_id = '$id' and tip = '$tip' ORDER by model ASC");

while ($row = $db->get_row($models)) {
	$buffer.= "<option value=".$row["id"].">".$row["model"]."</option>";
}
$buffer.= "</select>";

@header("Content-type: text/css; charset=".$config['charset']);
echo $buffer; //"ответ сервера"
?>

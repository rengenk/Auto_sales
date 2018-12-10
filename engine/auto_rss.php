<?PHP

define('DATALIFEENGINE', true);
define('ROOT_DIR', '..');
define('ENGINE_DIR', dirname (__FILE__));

@error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);

include ENGINE_DIR.'/data/config.php';
require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/inc/functions.inc.php';
include_once ENGINE_DIR.'/data/dbconfig.php';

$auto_rss_num = 10; //количество объявлений


echo <<<XML
<?xml version="1.0" encoding="{$config['charset']}"?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title>{$config['home_title']}</title>
<link>{$config['http_home_url']}</link>
<description>{$config['description']}</description>
XML;

	$result = $db->query ("SELECT * FROM " . PREFIX . "_auto_price LEFT JOIN dle_auto_models ON ( " . PREFIX . "_auto_price.model_id = " . PREFIX . "_auto_models.id) order by date desc limit $auto_rss_num ");
	while ($row = $db->get_row ($result)) {

    	if ($config['allow_alt_url'] == "yes"){
    		$link = totranslit($row['name']." ".$row['model']);
		} else {
		    $link = "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_id&id=".$row['price_id']."\">".$row['name']." ".$row['model']."</a>";
		}
		$model = $row['name']." ".$row['model'];


echo <<<XML
<item>
<title>Объявление ¹ {$row['price_id']} {$model}</title>
<link>{$config['http_home_url']}auto/prodaja-{$row['price_id']}-{$link}.html</link>
<description><![CDATA[{$model}<br><br><b>Äàòà:</b> {$row[date]}]]></description>
<model>{$model}</model>
</item>
XML;

}
echo <<<XML
</channel>
</rss>
XML;
?>

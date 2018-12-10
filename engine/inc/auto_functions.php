<?

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

$cities = get_vars ("cities");
if (!$cities) {
$cities = array ();

$result = $db->query("SELECT * FROM " . PREFIX . "_auto_cities ORDER BY id ASC");
while($row = $db->get_row($result)){

   $cities[$row['id']] = array ();

     foreach ($row as $key => $value)
     {
       $cities[$row['id']][$key] = $value;
     }
}
set_vars ("cities", $cities);
$db->free($result);
}

$tips = get_vars ("auto_tips");
if (!$tips) {
$tips = array ();

$result = $db->query("SELECT * FROM " . PREFIX . "_auto_tips ORDER BY tip_id ASC");
while($row = $db->get_row($result)){

   $tips[$row['tip_alt_name']] = array ();

     foreach ($row as $key => $value)
     {
       $tips[$row['tip_alt_name']][$key] = $value;
     }
}
set_vars ("auto_tips", $tips);
$db->free($result);
}

function option($tip) {
global $db, $auto_brands;
if ($tip=="") {$tip=1;}
$val = 0;
$names = $db->query("SELECT name, name_id FROM " . PREFIX . "_auto_models WHERE tip = '$tip' GROUP by name, name_id ASC");
$option = "";
while ($row = $db->get_row($names)) {
$option .= "<OPTION value=".$row["name_id"].">".$row["name"]."</OPTION>\n";
$val++;
}
return $option;
}


function photo($row, $vid) {
global $config;

if ($vid=="forlist") {
if ($row["screenshot"] > 0) {
$text = "<img align=\"center\" src=//img.artlebedev.ru/tools/decoder/\"".$config['http_home_url']."templates/{$config['skin']}/images/auto/image.gif\" border=\"0\">";}
else {
$text = "<img align=\"center\" src=//img.artlebedev.ru/tools/decoder/\"".$config['http_home_url']."templates/{$config['skin']}/images/auto/noimage.gif\" border=\"0\">";}
}

elseif ($vid=="forshow") {
if ($row['screenshot'] > 0) {
	$text = "";
	for ($n = 1; $n <= $row["screenshot"]; $n++) {
		$text .= "<div style='padding:5px;'><a href=\"".$config['http_home_url']."uploads/auto/photos/".$row["photo".$n]." \"onClick=\"return hs.expand(this)\"><img src=//img.artlebedev.ru/tools/decoder/\"".$config['http_home_url']."uploads/auto/thumbs/".$row["photo".$n]."\" border=\"0\" alt='".$row["name"]."&nbsp;".$row["model"]."' style='border: 1px outset silver;'></a></div>"; }
}
}
	return $text;
}

function makeDropDown($options, $name, $selected)
    {
        $output = "<select name=\"$name\">\r\n";
        foreach($options as $value=>$description)
        {
          $output .= "<option value=\"$value\"";
          if($selected == $value){ $output .= " selected "; }
          $output .= ">$description</option>\n";
        }
        $output .= "</select>";
        return $output;
    }



function show_last_block ($num) {
global $db, $config, $tpl, $cities, $tips;

	$last = get_vars ("last_price");
	if (!$last) {
		$last = array ();

		$result =  $db->query("SELECT * FROM " . PREFIX . "_auto_price LEFT JOIN " . PREFIX . "_auto_models ON (" . PREFIX . "_auto_price.model_id = " . PREFIX . "_auto_models.id) WHERE screenshot > 0 ORDER by date DESC LIMIT $num");
		while($row = $db->get_row($result)){

   			$last[$row['price_id']] = array ();

     		foreach ($row as $key => $value) {
       			$last[$row['price_id']][$key] = $value;
     		}
	}
	set_vars ("last_price", $last);
	$db->free($result);
 }

	$output = "<DIV><TABLE id=new_cars cellSpacing=0 cellPadding=0 width=\"100%\" border=0>
       <TBODY>
       <TR>\n";

	foreach ($last as $row) {

	$output .=
	"<TD width=\"10%\">&nbsp;</TD>
       <TD class=td>
       <TABLE height=\"100%\" cellSpacing=0 cellPadding=3 width=\"100%\" border=0>
       <TBODY>
       <TR>
       <TD class=td_t>\n";

	if ($config['allow_alt_url'] == "yes") {
		$output .= "<a href=\"".$config['http_home_url']."auto/prodaja-".$row['price_id']."-".totranslit($row['name']." ".$row['model']).".html\" class=standard>".$row['name']." ".$row['model']."</a>";
	} else {
		$output .= "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_id&id=".$row['price_id']."\" class=standard>".$row['name']." ".$row['model']."</a>";
	}

	$output .=
	"</TD></TR>
	   <TR>
       <TD>
       <DIV align=center>\n";

    foreach ($tips as $tip){
    	if ($tip['tip_id'] == $row['tip']) $tipimg = $tip['tip_img'];
    }

	if ($row['screenshot']>0) {

		if ($config['allow_alt_url'] == "yes") {
			$output .= "<a href=\"".$config['http_home_url']."auto/prodaja-".$row['price_id']."-".totranslit($row['name']." ".$row['model']).".html\">
			<img src=//img.artlebedev.ru/tools/decoder/\"".$config['http_home_url']."uploads/auto/thumbs/".$row["photo1"]."\" border=\"0\" alt='".$row["name"]."&nbsp;".$row["model"]."'>
			</a>";
		} else {
			$output .= "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_id&id=".$row['price_id']."\">
			<img src=//img.artlebedev.ru/tools/decoder/\"".$config['http_home_url']."uploads/auto/thumbs/".$row["photo1"]."\" border=\"0\" alt='".$row["name"]."&nbsp;".$row["model"]."'>
			</a>";
		}

	} else {
		if ($config['allow_alt_url'] == "yes") {
			$output .= "<a href=\"".$config['http_home_url']."auto/prodaja-".$row['price_id']."-".totranslit($row['name']." ".$row['model']).".html\">
			<img src=//img.artlebedev.ru/tools/decoder/\"".$config['http_home_url']."uploads/auto/thumbs/".$tipimg."\" border=\"0\" alt='".$row["name"]."&nbsp;".$row["model"]."'></a>";
		} else {
			$output .= "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_id&id=".$row['price_id']."\">
			<img src=//img.artlebedev.ru/tools/decoder/\"".$config['http_home_url']."uploads/auto/thumbs/".$tipimg."\" border=\"0\" alt='".$row["name"]."&nbsp;".$row["model"]."'></a>";
		}
	}

	$output .=
	"</DIV>
       <DIV class=new_cars_descr align=center>
       <DIV>".$row['year']."</DIV>".$cities[$row['city']]['name']."</DIV>
	   </TD></TR>
       <TR>
       <TD class=t10 style=\"PADDING-RIGHT: 0px; PADDING-LEFT: 0px; PADDING-BOTTOM: 0px; PADDING-TOP: 0px\"
		vAlign=bottom align=right height=10>
		<span class=new_cars_price>".$row['cost']." рубл.</span>
		</TD>
		</TR>
		</TBODY>
		</TABLE>
		</TD>\n";
  }

	$output .="<TD width=\"10%\">&nbsp;</TD></TR></TBODY></TABLE><BR></DIV>\n";

	return $output;
}
?>

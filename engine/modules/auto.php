<?

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

@ini_set("max_execution_time",0);

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

require_once ENGINE_DIR.'/inc/auto_functions.php';
require_once ENGINE_DIR.'/inc/makethumb.php';
require_once ENGINE_DIR.'/data/auto_config.php';

##===================================Функции создания и сохранения скриншота=====================##
$gd_version = 2;
class thumbnail2
{
	var $img;
    var $watermark_image_light;
    var $watermark_image_dark;

	function thumbnail2($imgfile)
	{
		//detect image format
		$this->img["format"]=ereg_replace(".*\.(.*)$","\\1",$imgfile);
		$this->img["format"]=strtoupper($this->img["format"]);
		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			$this->img["format"]="JPEG";
			$this->img["src"] = @imagecreatefromjpeg ($imgfile);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			$this->img["format"]="PNG";
			$this->img["src"] = @imagecreatefrompng ($imgfile);
		} elseif ($this->img["format"]=="GIF") {
			//GIF
			$this->img["format"]="GIF";
			$this->img["src"] = @imagecreatefromgif ($imgfile);
		} else {
			//DEFAULT
			echo "Этот тип файла не поддерживается! Скриншот к файлу можно только создавать из .jpg, gif и .png изображений!";
			exit();
		}
		$this->img["lebar"] = @imagesx($this->img["src"]);
		$this->img["tinggi"] = @imagesy($this->img["src"]);
		$this->img["lebar_thumb"] = $this->img["lebar"];
		$this->img["tinggi_thumb"] = $this->img["tinggi"];
		//default quality jpeg
		$this->img["quality"]=90;

		if ($this->img["src"] == "") {
			echo "Этот тип файла не поддерживается! Скриншот к файлу можно только создавать из .jpg, gif и .png изображений!";
			@unlink($imgfile);
			exit();

		}
	}

function size_auto2($size=100){
global $gd_version;

	if ($this->img["lebar"] < $size AND $this->img["tinggi"] < $size ) {
		$this->img["lebar_thumb"] = $this->img["lebar"];
		$this->img["tinggi_thumb"] = $this->img["tinggi"];
		return 0;
	} elseif ($this->img["lebar"]>=$this->img["tinggi"])
		{
    		$this->img["lebar_thumb"]=$size;
    		$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
		} else {
	    	$this->img["tinggi_thumb"]=$size;
    		$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
 	}

	if($gd_version==1)
          {
           $this->img["des"] = imagecreate($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
    	   @imagecopyresized ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
    	  }
	elseif($gd_version==2)
           {
            $this->img["des"] = imagecreatetruecolor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
    	    @imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
}

	$this->img["src"] = $this->img["des"];
	return 1;
}

function jpeg_quality2($quality=90)
	{
		//jpeg quality
		$this->img["quality"]=$quality;
	}

function save2($save="")
{

 		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			imagejpeg($this->img["src"],"$save",$this->img["quality"]);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			imagepng($this->img["src"],"$save");
		} elseif ($this->img["format"]=="GIF") {
			//GIF
			imagegif($this->img["src"],"$save");
		}

		imagedestroy($this->img["src"]);
}
function insert_watermark2($min_image)
    { global $config;
        $margin = 7;

		$this->watermark_image_light = ROOT_DIR.'/templates/'.$config['skin'].'/dleimages/watermark_light.png';
		$this->watermark_image_dark =  ROOT_DIR.'/templates/'.$config['skin'].'/dleimages/watermark_dark.png';

        $image_width = imagesx($this->img["src"]);
        $image_height = imagesy($this->img["src"]);

        list($watermark_width, $watermark_height)
            = getimagesize($this->watermark_image_light);


                $watermark_x = $image_width - $margin - $watermark_width;
                $watermark_y = $image_height - $margin - $watermark_height;


        $watermark_x2 = $watermark_x + $watermark_width;
        $watermark_y2 = $watermark_y + $watermark_height;

        if ($watermark_x < 0 OR $watermark_y < 0 OR
            $watermark_x2 > $image_width OR $watermark_y2 > $image_height OR
			$image_width < $min_image OR $image_height < $min_image)
        {
           return;
        }


        $test = imagecreatetruecolor(1, 1);
        imagecopyresampled($test, $this->img["src"], 0, 0, $watermark_x, $watermark_y, 1, 1, $watermark_width, $watermark_height);
        $rgb = imagecolorat($test, 0, 0);

        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        $max = min($r, $g, $b);
        $min = max($r, $g, $b);
        $lightness = (double)(($max + $min) / 510.0);
        imagedestroy($test);

        $watermark_image = ($lightness < 0.5) ? $this->watermark_image_light : $this->watermark_image_dark;

        $watermark = imagecreatefrompng($watermark_image);

        imagealphablending($this->img["src"], TRUE);
        imagealphablending($watermark, TRUE);

        imagecopy($this->img["src"], $watermark, $watermark_x, $watermark_y, 0, 0,$watermark_width, $watermark_height);

        imagedestroy($watermark);

    }


}

##===============================================================================================##



//****************Функция вывода таблицы объявлений*********************
	function show_list() {
	global $db, $tpl, $metatags, $config, $autoConfig, $tips, $cities;

 	$tip = stripslashes($_GET['tip']);
 	$tip_id = intval($tips[$tip]['tip_id']);

	$n = intval($_GET['n']);
	$m = intval($_GET['m']);

    $tip_search = intval($_GET['tip_id']);

	if ($tip_search!==0) {

		foreach ($tips as $key) {
		if ($key['tip_id'] == $tip_search) $tip = $key['tip_alt_name'];
		}
		$tip_id = $tip_search;
	}

	$year_search = intval($_GET['year']);
	$probeg_search = intval($_GET['probeg']);
	$city_search = intval($_GET['city']);


	$script = "<script type='text/javascript'>
			function makeSelectBrand(tip){
				new Ajax('/engine/ajax/makeselectbrand.php', {method: 'post', update: 'brands', data: 'tip=' + tip}).request();	}
			function makeSelect(id){
				var tip = document.getElementById(\"tip\").value;
				new Ajax('/engine/ajax/makeselect.php', {method: 'post', update: 'models', data: 'id=' + id + '& tip=' + tip}).request();	}
			function getModel(v){}
			</script>";

    //*******FOR PAGINATION**********//
	$astart = intval($_GET['astart']);
	if (!$astart or $astart == "") $astart = 0;

	if ($astart){
		$astart = $astart - 1;
		$astart = $astart * $autoConfig['num_price'];
	}
	$i = $astart;

	//*******FOR PAGINATION AND SEARCH**********//
    $link = "";
	if ($tip == "" and $tip_search=="") {
		$where = "";

		if ($config['allow_alt_url'] == "yes"){
			$link .= "<a href=\"".$config['http_home_url']."auto.html\">Продажа</a>";
	    } else {
	    	$link .= "<a href=\"".$config['http_home_url']."index.php?do=auto\">Продажа</a>";
	    }
		$tip_link = "";

	} else {
		$where = "WHERE tip = '$tip_id'";
		if ($config['allow_alt_url'] == "yes"){
			$link .= "<a href=\"".$config['http_home_url']."auto.html\">Продажа</a>&nbsp;&raquo;&nbsp;<a href=\"".$config['http_home_url']."auto/".$tip.".html\">".$tips[$tip]['tip_name']."</a>";
	    } else {
	    	$link .= "<a href=\"".$config['http_home_url']."index.php?do=auto\">Продажа</a>&nbsp;&raquo;&nbsp;<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=".$tip."\">".$tips[$tip]['tip_name']."</a>";
	    }
	    $tip_link = $tip."/";
   	}

    //***************ПО МАРКЕ АВТО**********************//
    if ($n !== 0 and $m == 0) {
    	if ($where !=="") $where .= " and name_id = '$n'";
    	else $where .= "WHERE name_id = '$n'";

    	$model_name = $db->super_query("SELECT * FROM " . PREFIX . "_auto_models WHERE name_id = '$n'");

        if ($config['allow_alt_url'] == "yes"){
        	$link .= "&nbsp;&raquo;&nbsp;<a href=\"".$config['http_home_url']."auto/".$tip.".html?n=".$model_name['name_id']."\" class=\"ntitle\">".$model_name['name']."</a>";
            $sort_link = "?n=$n";
        } else {
        	$link .= "&nbsp;&raquo;&nbsp;<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=".$tip."&n=".$model_name['name_id']."\" class=\"ntitle\">".$model_name['name']."</a>";
            $sort_link = "&n=$n";
        }

    }

   	//***************ПО МОДЕЛИ АВТО**********************//
        if ($m !== 0) {

    	if ($where !=="") $where .= " and id = '$m'";
    	else $where .= "WHERE id = '$m'";

    	$model_name = $db->super_query("SELECT * FROM " . PREFIX . "_auto_models WHERE id = '$m'");

		if ($config['allow_alt_url'] == "yes"){
        	$link .= "&nbsp;&raquo;&nbsp;<a href=\"".$config['http_home_url']."auto/".$tip.".html?n=".$model_name['name_id']."\" class=\"ntitle\">".$model_name['name']."</a>&nbsp;&raquo;&nbsp;<a href=\"".$config['http_home_url']."auto/".$tip.".html?m=".$model_name['id']."\" class=\"ntitle\">".$model_name['model']."</a>";
        	$sort_link = "?m=$m";
        } else {
        	$link .= "&nbsp;&raquo;&nbsp;<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=".$tip."&n=".$model_name['name_id']."\" class=\"ntitle\">".$model_name['name']."</a>&nbsp;&raquo;&nbsp;<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=".$tip."&m=".$model_name['id']."\" class=\"ntitle\">".$model_name['model']."</a>";
            $sort_link = "&m=$m";
        }
    }

    //***************ПО ГОДУ**********************//
        if ($year_search!==0 and $m == 0 and $n==0 and $tip_search!==0) {

			if ($where !=="") $where .= "and year > $year_search";
			else $where .= "WHERE year >= $year_search";
			$sort_link .= "?year=$year_search";

        } elseif($year_search!==0) {
			if ($where !=="") $where .= "and year > $year_search";
			else $where .= "WHERE year >= $year_search";
			$sort_link .= "?year=$year_search";
        }

    //***************ПО ПРОБЕГУ**********************//
        if ($probeg_search!==0) {

    	if ($where !=="") $where .= " and probeg < $probeg_search";
    	else $where .= "probeg < $probeg_search";
		$sort_link .= "&probeg=$probeg_search";
        }

    //***************ПО ГОРОДУ**********************//
        if ($city_search!==0) {

    	if ($where !=="") $where .= " and city = $city_search";
    	else $where .= "city = $city_search";
		$sort_link .= "&city=$city_search";
        }

      //***************ТОЛЬКО С ФОТО**********************//
        if ($_GET['photo']=="on") {
        $ch = "checked";

    	if ($where !=="") $where .= " and screenshot > 0";
    	else $where .= "screenshot > 0";
		$sort_link .= "&photo=on";
        }

    //*******END PAGINATION AND SORT**********//

	$table = $db->query("SELECT * FROM " . PREFIX . "_auto_price LEFT JOIN dle_auto_models ON ( " . PREFIX . "_auto_price.model_id = " . PREFIX . "_auto_models.id) ".$where." ORDER by date DESC LIMIT ".$astart.",".$autoConfig['num_price']);

	$tpl->load_template('auto_table.tpl');

	$tpl->copy_template .= $script;

    $tpl->copy_template = "<script type=\"text/javascript\" src=//img.artlebedev.ru/tools/decoder/\"/engine/ajax/show_models.js\"></script>".$tpl->copy_template;
    $tpl->set('{link}',  $link);
	$tpl->set_block("'\\[header\\](.*?)\\[/header\\]'si","\\1");
	$tpl->set_block("'\\[row\\](.*?)\\[/row\\]'si","");
	$tpl->set_block("'\\[footer\\](.*?)\\[/footer\\]'si","");
	$tpl->set_block("'\\[search\\](.*?)\\[/search\\]'si","");
	$tpl->compile('content');

	while ($row = $db->get_row($table)) {

    	if ($config['allow_alt_url'] == "yes"){
    		$link = "<a href=\"".$config['http_home_url']."auto/prodaja-".$row['price_id']."-".totranslit($row['name']." ".$row['model']).".html\">".$row['name']." ".$row['model']."</a>";
		} else {
		    $link = "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_id&id=".$row['price_id']."\">".$row['name']." ".$row['model']."</a>";
		}

    	if ($row['vip']) {
			$link = "<b>".$link."</b>";
    	}

    	if ($row["status"]) {
    		$link = "<strike>".$link."</strike>";
    	}
    	$block = array(
		 '{date}'          	=> $row['date'],
		 '{name}'          	=> $link,
		 '{year}'          	=> $row['year'],
		 '{cost}'			=> $row['cost'],
		 '{volume}'			=> $row['volume'],
		 '{fuel}'			=> $row['fuel'],
		 '{transmission}'	=> $row['transmission'],
		 '{privod}'			=> $row['privod'],
		 '{probeg}'			=> $row['probeg'],
		 '{city}'			=> $cities[$row['city']]['name'],
		 '{img}'			=> photo($row, "forlist"),
		 '{saws}'			=> $row['saws'],
		 );

    	$tpl->set('',  $block);
    	$tpl->set_block("'\\[header\\](.*?)\\[/header\\]'si","");
    	$tpl->set_block("'\\[row\\](.*?)\\[/row\\]'si","\\1");
    	$tpl->set_block("'\\[footer\\](.*?)\\[/footer\\]'si","");
    	$tpl->set_block("'\\[search\\](.*?)\\[/search\\]'si","");
    	$tpl->compile('content');

    	$i++;
    }

    $tpl->set_block("'\\[header\\](.*?)\\[/header\\]'si","");
    $tpl->set_block("'\\[row\\](.*?)\\[/row\\]'si","");
    $tpl->set_block("'\\[footer\\](.*?)\\[/footer\\]'si","\\1");
    $tpl->set_block("'\\[search\\](.*?)\\[/search\\]'si","");

    //**********************ПОИСК ПО БАЗЕ ОБЪЯВЛЕНИЙ*********************

    //*********************ТИП ТС****************************************//
	$tip_select = "<SELECT size=\"1\" id=\"tip\" name=\"tip_id\" onchange=\"makeSelectBrand(this.value)\" class=\"select\">
	<option value=\"0\">выберите тип ТС</option>";

	foreach ($tips as $tip) {
		if (isset($tip_search)) {
			if ($tip_search == $tip['tip_id']) $ts = "selected";
			else $ts = "";
		}
		$tip_select .= "<OPTION value=".$tip['tip_id']." {$ts}>".$tip['tip_name']."</OPTION>\n";
	}
	$tip_select .="</SELECT>";


	//********************МАРКИ ТРАНСПОРТНЫХ СРЕДСТВ***********************//

	$brand_select = "<div id=\"brands\">выберите тип ТС</div>";

	//********************МОДЕЛИ ТРАНСПОРТНЫХ СРЕДСТВ***********************//

	$model_select = "<div id=\"models\">выберите марку</div>";

	//********************ГОДЫ ПРОИЗВОДСТВА*********************************//
	$year_select = "<SELECT name=year class=\"select\">";
	$thisyear = intval(date("Y", (time())));

	for ($y = $autoConfig['start_year'];$y<$thisyear; $y++ ) {
		if (isset($year_search)) {
			if ($year_search == $y) $ys = "selected";
			else $ys = "";
		}
		$year_select .= "<OPTION value=$y {$ys}>$y</OPTION>";
	}
	$year_select .= "</SELECT>";

	//***********************ГОРОДА*********************************//
	$city_select = "<SELECT name=\"city\" class=\"select\">";

	foreach ($cities as $city) {
		if (isset($city_search)) {
			if ($city_search == $city['id']) $cs = "selected";
			else $cs = "";
		}
		$city_select.= "<OPTION value=\"{$city[id]}\" {$cs}>{$city[name]}</OPTION>";
	}
	$city_select.= "</SELECT>";

    if ($config['allow_alt_url'] == "yes"){
    		$formlink = $config['http_home_url']."auto.html";
	} else {
			$formlink = $config['http_home_url']."index.php?do=auto";	}

    $block = array(
		'{form}'          			=> "<FORM name=search action=\"{$formlink}\" method=get enctype=\"multipart/form-data\" >",
		'{/form}'          			=> "<INPUT id=subId type=submit value=\"Поиск\"></form>",
		'{tip_search}'          	=> $tip_select,
		'{year_search}'          	=> $year_select,
		'{brand_search}'          	=> $brand_select,
		'{probeg_search}'          	=> "<input type=text name=\"probeg\" value=\"{$probeg_search}\" size=8 class=inputstyle_01>",
		'{model_search}'          	=> $model_select,
		'{photo_search}'          	=> "<input type=checkbox name=photo {$ch}>",
		'{city_search}'          	=> $city_select,
		 );

    $tpl->set('',  $block);
    //**********************ПОИСК ПО БАЗЕ ОБЪЯВЛЕНИЙ*********************

    if ($autoConfig['search_form'] == "1") {
    	$tpl->set_block("'\\[search\\](.*?)\\[/search\\]'si","\\1");
    } else {
    	$tpl->set_block("'\\[search\\](.*?)\\[/search\\]'si","");
    }


	$db->free();
	$tpl->compile('content');
	$tpl->clear();


	//***************NAVIGATION******************//

	$tpl->load_template('navigation.tpl');
	//----------------------------------
    // Previous link
    //----------------------------------

	$no_prev = false;
	$no_next = false;

    if(isset($astart) and $astart != "" and $astart > 0){
        $prev = $astart / $autoConfig['num_price'];


        if ($config['allow_alt_url'] == "yes") {
          $prev_page = $url_page."/auto/".$tip_link.$prev.".html".$sort_link;
          $tpl->set_block("'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"".$prev_page."\">\\1</a>");
        } else {
          $prev_page = $PHP_SELF."?do=auto&op=show_list&tip=".$tip."&astart=".$prev.$sort_link;
          $tpl->set_block("'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"".$prev_page."\">\\1</a>");
        }

    }else{ $tpl->set_block("'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>"); $no_prev = TRUE; }

    //----------------------------------
    // Pages
    //----------------------------------
	if($autoConfig['num_price']){
	$row = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_auto_price LEFT JOIN " . PREFIX . "_auto_models ON (" . PREFIX . "_auto_price.model_id = " . PREFIX . "_auto_models.id) ".$where);
	$count_all = $row['count'];

	$pages_count = @ceil($count_all/$autoConfig['num_price']);
	$pages_start_from = 0;
	$pages = "";
	$pages_per_section = 3;
	if($pages_count > 10)
    {
            for($j = 1; $j <= $pages_per_section; $j++)
            {
               if($pages_start_from != $astart)
               {
						if ($config['allow_alt_url'] == "yes")
							$pages .= "<a href=\"".$url_page."/auto/".$tip_link.$j.".html".$sort_link."\">$j</a> ";
						else
							$pages .= "<a href=\"$PHP_SELF?do=auto&op=show_list&tip=".$tip."&astart=$j".$sort_link."\">$j</a> ";
                } else
                {
                     $pages .= " <span>$j</span> ";
                }

				$pages_start_from += $autoConfig['num_price'];
             }

             if(((($astart / $autoConfig['num_price']) + 1) > 1) && ((($astart / $autoConfig['num_price']) + 1) < $pages_count))
             {
               $pages   .= ((($astart / $autoConfig['num_price']) + 1) > ($pages_per_section + 2)) ? '... ' : ' ';
               $page_min = ((($astart / $autoConfig['num_price']) + 1) > ($pages_per_section + 1)) ? ($astart / $autoConfig['num_price']) : ($pages_per_section + 1);
               $page_max = ((($astart / $autoConfig['num_price']) + 1) < ($pages_count - ($pages_per_section + 1))) ? (($astart / $autoConfig['num_price']) + 1) : $pages_count - ($pages_per_section + 1);

               $pages_start_from = ($page_min - 1) * $autoConfig['num_price'];

                     for($j = $page_min; $j < $page_max + ($pages_per_section - 1); $j++)
                         {
                           if($pages_start_from != $astart)
                           {


							if ($config['allow_alt_url'] == "yes")
								$pages .= "<a href=\"".$url_page."/auto/".$tip_link.$j.".html".$sort_link."\">$j</a> ";
							else
								$pages .= "<a href=\"$PHP_SELF?do=auto&op=show_list&tip=".$tip."&astart=$j".$sort_link."\">$j</a> ";

                            }
                            else
                            {
                               $pages .= " <span>$j</span> ";
                            }

                            $pages_start_from += $autoConfig['num_price'];

                          }

                           $pages .= ((($astart / $autoConfig['num_price']) + 1) < $pages_count - ($pages_per_section + 1)) ? '... ' : ' ';

                        }
                        else
                        {
                                $pages .= '... ';
                        }

                        $pages_start_from = ($pages_count - $pages_per_section) * $autoConfig['num_price'];

                        for($j=($pages_count - ($pages_per_section - 1)); $j <= $pages_count; $j++)
                        {
                                if($pages_start_from != $astart)
                                {
									if ($config['allow_alt_url'] == "yes")
										$pages .= "<a href=\"".$url_page."/auto/".$tip_link.$j.".html".$sort_link."\">$j</a> ";
									else
										$pages .= "<a href=\"$PHP_SELF?do=auto&op=show_list&tip=".$tip."&astart=$j".$sort_link."\">$j</a> ";
                                }
                                else
                                {
                                        $pages .= " <span>$j</span> ";
                                }
                                $pages_start_from += $autoConfig['num_price'];
                        }

                }
                else
                {
                        for($j=1;$j<=$pages_count;$j++)
                        {
                                if($pages_start_from != $astart)
                                {
									if ($config['allow_alt_url'] == "yes")
										$pages .= "<a href=\"".$url_page."/auto/".$tip_link.$j.".html".$sort_link."\">$j</a> ";
									else
										$pages .= "<a href=\"$PHP_SELF?do=auto&op=show_list&tip=".$tip."&astart=$j".$sort_link."\">$j</a> ";

                                }
                                else
                                {
                                        $pages .= " <span>$j</span> ";
                                }
                                $pages_start_from += $autoConfig['num_price'];
                        }
                }
                $tpl->set('{pages}', $pages);
        }

//----------------------------------
// Next link
//----------------------------------
    if($autoConfig['num_price'] < $count_all and $i < $count_all){
		$next_page = $i / $autoConfig['num_price'] + 1;


		if ($config['allow_alt_url'] == "yes") {
			 $next = $url_page.'/auto/'.$tip_link.$next_page.'.html'.$sort_link;
			 $tpl->set_block("'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"".$next."\">\\1</a>");
		} else {
			 $next = $PHP_SELF."?do=auto&op=show_list&tip=".$tip."&astart=".$next_page.$sort_link;
			 $tpl->set_block("'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"".$next."\">\\1</a>");
		};

    }else{ $tpl->set_block("'\[next-link\](.*?)\[/next-link\]'si", "<span>\\1</span>"); $no_next = TRUE;}

	if  (!$no_prev OR !$no_next){

	$tpl->compile('content');

	}

	$tpl->clear();

	//****************NAVIGATION********************//


}
//****************Конец функции вывода таблицы объявлений*********************


//****************Функция вывода определенного объявления*******************************
	function show_id() {
	global $tpl, $db, $config, $thisdate, $is_logged, $member_id, $user_group, $metatags, $tips, $cities;

	require_once ENGINE_DIR.'/data/auto_config.php';

	$id = intval ($_GET['id']);
 	$num = $db->num_rows($db->query("SELECT * FROM " . PREFIX . "_auto_price WHERE price_id='$id'"));

	if (empty ($_GET['id']) or $num == 0){
		@header("HTTP/1.0 404 Not Found");
		if ($config['allow_alt_url'] == 1){
			$url=$config['http_home_url']."auto.html";
		} else {
			$url=$config['http_home_url']."index.php?do=auto";
		}

		msgbox ($lang['all_err_1'], "Недопустимый запрос<br><a href=\"{$url}\">Вернуться назад</a>");

	} else {

		$tpl->load_template('auto_show_id.tpl');

		$row =  $db->super_query("SELECT * FROM " . PREFIX . "_auto_price LEFT JOIN " . PREFIX . "_auto_models ON (" . PREFIX . "_auto_price.model_id = " . PREFIX . "_auto_models.id) WHERE price_id = '$id'");

        $block = array(
		 '{model_id}'          	=> $row['model_id'],
		 '{model}'				=> $row["name"]."&nbsp;".$row["model"],
		 '{name}'				=> $row["name"],
		 '{model_name}'			=> $row["model"],
		 '{year}'				=> $row['year'],
		 '{cost}'				=> $row['cost'],
		 '{privod}'				=> $row['privod'],
		 '{transmission}'		=> $row['transmission'],
		 '{volume}'				=> $row['volume'],
		 '{color}'				=> $row['color'],
		 '{fuel}'				=> $row['fuel'],
		 '{probeg}'				=> $row['probeg'],
		 '{saled}'				=> $row['saled'],
		 '{photo}'				=> photo($row, "forshow"),
		 '{memo}'				=> "<div id='comm-id-".$row['price_id']."'>".stripslashes($row['memo'])."</div>",
		 '{city}'				=> $cities[$row['city']]['name'],
		 '{date}'				=> $row['date'],
		 '{saws}'				=> $row['saws'],
		 '{phone}'				=> $row['phone'],
		 '{author_name}'		=> $row['author_name'],
		 '{email}'				=> "<a href=\"mailto:".$row["email"]."\">".$row["email"]."</a>",
		 );

		foreach ($tips as $key) {
			if ($key['tip_id'] == $row['tip']) $tip = $key['tip_alt_name'];
		}

		if ($row['status'] == 1) {
			$tpl->set('{status}',  "<br>Продано!!!");
		} else {
			$tpl->set('{status}',  "");
		}

		$tpl->set('',  $block);

        $metatags['title'] = "Продажа ".$row['name']."&nbsp;".$row['model'];
        create_keywords ($row["name"].",".$row["model"].",".$row['memo']);

        if ($config['allow_alt_url'] == "yes") {
        	$speedbar = "<a href=\"".$config['http_home_url']."auto.html\" class=\"ntitle\">Продажа</a>&nbsp;&raquo;&nbsp;
        	<a href=\"".$config['http_home_url']."auto/".$tip.".html\" class=\"ntitle\">".$tips[$tip]['tip_name']."</a>&nbsp;&raquo;&nbsp;
        	<a href=\"".$config['http_home_url']."auto/".$tip.".html?n=".$row['name_id']."\" class=\"ntitle\">".$row['name']."</a>&nbsp;&raquo;&nbsp;
        	<a href=\"".$config['http_home_url']."auto/".$tip.".html?m=".$row['id']."\" class=\"ntitle\">".$row['model']."</a>";
        } else {
        	$speedbar = "<a href=\"".$config['http_home_url']."index.php?do=auto\" class=\"ntitle\">Продажа</a>&nbsp;&raquo;&nbsp;
        	<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=$tip\" class=\"ntitle\">".$tips[$tip]['tip_name']."</a>&nbsp;&raquo;&nbsp;
        	<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=$tip&n=".$row['name_id']."\" class=\"ntitle\">".$row['name']."</a>&nbsp;&raquo;&nbsp;
        	<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=$tip&m=".$row['id']."\" class=\"ntitle\">".$row['model']."</a>";
        }

		$tpl->set('{speedbar}',  $speedbar);

		if ($config['allow_alt_url'] == "yes") {
			$tpl->set('{model_link}',  "<a href=\"".$config['http_home_url']."auto/".$tip.".html?m=".$row['id']."\">Продажа ".$row['name']."&nbsp;".$row['model']."</a>");
        } else {
            $tpl->set('{model_link}',  "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=$tip&m=".$row['id']."\">Продажа ".$row['name']."&nbsp;".$row['model']."</a>");
        }
        if (($is_logged AND $row["author"] == $member_id['user_id']) || $member_id['user_group'] == "1") {


			if ($config['allow_alt_url'] == "yes") {
				$tpl->set('[edit]',"<div class=comment><a href=\"".$config['http_home_url']."auto/saled/".$row['price_id'].".html\">продано</a> || <a href=\"".$config['http_home_url']."auto/edit/".$row['price_id'].".html\">редактировать</a>");
			} else {
				$tpl->set('[edit]',"<div class=comment><a href=\"".$config['http_home_url']."index.php?do=auto&op=saled&id=".$row['price_id']."\">продано</a> || <a href=\"".$config['http_home_url']."index.php?do=auto&op=edit&id=".$row['price_id']."\">редактировать</a>");
			}

			$tpl->set('[/edit]',"</div>");

			}

		else $tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");


		$tpl->compile('content');

		$tpl->clear();
		$db->query("UPDATE " . PREFIX . "_auto_price set saws=saws+1 where price_id = '$id'");
		$db->free();

	}
}
//****************Конец функции вывода определенного объявления**********************


//****************	Функция редактирования объявления *******************************
	function edit () {
	global $tpl, $db, $config, $thisdate, $is_logged, $member_id, $user_group, $metatags, $tips, $cities, $autoConfig;

	require_once ENGINE_DIR.'/data/auto_config.php';

	$id = intval ($_GET['id']);

	$num = $db->num_rows($db->query("SELECT * FROM " . PREFIX . "_auto_price WHERE price_id = '$id'"));

	if (empty ($_GET['id']) or $num == 0){
		@header("HTTP/1.0 404 Not Found");

		if ($config['allow_alt_url'] == 1){
			$url=$config['http_home_url']."auto/";
		} else {
			$url=$config['http_home_url']."index.php?do=auto";
		}

		msgbox ($lang['all_err_1'], "Недопустимый запрос<br><a href=\"{$url}\">Вернуться назад</a>");
	}


	$position =  $db->super_query("SELECT author FROM " . PREFIX . "_auto_price WHERE price_id = '$id'");
	$author = $position["author"];


	if (($is_logged AND $author == $member_id['user_id'] AND $autoConfig['allow_edit_price'] == 1) OR $member_id['user_group'] == "1") {

			$tpl->load_template('auto_edit_id.tpl');

            require_once ENGINE_DIR.'/classes/parse.class.php';

			$parse = new ParseFilter();
			$parse->safe_mode = true;

			$row =  $db->super_query("SELECT * FROM " . PREFIX . "_auto_price LEFT JOIN " . PREFIX . "_auto_models ON " . PREFIX . "_auto_price.model_id = " . PREFIX . "_auto_models.id WHERE price_id = '$id'");
            $memo = $parse->decodeBBCodes($row['memo'], false);

            $city_select = "<SELECT name=\"city\" class=\"select\">";

            foreach ($cities as $city) {
				if ($city['id'] == $row['city']) $selected = "selected";
				else $selected = "";
				$city_select.= "<OPTION value=\"{$city[id]}\" $selected>{$city[name]}</OPTION>";
            }
            $city_select.= "</SELECT>";

            $year_select = "<SELECT name=year class=\"select\">";
			$thisyear = intval(date("Y", (time())));

			for ($i = $autoConfig['start_year'];$i<$thisyear; $i++ ) {
				if ($i == $row['year']) $selected = "selected";
				else $selected = "";
				$year_select .= "<OPTION value=$i $selected>$i</OPTION>";
			}
			$year_select .= "</SELECT>";

            $addtype = "addauto";
            include_once ENGINE_DIR.'/modules/bbcode.php';



			$block = array(
		 	'{form}'				=> "<FORM name=alt action=\"\" id=\"add_form\" method=post enctype=\"multipart/form-data\" >",
		 	'{/form}'				=> "<INPUT id=subId type=submit value=\"Записать\"><input type=\"hidden\" name=\"op\" value=\"alt\"><input type=\"hidden\" name=\"id\" value=\"{$id}\"></form>",
		 	'{model}'				=> $row["name"]."&nbsp;".$row["model"],
		 	'{name}'				=> $row["name"],
		 	'{model_name}'			=> $row["model"],
		 	'{year}'				=> $year_select,
		 	'{cost}'				=> "<INPUT type=\"text\" name=\"cost\" value=\"{$row[cost]}\" class=\"inputstyle_03\">",
		 	'{privod}'				=> makeDropDown(array("пер."=>"передний","зад."=>"задний","4WD"=>"4WD"), "privod", $row["privod"]),
		 	'{transmission}'		=> makeDropDown(array("мех."=>"механика","авт."=>"автомат","вар."=>"вариатор"), "transmission", $row["transmission"]),
		 	'{volume}'				=> "<INPUT type=\"text\" size=\"4\" name=\"volume\" value=\"{$row[volume]}\" class=\"inputstyle_03\">",
		 	'{color}'				=> "<INPUT type=\"text\" name=\"color\" value=\"{$row[color]}\" class=\"inputstyle_03\">",
		 	'{fuel}'				=> makeDropDown(array("бен."=>"бензин","диз."=>"дизель","гиб."=>"гибридный", "газ"=>"газ"), "fuel", $row["fuel"]),
		 	'{probeg}'				=> "<INPUT type=\"text\" name=\"probeg\" value=\"{$row[probeg]}\" class=\"inputstyle_03\">",
		 	'{saled}'				=> $row['saled'],
		 	'{photo}'				=> photo($row, "forshow"),
		 	'{bbcodes}'				=> $bb_code,
		 	'{memo}'				=> "<textarea name=\"memo\" id=\"memo\" class=\"inputstyle_03\" style=\"width:100%;height:160px;\" onclick=\"setNewField(this.name, document.getElementById( 'add_form' ))\">{$memo}</textarea>",
		 	'{city}'				=> $city_select,
		 	'{date}'				=> $row['date'],
		 	'{saws}'				=> $row['saws'],
		 	'{phone}'				=> "<INPUT type=\"text\" name=\"phone\" value=\"{$row[phone]}\" class=\"inputstyle_03\">",
		 	'{author_name}'			=> "<INPUT type=\"text\" name=\"author_name\" value=\"{$row[author_name]}\" class=\"inputstyle_03\">",
		 	'{email}'				=> "<INPUT type=\"text\" name=\"email\" value=\"{$row[email]}\" class=\"inputstyle_03\">",
		 	);

			if ($row["status"] == 0) {
				$tpl->set('{status}', "<input type=\"checkbox\" name=\"status\">");
			} else {
				$tpl->set('{status}', "<input type=\"checkbox\" name=\"status\" checked>");
			}

			foreach ($tips as $key) {
				if ($key['tip_id'] == $row['tip']) $tip = $key['tip_alt_name'];
			}

			$tpl->set('',  $block);

            if ($config['allow_alt_url'] == "yes") {
        		$speedbar = "<a href=\"".$config['http_home_url']."auto.html\" class=\"ntitle\">Продажа</a>&nbsp;&raquo;&nbsp;
        		<a href=\"".$config['http_home_url']."auto/".$tip.".html\" class=\"ntitle\">".$tips[$tip]['tip_name']."</a>&nbsp;&raquo;&nbsp;
        		<a href=\"".$config['http_home_url']."auto/".$tip.".html?n=".$row['name_id']."\" class=\"ntitle\">".$row['name']."</a>&nbsp;&raquo;&nbsp;
        		<a href=\"".$config['http_home_url']."auto/".$tip.".html?m=".$row['id']."\" class=\"ntitle\">".$row['model']."</a>";
        	} else {
        		$speedbar = "<a href=\"".$config['http_home_url']."index.php?do=auto\" class=\"ntitle\">Продажа</a>&nbsp;&raquo;&nbsp;
        		<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=$tip\" class=\"ntitle\">".$tips[$tip]['tip_name']."</a>&nbsp;&raquo;&nbsp;
        		<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=$tip&n=".$row['name_id']."\" class=\"ntitle\">".$row['name']."</a>&nbsp;&raquo;&nbsp;
        		<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_list&tip=$tip&m=".$row['id']."\" class=\"ntitle\">".$row['model']."</a>";
        	}

            $tpl->set('{speedbar}',  $speedbar);

			$metatags['title'] = stripslashes("Редактирование объявления №".$row['price_id']."&nbsp;".$row['name']."&nbsp;".$row['model']);

			$tpl->compile('content');

			$tpl->clear();
			$db->free();

	} else {

			if ($config['allow_alt_url'] == 1){
				$url=$config['http_home_url']."auto.html";
			} else {
				$url=$config['http_home_url']."index.php?do=auto";
			}

			msgbox ($lang['all_err_1'], "Возможно пользователям не разрешено изменять свои объявления<br><a href=\"{$url}\">Вернуться назад</a>");
	}

}

//****************	Конец функции редактирования объявления *************************

//****************	Функция обновления объявления *******************************
	function alt () {
	global $tpl, $db, $config, $is_logged, $member_id, $user_group, $metatags;

	require_once ENGINE_DIR.'/data/auto_config.php';

	$id = intval ($_GET['id']);
	$num = $db->num_rows($db->query("SELECT * FROM " . PREFIX . "_auto_price WHERE price_id = '$id'"));

	if (empty ($_GET['id']) or $num == 0){
		msgbox ($lang['all_err_1'], "Недопустимый запрос<br><a href=\"{$url}\">Вернуться назад</a>");
	}

	$row =  $db->super_query("SELECT * FROM " . PREFIX . "_auto_price LEFT JOIN " . PREFIX . "_auto_models ON " . PREFIX . "_auto_price.model_id = " . PREFIX . "_auto_models.id WHERE price_id = '$id'");
	$author = $row['author'];

	if (($is_logged AND $author == $member_id['user_id'] AND $autoConfig['allow_edit_price'] == 1) || $member_id['user_group'] == "1") {

		require_once ENGINE_DIR.'/classes/parse.class.php';
		$tpl->load_template('auto_alt_id.tpl');

		$parse = new ParseFilter();
		$parse->safe_mode = true;

		$year = $parse->process($_POST['year']);
		$cost = $parse->process($_POST['cost']);
		$transmission = $parse->process($_POST['transmission']);
		$volume = $parse->process($_POST['volume']);
		$color = $parse->process($_POST['color']);
		$fuel = $parse->process($_POST['fuel']);
		$privod = $parse->process($_POST['privod']);
		$probeg = $parse->process($_POST['probeg']);
		$memo = $db->safesql($parse->BB_Parse($parse->process($_POST['memo'])));
		$author_name = $parse->process($_POST['author_name']);
		$email = $parse->process($_POST['email']);
		$city = $parse->process($_POST['city']);
		$phone = $parse->process($_POST['phone']);

		if ($_POST['status']) {
			$status =1;
		}else{
			$status=0;
		}

		$metatags['title'] = ("Редактирование объявления № ". $id);

		$db->query("UPDATE " . PREFIX . "_auto_price SET `year` = '$year', `cost` = '$cost', `transmission` = '$transmission', `privod` = '$privod', `volume` = '$volume', `color` = '$color', `fuel` = '$fuel', `probeg` = '$probeg', `memo` = '$memo', `author_name` = '$author_name', `email` = '$email', `city` = '$city', `phone` = '$phone', `status` = '$status' WHERE `price_id` = '$id' LIMIT 1");
		$tpl->set('{id}', $id);

		@unlink(ENGINE_DIR . '/cache/system/last_price.php');
    	clear_cache();

        if ($config['allow_alt_url'] == "yes"){
    		$link = "<a href=\"".$config['http_home_url']."auto/prodaja-".$row['price_id']."-".totranslit($row['name']." ".$row['model']).".html\">".$row['name']." ".$row['model']."</a>";
		} else {
		    $link = "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_id&id=".$row['price_id']."\">".$row['name']." ".$row['model']."</a>";
		}

        $tpl->set('{link}', $link);

		$tpl->compile('content');
		$tpl->clear();
		$db->free();
	} else {

		if ($config['allow_alt_url'] == 1){
			$url=$config['http_home_url']."auto.html";
		} else {
			$url=$config['http_home_url']."index.php?do=auto";
		}

		msgbox ($lang['all_err_1'], "Недопустимый запрос<br><a href=\"{$url}\">Вернуться назад</a>");
	}
}

//****************	Конец функции обновления объявления *******************************

//****************	Функция "Продано" *************************************************
	function saled () {
	global $tpl, $db, $config, $is_logged, $member_id, $user_group, $metatags, $autoConfig;

	require_once ENGINE_DIR.'/data/auto_config.php';

	$id = intval ($_GET['id']);
	$num = $db->num_rows($db->query("SELECT * FROM " . PREFIX . "_auto_price WHERE price_id = '$id'"));
	if (empty ($_GET['id']) or $num == 0){
		@header("HTTP/1.0 404 Not Found");

		if ($config['allow_alt_url'] == 1){
			$url=$config['http_home_url']."auto.html";
		} else {
			$url=$config['http_home_url']."index.php?do=auto";
		}

		msgbox ($lang['all_err_1'], "Недопустимый запрос<br><a href=\"{$url}\">Вернуться назад</a>");
	}

	$row =  $db->super_query("SELECT * FROM " . PREFIX . "_auto_price LEFT JOIN " . PREFIX . "_auto_models ON " . PREFIX . "_auto_price.model_id = " . PREFIX . "_auto_models.id WHERE price_id = '$id'");
	$author = $row['author'];

	if (($is_logged AND $author == $member_id['user_id']) AND $autoConfig['allow_sell_price'] == 1 || $member_id['user_group'] == "1") {

		$tpl->load_template('auto_alt_id.tpl');

		$db->query("UPDATE " . PREFIX . "_auto_price SET status = '1' WHERE price_id = '$id' LIMIT 1");
		$tpl->set('{id}', $id);

        if ($config['allow_alt_url'] == "yes"){
    		$link = "<a href=\"".$config['http_home_url']."auto/prodaja-".$row['price_id']."-".totranslit($row['name']." ".$row['model']).".html\">".$row['name']." ".$row['model']."</a>";
		} else {
		    $link = "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_id&id=".$row['price_id']."\">".$row['name']." ".$row['model']."</a>";
		}

        $tpl->set('{link}', $link);

		$tpl->compile('content');
		$tpl->clear();
		$db->free();

	} else {

		if ($config['allow_alt_url'] == 1){
			$url=$config['http_home_url']."auto.html";
		} else {
			$url=$config['http_home_url']."index.php?do=auto";
		}

	msgbox ($lang['all_err_1'], "Недопустимый запрос<br><a href=\"{$url}\">Вернуться назад</a>");

	}
}

//****************	конец функции "Продано" *******************************************

//****************	Функция добавления объявления **************************************

	function add () {
	global $tpl, $db, $lang, $config, $is_logged, $member_id, $user_group, $metatags, $thisdate, $xfields, $autoConfig, $tips, $auto_brands, $cities;

	include_once ENGINE_DIR.'/data/auto_config.php';

	session_start();

	$action = $_POST['action'];
	$user = $member_id['user_id'];

	$vip_groups  = explode (",", $autoConfig['vip_group']);

	if (in_array($member_id['group_id'], $vip_groups)) {
		$vip = 1;
	} else {
		$vip = 0;
	}


	if (!isset($tip)) {
		$tip = intval($_GET["tip"]);
	}

	if (!$is_logged) {
		msgbox ("Внимание!!!", "<p>Незарегистрированным пользователям нельзя добавлять объявления!!!</p><p>Вы можете зарегистрироваться <a href=\"http://www.ulan-udeauto.ru/index.php?do=register\">здесь.</a></p>");
	} else {

		if ($action !== "upload") {  //если action не upload

			if ($tip=="") {
				$tip=1;
			}

			$script = "
			<script language=\"javascript\" type=\"text/javascript\">
			function AddImages() {
     		var tbl = document.getElementById('tblSample');
     		var lastRow = tbl.rows.length;
     		if (lastRow < 5) {
	 		var iteration = lastRow+1;
     		var row = tbl.insertRow(lastRow);
     		var cellRight = row.insertCell(0);
     		var el = document.createElement('input');
     		el.setAttribute('type', 'file');
     		el.setAttribute('name', 'file_' + iteration);
     		el.setAttribute('size', '41');
     		el.setAttribute('value', iteration);
     		el.setAttribute('class', 'inputstyle_03');
     		cellRight.appendChild(el);
     		document.getElementById('images_number').value = iteration;}}

			function RemoveImages() {
    		var tbl = document.getElementById('tblSample');
    		var lastRow = tbl.rows.length;
    		if (lastRow > 1){
    		tbl.deleteRow(lastRow - 1);
			document.getElementById('images_number').value =  document.getElementById('images_number').value - 1;
     		}}</script>
     		<script type='text/javascript'>
			function makeSelectBrand(tip){
				new Ajax('/engine/ajax/makeselectbrand.php', {method: 'post', update: 'brands', data: 'tip=' + tip}).request();	}
			function makeSelect(id){
				var tip = document.getElementById(\"tip\").value;
				new Ajax('/engine/ajax/makeselect.php', {method: 'post', update: 'models', data: 'id=' + id + '& tip=' + tip}).request();	}
			function getModel(v){
				var model = document.getElementById(\"model\");
				model.setAttribute('value', v);}
			</script>";

			$tpl->load_template('auto_add.tpl');
			$tpl->copy_template = $script.$tpl->copy_template;

        //***********************FORM*********************************//
            $tpl->set('{form}', "<FORM name=alt id=\"add_form\" action=\"\" method=\"post\" enctype=\"multipart/form-data\">");

		//********************ТИПЫ ТРАНСПОРТНЫХ СРЕДСТВ***********************//

			$tip_select = "<SELECT size=\"1\" id=\"tip\" name=\"tip\" onchange=\"makeSelectBrand(this.value)\" class=\"select\">
			<option value=\"0\">выберите тип ТС</option>";

			foreach ($tips as $tip) {
				$tip_select .= "<OPTION value=".$tip['tip_id'].">".$tip['tip_name']."</OPTION>\n";
			}

            $tip_select .="</SELECT>";
            $tpl->set('{tip_select}', $tip_select);

		//********************МАРКИ ТРАНСПОРТНЫХ СРЕДСТВ***********************//

			$brand_select = "<div id=\"brands\">
			<input type=text name=\"name\" value=\"выберите марку\" size=25 readonly
			class=inputstyle_01></div>";

            $tpl->set('{brand_select}', $brand_select);

		//********************МОДЕЛИ ТРАНСПОРТНЫХ СРЕДСТВ***********************//

			$model_select = "<div id=\"models\">
			<input type=text name=\"model\" id=\"model\" value=\"выберите модель\" size=25 readonly class=inputstyle_01>
			</div>";

            $tpl->set('{model_select}', $model_select);

		//********************ГОДЫ ПРОИЗВОДСТВА*********************************//
            $year_select = "<SELECT name=year class=\"select\">";
			$thisyear = intval(date("Y", (time())));

			for ($i = $autoConfig['start_year'];$i<$thisyear; $i++ ) {
				$year_select .= "<OPTION value=$i>$i</OPTION>";
			}
			$year_select .= "</SELECT>";

			$tpl->set('{year_select}', $year_select);

        //***********************ЦЕНА*********************************//
            $tpl->set('{cost_input}', "<INPUT type=\"text\" name=\"cost\" class=\"inputstyle_03\">");


        //***********************ТРАНСМИССИЯ*********************************//
            $tpl->set('{transmission_input}', "<select name=\"transmission\" class=\"select\">
			<OPTION value=\"авт.\">автомат</OPTION>
			<OPTION value=\"мех.\">механика</OPTION>
			<OPTION value=\"вар.\">вариатор</OPTION>
			</SELECT>");

        //***********************ПРИВОД*********************************//
            $tpl->set('{privod_input}', "<select name=\"privod\" class=\"select\">
			<OPTION value=\"пер.\">передний</OPTION>
			<OPTION value=\"зад.\">задний</OPTION>
			<OPTION value=\"4WD\">4WD</OPTION>
			</SELECT>");

        //***********************ОБЪЕМ*********************************//
            $tpl->set('{volume_input}', "<INPUT type=\"text\" size=\"4\" name=\"volume\" class=\"inputstyle_03\">");

        //***********************ЦВЕТ*********************************//
            $tpl->set('{color_input}', "<INPUT type=\"text\" name=\"color\" class=\"inputstyle_03\">");

        //***********************ТОПЛИВО*********************************//
            $tpl->set('{fuel_input}', "<SELECT name=\"fuel\" class=\"select\">
			<OPTION value=\"бен.\">бензин</OPTION>
			<OPTION value=\"диз.\">дизель</OPTION>
			<OPTION value=\"гиб.\">гибрид</OPTION>
			<OPTION value=\"газ\">газ</OPTION>
			</SELECT>");

        //***********************ПРОБЕГ*********************************//
            $tpl->set('{probeg_input}', "<INPUT type=\"text\" name=\"probeg\" class=\"inputstyle_03\">");

        //***********************ФОТО*********************************//
            $tpl->set('{photo_input}', "<table id=\"tblSample\" class=\"upload\">
 				<tr id=\"row\"><td>
  				<input type=\"file\" size=\"41\" name=\"file_1\" class=\"inputstyle_03\"></td>
				</tr>
				</table>
				<div>
				<input type=button class=buttons value=' - ' style=\"width:30px;\" title='{$lang['images_rem_tl']}' onClick=\"RemoveImages();return false;\">
				<input type=button class=buttons value=' + ' style=\"width:30px;\" title='{$lang['images_add_tl']}' onClick=\"AddImages();return false;\"></div>
				<br>Вы можете разместить до пяти фотографий!");

        //***********************ДОП ИНФОРМАЦИЯ*********************************//
            $addtype = "addauto";
            include_once ENGINE_DIR.'/modules/bbcode.php';
            $tpl->set('{bbcode}',$bb_code);
            $tpl->set('{memo_input}', "<textarea name=\"memo\" id=\"memo\" class=\"inputstyle_03\" style=\"width:100%;height:160px;\" onclick=\"setNewField(this.name, document.getElementById( 'add_form' ))\"></textarea>");

        //***********************АВТОР*********************************//
            $tpl->set('{author_input}', "<INPUT type=\"text\" name=\"author_name\" value=\"{$member_id[fullname]}\" class=\"inputstyle_03\">");

        //***********************EMAIL*********************************//
            $tpl->set('{email_input}', "<INPUT type=\"text\" name=\"email\" value=\"{$member_id[email]}\" class=\"inputstyle_03\">");

        //***********************ГОРОДА*********************************//
            $city_select = "<SELECT name=\"city\" class=\"select\">";

            foreach ($cities as $city) {
				$city_select.= "<OPTION value=\"{$city[id]}\">{$city[name]}</OPTION>";
            }
            $city_select.= "</SELECT>";
            $tpl->set('{city_input}', $city_select);

        //***********************/FORM*********************************//
            $tpl->set('{/form}', "<input type=\"hidden\" name=\"images_number\" id=\"images_number\" value=\"1\">
			<input type=\"hidden\" name=\"model\" id=\"model\" value=\"\">
			<input type=\"hidden\" name=\"action\" value=\"upload\">
			<INPUT id=subId type=submit value=\"Записать\">
			</form>");


			//$metatags['title'] = "Добавление объявления о продаже - ".$tips[$tip]['tip_name'];
			$tpl->compile('content');
			$tpl->clear();

		} else {//если action = upload


			require_once ENGINE_DIR.'/data/auto_config.php';
			require_once ENGINE_DIR.'/classes/parse.class.php';

			$parse = new ParseFilter();
			$parse->safe_mode = true;

			$model = $parse->process($_POST['model']);
			$year = $parse->process($_POST['year']);
			$cost = $parse->process($_POST['cost']);
			$transmission = $parse->process($_POST['transmission']);
			$volume = $parse->process($_POST['volume']);
			$color = $parse->process($_POST['color']);
			$fuel = $parse->process($_POST['fuel']);
			$privod = $parse->process($_POST['privod']);
			$probeg = $parse->process($_POST['probeg']);
			$memo = $db->safesql($parse->BB_Parse($parse->process($_POST['memo'])));
			$author_name = $parse->process($_POST['author_name']);
			$email = $parse->process($_POST['email']);
			$city = $parse->process($_POST['city']);
			$phone = $parse->process($_POST['phone']);
			$tip = $parse->process($_POST['tip']);
			$author = $user;

			if(trim($model) == "" or !$model){ $stop = 'Выберите модель!<br>'; }
			if(trim($year) == "" or !$year){ $stop .= 'Укажите год производства!<br>'; }
			if(trim($cost) == "" or !$cost){ $stop .= 'Укажите стоимость авто!<br>'; }
			if(trim($volume) == "" or !$volume){ $stop .= 'Укажите объем двигателя!<br>'; }
			if(trim($phone) == "" or !$phone){ $stop .= 'Укажите свой телефон!<br>'; }

##==========================================Создание и сохранение скриншота======================##
			if ($_POST["images_number"] > 0) {  // если количество фото > 1
			$photos = array();
			$images_number = $_POST["images_number"];

			for ($n = 1; $n < ($images_number+1); $n++) {

				if ($autoConfig['allow_screenshot'] == 1) {
					$MAX_SIZE_THUMB = $autoConfig['maxsize_thumb'];
					$allowed_extensions_thumb = $autoConfig['allowed_screen'];
					$FILE_EXTS_THUMB  = explode (",", $allowed_extensions_thumb);

					foreach($FILE_EXTS_THUMB as $value) $FILE_EXTS_THUMB[] = ".".$value;

					$upload_dir = ROOT_DIR."/uploads/auto/photos/";
					$upload_dir_thumb = ROOT_DIR."/uploads/auto/thumbs/";

					if (!empty($_FILES['file_'.$n]['name'])) {
  					$file_type_thumb = $_FILES['file_'.$n]['type'];
  					$file_name_thumb = $_FILES['file_'.$n]['name'];
  					$file_name_arr_thumb = explode(".",$file_name_thumb);
					$type_thumb = end($file_name_arr_thumb);
					$file_name_thumb = time()."_".totranslit (stripslashes($file_name_arr_thumb[0])).".".totranslit($type_thumb);
					$filesize1_thumb = $_FILES['file_'.$n]['size'];
					$file_ext_thumb = strtolower(substr($file_name_thumb,strrpos($file_name_thumb,".")));

  					//Thumb Size Check
					if ( $filesize1_thumb > $MAX_SIZE_THUMB) {
						$stop .= 'Файл превышает максимально допустимый размер закачиваемого скриншота<br>';

					} else if (!in_array($file_ext_thumb, $FILE_EXTS_THUMB)){ //Thumb Extension Check
  					    $stop .= 'Извините, но такой тип изображения не разрешён для загрузки. Поддерживаются только .jpg, gif и .png изображения.<br>';
  					} else {
    					$temp_name_thumb = $_FILES['file_'.$n]['tmp_name'];
						$file_path = $upload_dir.$file_name_thumb;
						$file_path_thumb = $upload_dir_thumb.$file_name_thumb;
						if (is_uploaded_file($_FILES['file_'.$n]['tmp_name'])){
								//Download screenshot
							if (!$stop){
								@move_uploaded_file($temp_name_thumb, $file_path);
								@copy($file_path, $file_path_thumb);
								@chmod ($file_path, 0777);
								@chmod ($file_path_thumb, 0777);

								//Make and save a screenshot

								$thumb=new thumbnail2($file_path);
								if ($thumb->size_auto2($autoConfig['width_photo'])) {
									$thumb->jpeg_quality2($autoConfig['jpeg_quality']);

									if ($autoConfig['allow_watermark'] == "1")
				  						$thumb->insert_watermark2($autogConfig['max_watermark']);
          								$thumb->save2($file_path);
          								@chmod ($file_path, 0777);
								}

								$thumb=new thumbnail2($file_path_thumb);

								if ($thumb->size_auto2($autoConfig['widththumb'])) {
									$thumb->jpeg_quality2($config['jpeg_quality']);

									if ($autoConfig['allow_watermark_for_thumbs'] == "1") $thumb->insert_watermark2($autogConfig['max_watermark']);
										$thumb->save2($file_path_thumb);
										@chmod ($file_path_thumb, 0777);
								}

								$photos[$n] = $file_name_thumb;
							}

      					} else {
      						$stop .= 'Фото не было загружено на сервер.';
      					}
					}
				}

			}

		} // конец цикла
	} //если количество фото больше 1
##===============================================================================================##


	if ($stop) {
		$stop .= "  <a href=\"javascript:history.go(-1)\">вернуться назад</a>";
		msgbox ("Не все данные введены!!!", $stop);
	} else {

		$now = intval(time());
		$exp = date("Y-m-d", $now + $autoConfig['num_days']*24*60*60);

		$number = count($photos);
		$db->query("INSERT INTO " . PREFIX . "_auto_price ( `price_id` , `vip`, `date` , `model_id` , `year` , `cost` , `transmission` , `volume` , `color` , `kuzov` , `fuel` , `privod` , `city` , `probeg` , `memo` , `author_name` , `email` , `screenshot` , `phone` , `photo1` , `photo2` , `photo3` , `photo4` , `photo5` , `saws` , `exp` , `author` , `status` ) VALUES ('', '$vip', '$thisdate', '$model', '$year', '$cost', '$transmission', '$volume', '$color', '', '$fuel', '$privod', '$city', '$probeg', '$memo', '$author_name', '$email', '$number', '$phone', '$photos[1]', '$photos[2]', '$photos[3]', '$photos[4]', '$photos[5]', '0', '$exp', '$author', '0')");

  		@unlink(ENGINE_DIR . '/cache/system/last_price.php');
    	clear_cache();

		$tpl->load_template('auto_alt_id.tpl');

		$id = $db->insert_id();
		$row =  $db->super_query("SELECT * FROM " . PREFIX . "_auto_price LEFT JOIN " . PREFIX . "_auto_models ON " . PREFIX . "_auto_price.model_id = " . PREFIX . "_auto_models.id WHERE price_id = '$id'");


		if ($config['allow_alt_url'] == "yes"){
    		$link = "<a href=\"".$config['http_home_url']."auto/prodaja-".$row['price_id']."-".totranslit($row['name']." ".$row['model']).".html\">".$row['name']." ".$row['model']."</a>";
		} else {
		    $link = "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_id&id=".$row['price_id']."\">".$row['name']." ".$row['model']."</a>";
		}

        $tpl->set('{link}', $link);

		$tpl->compile('content');
		$tpl->clear();
		$db->free();

		session_unset($_POST);
		session_destroy();
	}

}
}
}
//****************	конец функции добавления объявления ********************************


//Операции
$op = !empty($_POST['op']) ? $_POST['op'] : $_GET['op'];

switch ($op) {

case "show_list":
        show_list();
        break;

case "show_id":
        show_id();
        break;

case "add":
        add();
        break;

case "edit":
        edit();
        break;

case "alt":
        alt();
        break;

case "saled":
        saled();
        break;

default:
        show_list();
        break;
}

?>

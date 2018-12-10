<?PHP

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}
if($member_db[1] != 1){ msg("error", $lang['opt_denied'], $lang['opt_denied']); }

 if($do_template == '' or !$do_template){
        $do_template = $config['skin'];
    }elseif($do_template != $config['skin']){
    }

include(ENGINE_DIR.'/data/auto_config.php');

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

if($action == "list" OR $action == "")
{
  echoheader("home", "Добро пожаловать");



  function load_template($tpl_name) {
  global $do_template;

  $text = @file_get_contents('./templates/'.$do_template.DIRECTORY_SEPARATOR .$tpl_name);

  return htmlspecialchars($text, ENT_QUOTES);

  }

  $tr_hidden = " style='display:none'";

    $templates_names = array("templates_add_position" => "auto_add.tpl", "templates_alt_id" => "auto_alt_id.tpl", "templates_edit_position" => "auto_edit_id.tpl", "templates_show_id" => "auto_show_id.tpl", "templates_show_table" => "auto_table.tpl", "templates_num" => "auto_num_price.tpl");

    foreach($templates_names as $template => $template_file)
    {
        $$template = load_template($template_file);

    }


function showRow($title="", $description="", $field="")
    {
        echo"<tr>
        <td style=\"padding:4px\" class=\"option\">
        <b>$title</b><br /><span class=small>$description</span>
        <td width=394 align=middle >
        $field
        </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";
        $bg = ""; $i++;
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

echo <<<HTML
<script language='JavaScript' type="text/javascript">

        function ChangeOption(selectedOption) {

               document.getElementById('general').style.display = "none";
               document.getElementById('templates_edit').style.display = "none";
               document.getElementById('models').style.display = "none";
               document.getElementById('users').style.display = "none";
               document.getElementById('price').style.display = "none";
               document.getElementById('categories').style.display = "none";
               document.getElementById('cities').style.display = "none";


           if(selectedOption == 'general') {document.getElementById('general').style.display = "";}
           if(selectedOption == 'templates_edit') {document.getElementById('templates_edit').style.display = "";}
           if(selectedOption == 'models') {document.getElementById('models').style.display = "";}
           if(selectedOption == 'users') {document.getElementById('users').style.display = "";}
           if(selectedOption == 'price') {document.getElementById('price').style.display = "";}
           if(selectedOption == 'categories') {document.getElementById('categories').style.display = "";}
           if(selectedOption == 'cities') {document.getElementById('cities').style.display = "";}

                }

</script>
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Настройка модуля</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;">
<table style="text-align:center;" width="100%" height="35px">
<tr style="vertical-align:middle;" >
 <td class=tableborder><a href="javascript:ChangeOption('general')"><img title="Основные настройки" src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tools.png" border="0"></a>
<td class=tableborder><a href="javascript:ChangeOption('templates_edit')"><img title="Настройка шаблонов" src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tmpl.png" border="0"></a>
 <td class=tableborder><a href="javascript:ChangeOption('categories')"><img title="Управление категориями авто" src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/cats.png" border="0"></a>
 <td class=tableborder><a href="javascript:ChangeOption('cities')"><img title="Управление городами" src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/dbset.png" border="0"></a>
 <td class=tableborder><a href="javascript:ChangeOption('models')"><img title="Управление марками и моделями авто" src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/autom.png" border="0"></a>
 <td class=tableborder><a href="javascript:ChangeOption('users')"><img title="Управление пользователями" src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/users.png" border="0"></a>
 <td class=tableborder><a href="javascript:ChangeOption('price')"><img title="Управление объявлениями" src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/xfset.png" border="0"></a>
</tr>
</table>
</td>
    </tr>
</table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
HTML;

echo <<<HTML
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
HTML;

echo <<<HTML
<tr style='' id="general"><td>
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;">
  <div class="navigation">Общие настройки</div></td>
    </tr>
</table>
<div class="unterline"></div>
  <table width="100%">
  <form action="admin.php?mod=admin_auto&action=AutoConfigChange" method="post">
HTML;


showRow("Показывать общее количество объявлений на главной странице :", "Если вы включите данную возможность, то на главной авторынка будет отображаться общее количество объявлений, имеющихся в базе", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "nummodels", "{$autoConfig['nummodels']}"));

showRow("Показывать блок VIP объявлений :", "Если вы включите данную возможность, то везде где будет тег {vip} будут выводится VIP объявления", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "allow_vip_block", "{$autoConfig['allow_vip_block']}"));

showRow("Показывать блок последних объявлений :", "Если вы включите данную возможность, то везде где будет тег {last} будут выводится последние объявления", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "allow_last_block", "{$autoConfig['allow_last_block']}"));

showRow("Количество последних объявлений в блоке:", "Укажите количество VIP объявлений в блоке:", "<input class=edit type=text style=\"text-align: center;\"  name='num_vip_block' value=\"{$autoConfig['num_last_block']}\" size=20>");

showRow("Количество выводимых объявлений на страницу:", "Укажите количество объявлений которое будет выводится в таблицу:", "<input class=edit type=text style=\"text-align: center;\"  name='num_price' value=\"{$autoConfig['num_price']}\" size=20>");

showRow("Начальный год:", "Укажите старейший год производства автомобилей :", "<input class=edit type=text style=\"text-align: center;\"  name='start_year' value=\"{$autoConfig['start_year']}\" size=20>");

showRow("Использовать автоматическую очистку старых объявлений: ", "Если вы отключите данную опцию, объявления будут автоматически удаляться по истечению их срока.", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "allowed_autodelete", "{$autoConfig['allowed_autodelete']}"));

showRow("Возможные типы фото:", "Возможные типы фото для загрузки", "<input class=edit type=text style=\"text-align: center;\"  name='allowed_screen' value=\"{$autoConfig['allowed_screen']}\" size=20>");

showRow("Ширина фото:", "Ширина, до которой будет автоматическое уменьшение", "<input class=edit type=text style=\"text-align: center;\"  name='width_photo' value=\"{$autoConfig['width_photo']}\" size=20>");

showRow("Ширина скриншота:", "Ширина автоматически создаваемого скриншота", "<input class=edit type=text style=\"text-align: center;\"  name='widththumb' value=\"{$autoConfig['widththumb']}\" size=20>");

showRow("Предел размера загружаемого фото", "Предел размера загружаемого фото в килобайтах", "<input class=edit type=text style=\"text-align: center;\"  name='maxsize_thumb' value=\"{$autoConfig['maxsize_thumb']}\" size=20>");

showRow("Качество JPEG:", "Качество загружаемого фото будет автоматически уменьшено до этой величины", "<input class=edit type=text style=\"text-align: center;\"  name='jpeg_quality' value=\"{$autoConfig['jpeg_quality']}\" size=20>");

showRow("Разрешить наложение водяных знаков:", "При загрузке фото на сервер, на него будет наложен водяной знак.", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "allow_watermark", "{$autoConfig['allow_watermark']}"));

showRow("Разрешить наложение водяных знаков на скриншоты:", "При загрузке скриншота на сервер, на него будет наложен водяной знак.", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "allow_watermark_for_thumbs", "{$autoConfig['allow_watermark_for_thumbs']}"));

showRow("Минимальный размер для накладывания водяного знака на скриншот:", "В этом поле укажите минимальный размер любой из сторон фото, до которого водяной знак накладываться не будет", "<input class=edit type=text style=\"text-align: center;\"  name='max_watermark' value=\"{$autoConfig['max_watermark']}\" size=20>");

echo "<tr><td>
<input type=\"submit\" class=\"buttons\" value=\"Сохранить\" style=\"width:150px;\">
</td></tr>
</table></td></tr>";

//********************************Конфигурация пользователей*****************************
echo <<<HTML
<tr style='display:none' id="users"><td>
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;">
  <div class="navigation">Настройки пользователей</div></td>
    </tr>
</table>
<div class="unterline"></div>
  <table width="100%">
HTML;

showRow("Разрешать добавлять объявления в базу: ", "Если вы отключите данную опцию, посетители не смогут добавлять объявления в базу.", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "allowed_addauto", "{$autoConfig['allowed_addauto']}"));

showRow("Cрок давности объявления:", "Укажите колво дней, после которого объявление будет удаляться", "<input class=edit type=text style=\"text-align: center;\"  name='num_days' value=\"{$autoConfig['num_days']}\" size=20>");

showRow("Разрешать редактировать свои объявления :", "Если вы включите данную возможность, то пользователи смогут редактировать свои объявления", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "allow_edit_price", "{$autoConfig['allow_edit_price']}"));

showRow("Разрешать продавать свои объявления :", "Если вы включите данную возможность, то пользователи смогут продавать свои объявления", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "allow_sell_price", "{$autoConfig['allow_sell_price']}"));

showRow("Группа VIP пользователей:", "Выберите ID группы, у которой все добавляемые объявления будут VIP", "<input class=edit type=text style=\"text-align: center;\"  name='vip_group' value=\"{$autoConfig['vip_group']}\" size=20>");

showRow("Разрешить добавлять фото к объявлению из пользовательской формы:", "Если вы выберете да, пользователи смогут добавлять фото к объявлениям.", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "allow_screenshot", "{$autoConfig['allow_screenshot']}"));

showRow("Использовать форму быстрого отбора:", "Если вы выберете да, будет доступна форма поиска/отбора", makeDropDown(array("1"=>$lang['opt_sys_yes'],"0"=>$lang['opt_sys_no']), "search_form", "{$autoConfig['search_form']}"));


echo "<tr><td>
<input type=\"submit\" class=\"buttons\" value=\"Сохранить\" style=\"width:150px;\"></form>
</td></tr>
</table></td></tr>";
//********************Конфигурация модуля*********************************

//********************Конфигурация шаблонов*********************************

echo <<<HTML
<tr style='display:none' id="templates_edit"><td>
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Настройка шаблонов модуля</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
<form method=post action="admin.php?mod=admin_auto&action=dosaveautotemplates">
HTML;


//******************************Вывод шаблонов***************************************

echo '<tr> <!- start add -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'add1\',\'add2\')">Добавление объявления в базу </a></b><br />В данном разделе настраивается шаблон добавления объявления в базу данных
    </tr>
    <tr id=\'add1\' '.$tr_hidden.'>
    <td width="210" valign="top" style="padding: 5px">
    <b>{AJAX}<br />
    <b>{option}<br />
    <b>{author_name}<br />
    <b>{email}<br />
    <b>{tip}<br />
    <td width="500" valign="top" style="padding: 5px">
  - Подключение функций для выбора модели авто<br />
  - Выборка марок авто<br />
  - Полное имя пользователя, размещающего объявление<br />
  - E-mail пользователя размещающего объявление<br />
  - Тип авто (1 - легковой, 2 - грузовой, 3 - мотоцикл, 4 - спецтехника)<br />
    </tr>
        <tr id=\'add2\' '.$tr_hidden.'>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="add_position">'.$templates_add_position.'</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr> <!-- End add -->';

echo '<tr> <!- start add -->
    <td height="40"  style="padding: 5px;" colspan="2">
 <b><a class="main" href="javascript:ShowOrHide(\'num1\',\'num2\')">Количество объявлений в базе</a></b><br />В данном разделе настраивается шаблон вывода количества объявлений в базе
    </tr>
    <tr id=\'num1\' '.$tr_hidden.'>
    <td width="210" valign="top" style="padding: 5px">
    <b>{num}<br />
    <td width="500" valign="top" style="padding: 5px">
  Количество объявлений<br />
    </tr>
        <tr id=\'num2\' '.$tr_hidden.'>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="templates_num">'.$templates_num.'</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr> <!-- End add -->';

echo '<tr> <!- start alt -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'alt1\',\'alt2\')">Добавление/обновление объявления</a></b><br />В данном разделе настраивается шаблон добавления объявления в базу данных
    </tr>
    <tr id=\'alt1\' '.$tr_hidden.'>
    <td width="210" valign="top" style="padding: 5px">
    <b>{id}<br />
    <td width="500" valign="top" style="padding: 5px">
  - Уникальный номер объявления<br />
  </tr>
        <tr id=\'alt2\' '.$tr_hidden.'>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="alt_id">'.$templates_alt_id.'</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr> <!-- End alt -->';

echo '<tr> <!- start edit -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'edit1\',\'edit2\')">Изменение объявления</a></b><br />В данном разделе настраивается шаблон формы изменения объявления
    </tr>
    <tr id=\'edit1\' '.$tr_hidden.'>
    <td width="210" valign="top" style="padding: 5px">
    <b>{tip}<br />
    <b>{tip_name}<br />
    <b>{name}<br />
    <b>{model_id}<br />
    <b>{model_name}<br />
    <b>{model}<br />
    <b>{photo}<br />
    <b>{id}<br />
    <b>{year}<br />
    <b>{cost}<br />
    <b>{transmission}<br />
    <b>{volume}<br />
    <b>{color}<br />
    <b>{fuel}<br />
  <b>{privod}<br />
    <b>{probeg}<br />
    <b>{memo}<br />
    <b>{author_name}<br />
    <b>{email}<br />
    <b>{city}<br />
    <b>{phone}<br />
    <b>{status}<br />

  <td width="500" valign="top" style="padding: 5px">
  - Тип авто (1 - легковой, 2 - грузовой, 3 - мотоцикл, 4 - спецтехника)<br />
  - Тип авто в строковом представлении<br />
  - Марка авто<br />
  - ИД модели в БД<br />
  - Название модели<br />
  - Название марки и модели вместе<br />
  - Вывод фото<br />
  - Уникальный ид этого объявления<br />
  - Год производства<br />
  - Цена авто<br />
  - Тип трансмиссии авто<br />
  - Объем двигателя авто<br />
  - Цвет авто<br />
  - Тип топлива авто<br />
  - Привод<br />
  - Общий пробег авто<br />
  - Дополнительные сведения об авто<br />
  - Полное имя продавца<br />
  - E-mail продавца<br />
  - Город<br />
  - Номер телефона<br />
  - Статус (0 - не продано, 1 - продано)<br />
  </tr>
        <tr id=\'edit2\' '.$tr_hidden.'>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_position">'.$templates_edit_position.'</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr> <!-- End edit -->';

echo '<tr> <!- start show_id -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'show_id1\',\'show_id2\')">Просмотр уникального объявления</a></b><br />В данном разделе настраивается шаблон показа объявления по ИД
    </tr>
    <tr id=\'show_id1\' '.$tr_hidden.'>
    <td width="210" valign="top" style="padding: 5px">
    <b>{tip}<br />
    <b>{tip_name}<br />
    <b>{name}<br />
    <b>{model_id}<br />
    <b>{model_name}<br />
    <b>{model}<br />
    <b>{photo}<br />
    <b>{id}<br />
    <b>{year}<br />
    <b>{cost}<br />
    <b>{transmission}<br />
    <b>{volume}<br />
    <b>{color}<br />
    <b>{fuel}<br />
  <b>{privod}<br />
    <b>{probeg}<br />
    <b>{memo}<br />
    <b>{author_name}<br />
    <b>{email}<br />
    <b>{city}<br />
    <b>{phone}<br />
    <b>{date}<br />
    <b>{saws}<br />
    <b>[edit][/edit]<br />

    <td width="500" valign="top" style="padding: 5px">
- Тип авто (1 - легковой, 2 - грузовой, 3 - мотоцикл, 4 - спецтехника)<br />
  - Тип авто в строковом представлении<br />
  - Марка авто<br />
  - ИД модели в БД<br />
  - Название модели<br />
  - Название марки и модели вместе<br />
  - Вывод фото<br />
  - Уникальный ид этого объявления<br />
  - Год производства<br />
  - Цена авто<br />
  - Тип трансмиссии авто<br />
  - Объем двигателя авто<br />
  - Цвет авто<br />
  - Тип топлива авто<br />
  - Тип привода авто<br />
  - Общий пробег авто<br />
  - Дополнительные сведения об авто<br />
  - Полное имя продавца<br />
  - E-mail продавца<br />
  - Город<br />
  - Номер телефона<br />
  - Дата размещения объявления<br />
  - Количество просмотров данного объявления<br />
  - Блок для редкатирования хозяином или админом<br />
  </tr>
        <tr id=\'show_id2\' '.$tr_hidden.'>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="show_id">'.$templates_show_id.'</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr> <!-- End show_id -->';

echo '<tr> <!- start show_table -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'show_table1\',\'show_table2\')">Просмотр таблицы объявлений</a></b><br />В данном разделе настраивается шаблон показа таблицы последних объявлений
    </tr>
    <tr id=\'show_table1\' '.$tr_hidden.'>
    <td width="210" valign="top" style="padding: 5px">
    <b>{AJAX}<br />
    <b>{row}<br />
    <b>{option}<br />
    <b>{tip}<br />

    <td width="500" valign="top" style="padding: 5px">
-1- Подключение AJAX для выбора модели авто<br />
  - Вывод строки с объявлением<br />
  - Заполнение выборки моделями авто<br />
  - Тип авто (1 - легковой, 2 - грузовой, 3 - мотоцикл, 4 - спецтехника)<br />

  </tr>
        <tr id=\'show_table2\' '.$tr_hidden.'>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="show_table">'.$templates_show_table.'</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr> <!-- End show_id -->
<tr><td><input type="submit" class="buttons" value="Сохранить" style="width:150px;">
</td></tr></form>';


echo "</table></td></tr>";
//**************************************Конец вывода шаблонов*********************************


//*********************************Управление марками и моделями********************************
echo <<<HTML
<tr style='display:none' id="models" ><td>
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;">
  <div class="navigation">Управление марками и моделями авто</div></td>
    </tr>
</table>
<div class="unterline"></div>
  <table width="100%">
  <form action="admin.php?mod=admin_auto&action=add_model" method="post">
HTML;

echo <<<HTML
<script language='JavaScript' type="text/javascript">

        function ShowNew(selectedOption) {

                document.getElementById('new_name').style.display = "none";
                document.getElementById('new_exist').value = "0";
                if(selectedOption == 'new') {document.getElementById('new_name').style.display = "";
                  document.getElementById('new_exist').value = "1";
                  }

                }

</script>
HTML;

showRow("Тип авто:", "Выберите тип авто", makeDropDown(array("1"=>"Легковой автомобиль","2"=>"Грузовой автомобиль", "3"=>"Мотоцикл", "4"=>"Спецтехника"), "tip", "1"));


//*****************************Добавление новых моделей**************************************
echo "<tr><td style=\"padding:4px;\">Марка авто:<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('Выберите марку автомобиля, для которого хотите добавить модель', this, event, '150px')\">[?]</a></td>
<td style=\"padding:4px;\">\n
<SELECT size=1 name=\"name\" onchange=\"ShowNew(this.value)\">";
echo option();
echo "<OPTION value=\"new\">--новая марка--></OPTION>\n";
echo "</SELECT>
<input name=\"new_name\" type=\"text\" id=\"new_name\" class=\"edit\" style='display:none'/>
<input name=\"new_exist\" type=\"hidden\" id=\"new_exist\" value=\"0\">
Новая модель:<input name=\"model\" type=\"text\" class=\"edit\"/>
</td></tr>
<tr>
<td style=\"padding:4px;\" colspan=\"2\">
<input type=\"submit\" class=\"buttons\" value=\"Добавить\" style=\"width:150px;\">
<div class=\"unterline\"></div>
</td>
</tr>
</form>
<form action=\"admin.php?mod=admin_auto&action=delete_model\" method=\"post\">\n";
//*****************************Удаление моделей**************************************

echo "<tr>
<td style=\"padding:4px;\">Модель авто:<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('Выберите модель авто', this, event, '150px')\">[?]</a></td>
<td style=\"padding:4px;\" align=\"left\">\n
<select name=\"id[]\" size=\"14\" multiple>";
echo BigOption();
echo "</select></td></tr>";
echo "<tr>
<td style=\"padding:4px;\" colspan=\"2\">
<input type=\"submit\" class=\"buttons\" value=\"Удалить\" style=\"width:150px;\">
<div class=\"unterline\"></div>
</td>
</tr>
</form>\n";

//*********************************Управление марками и моделями ********************************

echo "</table></td></tr>";

echo <<<HTML
<tr style='display:none' id="price"><td>
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;">
  <div class="navigation">Управление объявлениями</div></td>
    </tr>
</table>
<div class="unterline"></div>
  <form method=post action="admin.php?mod=admin_auto&action=process">
  <table width="100%">
HTML;

price_table();

echo "<tr><td>
<input type=\"submit\" class=\"buttons\" value=\"Произвести\" style=\"width:150px;\"></form>
</td></tr>
</table></td></tr>";


# Управление категориями ТС
echo <<<HTML
<tr style='display:none' id="categories"><td>
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;">
  <div class="navigation">Категории авто</div></td>
    </tr>
</table>
<div class="unterline"></div>

<form method=post action="admin.php?mod=admin_auto&action=addtip">
  <table width="100%">
    <tr>
        <td width="260" style="padding:4px;">{$lang['cat_name']}</td>
        <td><input class="edit" type="text" name="tip_name"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_catname]}', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$lang['cat_addicon']}</td>
        <td><input class="edit" onFocus="this.select()" value="$lang[cat_icon]" type="text" name="tip_icon"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_caticon]}', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:4px;">&nbsp;</td>
        <td><input type="submit" class="buttons" value="{$lang['vote_new']}"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
</table>
</form>

  <table width="100%">
  <tr><td>
  <table width="100%" border=1>
  <tr>
    <td>ID</td>
    <td>Название категории</td>
    <td>Альтернативное название</td>
    <td>Действия</td>
    </tr>
    <tr><td colspan=4><div class="unterline"></div></td></tr>
HTML;

foreach ($tips as $tip) {
echo "
<tr>
    <td>{$tip[tip_id]}</td>
    <td>{$tip[tip_name]}</td>
    <td>{$tip[tip_alt_name]}</td>
    <td>
    <nobr>[<a class=maintitle href=\"?mod=admin_auto&action=remove_tip&tip=" .
            $tip['tip_id']."\">удалить</a>]</nobr></td>
    </tr>";

}


echo "</table><tr><td>
</td></tr>
</table></td></tr>";

# Управление городами
echo <<<HTML
<tr style='display:none' id="cities"><td>
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;">
  <div class="navigation">Справочник городов</div></td>
    </tr>
</table>
<div class="unterline"></div>

<form method=post action="admin.php?mod=admin_auto&action=addcity">
  <table width="100%">
    <tr>
        <td width="260" style="padding:4px;">Название города</td>
        <td><input class="edit" type="text" name="name"><a href="#" class="hintanchor" onMouseover="showhint('Укажите название города', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:4px;">&nbsp;</td>
        <td><input type="submit" class="buttons" value="{$lang['vote_new']}"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
</table>
</form>

  <table width="100%">
  <tr><td>
  <table width="100%" border=1>
  <tr>
    <td>ID</td>
    <td>Название города</td>
    <td>Действия</td>
    </tr>
    <tr><td colspan=3><div class="unterline"></div></td></tr>
HTML;

foreach ($cities as $city) {
echo "
<tr>
    <td>{$city[id]}</td>
    <td>{$city[name]}</td>
    <td><nobr>[<a class=maintitle href=\"?mod=admin_auto&action=remove_city&id=" .
            $city['id']."\">удалить</a>]</nobr></td>
    </tr>";
}


echo "</table><tr><td>
</td></tr>
</table></td></tr>";



echo <<<HTML
    </table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src=//img.artlebedev.ru/tools/decoder/"engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
HTML;

    echofooter();
}

elseif($action == "AutoConfigChange")
{
$accepted_files=trim(stripslashes ($_POST['accepted_files']));
$accepted_files=htmlspecialchars ($accepted_files, ENT_QUOTES);

$content  = "<?PHP\n\n";
$content .= "\$autoConfig['nummodels']                = ".intval($_POST['nummodels']).";\n\n";
$content .= "\$autoConfig['num_price']                = ".intval($_POST['num_price']).";\n\n";
$content .= "\$autoConfig['allowed_addauto']        = ".intval($_POST['allowed_addauto']).";\n\n";
$content .= "\$autoConfig['allowed_autodelete']        = ".intval($_POST['allowed_autodelete']).";\n\n";
$content .= "\$autoConfig['allowed_screen']        = '".trim($_POST['allowed_screen'])."';\n\n";
$content .= "\$autoConfig['widththumb']        = ".intval($_POST['widththumb']).";\n\n";
$content .= "\$autoConfig['width_photo']         = ".intval($_POST['width_photo']).";\n\n";
$content .= "\$autoConfig['jpeg_quality']            = ".intval($_POST['jpeg_quality']).";\n\n";
$content .= "\$autoConfig['maxsize_thumb']            = ".intval($_POST['maxsize_thumb']).";\n\n";
$content .= "\$autoConfig['allow_watermark']        = ".intval($_POST['allow_watermark']).";\n\n";
$content .= "\$autoConfig['allow_watermark_for_thumbs']  = ".intval($_POST['allow_watermark_for_thumbs']).";\n\n";
$content .= "\$autoConfig['max_watermark']        = ".intval($_POST['max_watermark']).";\n\n";
$content .= "\$autoConfig['allow_screenshot']         = ".intval($_POST['allow_screenshot']).";\n\n";
$content .= "\$autoConfig['allow_edit_price']         = ".intval($_POST['allow_edit_price']).";\n\n";
$content .= "\$autoConfig['vip_group']        = ".intval($_POST['vip_group']).";\n\n";
$content .= "\$autoConfig['allow_sell_price']         = ".intval($_POST['allow_sell_price']).";\n\n";
$content .= "\$autoConfig['allow_vip_block']        = ".intval($_POST['allow_vip_block']).";\n\n";
$content .= "\$autoConfig['allow_last_block']         = ".intval($_POST['allow_last_block']).";\n\n";
$content .= "\$autoConfig['num_last_block']         = ".intval($_POST['num_vip_block']).";\n\n";
$content .= "\$autoConfig['num_days']         = ".intval($_POST['num_days']).";\n\n";
$content .= "\$autoConfig['start_year']         = ".intval($_POST['start_year']).";\n\n";
$content .= "\$autoConfig['search_form']        = ".intval($_POST['search_form']).";\n\n";
$content .= "?>";

$filename = "./engine/data/auto_config.php";
if ( $file = fopen($filename, "w") ) {
        fwrite($file, $content);
        fclose($file);
        } else {
                echo "не удалось записать";
                exit();
        }

msg("info", $lang['opt_sysok'], "Настройки модуля успешно сохранены!<br /><br /><a href=$PHP_SELF?mod=admin_auto&action=list>$lang[db_prev]</a>");

}

elseif($action == "dosaveautotemplates")
{
  extract($_POST, EXTR_SKIP);

    if($do_template == "" or !$do_template){ $do_template = "Default"; }

  function save_template($tpl_name, $text) {
  global $do_template;

  $handle = fopen('./templates/'. $do_template .DIRECTORY_SEPARATOR .$tpl_name,"w");
    fwrite($handle, $text);
  fclose($handle);

  }

    $templates_names = array("add_position" => "auto_add.tpl", "alt_id" => "auto_alt_id.tpl", "edit_position" => "auto_edit_id.tpl", "show_id" => "auto_show_id.tpl", "show_table" => "auto_table.tpl", "templates_num" => "auto_num_price.tpl");

    foreach($templates_names as $template => $template_file)
    {
    save_template($template_file, stripslashes($$template));
    }

  msg("info",$lang['opt_editok'],$lang['opt_editok_1'],"$PHP_SELF?mod=admin_auto&action=list");
 }

elseif($action == "add_model")
{
  $tip = $_POST['tip'];
  if ($_POST['new_exist']==1) {
    $name = $_POST['new_name'];
    }else {
    $name = $_POST['name'];}
   $model = $_POST['model'];
   if ($model == "") {
   msg("info","Добавление модели","Укажите название модели!!!","$PHP_SELF?mod=admin_auto&action=list");
   } else {
  $db->query("INSERT INTO `dle_auto_models` (`id` ,`name` ,`model` ,`tip` )
VALUES (NULL , '$name', '$model', '$tip' )");


  msg("info","Добавление модели","Модель успешно добавлена в базу","$PHP_SELF?mod=admin_auto&action=list");

  }
 }

elseif($action == "addtip"){

    $tip_name = strip_tags($db->safesql($_POST['tip_name']));

    if (!$tip_name) {
        msg("error", $lang['cat_error'], $lang['cat_ername'],
            "javascript:history.go(-1)");
    }



    if ($_POST['tip_icon'] == $lang['cat_icon']) {
        $_POST['tip_icon'] = "";
    }
    $tip_icon = htmlspecialchars($db->safesql($_POST['tip_icon']));

    $tip_alt_name = totranslit($tip_name);

    $db->query("INSERT INTO " . PREFIX .
        "_auto_tips (tip_id, tip_name, tip_alt_name, tip_img) values (NULL, '$tip_name', '$tip_alt_name', '$tip_icon')");

    @unlink(ENGINE_DIR . '/cache/system/auto_tips.php');
    clear_cache();

    msg("info", $lang['cat_addok'], $lang['cat_addok_1'], "?mod=admin_auto");


}

elseif($action == "addcity"){

    $city_name = strip_tags($db->safesql($_POST['name']));

    if (!$city_name) {
        msg("error", "Ошибка", "Не указано название города",
            "javascript:history.go(-1)");
    }

    $db->query("INSERT INTO " . PREFIX .
        "_auto_cities (id, name) values (NULL, '$city_name')");

    @unlink(ENGINE_DIR . '/cache/system/cities.php');
    clear_cache();

    msg("info", $lang['cat_addok'], "Город успешно добавлен", "?mod=admin_auto&action=list");

}

elseif($action == "remove_city"){

    $id = intval($_GET['id']);

    if ($id == "") {
      msg("info","Удаление города","Неправильный ID города!!!","$PHP_SELF?mod=admin_auto&action=list");
    } else {
        $db->query("DELETE FROM " . PREFIX . "_auto_cities WHERE id = '$id' LIMIT 1");
        @unlink(ENGINE_DIR . '/cache/system/cities.php');
      clear_cache();
      msg("info","Удаление города","Город успешно удален из базы","$PHP_SELF?mod=admin_auto&action=list");
    }

}

elseif($action == "remove_tip"){

    $tip_id = intval($_GET['tip']);

    if ($tip_id == "") {
      msg("info","Удаление категории","Неправильный ID категории!!!","$PHP_SELF?mod=admin_auto&action=list");
    } else {
        $db->query("DELETE FROM " . PREFIX . "_auto_tips WHERE tip_id = '$tip_id' LIMIT 1");
        @unlink(ENGINE_DIR . '/cache/system/auto_tips.php');
      clear_cache();
      msg("info","Удаление категории","Категория успешно удалена из базы","$PHP_SELF?mod=admin_auto&action=list");
    }

}


 elseif($action == "delete_model")
{
  $id = $_POST['id'];
    if ($id == "") {
    msg("info","Удаление модели","Укажите название модели!!!","$PHP_SELF?mod=admin_auto&action=list");

    }else{
  for ($n=0; $n < count($id); $n++ ) {
  $db->query("DELETE FROM `dle_auto_models` WHERE `dle_auto_models`.`id` = ".$id["$n"]." LIMIT 1");
    }
  msg("info","Удаление моделей","Модель(и) успешно удалены из базы","$PHP_SELF?mod=admin_auto&action=list");
  }
 }

elseif($action == "process")
{
$for_vip = $_POST['vip'];
$for_sale = $_POST['sale'];
$for_delete = $_POST['delete'];

if (is_array($for_vip)) {
while (list($key, $val) = each($for_vip)) {
$db->query("UPDATE dle_auto_price SET vip = 1 WHERE price_id = $val LIMIT 1");
}}
$db->free();
@unlink(ENGINE_DIR . '/cache/system/last_price.php');
clear_cache();

if (is_array($for_sale)) {
while (list($key, $val) = each($for_sale)) {
$db->query("UPDATE dle_auto_price SET status = 1 WHERE price_id = $val LIMIT 1");
}}
$db->free();
@unlink(ENGINE_DIR . '/cache/system/last_price.php');
clear_cache();
if (is_array($for_delete)) {

while (list($key, $val) = each($for_delete)) {
$position = $db->query("SELECT * FROM dle_auto_price WHERE price_id = $val");

while ($row = $db->get_row($position)) {
if ($row['screenshot']>0) {

for ($n=1; $n<=$row['screenshot']; $n++) {
@unlink(ROOT_DIR."/uploads/auto/photos/".$row['photo'.$n]);
@unlink(ROOT_DIR."/uploads/auto/thumbs/".$row['photo'.$n]);
}
}

}

$db->query("DELETE FROM dle_auto_price WHERE price_id = $val LIMIT 1");
$db->free();
@unlink(ENGINE_DIR . '/cache/system/last_price.php');
clear_cache();
}
}

msg("info","Управление объявлениями","Объявление(я) успешно отредактированы","$PHP_SELF?mod=admin_auto&action=list");
}

function option() {
global $db;
$val = 0;
$names = $db->query("SELECT name FROM `dle_auto_models`GROUP by name ASC");
$option = "";
while ($row = $db->get_row($names)) {
$option .= "<OPTION value=".$row["name"].">".$row["name"]."</OPTION>\n";
$val++;
}
return $option;
}

function BigOption() {
global $db;
$val = 0;
$names = $db->query("SELECT id, name, model FROM `dle_auto_models`ORDER by name ASC");
$option = "";
while ($row = $db->get_row($names)) {
$option .= "<OPTION value=".$row["id"].">".$row["name"]."&nbsp;".$row["model"]."</OPTION>\n";
$val++;
}
return $option;
}

function price_table() {
global $db, $config;


$table = "<tr><td>
<table width=\"100%\" border=\"1\">
  <tr>
    <td width=\"40px\">ID</td>
    <td>Марка модель</td>
     <td width=\"100px\">Редактировать</td>
     <td width=\"60px\">VIP</td>
    <td width=\"60px\">Продать</td>
    <td width=\"60px\">Удалить</td>
  </tr>\n";

$price =  $db->query("SELECT * FROM `dle_auto_price` LEFT JOIN `dle_auto_models` ON dle_auto_price.model_id=dle_auto_models.id ORDER by date DESC");
while ($row = $db->get_row($price)) {

      if ($config['allow_alt_url'] == "yes"){
        $link = "<a href=\"".$config['http_home_url']."auto/prodaja-".$row['price_id']."-".totranslit($row['name']." ".$row['model']).".html\">".$row['name']." ".$row['model']."</a>";
    } else {
        $link = "<a href=\"".$config['http_home_url']."index.php?do=auto&op=show_id&id=".$row['price_id']."\">".$row['name']." ".$row['model']."</a>";
    }
$table .= "<tr><td width=\"40px\">".$row['price_id']."</td>";
$table .= "<td>$link</a></td>";
$table .= "<td><a href=/auto/edit/".$row['price_id'].".html target=\"_blank\">редактировать</a></td>";
$table .= "<td width=\"40\">".makeCheckBox("vip[]", "vip", $row, $row['price_id'])."</td>";
$table .= "<td width=\"40\">".makeCheckBox("sale[]", "status", $row, $row['price_id'])."</td>";
$table .= "<td width=\"40\">".makeCheckBox("delete[]", "", "", $row['price_id'])."</td>";
$table .= "</tr>";
}
$table .= "</table></td></tr>";

echo $table;
}


function makeCheckBox($name, $selected, $row, $value) {

$output = "<input type=\"checkbox\" name=\"$name\" value=\"$value\"";
if($row[$selected]==1){
$output .= " checked ";
}
$output .= ">\n";
return $output;
}

?>

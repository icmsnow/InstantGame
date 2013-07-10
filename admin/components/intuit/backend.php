<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.10                               //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2012                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	define('IS_BILLING', $inCore->isComponentInstalled('billing'));

	cpAddPathway('Интуит', '?view=components&do=config&id='.(int)$_REQUEST['id']);

	echo '<h3>Интуит</h3>';

    $opt = $inCore->request('opt', 'str', 'config');

	$toolmenu = array();

    if ($opt=='config' || $opt=='saveconfig'){

        $toolmenu[0]['icon'] = 'save.gif';
        $toolmenu[0]['title'] = 'Сохранить';
        $toolmenu[0]['link'] = 'javascript:document.optform.submit();';

        cpToolMenu($toolmenu);
    }
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
	$GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('intuit');

    cmsCore::loadModel('intuit');

	if($opt=='saveconfig'){

		if(!cmsCore::validateForm()) { cmsCore::error404(); }

		$cfg = array();
		$cfg['rate_rat']			= $inCore->request('rate_rat', 'int');
		$cfg['rate_kar'] 			= $inCore->request('rate_kar', 'int');
		$cfg['rate_bil']			= $inCore->request('rate_bil', 'int');
		
		$cfg['Size_tabl']			= $inCore->request('Size_tabl', 'int');
		
		$cfg['TimeOutWaiting']		= $inCore->request('TimeOutWaiting', 'int');
		$cfg['TimeOutTurn']			= $inCore->request('TimeOutTurn', 'int');

		$cfg['seo_link']			= $inCore->request('seo_link', 'str');
		$cfg['seo_title']			= $inCore->request('seo_title', 'str');
		$cfg['seo_keywords']			= $inCore->request('seo_keywords', 'str');
		$cfg['seo_description']			= $inCore->request('seo_description', 'str');

		$inCore->saveComponentConfig('intuit', $cfg);
		cmsCore::addSessionMessage('Настройки успешно сохранены', 'success');
		cmsCore::redirectBack();

	}



?>

<?php
    if ($opt=='config'){
?>
<form action="index.php?view=components&do=config&id=<?php echo (int)$_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
<input type="hidden" name="csrf_token" value="<?php echo cmsUser::getCsrfToken(); ?>" />
<div id="config_tabs" style="margin-top:12px;">
    <ul id="tabs">
        <li><a href="#basic"><span>Общие</span></a></li>
        <li><a href="#seo"><span>SEO</span></a></li>
    </ul>
	<div id="basic">
		<table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Типы ставок</h4></td>
        </tr>
        <tr>
            <td valign="top"><strong>Использовать:</strong>
			<?php if (!IS_BILLING){ ?>
                <p>
                   Для того, что-бы делать реальные ставки установите: &laquo;<a href="http://www.instantcms.ru/billing/about.html">Биллинг пользователей</a>&raquo;
                </p>
            <?php }?></td>

            <td width="100" valign="top">
                <input name="rate_rat" type="checkbox" <?php if (@$cfg['rate_rat']) { echo 'checked'; } ?> value="1" /> Рейтинг <br>
				<input name="rate_kar" type="checkbox" <?php if (@$cfg['rate_kar']) { echo 'checked'; } ?> value="1"/> Карма <br>
				<?php if (IS_BILLING){ ?>
                   <input name="rate_bil" type="checkbox"<?php if (@$cfg['rate_bil']) { echo 'checked'; } ?> value="1"/> Биллинг
            <?php }?>				
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Игровое поле</h4></td>
        </tr>
        <tr>
            <td valign="top">
                <strong>Размер игрового поля:</strong><br />
            </td>
            <td valign="top">
                <input name="Size_tabl" type="text" id="Size_tabl" value="<?php echo @$cfg['Size_tabl'];?>" size="5" />
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Ограничения по времени</h4></td>
        </tr>
        <tr>
            <td valign="top">
                <strong>Время ожидания подклюения игрока:</strong><br />
                <span class="hinttext">После истечения времени, игра завершится, а ставка вернётся</span>
            </td>
            <td valign="top">
                <input name="TimeOutWaiting" type="text" id="TimeOutWaiting" value="<?php echo @$cfg['TimeOutWaiting'];?>" size="5" />
            </td>
        </tr>
        <tr>
            <td valign="top">
                <strong>Время ожидания хода игрока:</strong><br />
                <span class="hinttext">После истечения времени, игра завершится, победитель тот кто сходил последним</span>
            </td>
            <td valign="top">
                <input name="TimeOutTurn" type="text" id="TimeOutTurn" value="<?php echo @$cfg['TimeOutTurn'];?>" size="5" />
            </td>
        </tr>
		</table>
	</div>
		<div id="seo">
		<table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
		<tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>SEO</h4></td>
        </tr>
        <tr>
                <td valign="top"><strong>link</strong></td>
                <td valign="top"><textarea  name="seo_link" type="text" id="seo_link" rows="1" style="border: solid 1px gray;width:300px;"><?php echo $cfg['seo_link'];?></textarea></td>
        </tr>
        <tr>
                <td valign="top"><strong>title</strong></td>
                <td valign="top"><textarea  name="seo_title" type="text" id="seo_title" rows="2" style="border: solid 1px gray;width:300px;"><?php echo $cfg['seo_title'];?></textarea></td>
        </tr>
        <tr>
                <td valign="top"><strong>keywords</strong></td>
                <td valign="top"><textarea  name="seo_keywords" type="text" id="seo_keywords" rows="2" style="border: solid 1px gray;width:300px;"><?php echo $cfg['seo_keywords'];?></textarea></td>
        </tr>
        <tr>
                <td valign="top"><strong>description</strong></td>
                <td valign="top"><textarea  name="seo_description" type="text" id="seo_description" rows="2" style="border: solid 1px gray;width:300px;"><?php echo $cfg['seo_description'];?></textarea></td>
        </tr>
 
    </table>
	</div>
</div>
<script type="text/javascript">$('#config_tabs > ul#tabs').tabs();</script>
    <p>
        <input name="opt" type="hidden" value="saveconfig" />
        <input name="save" type="submit" id="save" value="Сохранить" />
        <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
    </p>
</form>
<?php } ?>
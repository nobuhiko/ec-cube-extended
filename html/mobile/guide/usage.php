<?php
/**
 * モバイルサイト/ご利用方法
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'guide/usage.tpl';	// メインテンプレート
		$this->tpl_title = 'ご利用方法';
	}
}

$objPage = new LC_Page();

switch (@$_GET['page']) {
case '1':
case '2':
case '3':
case '4':
	$objPage->tpl_mainpage = 'guide/usage' . $_GET['page'] . '.tpl';
	break;
}

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>

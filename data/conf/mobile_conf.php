<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 *
 * モバイルサイト共有設定ファイル
 */

require_once(dirname(__FILE__) . "/../install.inc");

// モバイルサイト設定ファイルを読み込む。
//require_once(dirname(__FILE__) . '/../install_mobile.inc');


//--------------------------------------------------------------------------------------------------------
// conf.php で定義される定数のうち、モバイルサイト用に変更が必要なもの
/*
define ('MOBILE_HTML_PATH', HTML_PATH);
define ('MOBILE_URL_DIR', URL_DIR);
define ('MOBILE_SITE_URL', SITE_URL);
define ('MOBILE_SSL_URL', SSL_URL);
*/
define('MOBILE_TEMPLATE_DIR', DATA_PATH . 'Smarty/templates/mobile');	// SMARTYテンプレート
define('MOBILE_COMPILE_DIR', DATA_PATH . 'Smarty/templates_c/mobile');	// SMARTYコンパイル

/*
define('IMAGE_TEMP_DIR', HTML_PATH . 'upload/temp_image/');	// 画像一時保存
define('IMAGE_SAVE_DIR', HTML_PATH . 'upload/save_image/');	// 画像保存先
define('IMAGE_TEMP_URL', URL_DIR . 'upload/temp_image/');	// 画像一時保存URL
define('IMAGE_SAVE_URL', URL_DIR . 'upload/save_image/');	// 画像保存先URL
*/
//--------------------------------------------------------------------------------------------------------
// モバイルサイト専用の設定

/**
 * モバイルサイトであることを表す定数
 */
define('MOBILE_SITE', true);

/**
 * セッションの存続時間 (秒)
 */
define('MOBILE_SESSION_LIFETIME', 1800);

/**
 * 空メール機能を使用するかどうか
 */
define('MOBILE_USE_KARA_MAIL', false);

/**
 * 空メール受け付けアドレスのユーザー名部分
 */
define('MOBILE_KARA_MAIL_ADDRESS_USER', 'eccube');

/**
 * 空メール受け付けアドレスのユーザー名とコマンドの間の区切り文字
 * qmail の場合は '-'
 */
define('MOBILE_KARA_MAIL_ADDRESS_DELIMITER', '+');

/**
 * 空メール受け付けアドレスのドメイン部分
 */
define('MOBILE_KARA_MAIL_ADDRESS_DOMAIN', 'mobile.ec-cube.net');

/**
 * 携帯のメールアドレスではないが、携帯だとみなすドメインのリスト
 * 任意の数の「,」「 」で区切る。
 */
define('MOBILE_ADDITIONAL_MAIL_DOMAINS', 'rebelt.co.jp');

/**
 * 携帯電話向け変換画像保存ディレクトリ
 */
define('MOBILE_IMAGE_DIR', HTML_PATH . 'converted_images');
define('MOBILE_IMAGE_URL', URL_DIR . 'converted_images');

/* URL */
define ('MOBILE_URL_SITE_TOP', MOBILE_URL_DIR . 'index.php');								// サイトトップ
define ("MOBILE_URL_CART_TOP", MOBILE_URL_DIR . "cart/index.php");							// カートトップ
define ("MOBILE_URL_SHOP_TOP", MOBILE_SSL_URL . "shopping/index.php");						// 会員情報入力
define ("MOBILE_URL_SHOP_CONFIRM", MOBILE_URL_DIR . "shopping/confirm.php");				// 購入確認ページ
define ("MOBILE_URL_SHOP_PAYMENT", MOBILE_URL_DIR . "shopping/payment.php");				// お支払い方法選択ページ
define ("MOBILE_DETAIL_P_HTML", MOBILE_URL_DIR . "products/detail.php?product_id=");		// 商品詳細(HTML出力)

//--------------------------------------------------------------------------------------------------------
// conf.php から残りの設定を読み込む。
// 定数の定義が重複するため、error_reporting のレベルを調整する。

$error_reporting = error_reporting();
error_reporting($error_reporting & ~E_NOTICE);
require_once(DATA_PATH . 'conf/conf.php');
error_reporting($error_reporting);
unset($error_reporting);
?>

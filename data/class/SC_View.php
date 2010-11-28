<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

$SC_VIEW_PHP_DIR = realpath(dirname(__FILE__));
require_once($SC_VIEW_PHP_DIR . "/../module/Smarty/libs/Smarty.class.php");
//require_once(CLASS_EX_PATH . "util_extends/SC_Utils_Ex.php");

class SC_View {

    var $_smarty;
    var $objSiteInfo; // サイト情報

    // コンストラクタ
    function SC_View($siteinfo = true) {
        global $SC_VIEW_PHP_DIR;

        $this->_smarty = new Smarty;
        $this->_smarty->left_delimiter = '<!--{';
        $this->_smarty->right_delimiter = '}-->';
        $this->_smarty->register_modifier("sfDispDBDate", array("SC_Utils_Ex", "sfDispDBDate"));
        $this->_smarty->register_modifier("sfConvSendDateToDisp", array("SC_Utils_Ex", "sfConvSendDateToDisp"));
        $this->_smarty->register_modifier("sfConvSendWdayToDisp", array("SC_Utils_Ex", "sfConvSendWdayToDisp"));
        $this->_smarty->register_modifier("sfGetVal", array("SC_Utils_Ex", "sfGetVal"));
        $this->_smarty->register_modifier("sfGetErrorColor", array("SC_Utils_Ex", "sfGetErrorColor"));
        $this->_smarty->register_modifier("sfTrim", array("SC_Utils_Ex", "sfTrim"));
        $this->_smarty->register_modifier("sfCalcIncTax", array("SC_Helper_DB_Ex", "sfCalcIncTax"));
        $this->_smarty->register_modifier("sfPrePoint", array("SC_Utils_Ex", "sfPrePoint"));
        $this->_smarty->register_modifier("sfGetChecked",array("SC_Utils_Ex", "sfGetChecked"));
        $this->_smarty->register_modifier("sfTrimURL", array("SC_Utils_Ex", "sfTrimURL"));
        $this->_smarty->register_modifier("sfMultiply", array("SC_Utils_Ex", "sfMultiply"));
        $this->_smarty->register_modifier("sfPutBR", array("SC_Utils_Ex", "sfPutBR"));
        $this->_smarty->register_modifier("sfRmDupSlash", array("SC_Utils_Ex", "sfRmDupSlash"));
        $this->_smarty->register_modifier("sfCutString", array("SC_Utils_Ex", "sfCutString"));
        $this->_smarty->plugins_dir=array("plugins", $SC_VIEW_PHP_DIR . "/../smarty_extends");
        $this->_smarty->register_modifier("sf_mb_convert_encoding", array("SC_Utils_Ex", "sf_mb_convert_encoding"));
        $this->_smarty->register_modifier("sfGetEnabled", array("SC_Utils_Ex", "sfGetEnabled"));
//        $this->_smarty->register_modifier("sfPrintEbisTag", array("SC_Utils_Ex", "sfPrintEbisTag"));
//        $this->_smarty->register_modifier("sfPrintAffTag", array("SC_Utils_Ex", "sfPrintAffTag"));
        $this->_smarty->register_modifier("sfGetCategoryId", array("SC_Utils_Ex", "sfGetCategoryId"));
        $this->_smarty->register_modifier("sfNoImageMainList", array("SC_Utils_Ex", "sfNoImageMainList"));
        $this->_smarty->register_function("sfIsHTTPS", array("SC_Utils_Ex", "sfIsHTTPS"));
        $this->_smarty->register_function("sfSetErrorStyle", array("SC_Utils_Ex", "sfSetErrorStyle"));
        $this->_smarty->register_function("printXMLDeclaration", array("SC_Utils_Ex", "printXMLDeclaration"));
        $this->_smarty->default_modifiers = array('script_escape');

        if(ADMIN_MODE == '1') {
            $this->time_start = SC_Utils_Ex::sfMicrotimeFloat();
        }

        // サイト情報を取得する
        if($siteinfo) {
            if(!defined('LOAD_SITEINFO')) {
                $this->objSiteInfo = new SC_SiteInfo();
                $arrInfo['arrSiteInfo'] = $this->objSiteInfo->data;

                // 都道府県名を変換
                $masterData = new SC_DB_MasterData_Ex();
                $arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
                $arrInfo['arrSiteInfo']['pref'] =
                    isset($arrPref[$arrInfo['arrSiteInfo']['pref']])
                    ? $arrPref[$arrInfo['arrSiteInfo']['pref']] : "";

                 // サイト情報を割り当てる
                foreach ($arrInfo as $key => $value){
                    $this->_smarty->assign($key, $value);
                }

                define('LOAD_SITEINFO', 1);
            }
        }
    }

    // テンプレートに値を割り当てる
    function assign($val1, $val2) {
        $this->_smarty->assign($val1, $val2);
    }

    // テンプレートの処理結果を取得
    function fetch($template, $no_error=false) {
        return $this->_smarty->fetch($template);
    }

    /**
     * SC_Display用にレスポンスを返す
     * @global string $GLOBAL_ERR
     * @param array $template
     * @param boolean $no_error
     * @return string
     */
    function getResponse($template, $no_error = false) {
        if(!$no_error) {
            global $GLOBAL_ERR;
            if(!defined('OUTPUT_ERR')) {
                // GLOBAL_ERR を割り当て
                $this->assign("GLOBAL_ERR", $GLOBAL_ERR);
                define('OUTPUT_ERR','ON');
            }
        }
        $res =  $this->_smarty->fetch($template);
        if(ADMIN_MODE == '1') {
            $time_end = SC_Utils_Ex::sfMicrotimeFloat();
            $time = $time_end - $this->time_start;
            $res .= '処理時間: ' . sprintf('%.3f', $time) . '秒';
        }
        return $res;
    }

    // テンプレートの処理結果を表示
    function display($template, $no_error = false) {
        if(!$no_error) {
            global $GLOBAL_ERR;
            if(!defined('OUTPUT_ERR')) {
                // GLOBAL_ERR を割り当て
                $this->assign("GLOBAL_ERR", $GLOBAL_ERR);
                define('OUTPUT_ERR','ON');
            }
        }

        $this->_smarty->display($template);
        if(ADMIN_MODE == '1') {
            $time_end = SC_Utils_Ex::sfMicrotimeFloat();
            $time = $time_end - $this->time_start;
            echo '処理時間: ' . sprintf('%.3f', $time) . '秒';
        }
    }

      // オブジェクト内の変数をすべて割り当てる。
      function assignobj($obj) {
        $data = get_object_vars($obj);
        
        foreach ($data as $key => $value){
            $this->_smarty->assign($key, $value);
        }
      }

      // 連想配列内の変数をすべて割り当てる。
      function assignarray($array) {
          foreach ($array as $key => $val) {
              $this->_smarty->assign($key, $val);
          }
      }

    /* サイト初期設定 */
    function initpath() {
        global $SC_VIEW_PHP_DIR;

        $array['tpl_mainnavi'] = $SC_VIEW_PHP_DIR . '/../Smarty/templates/frontparts/mainnavi.tpl';

        $objDb = new SC_Helper_DB_Ex();
        $array['tpl_root_id'] = $objDb->sfGetRootId();
        $this->assignarray($array);
    }

    // デバッグ
    function debug($var = true){
        $this->_smarty->debugging = $var;
    }
}

class SC_AdminView extends SC_View{
    function SC_AdminView() {
        parent::SC_View(false);
        $this->_smarty->template_dir = TEMPLATE_ADMIN_DIR;
        $this->_smarty->compile_dir = COMPILE_ADMIN_DIR;
        $this->assign('TPL_DIR_DEFAULT', URL_DIR . USER_DIR . USER_PACKAGE_DIR . DEFAULT_TEMPLATE_NAME . '/');
        $this->assign('TPL_DIR', URL_DIR . USER_DIR . USER_PACKAGE_DIR . 'admin/');
        $this->initpath();
    }
}

class SC_SiteView extends SC_View{
    function SC_SiteView($setPrevURL = true) {
        parent::SC_View();

        $this->_smarty->template_dir = TEMPLATE_DIR;
        $this->_smarty->compile_dir = COMPILE_DIR;

        // テンプレート変数を割り当て
        $this->assign("TPL_DIR", URL_DIR . USER_DIR . USER_PACKAGE_DIR . TEMPLATE_NAME . "/");

        // ヘッダとフッタを割り当て
        $header_tpl = USER_PATH . USER_PACKAGE_DIR . TEMPLATE_NAME . "/" . "header.tpl";
        $footer_tpl = USER_PATH . USER_PACKAGE_DIR . TEMPLATE_NAME . "/" . "footer.tpl";

        // ユーザー作成のテンプレートが無ければ, 指定テンプレートを割り当て
        if (!$this->_smarty->template_exists($header_tpl)) {
            $header_tpl = TEMPLATE_DIR . "header.tpl";
        }
        if (!$this->_smarty->template_exists($footer_tpl)) {
            $footer_tpl = TEMPLATE_DIR . "footer.tpl";
        }

        $this->assign("header_tpl", $header_tpl);
        $this->assign("footer_tpl", $footer_tpl);

        $this->initpath();

        if ($setPrevURL) {
            $objCartSess = new SC_CartSession();
            $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
        }
    }
}

class SC_UserView extends SC_SiteView{
    function SC_UserView($template_dir, $compile_dir = COMPILE_DIR) {
        parent::SC_SiteView();
        $this->_smarty->template_dir = $template_dir;
        $this->_smarty->compile_dir = $compile_dir;
    }
}

class SC_InstallView extends SC_View{
    function SC_InstallView($template_dir, $compile_dir = COMPILE_DIR) {
        parent::SC_View(false);
        $this->_smarty->template_dir = $template_dir;
        $this->_smarty->compile_dir = $compile_dir;
    }
}

class SC_MobileView extends SC_SiteView {
    function SC_MobileView($setPrevURL = true) {
        parent::SC_SiteView($setPrevURL);
        $this->_smarty->template_dir = MOBILE_TEMPLATE_DIR;
        $this->_smarty->compile_dir = MOBILE_COMPILE_DIR;
        // テンプレート変数を割り当て
        $this->assign("TPL_DIR", URL_DIR . USER_DIR . USER_PACKAGE_DIR . MOBILE_TEMPLATE_NAME . "/");
    }
}

class SC_SmartphoneView extends SC_SiteView {
    function SC_SmartphoneView($setPrevURL = true) {
        parent::SC_SiteView($setPrevURL);
        $this->_smarty->template_dir = SMARTPHONE_TEMPLATE_DIR;
        $this->_smarty->compile_dir = SMARTPHONE_COMPILE_DIR;
        // テンプレート変数を割り当て
        $this->assign("TPL_DIR", URL_DIR . USER_DIR . USER_PACKAGE_DIR . SMARTPHONE_TEMPLATE_NAME . "/");
    }
}
?>

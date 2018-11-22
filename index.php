<?php
/*-----------引入檔案區--------------*/
include_once "header.php";
$xoopsOption['template_main'] = "ntpc_openid_index.tpl";
include_once XOOPS_ROOT_PATH . "/header.php";

// openid library 與 設定檔
require_once 'class/openid.php';
require_once 'config.php';

/*-----------執行動作判斷區----------*/
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op = system_CleanVars($_REQUEST, 'op', '', 'string');
// $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');

// 已登入則重導回首頁
if ($xoopsUser) redirectTo(XOOPS_URL);

$openid = new LightOpenID(XOOPS_URL);

switch ($openid->mode) {
    case 'id_res':
        if ($openid->validate()) {
            // 驗證成功
            $user_data = getOpenidUserData($openid);
//            setUserThenCheck($user_data);
        } else {
            // 驗證失敗
            redirectTo(XOOPS_URL);
        }
        break;

    case 'cancel':
        redirectTo(XOOPS_URL);
        break;

    default:
        flow_start();
        break;
}

/*-----------秀出結果區--------------*/
include_once XOOPS_ROOT_PATH . '/footer.php';


/*-----------function區--------------*/

//顯示預設頁面內容
function show_content()
{
    global $xoopsTpl;

    $main = "模組開發中";
    $xoopsTpl->assign('content', $main);
}

/**
 * 啟動 OpenID 流程
 */
function flow_start() {
    global $openid;

    $openid->identity = 'https://openid.ntpc.edu.tw/';
    $openid->required = OPENID_REQUIRED;
    redirectTo($openid->authUrl());
}

/**
 * 取得 OpenID 回傳之資料
 */
function getOpenidUserData() {
    global $openid;

    $data = $openid->getAttributes();
    ddd($data, true);
}
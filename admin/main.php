<?php
/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = "ntpc_openid_adm_main.tpl";
include_once "header.php";
include_once "../function.php";



/*-----------執行動作判斷區----------*/
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op = system_CleanVars($_REQUEST, 'op', '', 'string');
// $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');

switch ($op) {

    // case "xxx":
    // xxx();
    // header("location:{$_SERVER['PHP_SELF']}");
    // exit;

    case 'getAllRules':
        die(getJSONResponse(getAllRules()));

    default:
        show_content();
        break;
}

include_once 'footer.php';


/*-----------function區--------------*/

//顯示預設頁面內容
function show_content()
{
    global $xoopsTpl;

    $main = "登入規則設定施工中....";
    $xoopsTpl->assign('content', $main);
}





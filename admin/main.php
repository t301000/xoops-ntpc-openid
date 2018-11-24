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
        die(getJSONResponse(getAllRules(false), false));

    case 'addRule':
        $data = file_get_contents("php://input");

        $result = store(json_decode($data, true));

        die(getJSONResponse(compact('result')));

    default:
        show_content();
        break;
}

include_once 'footer.php';


/*-----------function區--------------*/

//顯示預設頁面內容
function show_content()
{
    global $xoopsTpl, $xoopsModuleConfig;

    $data['schoolCode'] = $xoopsModuleConfig['school_code'];
    $xoopsTpl->assign('data', $data);
}

function store($data) {
    global $xoopsDB;

    $rule = getJSONString($data, false);

    $sql = "INSERT INTO {$xoopsDB->prefix('ntpc_openid_login_rules')} (rule) VALUES ('{$rule}')";
    $xoopsDB->query($sql) or web_error($sql);
    $sn = $xoopsDB->getInsertId();

    $result['sn'] = $sn;
    $result['enable'] = 1;
    $result['rule'] = $data;

    return $result;
}



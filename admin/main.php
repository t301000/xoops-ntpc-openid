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

    case 'updateRule':
        // {sn: 1, rule: {id: "014569123", role: ["學生"]}}
        $data = file_get_contents("php://input");

        $result = update(json_decode($data, true));

        die(getJSONResponse(compact('result')));

    case 'delRule':
        $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');

        $result = destroy($sn);

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
    $sort = 99;

    $sql = "INSERT INTO {$xoopsDB->prefix('ntpc_openid_login_rules')} (rule, sort) VALUES ('{$rule}', '{$sort}')";
    $xoopsDB->query($sql) or web_error($sql);
    $sn = $xoopsDB->getInsertId();

    $result['sn'] = $sn;
    $result['enable'] = 1;
    $result['rule'] = $data;
    $result['sort'] = $sort;

    return $result;
}

function update($data) {
    global $xoopsDB;

    $rule = getJSONString($data['rule'], false);

    $sql = "UPDATE {$xoopsDB->prefix('ntpc_openid_login_rules')} SET rule = '{$rule}' WHERE sn = '{$data['sn']}'";
    $xoopsDB->query($sql) or web_error($sql);

    return getRuleBySN($data['sn']);
}

function destroy($sn) {
    global $xoopsDB;

    $sql = "DELETE FROM {$xoopsDB->prefix('ntpc_openid_login_rules')} WHERE sn = '{$sn}'";
    $result = $xoopsDB->queryF($sql) or web_error($sql);

    return $result;
}

function getRuleBySN($sn) {
    global $xoopsDB;

    $sql = "SELECT 
               sn, rule, sort, enable
           FROM
               {$xoopsDB->prefix('ntpc_openid_login_rules')}
           WHERE sn = '{$sn}'";
    $result = $xoopsDB->queryF($sql) or web_error($sql);

    list($sn, $rule, $sort, $enable) = $xoopsDB->fetchRow($result);
    $sn = (int) $sn;
    $rule = json_decode($rule, true);
    $sort = (int) $sort;
    $enable = (int) $enable;

    return compact('sn', 'sort', 'rule', 'enable');
}


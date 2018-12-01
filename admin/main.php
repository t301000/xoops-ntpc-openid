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

    case 'getAllRules':
        die(getJSONResponse(getAllLoginRules(false), false));

    case 'addRule':
        // {id: "014569123", role: ["學生"]}
        $data = file_get_contents("php://input");

        $result = store(json_decode($data, true));

        die(getJSONResponse(compact('result')));

    case 'updateRule':
        // {sn: 1, rule: {id: "014569123", role: ["學生"]}}
        $data = file_get_contents("php://input");

        $result = update(json_decode($data, true));

        die(getJSONResponse(compact('result')));

    case 'toggleRuleActive':
        $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');

        $result = toggleActive($sn);

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

    get_jquery(true); // 引入 jquery-ui，拖拉排序必備

    $data['schoolCode'] = $xoopsModuleConfig['school_code'];
    $xoopsTpl->assign('data', $data);
}

// 新增登入規則
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

// 更新登入規則
function update($data) {
    global $xoopsDB;

    $rule = getJSONString($data['rule'], false);

    $sql = "UPDATE {$xoopsDB->prefix('ntpc_openid_login_rules')} SET rule = '{$rule}' WHERE sn = '{$data['sn']}'";
    $xoopsDB->query($sql) or web_error($sql);

    return getLoginRuleBySN($data['sn']);
}

// 啟用 / 停用 登入規則
function toggleActive($sn) {
    global $xoopsDB;

    $rule = getLoginRuleBySN($sn);
    $enable = (int) $rule['enable'] ? 0 : 1;

    $sql = "UPDATE `{$xoopsDB->prefix('ntpc_openid_login_rules')}` SET `enable` = '{$enable}' WHERE `sn` = '{$sn}'";
    $result = $xoopsDB->queryF($sql) or web_error($sql);

    return $result;
}

// 刪除登入規則
function destroy($sn) {
    global $xoopsDB;

    $sql = "DELETE FROM {$xoopsDB->prefix('ntpc_openid_login_rules')} WHERE sn = '{$sn}'";
    $result = $xoopsDB->queryF($sql) or web_error($sql);

    return $result;
}

// 取得指定之單一條登入規則
function getLoginRuleBySN($sn) {
    global $xoopsDB;

    $sql = "SELECT 
               sn, rule, enable
           FROM
               {$xoopsDB->prefix('ntpc_openid_login_rules')}
           WHERE sn = '{$sn}'";
    $result = $xoopsDB->queryF($sql) or web_error($sql);

    list($sn, $rule, $enable) = $xoopsDB->fetchRow($result);
    $sn = (int) $sn;
    $rule = json_decode($rule, true);
    $enable = (int) $enable;

    return compact('sn', 'rule', 'enable');
}

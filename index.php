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
            // ddd($user_data);
            $canLogin = loginGuard($user_data);
            // echo "canLogin => $canLogin";

            // 拒絕登入則導回首頁
            if (!$canLogin) {
                redirect_header(XOOPS_URL, 3, REJECTED_MESSAGE, false);
            }

            login_user($user_data);

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

    $user_data = [];

    $identity_array = array_values(explode('/', $openid->identity));
    $user_data['openid'] = end($identity_array);

    $attr = $openid->getAttributes();

    // 將取回之各欄位整理鰾轉換 key
    foreach (OPENID_REQUIRED as $k) {
        $user_data[CONVERT[$k]] = $attr[$k];
    }

    // 若有取回年班座號，則整理之
    if (in_array('pref/language', OPENID_REQUIRED)) {
        $classInfo = [];
        $classInfo['grade'] = substr($attr['pref/language'], 0, 2);
        $classInfo['class'] = substr($attr['pref/language'], 2, 2);
        $classInfo['num'] = substr($attr['pref/language'], 4, 2);
        $user_data['classInfo'] = $classInfo;
    }

    // 授權資訊
    $user_data['authInfos'] = json_decode($attr['pref/timezone'], true);

    return $user_data;

    /**
     *
     * $user_data array(5)
     *     'openid' => string(4) "t301"
     *     'email' => string(21) "t301@apps.ntpc.edu.tw"
     *     'name' => string UTF-8(3) "林士立"
     *     'classInfo' => array(3)      // 班級資訊：年級、班級、座號
     *         'grade' => string(2) "00"
     *         'class' => string(2) "00"
     *         'num' => string(2) "00"
     *     'authInfos' => array(2)
     *         array(5)
     *             'id' => string(6) "014569"
     *             'name' => string UTF-8(10) "新北市立育林國民中學"
     *             'role' => string UTF-8(2) "教師"
     *             'title' => string UTF-8(5) "教師兼組長"
     *             'groups' => array(1)
     *                 string UTF-8(4) "資訊組長"
     *         array(5)
     *             'id' => string(6) "014569"
     *             'name' => string UTF-8(10) "新北市立育林國民中學"
     *             'role' => string UTF-8(2) "家長"
     *             'title' => string UTF-8(2) "其他"
     *             'groups' => array(1)
     *                 string UTF-8(2) "其他"
     */
}

/**
 * 檢查是否能登入
 *
 * @param $data
 *
 * @return bool
 */
function loginGuard($data) {
    $result = false;

    // 整理授權資訊，將屬於同一所學校之 data 集中，以校代碼為 key
    $authInfos = array_reduce($data['authInfos'], function ($accu, $item) {
        $accu[$item['id']]['name'] = $item['name'];
        if (!isset($accu[$item['id']]['role'])) $accu[$item['id']]['role'] = [];
        if (!isset($accu[$item['id']]['title'])) $accu[$item['id']]['title'] = [];
        if (!isset($accu[$item['id']]['groups'])) $accu[$item['id']]['groups'] = [];
        array_push($accu[$item['id']]['role'], $item['role']);
        array_push($accu[$item['id']]['title'], $item['title']);
        $accu[$item['id']]['groups'] = array_merge($accu[$item['id']]['groups'], $item['groups']);

        return $accu;
    }, []);

    // ddd($authInfos);
    /**
     * $authInfos array(1)
     *     '014569' => array(4)
     *         'name' => string UTF-8(10) "新北市立育林國民中學"
     *         'role' => array(2)
     *              string UTF-8(2) "教師"
     *              string UTF-8(2) "家長"
 *              'title' => array(2)
     *              string UTF-8(5) "教師兼組長"
     *              string UTF-8(2) "其他"
 *              'groups' => array(2)
     *              string UTF-8(4) "資訊組長"
     *              string UTF-8(2) "其他"
     *
     */

    // 逐一檢查登入規則，first match
    foreach (RULES as $rule) {
        $result = checkSingleRule($rule, $authInfos);
        // echo "rule => $result <br><br>";

        if ($result) {
            return true;
        }
    }

    return $result;
}


/**
 * 檢查單一條規則
 *
 * @param $rule
 * @param $authInfos
 *
 * @return bool
 */
function checkSingleRule(array $rule, $authInfos) {
    $result = false;

    // 逐一檢查欄位，有 false 即回傳 false
    foreach ($rule as $k => $v) {
        $method = 'check_' .$k;
        $result = $method($rule, $authInfos);
        // echo "$k = $v => $result <br>";

        if (!$result) {
            return false;
        }
    }

    return $result;
}

/**
 * 檢查校代碼
 *
 * @param $rule
 * @param $authInfos
 *
 * @return bool
 */
function check_id($rule, $authInfos) {
    return array_key_exists($rule['id'], $authInfos);
}

/**
 * 檢查身份
 *
 * @param $rule
 * @param $authInfos
 *
 * @return bool
 */
function check_role($rule, $authInfos) {
    $roles = is_array($rule['role']) ? $rule['role'] : array($rule['role']);

    // 有指定校代碼，則只檢查該校之授權資訊
    if (isset($rule['id'])) {
        return count(array_intersect($roles, $authInfos[$rule['id']]['role'])) > 0;
    }

    // 沒有指定校代碼，則逐一檢查各筆授權資訊
    $result = false;
    foreach ($authInfos as $authInfo) {
        $result = count(array_intersect($roles, $authInfo['role'])) > 0;
        if ($result) {
            return true;
        }
    }

    return $result;
}





function login_user($data) {

}

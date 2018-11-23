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

// 已登入則重導回首頁
if ($xoopsUser) redirectTo(XOOPS_URL);

$openid = new LightOpenID(XOOPS_URL);

switch ($op) {
    case 'check': // 多重身份，已選擇使用之身份
        $idx = system_CleanVars($_REQUEST, 'idx', 0, 'int');
        $user_data = $_SESSION['temp_user_data'];
        $user_data['used_authInfo'] = $user_data['authInfos'][$idx];

        checkThenLogin($user_data);
        break;

    default:
        switch ($openid->mode) {
            case 'id_res':
                if ($openid->validate()) {
                    // 驗證成功
                    $user_data = getOpenidUserData();
                    // ddd($user_data);

                    // 若有多重身份
                    if (count($user_data['authInfos']) > 1) {
                        $_SESSION['temp_user_data'] = $user_data;
                        show_authInfo_table();
                    } else { // 單一身份
                        checkThenLogin($user_data);
                    }
                } else {
                    // 驗證失敗
                    redirectTo(XOOPS_URL);
                }
                break;

            case 'cancel':
                redirectTo(XOOPS_URL);
                break;

            default:
                clearTempSession();
                flow_start();
                break;
        }
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

//顯示身份選擇頁面
function show_authInfo_table()
{
    global $xoopsTpl;

    $user_data = $_SESSION['temp_user_data'];
    $infos = $user_data['authInfos'];
    $list = '';
    foreach ($infos as $idx => $info) {
        $href = "{$_SERVER['PHP_SELF']}?op=check&idx=$idx";
        $list .= '<a class="btn btn-block btn-link" style="font-size: 18px;margin-bottom: 15px;" href="' . $href . '">';
        $list .= "{$info['name']} {$info['role']} " . implode(',', $info['groups']);
        $list .= "</a>";
    }
    $main = <<<INFOS
        <div style="width: 60%; min-width: 400px; border: 1px solid #d9edf7; border-radius: 10px; overflow: hidden;" class="center-block">
            <h2 class="text-center bg-info" style="margin-top: 0; margin-bottom: 30px; padding: 15px;">選擇登入身份</h2>
            $list
        </div>        
INFOS;

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
 * 檢查登入規則，符合則登入
 *
 * @param $user_data
 */
function checkThenLogin($user_data) {
    $canLogin = loginGuard($user_data);
    // echo "canLogin => $canLogin";
    // die;

    // 拒絕登入則導回首頁
    if (!$canLogin) {
        redirect_header(XOOPS_URL, 5, REJECTED_MESSAGE, false);
    }

    login_user($user_data);
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

    // 只檢查選擇使用之身份
    $authInfo_check = isset($data['used_authInfo']) ? $data['used_authInfo'] : $data['authInfos'][0];
    // ddd($authInfo_check);

    // 整理授權資訊，將屬於同一所學校之 data 集中，以校代碼為 key
    // 主要用於多重身份
    $authInfos = array_reduce(array($authInfo_check), function ($accu, $item) {
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
     *         'title' => array(2)
     *              string UTF-8(5) "教師兼組長"
     *              string UTF-8(2) "其他"
     *         'groups' => array(2)
     *              string UTF-8(4) "資訊組長"
     *              string UTF-8(2) "其他"
     *
     */

    // 逐一檢查授權資訊是否符合登入規則，一有符合即回傳 true
    foreach ($authInfos as $id => $authInfo) {
        $result = checkSingleAuthInfo($id, $authInfo);
        // echo "authInfo => $result <hr>";

        if ($result) {
            return true;
        }
    }

    return $result;
}


function checkSingleAuthInfo($id, $authInfo) {
    $result = false;

    // 逐一檢查登入規則，first match
    foreach (RULES as $rule) {
        $result = checkSingleRule($rule, $id, $authInfo);
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
function checkSingleRule(array $rule, $id, $authInfo) {
    $result = false;

    // 逐一檢查欄位，有 false 即回傳 false
    foreach ($rule as $k => $v) {
        $method = 'check_' .$k;
        $result = $method($rule, $id, $authInfo);
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
function check_id($rule, $id, $authInfo) {
    return $rule['id'] === $id;
}

/**
 * 檢查身份
 *
 * @param $rule
 * @param $authInfos
 *
 * @return bool
 */
function check_role($rule, $id, $authInfo) {
    $roles = is_array($rule['role']) ? $rule['role'] : array($rule['role']);

    return count(array_intersect($roles, $authInfo['role'])) > 0;
}


/**
 * 清除暫存之 session
 */
function clearTempSession() {
    if (isset($_SESSION['temp_user_data'])) {
        unset($_SESSION['temp_user_data']);
    }
}


function login_user($data) {
    redirect_header(XOOPS_URL, 5, "passed login check", false);
}

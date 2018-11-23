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


function login_user($data, $url = '', $from = '', $sig = '', $bio = '', $occ = '') {
    global $xoopsModuleConfig, $xoopsConfig;
    $member_handler = xoops_getHandler('member');

    $uname = trim($data['openid']) . "_ntpc";
    $email = trim($data['email']);

    // 取得 uid
    $uid = get_uid($uname, $data, false); // 個人帳號
    // ddd($uid);

    // 若為行政，則替換為行政帳號
    $officer = array_intersect($data['used_authInfo']['groups'], OFFICER);
    if ($is_officer = count($officer) > 0) {
        $uname = $officer[0]; // 取第一個
        $uid = get_uid($uname, $data, true); // 行政帳號 uid
        // ddd($uid);
    }

    /******* 登入 user *******/
    // 一般以個人帳號登入
    // 行政以行政帳號登入
    $pass = getPass($uname);

    if ($uname == '' || $pass == '') {
        redirect_header(XOOPS_URL . '/user.php', 1, _MD_NTPCOPENID_INCORRECTLOGIN);
        exit();
    }

    xoops_loadLanguage('auth');

    include_once $GLOBALS['xoops']->path('class/auth/authfactory.php');

    $xoopsAuth = XoopsAuthFactory::getAuthConnection($uname);
    //登入
    $user = $xoopsAuth->authenticate($uname, $pass);

    //若登入成功
    if (false != $user) {

        $SchoolCode = trim($data['used_authInfo']['id']); // 校代碼
        $JobName = trim($data['used_authInfo']['role']); // 身份

        // 處理群組
        //add2group($user->getVar('uid'), $email, $SchoolCode, $JobName);
        //add2group($uid, $email, $SchoolCode, $JobName);

        if (0 == $user->getVar('level')) {
            redirect_header(XOOPS_URL . '/index.php', 5, _MD_TNOPENID_NOACTTPADM);
            exit();
        }
        //若網站關閉
        if ($xoopsConfig['closesite'] == 1) {
            $allowed = false;
            foreach ($user->getGroups() as $group) {
                if (in_array($group, $xoopsConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) {
                redirect_header(XOOPS_URL . '/index.php', 1, _MD_TNOPENID_NOPERM);
                exit();
            }
        }

        //設定最後登入時間
        $user->setVar('last_login', time());

        // 若為行政帳號登入，則更新 email
        if ($is_officer) {
            $user->setVar('email', $data['email']);
        } else {
            // 非行政，則更新身份
            $user->setVar("user_icq", $JobName);
        }

        $user->setVar("user_from", $from);
        $user->setVar("url", formatURL($url));
        $user->setVar("user_sig", $sig);
        $user->setVar("bio", $bio);
        if ($SchoolCode) {
            $user->setVar("user_occ", $occ);
            $user->setVar("user_intrest", $SchoolCode);
        }

        if (!$member_handler->insertUser($user, true)) {
        }

        $login_from = $_SESSION['login_from'];

        // Regenrate a new session id and destroy old session
        $GLOBALS["sess_handler"]->regenerate_id(true);
        $_SESSION                    = array();
        $_SESSION['xoopsUserId']     = $user->getVar('uid');
        $_SESSION['xoopsUserGroups'] = $user->getGroups();
        $user_theme                  = $user->getVar('theme');
        if (in_array($user_theme, $xoopsConfig['theme_set_allowed'])) {
            $_SESSION['xoopsUserTheme'] = $user_theme;
        }

        // Set cookie for rememberme
        if (!empty($xoopsConfig['usercookie'])) {
            setcookie($xoopsConfig['usercookie'], 0, -1, '/', XOOPS_COOKIE_DOMAIN, 0);
        }
        //若有要轉頁
        if (!empty($xoopsModuleConfig['redirect_url'])) {
            $redirect_url = $xoopsModuleConfig['redirect_url'];
        } else {
            $redirect_url = empty($login_from) ? XOOPS_URL . '/index.php' : $login_from;
        }

        // RMV-NOTIFY
        // Perform some maintenance of notification records
        $notification_handler = xoops_getHandler('notification');
        $notification_handler->doLoginMaintenance($user->getVar('uid'));

        redirect_header($redirect_url, 1, sprintf("", $user->getVar('uname')), false);
    } else { // 登入失敗
        redirect_header(XOOPS_URL . '/user.php', 5, $xoopsAuth->getHtmlErrors());
    }
}


/**
 * 取得 uid，個人帳號 或 行政帳號
 *
 * @param      $uname
 * @param      $data
 * @param bool $officer 是否為行政帳號
 *
 * @return mixed
 */
function get_uid($uname, $data, $officer = false) {
    $member_handler = xoops_getHandler('member');
    $user_existed = $member_handler->getUserCount(new Criteria('uname', $uname)) > 0;
    if (!$user_existed) {
        // 不存在 => 新增，並取得 uid
        $data['uname'] = $uname;
        $uid = createUser($data, $officer);
    } else {
        // 存在 => 取得 uid
        $uid = ($member_handler->getUsers(new Criteria('uname', $uname)))[0]->uid();
    }

    return $uid;
}

/**
 * 建立帳號，回傳 uid
 *
 * @param        $data user data from openid
 * @param bool   $officer 是否為行政帳號
 * @param string $url
 * @param string $from
 * @param string $sig
 * @param string $bio
 * @param string $occ
 * @param string $aim
 * @param string $yim
 * @param string $msnm
 *
 * @return mixed
 */
function createUser($data, $officer = false, $url = '', $from = '', $sig = '', $bio = '', $occ = '', $aim = '', $yim = '', $msnm = '') {
    global $xoopsConfig, $xoopsDB;

    $member_handler = xoops_getHandler('member');

    $uname = trim($data['uname']);
    $email = trim($data['email']);
    $name = $officer ? $uname : trim($data['name']);
    $JobName = $officer ? '行政' : trim($data['used_authInfo']['role']);
    $SchoolCode = trim($data['used_authInfo']['id']);


    $pass    = randStr(128);
    $newuser = $member_handler->createUser();
    $newuser->setVar("user_viewemail", 1);
    $newuser->setVar("attachsig", 0);
    $newuser->setVar("name", $name);
    $newuser->setVar("uname", $uname);
    $newuser->setVar("email", $email);
    $newuser->setVar("url", formatURL($url));
    $newuser->setVar("user_avatar", 'avatars/blank.gif');
    $newuser->setVar('user_regdate', time());
    $newuser->setVar("user_icq", $JobName);
    $newuser->setVar("user_from", $from);
    $newuser->setVar("user_sig", $sig);
    $newuser->setVar("theme", $xoopsConfig['theme_set']);
    // $newuser->setVar("user_yim", $yim);
    // $newuser->setVar("user_aim", $aim);
    // $newuser->setVar("user_msnm", $msnm);
    $newuser->setVar("pass", md5($pass));
    $newuser->setVar("timezone_offset", $xoopsConfig['default_TZ']);
    $newuser->setVar("uorder", $xoopsConfig['com_order']);
    $newuser->setVar("umode", $xoopsConfig['com_mode']);
    // RMV-NOTIFY
    $newuser->setVar("notify_method", 1);
    $newuser->setVar("notify_mode", 1);
    $newuser->setVar("bio", $bio);
    $newuser->setVar("rank", 1);
    $newuser->setVar("level", 1);
    //$newuser->setVar("user_occ", $myts->addSlashes($user_profile['work'][0]['employer']['name']));
    $newuser->setVar("user_intrest", $SchoolCode);
    $newuser->setVar('user_mailok', true);
    if (!$member_handler->insertUser($newuser, 1)) {
        redirect_header(XOOPS_URL, 5, _MD_NTPCOPENID_CREATE_USER_FAIL);
    }

    $uid = $newuser->getVar('uid');

    if ($uid) {
        // 加入註冊會員群組
        $sql = "INSERT INTO `" . $xoopsDB->prefix('groups_users_link') . "`  (groupid, uid) VALUES  (2, " . $uid . ")";

        // 若為行政帳號，加入行政群組
        if ($officer) {
            $sql .= ", (" . OFFICER_GID . ", $uid)";
        }

        $xoopsDB->queryF($sql) or web_error($sql);

        // 紀錄隨機密碼
        $sql = "replace into `" . $xoopsDB->prefix('tad_login_random_pass') . "` (`uname` , `random_pass`) values  ('{$uname}','{$pass}')";
        $xoopsDB->queryF($sql) or web_error($sql);

    } else {
        redirect_header(XOOPS_URL, 5, _MD_NTPCOPENID_CREATE_USER_FAIL);
    }

    return $uid;
}

/**
 * 處理密碼
 *
 * @param string $uname
 */
function getPass($uname = "")
{
    global $xoopsDB;

    if (empty($uname)) {
        return;
    }

    $sql               = "select `random_pass` from `" . $xoopsDB->prefix('tad_login_random_pass') . "` where `uname`='{$uname}'";
    $result            = $xoopsDB->queryF($sql) or web_error($sql);
    list($random_pass) = $xoopsDB->fetchRow($result);

    $sql        = "select `pass` from `" . $xoopsDB->prefix('users') . "` where `uname`='{$uname}'";
    $result     = $xoopsDB->queryF($sql) or web_error($sql);
    list($pass) = $xoopsDB->fetchRow($result);
    if ($pass !== md5($random_pass)) {
        $sql = "update `" . $xoopsDB->prefix('users') . "` set `pass`=md5('{$random_pass}') where `uname`='{$uname}'";
        $xoopsDB->queryF($sql) or web_error($sql);
    }

    return $random_pass;
}





function add2group($uid = "", $email = "", $SchoolCode = "", $JobName = "")
{
    global $xoopsDB, $xoopsUser;

    $member_handler = xoops_getHandler('member');
    $user           = &$member_handler->getUser($uid);
    if ($user) {
        $userGroups = $user->getGroups();
    } else {
        header('location:' . XOOPS_URL);
        exit;
    }

    $sql    = "SELECT `item`,`kind`,`group_id` FROM `" . $xoopsDB->prefix('tad_login_config') . "`";
    $result = $xoopsDB->queryF($sql) or web_error($sql);
    while (list($item, $kind, $group_id) = $xoopsDB->fetchRow($result)) {
        if (!in_array($group_id, $userGroups)) {
            //echo "<h1>{$group_id}-{$item}-{$SchoolCode}-{$email}</h1>";
            if (!empty($SchoolCode) and strpos($item, $SchoolCode) !== false and $JobName == $kind) {
                $sql = "insert into `" . $xoopsDB->prefix('groups_users_link') . "` (groupid,uid ) values($group_id,$uid)";
                $xoopsDB->queryF($sql) or web_error($sql);
                //echo "{$group_id}, {$uid}<br>";
            }

            if (empty($item) and $JobName == $kind) {
                $sql = "insert into `" . $xoopsDB->prefix('groups_users_link') . "` (groupid,uid ) values($group_id,$uid)";
                $xoopsDB->queryF($sql) or web_error($sql);
            }

            if (!empty($email) and strpos($item, '*') !== false) {
                $item     = trim($item);
                $new_item = str_replace('*', '', $item);
                // die($new_item);
                if (strpos($email, $new_item) !== false) {
                    $sql = "insert into `" . $xoopsDB->prefix('groups_users_link') . "` (groupid,uid ) values($group_id,$uid)";
                    $xoopsDB->queryF($sql) or web_error($sql);
                }
            }

            if (!empty($email) and strpos($item, $email) !== false) {
                $sql = "insert into `" . $xoopsDB->prefix('groups_users_link') . "` (groupid,uid ) values($group_id,$uid)";
                $xoopsDB->queryF($sql) or web_error($sql);
                //echo "{$group_id}, {$uid}<br>";
            }
        }
    }
}

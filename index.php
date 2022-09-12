<?php

use XoopsModules\Tadtools\Utility;

/*-----------引入檔案區--------------*/
include_once "header.php";
$xoopsOption['template_main'] = "ntpc_openid_index.tpl";
include_once XOOPS_ROOT_PATH . "/header.php";

// openid library 與 設定檔
require_once 'class/openid.php';
require_once 'config.php';

// 是否建立行政帳號
// 偏好設定校代碼有設才建立
$createOfficer = trim($xoopsModuleConfig['school_code']) !== '' ;

/*-----------執行動作判斷區----------*/
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op = system_CleanVars($_REQUEST, 'op', '', 'string');

// 已登入
if ($xoopsUser) {
    $toUrl = XOOPS_URL;
    switch ($op) {
        case 'change_user':
            // 變身
            change_user();
            $toUrl = empty($_SESSION['url_back_after_change_user']) ? XOOPS_URL : $_SESSION['url_back_after_change_user'];
            redirectTo($toUrl);
            break;

        case 'proxy_user_start':
            // 代理
            $toUid = system_CleanVars($_REQUEST, 'to_uid', 0, 'int');
            proxy_user($toUid);
            $toUrl = system_CleanVars($_REQUEST, 'fromUrl', XOOPS_URL, 'string');
            redirectTo($toUrl);
            break;

        case 'proxy_user_end':
            // 代理結束
            proxy_user();
            redirectTo($toUrl);
            break;
    }
    // 重導回首頁
    redirectTo($toUrl);
}

$openid = new LightOpenID(XOOPS_URL);

// 假資料測試模式 或 正常模式
if (FAKE_MODE) {
    fake_mode($op);
} else {
    try {
        normal_mode($op);
    } catch (Exception $e) {
        die($e->getMessage());
    }
}


/*-----------秀出結果區--------------*/
include_once XOOPS_ROOT_PATH . '/footer.php';


/*-----------function區--------------*/

/**
 * 變身
 */
function change_user() {
    global $xoopsModuleConfig;

    // 若偏好設定中 不允許切換身分 則停止
    if (!$xoopsModuleConfig['can_change_user']) {
        return null;
    }

    if (empty($_SESSION['ntpcUids']) || empty($_SESSION['ntpcUids']['officer'])) {
        return null;
    }

    $type = $_SESSION['xoopsUserId'] === $_SESSION['ntpcUids']['officer']['uid'] ? 'personal' : 'officer';
    $_SESSION['xoopsUserId'] = $_SESSION['ntpcUids'][$type]['uid'];
    $_SESSION['xoopsUserGroups'] = $_SESSION['ntpcUids'][$type]['gids'];
}

    /**
     * 代理
     *
     * @param int $toUid 欲代理之 uid
     *
     * @return null
     */
function proxy_user($toUid = 0) {
    global $xoopsModuleConfig, $xoopsUser;

    // 若偏好設定中 不允許代理 則停止
    if (!$xoopsModuleConfig['can_proxy_user']) {
        return null;
    }

    if ($toUid === 0) {
        // 結束代理，恢復原本 uid
        $_SESSION['xoopsUserId'] = $_SESSION['proxyFromUid'];
        unset($_SESSION['proxyFromUid']);

        return false;
    }

    // 可執行代理之群組 id
    $can_gids = $xoopsModuleConfig['groups_can_proxy'];
    // user 所屬群組 id
    $gids = $xoopsUser->getGroups();
    // 是否可執行代理
    $can_proxy = (count(array_intersect($gids, $can_gids)) > 0);

    if ($can_proxy) {
        $_SESSION['proxyFromUid'] = isset($_SESSION['proxyFromUid']) ? $_SESSION['proxyFromUid'] : $xoopsUser->uid();
        $_SESSION['xoopsUserId'] = $toUid;
    }
}

/**
 * 測試模式，略過正常 OpenID 流程，使用假資料
 *
 * @param $op
 */
function fake_mode($op) {
    switch ($op) {
        case 'check':
            // 檢查是否可登入，可則登入

            // 取得欲使用之假資料
            // $idx 為授權資訊 index
            // $uidx 為假資料 user index
            $idx = system_CleanVars($_REQUEST, 'idx', 0, 'int');
            $uidx = system_CleanVars($_REQUEST, 'uidx', 0, 'int');
            $user_data = FAKE_USERS[$uidx];
            $user_data['used_authInfo'] = $user_data['authInfos'][$idx];

            checkThenLogin($user_data);
            break;

        default:
            // 顯示假資料列表
            showFakeUserList();
            break;
    }
}

/**
 * 顯示假資料列表
 */
function showFakeUserList() {
    global $xoopsTpl;

    $list = '';
    foreach (FAKE_USERS as $uidx => $person) {
        $list .= getListItem($uidx, $person);
    }
    $main = <<<INFOS
        <div class="center-block text-center mx-auto" style="width: 80%;">
          <h2>FAKE USERS LIST</h2>
          <div style="display: flex;">$list</div>
        </div>
INFOS;

    $xoopsTpl->assign('content', $main);
}

/**
 * 取得一筆假資料 user 顯示項目
 *
 * @param $uidx
 * @param $data
 *
 * @return string
 */
function getListItem($uidx, $data) {
    $authInfos='';
    foreach ($data['authInfos'] as $idx => $info) {
        $authInfos.="<hr>";

        $authInfos.='<a href="' . $_SERVER['PHP_SELF'] . '?op=check&uidx=' . $uidx . '&idx=' . $idx . '">';
            $authInfos.="<div>{$info['id']}</div>";
            $authInfos.="<div>{$info['role']}</div>";
            $authInfos.="<div>{$info['title']}</div>";

            $authInfos.= '<div style="background-color: #feeaa5">';
                foreach ($info['groups'] as $item) {
                    $authInfos .= $item;
                    $authInfos .= '<br>';
                }
            $authInfos.='</div>';
        $authInfos.='</a>';
    }
    return <<<LISTITEM
        <div style="border: 1px solid #b8b8b8; flex: 1; margin: 5px;">
          {$data['name']}<br>
          {$data['openid']}<br>
          $authInfos
        </div>
LISTITEM;

}

/**
 * 正常模式
 *
 * @param $op
 *
 * @throws \ErrorException
 */
function normal_mode($op) {
    global $openid;

    switch ($op) {
        case 'check': // 多重身份，已選擇使用之身份
            if (!isset($_SESSION['temp_user_data'])) {
                redirectTo(XOOPS_URL);
            }

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
                            $user_data['used_authInfo'] = $user_data['authInfos'][0];
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
}


/**
 * 顯示身份選擇頁面
 */
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
        <div style="width: 60%; min-width: 400px; border: 1px solid #d9edf7; border-radius: 10px; overflow: hidden;" class="center-block mx-auto">
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
    global $xoopsModuleConfig;

    $canLogin = loginGuard($user_data);
    // echo "canLogin => $canLogin";
    // die;

    // 拒絕登入則導回首頁
    if (!$canLogin) {
        clearTempSession();
        redirect_header(XOOPS_URL, 5, $xoopsModuleConfig['reject_msg'], false);
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
    global $xoopsModuleConfig;

    $result = false;

    // 只檢查選擇使用之身份
    // 單一身份者取第一筆授權資訊
    $authInfo_check = isset($data['used_authInfo']) ? $data['used_authInfo'] : $data['authInfos'][0];
    // die($authInfo_check);

    // 整理授權資訊，將屬於同一所學校之 data 集中，以校代碼為 key
    // 主要用於多重身份
    $authInfos = array_reduce(array($authInfo_check), function ($accu, $item) {
        $accu[$item['id']]['id'] = $item['id'];
        $accu[$item['id']]['name'] = $item['name'];
        if (!isset($accu[$item['id']]['role'])) $accu[$item['id']]['role'] = [];
        if (!isset($accu[$item['id']]['title'])) $accu[$item['id']]['title'] = [];
        if (!isset($accu[$item['id']]['groups'])) $accu[$item['id']]['groups'] = [];
        array_push($accu[$item['id']]['role'], $item['role']);
        array_push($accu[$item['id']]['title'], $item['title']);
        $accu[$item['id']]['groups'] = array_merge($accu[$item['id']]['groups'], $item['groups']);

        return $accu;
    }, []);

    // die($authInfos);
    /**
     * $authInfos array(1)
     *     '014569' => array(4)
     *         'id' => string UTF-8(6) "014569"
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

    // 所有啟用之登入規則
    $rules = getAllLoginRules();
    // die(var_dump($rules));

    // 空陣列 且 偏好設定校代碼有設 => 加入預設規則：只檢查校代碼
    if (empty($rules) && $xoopsModuleConfig['school_code']) {
        $rules[] = ['rule' => ['id' => $xoopsModuleConfig['school_code']]];
    }

    // 若規則陣列為空，則允許登入
    if (empty($rules)) return true;

    // 規則陣列不為空
    // 逐一檢查授權資訊是否符合登入規則，一有符合即回傳 true
    foreach ($authInfos as $id => $authInfo) {
        $result = checkSingleAuthInfo($id, $authInfo, $rules);
        // echo "authInfo => $result <hr>";

        if ($result) {
            return true;
        }
    }

    return $result;
}


/**
 * 檢查單一筆授權資訊
 *
 * @param $id
 * @param $authInfo
 * @param $rules
 *
 * @return bool
 */
function checkSingleAuthInfo($id, $authInfo, $rules) {
    $result = false;

    // 逐一檢查登入規則，first match
    foreach ($rules as $rule) {
        $result = checkSingleRule($rule['rule'], $id, $authInfo);
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
    $result = true;

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


/**
 * 登入 user
 *
 * @param        $data
 * @param string $url
 * @param string $from
 * @param string $sig
 * @param string $bio
 * @param string $occ
 */
function login_user($data, $url = '', $from = '', $sig = '', $bio = '', $occ = '') {
    global $xoopsModuleConfig, $xoopsConfig, $createOfficer;
    $member_handler = xoops_getHandler('member');

    $uname = trim($data['openid']) . "_ntpc";
    $email = trim($data['email']);

    $all_uids = []; // 存放所有 uid，for 變身用
    // 取得 uid
    $uid = get_uid($uname, $data, false); // 個人帳號
    // die($uid);
    $all_uids['personal'] = [
    	'uid' => $uid,
    	'gids' => $member_handler->getGroupsByUser($uid)
    ];

    // 如果要建立行政帳號
    $is_officer = false; // 是否具有行政身分
    if ($createOfficer && $data['used_authInfo']['id'] === $xoopsModuleConfig['school_code']) {
        $allEnabledOfficer = array_column(getAllOfficers(true), 'name'); // 所有啟用之正規行政帳號職稱
        /* 取得所有啟用之自定義行政帳號職稱
         * 原格式：
         *   [  ['sn' => 100, 'name' => '課研組長', 'openid' => 't301', 'enable' => 1], [...], ....  ]
         * 整理為：
         *   [  't301' => '課研組長', .....  ]
        */
        $allEnabledCustomOfficer = array_column(getAllCustomOfficers(true), 'name', 'openid');
        // 若為行政，則替換為行政帳號
        $officer = array_intersect($data['used_authInfo']['groups'], $allEnabledOfficer);
        if ($is_officer = count($officer) > 0) {
            $uname = array_values($officer)[0]; // 取第一個
        } else if ($is_officer = array_key_exists($data['openid'], $allEnabledCustomOfficer)) {
            // 如果是自定義的行政職稱，如：課研組長
            $uname = $allEnabledCustomOfficer[$data['openid']]; // uname 設為自定義職稱，如：課研組長
            $data['used_authInfo']['groups'][] = $uname; // 使用的授權資訊加入自定義職稱
            //die(var_dump($data['used_authInfo']));
        }

        if ($is_officer) {
            $uid = get_uid($uname, $data, true); // 行政帳號 uid
            // ddd($uid);
            $all_uids['officer'] = ['uid' => $uid, 'gids' => []];
        }
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
        // 若群組有更動，則重新登入 user
        if (syncGroup($user, $data, $is_officer)) {
            $user = $xoopsAuth->authenticate($uname, $pass);
        }

        if (0 == $user->getVar('level')) {
            redirect_header(XOOPS_URL . '/index.php', 5, _MD_NTPCOPENID_INCORRECTLOGIN);
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
        if ($is_officer) {
            $all_uids['officer']['gids'] = $_SESSION['xoopsUserGroups'];
        }
        $_SESSION['ntpcUids'] = $all_uids; // 將該使用者所有的帳號 uid 與 gids 存入 session，for 變身用

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
        clearTempSession();
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
    global $xoopsConfig, $xoopsDB, $xoopsModuleConfig;

    $member_handler = xoops_getHandler('member');

    $uname = trim($data['uname']);
    $email = trim($data['email']);
    $name = $officer ? $uname : trim($data['name']);
    $JobName = $officer ? '行政' : trim($data['used_authInfo']['role']);
    $SchoolCode = trim($data['used_authInfo']['id']);

    $pass    = Utility::randStr(128);
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

        // 若為行政帳號，加入行政群組，gid 必大於 3（1~3為內建群組）
        if ($officer && $xoopsModuleConfig['officer_gid'] > 3) {
            $sql .= ", (" . $xoopsModuleConfig['officer_gid'] . ", $uid)";
        }

        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        // 紀錄隨機密碼
        $sql = "replace into `" . $xoopsDB->prefix('ntpc_openid_random_pass') . "` (`uname` , `random_pass`) values  ('{$uname}','{$pass}')";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

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

    $sql               = "select `random_pass` from `" . $xoopsDB->prefix('ntpc_openid_random_pass') . "` where `uname`='{$uname}'";
    $result            = $xoopsDB->queryF($sql) or Utility::web_error($sql);
    list($random_pass) = $xoopsDB->fetchRow($result);
    
    if (!$random_pass) {
        // 隨機密碼找不到
        $random_pass = $pass = Utility::randStr(128);
        $sql = "replace into `" . $xoopsDB->prefix('ntpc_openid_random_pass') . "` (`uname` , `random_pass`) values  ('{$uname}','{$pass}')";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);
    }

    $sql        = "select `pass` from `" . $xoopsDB->prefix('users') . "` where `uname`='{$uname}'";
    $result     = $xoopsDB->queryF($sql) or Utility::web_error($sql);
    list($pass) = $xoopsDB->fetchRow($result);
    if ($pass !== md5($random_pass)) {
        $sql = "update `" . $xoopsDB->prefix('users') . "` set `pass`=md5('{$random_pass}') where `uname`='{$uname}'";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);
    }

    return $random_pass;
}

/**
 * 同步調整群組，回傳是否須重新登入 user
 *
 * @param \XoopsUser $user
 * @param            $data
 * @param bool       $is_officer
 *
 * @return bool
 */
function syncGroup(XoopsUser $user, $data, $is_officer = false) {
    global $xoopsModuleConfig, $xoopsDB;

    // 是否須重新登入，群組有變更則須重新登入 user
    $need_relogin = false;

    // 群組分配規則
    //$group_rules = GROUP_RULES;
    $group_rules = getAllGroupRules(true);
    // die(var_dump($group_rules));

    // 目前持有的群組 id 陣列，轉字串為數字
    $gids_current = array_map(function ($gid) {
        return (int)$gid;
    }, $user->getGroups());

    // 基礎必備的 gid
    // 行政帳號附加行政群組 gid
    $base_gids = $is_officer ? [2, $xoopsModuleConfig['officer_gid']] : [2];

    // 收集應具備的群組 id
    $gids = array_reduce($group_rules, function ($accu, $rule) use ($data) {
        $gid = checkGroupRule($data, $rule);
        if ($gid) {
            $accu[] = $gid;
        }

        return $accu;
    }, $base_gids);
    $gids = array_unique($gids); // 去除重複元素

    $gids_to_add = array_diff($gids, $gids_current);
    $gids_to_remove = array_diff($gids_current, $gids);

    //*************** for debug
    if (false) {
        echo "現有 gid <br>";
        echo "<pre>";
        print_r($gids_current);
        echo "</pre>";
        echo "應有 gid <br>";
        echo "<pre>";
        print_r($gids);
        echo "</pre>";
        echo "增加 gid <br>";
        echo "<pre>";
        print_r($gids_to_add);
        echo "</pre>";
        echo "移除 gid <br>";
        echo "<pre>";
        print_r($gids_to_remove);
        echo "</pre>";
        die();
    }

    // 新增群組
    if (count($gids_to_add) > 0) {
        $values = array_map(function ($gid) use ($user) {
            return "($gid, {$user->uid()})";
        }, $gids_to_add);
        $values = implode(', ', $values);

        $sql = "INSERT INTO
                    {$xoopsDB->prefix('groups_users_link')}
                    (groupid, uid)
                VALUES
                    {$values}";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        $need_relogin = true;
    }

    // 移除群組
    if (count($gids_to_remove) > 0) {
        $ids = implode(', ', $gids_to_remove);
        $sql = "DELETE FROM 
                    {$xoopsDB->prefix('groups_users_link')}
                WHERE 
                    uid = {$user->uid()}
                    AND 
                    groupid IN ({$ids})";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        $need_relogin = true;
    }

    return $need_relogin;
}


/**
 * 檢查一條群組分配規則，符合則回傳 gid，不符合則回傳 0
 *
 * @param $data
 * @param $rule
 *
 * @return int
 */
function checkGroupRule($data, $rule) {
    // 攤平陣列，只取需要的欄位
    $data_to_check['openid'] = array($data['openid']);
    // $data_to_check['email'] = array($data['email']);
    $data_to_check['id'] = array($data['used_authInfo']['id']);
    $data_to_check['role'] = array($data['used_authInfo']['role']);
    $data_to_check['title'] = array($data['used_authInfo']['title']);
    $data_to_check['groups'] = $data['used_authInfo']['groups']; // 為陣列

    $gid = $rule['gid']; // 取出此規則通過後分配之 group id
    unset($rule['gid']); // 清除，後續檢查時不檢查檢查 gid，因待檢查資料無此欄位

    //*************** for debug
    if (false) {
        echo "待檢查資料：<br><pre>";
        print_r($data_to_check);
        echo "</pre>";
        echo "規則：<br><pre>";
        print_r($rule);
        echo "</pre>";
        die;
    }

    $result = true;
    // 逐一檢查各欄位，false first
    foreach ($rule['rule'] as $k => $v) {
        // if ($k === 'groups') {
        //     // groups 採陣列交集比對
        //     $v = is_array($v) ? $v : [$v];
        //     $result = count(array_intersect($data_to_check[$k], $v)) > 0;
        // } else {
        //     // 其餘採字串比對
        //     $result = $data_to_check[$k] === $v;
        // }

        $v = is_array($v) ? $v : [$v];
        $result = count(array_intersect($data_to_check[$k], $v)) > 0;

        if (!$result) {
            // 不符合，回傳 0
            return 0;
        }
    }

    return $gid;
}

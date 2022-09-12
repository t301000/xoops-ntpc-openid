<?php
$modversion = array();

//---模組基本資訊---//
$modversion['name']        = 'NTPC OpenID';
$modversion['version']     = 1.50;
$modversion['description'] = '新北市 OpenID 登入模組';
$modversion['author']      = 't301000';
$modversion['credits']     = 't301000';
$modversion['help']        = 'page=help';
$modversion['license']     = 'GNU GPL 2.0';
$modversion['license_url'] = 'www.gnu.org/licenses/gpl-2.0.html/';
$modversion['image']       = 'images/logo.png';
$modversion['dirname']     = basename(dirname(__FILE__));

//---模組狀態資訊---//
$modversion['release_date']        = '2019/07/07';
$modversion['module_website_url']  = 'https://github.com/t301000/xoops-ntpc-openid';
$modversion['module_website_name'] = 'NTPC OpenID';
$modversion['module_status']       = 'release';
$modversion['author_website_url']  = 'https://github.com/t301000/xoops-ntpc-openid';
$modversion['author_website_name'] = 'NTPC OpenID';
$modversion['min_php']             = 5.2;
$modversion['min_xoops']           = '2.5';
$modversion['min_tadtools']        = '1.20';

//---paypal資訊---//
$modversion['paypal']                  = array();
$modversion['paypal']['business']      = 't301000@gmail.com';
$modversion['paypal']['item_name']     = 'Donation : ' . '贊助對象名稱';
$modversion['paypal']['amount']        = 0;
$modversion['paypal']['currency_code'] = 'USD';

//---後台使用系統選單---//
$modversion['system_menu'] = 1;

//---模組資料表架構---//
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][0] = 'ntpc_openid_random_pass';
$modversion['tables'][1] = 'ntpc_openid_login_rules';
$modversion['tables'][2] = 'ntpc_openid_group_rules';
$modversion['tables'][3] = 'ntpc_openid_officer_list';

//---後台管理介面設定---//
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/menu.php';

//---前台主選單設定---//
$modversion['hasMain'] = 1;
//$modversion['sub'][1]['name'] = '';
//$modversion['sub'][1]['url'] = '';

//---模組自動功能---//
$modversion['onInstall'] = "include/onInstall.php";
$modversion['onUpdate'] = "include/onUpdate.php";
//$modversion['onUninstall'] = "include/onUninstall.php";

//---樣板設定---//
$modversion['templates']                    = array();
$i                                          = 1;
$modversion['templates'][$i]['file']        = 'ntpc_openid_adm_main.tpl';
$modversion['templates'][$i]['description'] = '後台登入規則管理頁樣板';

$i++;
$modversion['templates'][$i]['file']        = 'ntpc_openid_adm_group.tpl';
$modversion['templates'][$i]['description'] = '後台自動群組管理頁樣板';

$i++;
$modversion['templates'][$i]['file']        = 'ntpc_openid_adm_officer.tpl';
$modversion['templates'][$i]['description'] = '後台行政帳號管理頁樣板';

$i++;
$modversion['templates'][$i]['file']        = 'ntpc_openid_adm_custom_officer.tpl';
$modversion['templates'][$i]['description'] = '後台自定義行政帳號管理頁樣板';

$i++;
$modversion['templates'][$i]['file']        = 'ntpc_openid_index.tpl';
$modversion['templates'][$i]['description'] = '模組首頁樣板';

//---偏好設定---//
$modversion['config'] = array();
$i=0;
$modversion['config'][$i]['name']    = 'school_code';
$modversion['config'][$i]['title']    = '_MI_NTPCOPENID_CONFIG_SCHOOLID';
$modversion['config'][$i]['description']    = '_MI_NTPCOPENID_CONFIG_SCHOOLID_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']    = 'text';
$modversion['config'][$i]['default']    = '';

$i++;
$modversion['config'][$i]['name']    = 'officer_gid';
$modversion['config'][$i]['title']    = '_MI_NTPCOPENID_CONFIG_OFFICER_GID';
$modversion['config'][$i]['description']    = '_MI_NTPCOPENID_CONFIG_OFFICER_GID_DESC';
$modversion['config'][$i]['formtype']    = 'group';
$modversion['config'][$i]['valuetype']    = 'int';
$modversion['config'][$i]['default']    = 2;

$i++;
$modversion['config'][$i]['name']    = 'can_change_user';
$modversion['config'][$i]['title']    = '_MI_NTPCOPENID_CONFIG_CAN_CHANGE_USER';
$modversion['config'][$i]['description']    = '_MI_NTPCOPENID_CONFIG_CAN_CHANGE_USER_DESC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']    = 'int';
$modversion['config'][$i]['default']    = 0;

$i++;
$modversion['config'][$i]['name']    = 'can_proxy_user';
$modversion['config'][$i]['title']    = '_MI_NTPCOPENID_CONFIG_CAN_PROXY_USER';
$modversion['config'][$i]['description']    = '_MI_NTPCOPENID_CONFIG_CAN_PROXY_USER_DESC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']    = 'int';
$modversion['config'][$i]['default']    = 0;

$i++;
$modversion['config'][$i]['name']    = 'groups_can_proxy';
$modversion['config'][$i]['title']    = '_MI_NTPCOPENID_CONFIG_GROUPS_CAN_PROXY';
$modversion['config'][$i]['description']    = '_MI_NTPCOPENID_CONFIG_GROUPS_CAN_PROXY_DESC';
$modversion['config'][$i]['formtype']    = 'group_multi';
$modversion['config'][$i]['valuetype']    = 'array';
$modversion['config'][$i]['default']    = 1;

$i++;
$modversion['config'][$i]['name']    = 'reject_msg';
$modversion['config'][$i]['title']    = '_MI_NTPCOPENID_CONFIG_REJECT_MSG';
$modversion['config'][$i]['description']    = '_MI_NTPCOPENID_CONFIG_REJECT_MSG_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']    = 'text';
$modversion['config'][$i]['default']    = '您無法登入本校網站';
//---搜尋---//
//$modversion['hasSearch'] = 1;
//$modversion['search']['file'] = "include/search.php";
//$modversion['search']['func'] = "搜尋函數名稱";

//---區塊設定---//
$modversion['blocks'] = array();
$i=1;
$modversion['blocks'][$i]['file'] = "ntpc_openid.php";
$modversion['blocks'][$i]['name'] = _MI_NTPCOPENID_BLOCK_NAME_1;
$modversion['blocks'][$i]['description'] = _MI_NTPCOPENID_BLOCK_DESC_1;
$modversion['blocks'][$i]['show_func'] = "ntpc_openid";
$modversion['blocks'][$i]['template'] = "ntpc_openid.tpl";
// $modversion['blocks'][$i]['edit_func'] = "ntpc_openid_edit";
// $modversion['blocks'][$i]['options'] = "設定值1|設定值2";

$i++;
$modversion['blocks'][$i]['file'] = "ntpc_openid_change_user.php";
$modversion['blocks'][$i]['name'] = _MI_NTPCOPENID_BLOCK_NAME_2;
$modversion['blocks'][$i]['description'] = _MI_NTPCOPENID_BLOCK_DESC_2;
$modversion['blocks'][$i]['show_func'] = "ntpc_openid_change_user";
$modversion['blocks'][$i]['template'] = "ntpc_openid_change_user.tpl";
// $modversion['blocks'][$i]['edit_func'] = "ntpc_openid_edit";
// $modversion['blocks'][$i]['options'] = "設定值1|設定值2";

$i++;
$modversion['blocks'][$i]['file'] = "ntpc_openid_proxy_user.php";
$modversion['blocks'][$i]['name'] = '帳號代理';
$modversion['blocks'][$i]['description'] = '帳號代理';
$modversion['blocks'][$i]['show_func'] = "ntpc_openid_proxy_user";
$modversion['blocks'][$i]['template'] = "ntpc_openid_proxy_user.tpl";
// $modversion['blocks'][$i]['edit_func'] = "ntpc_openid_edit";
// $modversion['blocks'][$i]['options'] = "設定值1|設定值2";

//---評論---//
//$modversion['hasComments'] = 1;
//$modversion['comments']['pageName'] = '單一頁面.php';
//$modversion['comments']['itemName'] = '主編號';

//---通知---//
//$modversion['hasNotification'] = 1;

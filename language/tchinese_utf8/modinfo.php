<?php
xoops_loadLanguage('modinfo_common', 'tadtools');

// 後台
define("_MI_NTPCOPENID_ADMENU1", "登入規則");
define("_MI_NTPCOPENID_ADMENU1_DESC", "登入規則管理頁");

define("_MI_NTPCOPENID_ADMENU2", "自動群組");
define("_MI_NTPCOPENID_ADMENU2_DESC", "自動群組管理頁");

define("_MI_NTPCOPENID_ADMENU3", "行政帳號");
define("_MI_NTPCOPENID_ADMENU3_DESC", "行政帳號管理頁");

// 區塊
define('_MI_NTPCOPENID_BLOCK_NAME_1', '新北市 OpenID 登入');
define('_MI_NTPCOPENID_BLOCK_DESC_1', '新北市 OpenID 登入');
define('_MI_NTPCOPENID_BLOCK_NAME_2', '切換身分');
define('_MI_NTPCOPENID_BLOCK_DESC_2', '切換身分');

// 偏好設定
define('_MI_NTPCOPENID_CONFIG_SCHOOLID', '學校 / 單位代碼');
define('_MI_NTPCOPENID_CONFIG_SCHOOLID_DESC', '學校 / 單位代碼，若未設定則不會自動建立行政人員帳號。');

define('_MI_NTPCOPENID_CONFIG_OFFICER_GID', '行政群組');
define('_MI_NTPCOPENID_CONFIG_OFFICER_GID_DESC', '行政人員所屬群組');

define('_MI_NTPCOPENID_CONFIG_CAN_CHANGE_USER', '允許切換身分');
define('_MI_NTPCOPENID_CONFIG_CAN_CHANGE_USER_DESC', '允許在個人與行政帳號間切換');

define('_MI_NTPCOPENID_CONFIG_REJECT_MSG', '拒絕登入訊息');
define('_MI_NTPCOPENID_CONFIG_REJECT_MSG_DESC', '無法登入時顯示之訊息');

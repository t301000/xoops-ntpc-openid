<?php

use XoopsModules\Tadtools\Utility;

/**
 * 模組安裝後執行
 *
 * @param $module
 *
 * @return bool|void
 */
function xoops_module_install_ntpc_openid(&$module) {

    $result = true;

    if (checkIfTableExist('tad_login_random_pass')) {
        $result = copyTable('tad_login_random_pass', 'ntpc_openid_random_pass');
    }

    $result = insertDefaultOfficer();

    return $result;
}

/**
 * 檢查資料表是否存在
 *
 * @param string $table_name
 *
 * @return bool
 */
function checkIfTableExist($table_name = 'tad_login_random_pass') {
    global $xoopsDB;

    $sql = "SHOW TABLES LIKE '{$xoopsDB->prefix('tad_login_random_pass')}'";
    $result = $xoopsDB->query($sql);

    list($table) = $xoopsDB->fetchRow($result);

    return !!$table;
}

/**
 * 複製資料表資料
 *
 * @param string $from
 * @param string $to
 */
function copyTable($from = 'tad_login_random_pass', $to = 'ntpc_openid_random_pass') {
    global $xoopsDB;

    $sql = "INSERT INTO `{$xoopsDB->prefix($to)}` SELECT * FROM `{$xoopsDB->prefix($from)}`";
    $result = $xoopsDB->query($sql);

    return $result;
}

/**
 * 新增預設行政帳號職稱
 */
function insertDefaultOfficer() {
    global $xoopsDB;

    $sql = "INSERT INTO `{$xoopsDB->prefix(ntpc_openid_officer_list)}` (`name`, `enable`) VALUES
                ('校長', 1),
                ('幼兒園園長', 0),
                ('教務主任', 1),
                ('學務主任', 1),
                ('總務主任', 1),
                ('輔導主任', 1),
                ('校務主任', 0),
                ('人事主任', 1),
                ('會計主任', 1),
                ('圖書館主任', 1),
                ('幼兒園主任', 0),
                ('教學組長', 1),
                ('註冊組長', 1),
                ('資訊組長', 1),
                ('設備組長', 1),
                ('試務組長', 1),
                ('實研組長', 1),
                ('音樂組長', 0),
                ('訓育組長', 1),
                ('生活教育組長', 1),
                ('體育組長', 1),
                ('衛生組長', 1),
                ('文書組長', 1),
                ('出納組長', 1),
                ('事務組長', 1),
                ('輔導組長', 1),
                ('資料組長', 1),
                ('特教組長', 1),
                ('教保組長', 0),
                ('幼兒園組長', 0),
                ('人事助理員', 1),
                ('會計佐理員', 1),
                ('心理師', 1),
                ('社工師', 1),
                ('校護', 1),
                ('幹事', 1),
                ('教官', 1),
                ('理事長', 0),
                ('理事', 0),
                ('監事', 0),
                ('幼兒園職員', 0),
                ('幼兒園教師', 0),
                ('教保服務人員', 0),
                ('導師', 0),
                ('科任教師', 0),
                ('專任輔導教師', 0),
                ('其他', 0)";
    $result = $xoopsDB->query($sql);

    return $result;

}


?>

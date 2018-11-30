<?php

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

    $sql = "INSERT INTO `{$xoopsDB->prefix(ntpc_openid_officer_list)}` (`name`) VALUES
                ('校長'),
                ('幼兒園園長'),
                ('教務主任'),
                ('學務主任'),
                ('總務主任'),
                ('輔導主任'),
                ('校務主任'),
                ('人事主任'),
                ('會計主任'),
                ('圖書館主任'),
                ('幼兒園主任'),
                ('教學組長'),
                ('註冊組長'),
                ('資訊組長'),
                ('設備組長'),
                ('試務組長'),
                ('實研組長'),
                ('音樂組長'),
                ('訓育組長'),
                ('生活教育組長'),
                ('體育組長'),
                ('衛生組長'),
                ('文書組長'),
                ('出納組長'),
                ('事務組長'),
                ('輔導組長'),
                ('資料組長'),
                ('特教組長'),
                ('教保組長'),
                ('幼兒園組長'),
                ('人事助理員'),
                ('會計佐理員'),
                ('心理師'),
                ('社工師'),
                ('校護'),
                ('幹事'),
                ('教官'),
                ('理事長'),
                ('理事'),
                ('監事'),
                ('幼兒園職員'),
                ('幼兒園教師'),
                ('教保服務人員'),
                ('導師'),
                ('科任教師'),
                ('專任輔導教師'),
                ('其他')";
    $result = $xoopsDB->query($sql);

    return $result;

}


?>

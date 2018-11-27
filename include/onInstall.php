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

?>

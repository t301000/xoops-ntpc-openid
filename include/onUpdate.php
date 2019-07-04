<?php

use XoopsModules\Tadtools\Utility;

function xoops_module_update_ntpc_openid(&$module, $old_version) {
    GLOBAL $xoopsDB;
    
    if (!checkIfTableExist()) {
        return createTable();
    }

    return true;
}

/**
 * 檢查 ntpc_openid_custom_officer 資料表是否存在
 *
 * @return bool
 */
function checkIfTableExist() {
    global $xoopsDB;

    $sql = "SHOW TABLES LIKE '{$xoopsDB->prefix('ntpc_openid_custom_officer')}'";
    $result = $xoopsDB->query($sql);

    list($table) = $xoopsDB->fetchRow($result);

    return !!$table;
}

/**
 * 建立資料表：ntpc_openid_custom_officer
 */
function createTable(){
    global $xoopsDB;

    $sql = "CREATE TABLE `{$xoopsDB->prefix('ntpc_openid_custom_officer')}` (
                `sn` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `openid` varchar(255) NOT NULL,
                `enable` tinyint(1) NOT NULL DEFAULT '1',
                PRIMARY KEY (`sn`),
                UNIQUE KEY `name` (`name`)
            )";
    $result = $xoopsDB->query($sql);

    return $result;
}

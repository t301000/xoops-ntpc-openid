<?php

use XoopsModules\Tadtools\Utility;

/*
function xoops_module_update_模組目錄(&$module, $old_version) {
    GLOBAL $xoopsDB;
    
        //if(!chk_chk1()) go_update1();

    return true;
}

//檢查某欄位是否存在
function chk_chk1(){
    global $xoopsDB;
    $sql="select count(`欄位`) from ".$xoopsDB->prefix("資料表");
    $result=$xoopsDB->query($sql);
    if(empty($result)) return false;
    return true;
}

//執行更新
function go_update1(){
    global $xoopsDB;
    $sql="ALTER TABLE ".$xoopsDB->prefix("資料表")." ADD `欄位` smallint(5) NOT NULL";
    $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL,3,  mysql_error());

    return true;
}

*/
?>

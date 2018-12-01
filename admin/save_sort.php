<?php
    /**
     * 處理前端拖拉排序之 request，寫入資料庫
     */

    include_once "../../../mainfile.php";
    include_once "../function.php";

    if (! $xoopsUser || ! isAdmin()) {
        die("未登入 或 不具有模組管理權");
    }

    $sort = 1;
    foreach ($_POST['sn'] as $sn) {
        $sql = "update " . $xoopsDB->prefix("ntpc_openid_login_rules") . " set `sort`='{$sort}' where `sn`='{$sn}'";
        $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . " (" . date("Y-m-d H:i:s") . ")" . $sql);
        $sort++;
    }
    echo _TAD_SORTED . "(" . date("Y-m-d H:i:s") . ")";


    /********************* function 區 *********************/

    /**
     * 是否是模組管理員
     *
     * @return bool
     */
    function isAdmin() {
        global $xoopsUser;

        $modhandler = xoops_gethandler('module');
        $xoopsModule = $modhandler->getByDirname("ntpc_openid");

        return $xoopsUser->isAdmin($xoopsModule->mid());
    }

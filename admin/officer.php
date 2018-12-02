<?php
    /*-----------引入檔案區--------------*/
    $xoopsOption['template_main'] = "ntpc_openid_adm_officer.tpl";
    include_once "header.php";
    include_once "../function.php";

    /*-----------執行動作判斷區----------*/
    include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
    $op = system_CleanVars($_REQUEST, 'op', '', 'string');
    // $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');

    switch ($op) {

        case 'getOfficerList':
            getOfficerList();
            break;

        case 'toggle':
            $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');
            toggleEnable($sn);
            break;

        default:
            show_content();
            break;
    }

    include_once 'footer.php';


    /*-----------function區--------------*/

    //顯示預設頁面內容
    function show_content()
    {
        global $xoopsTpl, $xoopsModuleConfig;

        $data = '_';
        $xoopsTpl->assign('data', $data);
    }

    /**
     * 取得所有行政帳號
     */
    function getOfficerList() {
        die(getJSONResponse(getAllOfficers(false), false));
    }

    /**
     * 啟用 / 停用某行政帳號
     *
     * @param $sn
     */
    function toggleEnable($sn) {
        global $xoopsDB;

        $officer = getOfficerBySN($sn);
        $enable = (int) $officer['enable'] ? 0 : 1;

        $sql = "UPDATE {$xoopsDB->prefix('ntpc_openid_officer_list')} SET enable = {$enable}  WHERE sn = {$sn}";
        $result = $xoopsDB->queryF($sql) or die(getJSONString("啟用 / 停用 sn = {$sn} 之行政帳號時發生錯誤"));

        die(getJSONString(['sn' => (int) $sn, 'msg' => '啟用 / 停用完成']));
    }

    /**
     * 取得某行政帳號
     *
     * @param $sn
     *
     * @return array
     */
    function getOfficerBySN($sn) {
        global $xoopsDB;

        $sql = "SELECT sn, name, enable FROM {$xoopsDB->prefix('ntpc_openid_officer_list')} WHERE sn = {$sn}";
        $result = $xoopsDB->query($sql) or die(getJSONString("取得 sn = {$sn} 之行政帳號時發生錯誤"));

        return $xoopsDB->fetchArray($result);
    }




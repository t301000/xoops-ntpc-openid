<?php
    /*-----------引入檔案區--------------*/
    $xoopsOption['template_main'] = "ntpc_openid_adm_custom_officer.tpl";
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

        case 'createOfficer':
            $data = [];
            createOfficer($data);
            break;

        case 'updateOfficer':
            $data = [];
            updateOfficer($data);
            break;

        case 'deleteOfficer':
            $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');
            deleteOfficer($sn);
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
     * 取得所有自定義行政帳號
     */
    function getOfficerList() {
        die(getJSONResponse(getAllCustomOfficers(false), false));
    }

    /**
     * 啟用 / 停用某自定義行政帳號
     *
     * @param $sn
     */
    function toggleEnable($sn) {
        global $xoopsDB;

        $officer = getOfficerBySN($sn);
        $enable = (int) $officer['enable'] ? 0 : 1;

        $sql = "UPDATE {$xoopsDB->prefix('ntpc_openid_custom_officer')} SET enable = {$enable}  WHERE sn = {$sn}";
        $result = $xoopsDB->queryF($sql) or die(getJSONString("啟用 / 停用 sn = {$sn} 之自定義行政帳號時發生錯誤"));

        die(getJSONString(['sn' => (int) $sn, 'msg' => '啟用 / 停用完成']));
    }

    /**
     * 取得某自定義行政帳號
     *
     * @param $sn
     *
     * @return array
     */
    function getOfficerBySN($sn) {
        global $xoopsDB;

        $sql = "SELECT sn, name, enable FROM {$xoopsDB->prefix('ntpc_openid_custom_officer')} WHERE sn = {$sn}";
        $result = $xoopsDB->query($sql) or die(getJSONString("取得 sn = {$sn} 之自定義行政帳號時發生錯誤"));

        return $xoopsDB->fetchArray($result);
    }

    /**
     * 新增自定義行政帳號
     *
     * @param $data
     */
    function createOfficer($data) {
        die('create custom officer');
    }

    /**
     * 更新自定義行政帳號
     *
     * @param $data
     */
    function updateOfficer($data) {
        die('update custom officer');
    }

    /**
     * 刪除自定義行政帳號
     *
     * @param $sn
     */
    function deleteOfficer($sn) {
        die('delete custom officer');
    }




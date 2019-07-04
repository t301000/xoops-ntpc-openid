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
            $data['name'] = system_CleanVars($_REQUEST, 'name', '', 'string');
            $data['openid'] = system_CleanVars($_REQUEST, 'openid', '', 'string');
            $data['enable'] = system_CleanVars($_REQUEST, 'enable', 1, 'int');

            createOfficer($data);
            break;

        case 'updateOfficer':
            $data['sn'] = system_CleanVars($_REQUEST, 'sn', 0, 'int');
            $data['name'] = system_CleanVars($_REQUEST, 'name', '', 'string');
            $data['openid'] = system_CleanVars($_REQUEST, 'openid', '', 'string');
            $data['enable'] = system_CleanVars($_REQUEST, 'enable', 1, 'int');
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
        $result = $xoopsDB->queryF($sql) or die(http_response_code(500));

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
        $result = $xoopsDB->query($sql) or die(http_response_code(500));

        return $xoopsDB->fetchArray($result);
    }

    /**
     * 新增自定義行政帳號
     *
     * @param $data
     */
    function createOfficer($data) {
        global $xoopsDB;

        $sql = "INSERT INTO {$xoopsDB->prefix('ntpc_openid_custom_officer')} (name, openid, enable) VALUES ('{$data['name']}', '{$data['openid']}', {$data['enable']})";
        $result = $xoopsDB->query($sql) or die(http_response_code(500));

        $sn = $xoopsDB->getInsertId();

        die(getJSONResponse(compact('sn'), false));
    }

    /**
     * 更新自定義行政帳號
     *
     * @param $data
     */
    function updateOfficer($data) {
        global $xoopsDB;

        $sql = "UPDATE {$xoopsDB->prefix('ntpc_openid_custom_officer')} SET name = '{$data['name']}', openid = '{$data['openid']}', enable = {$data['enable']} WHERE sn = {$data['sn']}";
        $result = $xoopsDB->query($sql) or die(getJSONString(http_response_code(500)));

        die(getJSONString(['sn' => $data['sn'], 'msg' => '更新完成'], true));
    }

    /**
     * 刪除自定義行政帳號
     *
     * @param $sn
     */
    function deleteOfficer($sn) {
        global $xoopsDB;

        $sql = "DELETE FROM {$xoopsDB->prefix('ntpc_openid_custom_officer')} WHERE sn = {$sn}";
        $result = $xoopsDB->queryF($sql) or die(http_response_code(500));

        die(getJSONString(['sn' => $sn, 'msg' => '刪除完成'], true));
    }




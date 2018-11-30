<?php
    /*-----------引入檔案區--------------*/
    $xoopsOption['template_main'] = "ntpc_openid_adm_group.tpl";
    include_once "header.php";
    include_once "../function.php";



    /*-----------執行動作判斷區----------*/
    include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
    $op = system_CleanVars($_REQUEST, 'op', '', 'string');
    // $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');

    switch ($op) {

        case 'getAllRulesAndGroups':
            getAllRulesAndGroups();
            break;

        case 'addRule':
            // {id: "014569123", openid: ["user1"], role: ["學生"], title: ["教師兼主任"], groups: ["學務主任"], gid: 2}
            $data = file_get_contents("php://input");
            store(json_decode($data, true));
            break;

        case 'updateRule':
            $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');
            $data = file_get_contents("php://input");
            update($sn, json_decode($data, true));
            break;

        case 'delRule':
            $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');
            destroy($sn);
            break;

        case 'toggleRule':
            $sn = system_CleanVars($_REQUEST, 'sn', 0, 'int');
            toggle($sn);
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

        $data['schoolCode'] = $xoopsModuleConfig['school_code'];
        $xoopsTpl->assign('data', $data);
    }


    /**
     * 取得所有群組規則與群組資料
     */
    function getAllRulesAndGroups() {
        $rules = getAllRules();
        $xoopsGroups = getAllGroups();

        die(getJSONResponse(compact('rules', 'xoopsGroups'), false));
    }

    /**
     * 取得所有群組規則
     *
     * @param bool $only_enable 是否只有啟用
     *
     * @return array
     */
    function getAllRules($only_enable = false) {
        global $xoopsDB;

        $rules = [];
        $sql = "SELECT sn, rule, gid, enable FROM {$xoopsDB->prefix('ntpc_openid_group_rules')}";
        if ($only_enable) {
            $sql .= " WHERE enable = 1";
        }
        $result = $xoopsDB->query($sql) or die(getJSONString('取得全部群組規則時發生錯誤'));

        while ($item = $xoopsDB->fetchArray($result)) {
            $item['rule'] = json_decode($item['rule'], true);
            $rules[] = $item;
        }

        return convert($rules);
        // $rules = [
        //  [
        //     "sn" => "1",
        //     "rule" => ["id" => "014569", "role" => ["教師"]]",
        //     "gid" => "5",
        //     "enable" => "1"
        //  ],
        //  ......
        //]
    }

    /**
     * 取得某一筆群組規則
     *
     * @param $sn
     *
     * @return array|false
     */
    function getRuleBySN($sn) {
        global $xoopsDB;

        $sql = "SELECT sn, rule, gid, enable FROM {$xoopsDB->prefix('ntpc_openid_group_rules')} WHERE sn = {$sn}";
        $result = $xoopsDB->query($sql) or die(getJSONString("取得 sn = {$sn} 之群組規則時發生錯誤"));

        return $xoopsDB->fetchArray($result);
    }

    /**
     * 取得所有群組資料
     *
     * @return array
     */
    function getAllGroups() {
        global $xoopsDB;

        $groups = [];
        $sql = "SELECT groupid as gid, name FROM {$xoopsDB->prefix('groups')}";
        $result = $xoopsDB->query($sql) or die(getJSONString('取得群組時發生錯誤'));

        while ($item = $xoopsDB->fetchArray($result)) {
            $groups[] = $item;
        }

        return convert($groups);
    }

    /**
     * 轉換部份欄位值之資料型別
     *
     * @param array $data
     *
     * @return array
     */
    function convert(array $data) {
        // 須轉換為數字之欄位
        $to_number = ['sn', 'gid', 'enable'];

        $data = array_map(function (array $item) use($to_number) {

            // $item = [
            //     "sn" => "1",
            //     "rule" => ["id" => "014569", "role" => ["教師"]]",
            //     "gid" => "5",
            //     "enable" => "1"
            // ]
            // array_walk callback 第一個參數在此定義為傳址，表示要直接修改值
            array_walk($item, function (&$value, $key, $casts) {
                $value = in_array($key, $casts) ? (int) $value : $value;
            }, $to_number);

            return $item;
        }, $data);

        return $data;
    }

    /**
     * 新增群組規則
     *
     * @param $data
     */
    function store($data) {
        global $xoopsDB;

        $rule = getJSONString($data['rule'], false);
        $gid = (int) $data['gid'];

        $sql = "INSERT INTO {$xoopsDB->prefix('ntpc_openid_group_rules')} (rule, gid) VALUES ('{$rule}', {$gid})";
        $result = $xoopsDB->query($sql) or die(getJSONString('新增群組規則時發生錯誤'));

        $sn = $xoopsDB->getInsertId();

        die(getJSONResponse(compact('sn'), false));
    }

    /**
     * 更新群組規則
     *
     * @param $sn
     * @param $data
     */
    function update($sn, $data) {
        global $xoopsDB;

        $rule = getJSONString($data['rule'], false);
        $gid = (int) $data['gid'];

        $sql = "UPDATE {$xoopsDB->prefix('ntpc_openid_group_rules')} SET rule = '{$rule}', gid = {$gid} WHERE sn = {$sn}";
        $result = $xoopsDB->query($sql) or die(getJSONString("更新 sn = {$sn} 之群組規則時發生錯誤"));

        die(getJSONString(['sn' => (int) $sn, 'msg' => '更新完成']));
    }

    /**
     * 刪除群組規則
     *
     * @param $sn
     */
    function destroy($sn) {
        global $xoopsDB;

        $sql = "DELETE FROM {$xoopsDB->prefix('ntpc_openid_group_rules')} WHERE sn = {$sn}";
        $result = $xoopsDB->queryF($sql) or die(getJSONString("刪除 sn = {$sn} 之群組規則時發生錯誤"));

        die(getJSONString(['sn' => (int) $sn, 'msg' => '刪除完成']));
    }

    /**
     * 啟用 / 停用 某條群組規則
     *
     * @param $sn
     */
    function toggle($sn) {
        global $xoopsDB;

        $rule = getRuleBySN($sn);
        $enable = (int) $rule['enable'] ? 0 : 1;

        $sql = "UPDATE {$xoopsDB->prefix('ntpc_openid_group_rules')} SET enable = {$enable}  WHERE sn = {$sn}";
        $result = $xoopsDB->queryF($sql) or die(getJSONString("啟用 / 停用 sn = {$sn} 之群組規則時發生錯誤"));

        die(getJSONString(['sn' => (int) $sn, 'msg' => '啟用 / 停用完成']));
    }

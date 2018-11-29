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


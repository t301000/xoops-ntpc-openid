<?php
//引入TadTools的函式庫
if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/tad_function.php")) {
    redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50", 3, _TAD_NEED_TADTOOLS);
}
include_once XOOPS_ROOT_PATH . "/modules/tadtools/tad_function.php";

/********************* 自訂函數 *********************/


if (!function_exists('dd')) {
    /**
     * var_dump and die
     *
     * @param string $msg
     * @param bool $die
     */
    function dd($msg = '', $die = false)
    {
        echo '<pre>';
        var_dump($msg);
        echo '</pre>';

        if ($die) die();
    }
}


if (!function_exists('redirectTo')) {
    /**
     * 重導向
     *
     * @param string $to
     */
    function redirectTo($to = '/')
    {
        header("location: {$to}");
        die();
    }
}

if (!function_exists('getAllLoginRules')) {
    /**
     * 取得所有登入規則
     *
     * @param bool $only_enabled 是否只有啟用的規則
     *
     * @return array
     */
    function getAllLoginRules($only_enabled = true)
    {
        global $xoopsDB;

        $rules = [];

        $sql = "SELECT 
                    sn, rule, enable
                FROM
                    {$xoopsDB->prefix('ntpc_openid_login_rules')}";
        $sql .= $only_enabled ?
                " WHERE
                    enable = 1" : '';
        $sql .=" ORDER BY 
                    sort ASC, sn ASC";
        $result = $xoopsDB->queryF($sql) or web_error($sql);

        while (list($sn, $rule, $enable) = $xoopsDB->fetchRow($result)) {
            $sn = (int) $sn;
            $rule = json_decode($rule, true);
            $enable = (int) $enable;
            $rules[] = compact('sn', 'rule', 'enable');
        }

        return $rules;
    }
}

if (!function_exists('getAllGroupRules')) {
    /**
     * 取得所有群組規則
     *
     * @param bool $only_enable 是否只有啟用
     *
     * @return array
     */
    function getAllGroupRules($only_enable = false) {
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
}

if (!function_exists('convert')) {
    /**
     * 轉換部份欄位值之資料型別
     *
     * @param array $data
     *
     * @return array
     */
    function convert(array $data)
    {
        // 須轉換為數字之欄位
        $to_number = ['sn', 'gid', 'enable'];

        $data = array_map(function (array $item) use ($to_number) {

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
}

if (!function_exists('getJSONResponse')) {
    /**
     * 將資料以 json 格式回傳，設定 header Content-Type
     *
     * @param      $data
     * @param bool $number_check 是否處理將 字串型的數字 轉為 數字
     *
     * @return false|string
     */
    function getJSONResponse($data, $number_check = false)
    {
        header('Content-Type:application/json;charset=utf-8');

        return getJSONString($data, $number_check);
    }
}

if (!function_exists('getJSONString')) {
    /**
     * 將資料以 json_encode 轉成 json string
     *
     * @param      $data
     * @param bool $number_check 是否處理將 字串型的數字 轉為 數字
     *
     * @return false|string
     */
    function getJSONString($data, $number_check = false)
    {
        $params = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $params = $number_check ? JSON_NUMERIC_CHECK | $params : $params;

        return json_encode($data, $params);
    }
}

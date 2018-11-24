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

if (!function_exists('getAllRules')) {
    /**
     * 取得所有登入規則
     *
     * @param bool $only_enabled 是否只有啟用的規則
     *
     * @return array
     */
    function getAllRules($only_enabled = true)
    {
        global $xoopsDB;

        $rules = [];

        $sql = "SELECT 
                    sn, rule, sort, enable
                FROM
                    {$xoopsDB->prefix('ntpc_openid_login_rules')}";
        $sql .= $only_enabled ?
                " WHERE
                    enable = 1" : '';
        $sql .=" ORDER BY 
                    sort ASC, sn ASC";
        $result = $xoopsDB->queryF($sql) or web_error($sql);

        while (list($sn, $rule, $sort, $enable) = $xoopsDB->fetchRow($result)) {
            $sn = (int) $sn;
            $rule = json_decode($rule, true);
            $sort = (int) $sort;
            $enable = (int) $enable;
            $rules[] = compact('sn', 'sort', 'rule', 'enable');
        }

        return $rules;
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

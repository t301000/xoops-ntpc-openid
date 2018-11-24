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
     * 取得所有啟用之登入規則
     *
     * @return array
     */
    function getAllRules()
    {
        global $xoopsDB;

        $rules = [];

        $sql = "SELECT 
                    rule, sort
                FROM
                    {$xoopsDB->prefix('ntpc_openid_login_rules')}
                WHERE
                    enable = 1
                ORDER BY 
                    sort ASC";
        $result = $xoopsDB->queryF($sql) or web_error($sql);

        while (list($rule, $sort) = $xoopsDB->fetchRow($result)) {
            $rule = json_decode($rule, true);
            $sort = (int) $sort;
            $rules[] = compact('sort', 'rule');
        }

        return $rules;
    }
}

if (!function_exists('getJSONResponse')) {
    /**
     * 處理欲回傳之 json
     *
     * @param $data
     *
     * @return false|string
     */
    function getJSONResponse($data)
    {
        header('Content-Type:application/json;charset=utf-8');

        return json_encode($data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

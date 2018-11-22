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





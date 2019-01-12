<?php
    //區塊主函式
    function ntpc_openid_change_user($options = "")
    {
        global $xoopsConfig, $xoopsDB, $xoopsUser;
        if (!$xoopsUser) {
            return null;
        }

        // list($url, $query_string) = explode('?', $_SERVER['REQUEST_URI']);
        // $module = explode('/', $url)[2];

        // if ($module !== 'ntpc_openid') {
        //     $_SESSION['url_back_after_change_user'] = $_SERVER['REQUEST_URI'];
        // }

        $block = [];
        $handler = xoops_getHandler('member');
        foreach($_SESSION['ntpcUids'] as $type => $uid) {
            if ($xoopsUser->uid() !== $uid) {
                $block['name'] = $handler->getUser($uid)->name();
            }
        }
       
        return $block;
    }

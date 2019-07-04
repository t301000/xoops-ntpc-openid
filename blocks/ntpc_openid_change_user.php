<?php
    //區塊主函式
    function ntpc_openid_change_user($options = "")
    {
        global $xoopsConfig, $xoopsDB, $xoopsUser;
        if (!$xoopsUser) {
            return null;
        }

        $modhandler     = xoops_getHandler('module');
        $xoopsModule    = $modhandler->getByDirname("ntpc_openid");
        $config_handler = xoops_getHandler('config');
        $modConfig      = $config_handler->getConfigsByCat(0, $xoopsModule->mid());

        if (!$modConfig['can_change_user']) {
            return null;
        }

        $block = [];
        $handler = xoops_getHandler('member');
        foreach($_SESSION['ntpcUids'] as $type => $data) {
            if ($xoopsUser->uid() !== $data['uid']) {
                $block['name'] = $handler->getUser($data['uid'])->name();
            }
        }
       
        return count($block) > 0 ? $block : null;
    }

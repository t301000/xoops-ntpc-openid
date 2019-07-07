<?php
    //區塊主函式
    function ntpc_openid_proxy_user($options = "")
    {
        global $xoopsConfig, $xoopsDB, $xoopsUser;
        if (!$xoopsUser) {
            return null;
        }

        $modhandler     = xoops_getHandler('module');
        $xoopsModule    = $modhandler->getByDirname("ntpc_openid");
        $config_handler = xoops_getHandler('config');
        $modConfig      = $config_handler->getConfigsByCat(0, $xoopsModule->mid());

        if (!$modConfig['can_proxy_user']) {
            return null;
        }

        $memberHandler     = xoops_getHandler('member');
        $adminIDs = $memberHandler->getUsersByGroup(1);
        // 篩選條件：非管理員群組
        $criteria = new Criteria('uid', '(' . implode(',', $adminIDs) . ')', 'NOT IN');
        $criteria->setSort('name'); // 以姓名排序
        $allUsers = $memberHandler->getUsers($criteria); // 取得 XoopsUser list
        $block = array_map(function($user) {
            return [ 'uid' => $user->uid(), 'name' => $user->name()];
        }, $allUsers);
       
        return count($block) > 0 ? $block : null;
    }

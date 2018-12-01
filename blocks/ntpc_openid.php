<?php
    //區塊主函式
    function ntpc_openid($options = "")
    {
        global $xoopsConfig, $xoopsDB, $xoopsUser;
        if ($xoopsUser) {
            // return;
        }

        // include_once XOOPS_ROOT_PATH . "/modules/tad_login/function.php";

        // $modhandler     = xoops_getHandler('module');
        // $xoopsModule    = $modhandler->getByDirname("tad_login");
        // $config_handler = xoops_getHandler('config');
        // $modConfig      = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
        //
        // $block['show_btn']  = $options[0];
        // $block['show_text'] = $options[1];
        // $big                = ($options[2] == '1') ? '_l' : '';
        // $i                  = 0;
        // foreach ($modConfig['auth_method'] as $openid) {
        //     if ($openid == 'facebook') {
        //         $url = facebook_login('return');
        //     } elseif ($openid == 'google') {
        //         $url = google_login('return');
        //     } elseif ($openid == 'edu') {
        //         $url = edu_login('return');
        //     } else {
        //         $url = XOOPS_URL . "/modules/tad_login/index.php?login&op={$openid}";
        //     }
        //     $auth_method[$i]['title'] = $openid;
        //     $auth_method[$i]['url']   = $url;
        //     $auth_method[$i]['logo']  = XOOPS_URL . "/modules/tad_login/images/{$openid}{$big}.png";
        //     $auth_method[$i]['text']  = constant('_' . strtoupper($openid)) . _MB_TADLOGIN_LOGIN;
        //     $i++;
        // }
        //
        // $block['auth_method'] = $auth_method;
        // $block['use_big']     = ($options[2] == '1') ? '1' : '0';
        $block = 'hello';
        return $block;
    }

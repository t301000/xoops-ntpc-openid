<?php
$adminmenu = array();

$i                      = 1;
$adminmenu[$i]['title'] = _MI_TAD_ADMIN_HOME;
$adminmenu[$i]['link']  = 'admin/index.php';
$adminmenu[$i]['desc']  = _MI_TAD_ADMIN_HOME_DESC;
$adminmenu[$i]['icon']  = 'images/admin/home.png';

$i++;
$adminmenu[$i]['title'] = _MI_NTPCOPENID_ADMENU1;
$adminmenu[$i]['link']  = "admin/main.php";
$adminmenu[$i]['desc']  = _MI_NTPCOPENID_ADMENU1_DESC;
$adminmenu[$i]['icon']  = 'images/admin/shield.png';

$i++;
$adminmenu[$i]['title'] = _MI_NTPCOPENID_ADMENU2;
$adminmenu[$i]['link']  = "admin/group.php";
$adminmenu[$i]['desc']  = _MI_NTPCOPENID_ADMENU2_DESC;
$adminmenu[$i]['icon']  = 'images/admin/team.png';

$i++;
$adminmenu[$i]['title'] = _MI_NTPCOPENID_ADMENU3;
$adminmenu[$i]['link']  = "admin/officer.php";
$adminmenu[$i]['desc']  = _MI_NTPCOPENID_ADMENU3_DESC;
$adminmenu[$i]['icon']  = 'images/admin/student.png';

$i++;
$adminmenu[$i]['title'] = _MI_NTPCOPENID_ADMENU4;
$adminmenu[$i]['link']  = "admin/custom_officer.php";
$adminmenu[$i]['desc']  = _MI_NTPCOPENID_ADMENU4_DESC;
$adminmenu[$i]['icon']  = 'images/admin/student.png';

$i++;
$adminmenu[$i]['title'] = _MI_TAD_ADMIN_ABOUT;
$adminmenu[$i]['link']  = 'admin/about.php';
$adminmenu[$i]['desc']  = _MI_TAD_ADMIN_ABOUT_DESC;
$adminmenu[$i]['icon']  = 'images/admin/about.png';

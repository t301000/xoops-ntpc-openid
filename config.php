<?php
// 行政人員職稱，https://openid.ntpc.edu.tw/home/about/
define('OFFICER', [
    '校長',
    '教務主任',
    '學務主任',
    '總務主任',
    '輔導主任',
    '人事主任',
    '會計主任',
    '教學組長',
    '註冊組長',
    '資訊組長',
    '設備組長',
    '訓育組長',
    '生活教育組長',
    '體育組長',
    '衛生組長',
    '文書組長',
    '出納組長',
    '事務組長',
    '輔導組長',
    '資料組長',
    '特教組長',
    '校護',
]);

// 向 OpenID 要求取回之欄位
define('OPENID_REQUIRED', [
    // 'namePerson/friendly', // 暱稱
    'contact/email', // email => 必要
    'namePerson', // 姓名 => 必要
    // 'birthDate', // 生日，1985-06-12
    // 'person/gender', // 性別，M 男
    // 'contact/postalCode/home', // 識別碼
    // 'contact/country/home', // 單位簡稱，xx國中
    // 'pref/language', // 年級班級座號，6 碼
    'pref/timezone' // 授權資訊，含單位代碼、單位全銜、職務別、職稱別、身份別等資料，陣列 => 必要
]);

// 資料轉換之 key 對照
define('CONVERT', [
    'namePerson/friendly' => 'nickName', // 暱稱
    'contact/email' => 'email', // email
    'namePerson' => 'name', // 姓名
    'birthDate' => 'birthday', // 生日，1985-06-12
    'person/gender' => 'gender', // 性別，M 男
    'contact/postalCode/home' => 'code', // 識別碼
    'contact/country/home' => 'schoolNameShort', // 單位簡稱，xx國中
    'pref/language' => 'classInfo', // 年級班級座號，6 碼
    'pref/timezone' => 'authInfos'// 授權資訊，含單位代碼、單位全銜、職務別、職稱別、身份別等資料，陣列
]);

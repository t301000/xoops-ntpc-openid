<?php
/**
 * 登入規則，每筆規則須為陣列
 * 逐一檢查，first match
 * 每筆規則中全部欄位檢查採 AND
 * 各筆規則檢查採 OR
 *
 * 可用欄位：
 *    校代碼 id => 字串
 *    身份 role => 字串 或 陣列
 */
define('RULES', [
    ['id' => '014568', 'role' => '教師'],
    ['id' => '014569', 'role' => ['學生']],
    ['id' => '014569', 'role' => '教師'],
]);

// 被拒絕登入之訊息
define('REJECTED_MESSAGE', '您無法登入本校網站');

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

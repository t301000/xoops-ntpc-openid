<?php
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

// 是否啟用測試模式，略過真實 OpenID 流程，使用假資料
define('FAKE_MODE', false);
// 假資料
define('FAKE_USERS', [
    [
        'openid' => "m321",
        'email' => "m321@apps.ntpc.edu.tw",
        'name' => "林資訊",
        'classInfo' => [ // 班級資訊：年級、班級、座號,
            'grade' => "00",
            'class' => "00",
            'num'   => "00"
        ],
        'authInfos' => [
            [
                'id' => "014569",
                'name' => "新北市立xx國民中學",
                'role' => "教師",
                'title' => "教師兼組長",
                'groups' => ["資訊組長"],
            ],
            [
                'id' => "014569",
                'name' => "新北市立xx國民中學",
                'role' => "家長",
                'title' => "其他",
                'groups' => ["其他"],
            ],
        ]
    ],
    [
        'openid' => "bc1995",
        'email' => "bc1995@apps.ntpc.edu.tw",
        'name' => "黃文書",
        'classInfo' => [ // 班級資訊：年級、班級、座號,
            'grade' => "00",
            'class' => "00",
            'num'   => "00"
        ],
        'authInfos' => [
            [
                'id' => "014569",
                'name' => "新北市立xx國民中學",
                'role' => "教師",
                'title' => "教師兼組長",
                'groups' => ["文書組長"],
            ],
        ]
    ],
    [
        'openid' => "yy1234",
        'email' => "yy1234@apps.ntpc.edu.tw",
        'name' => "許教務",
        'classInfo' => [ // 班級資訊：年級、班級、座號,
            'grade' => "00",
            'class' => "00",
            'num'   => "00"
        ],
        'authInfos' => [
            [
                'id' => "014550",
                'name' => "新北市立oo國民中學",
                'role' => "教師",
                'title' => "教師兼主任",
                'groups' => ["教務主任"],
            ],
            [
                'id' => "014570",
                'name' => "新北市立xx國民小學",
                'role' => "家長",
                'title' => "其他",
                'groups' => ["其他"],
            ],
        ]
    ],
    [
        'openid' => "stu001",
        'email' => "stu001@apps.ntpc.edu.tw",
        'name' => "劉學生",
        'classInfo' => [ // 班級資訊：年級、班級、座號,
            'grade' => "00",
            'class' => "00",
            'num'   => "00"
        ],
        'authInfos' => [
            [
                'id' => "014569",
                'name' => "新北市立oo國民中學",
                'role' => "學生",
                'title' => "其他",
                'groups' => ["其他"],
            ],
        ]
    ],
]);

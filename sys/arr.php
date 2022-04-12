<?php
$FREQ_ARR=array("1"=>"Monthly","3"=>"Quarterly","12"=>"Yearly","120"=>"Lifetime");
$USER_ACT_ARR=array("1"=>"Active","0"=>"Suspended");

$YN_ARR=array("No","Yes");

$AREA_ARR=array("1"=>"Top","2"=>"Left","3"=>"Bottom");
$ACT_ARR=array("Hidden","Active");

$AR_ARR=array("integrations"=>"Integrations", "campaigns"=>"Campaigns", "pages"=>"Pages", "contents"=>"Contents", "unlimited"=>"Unlimited","m-edition"=>"Millionaire Edition");
$LUX_ARR=array("fr"=>"Flickr","oc"=>"Openclipart","if"=>"Iconfinder");

$LG_TYPE_ARR=array("1"=>"Personal Use License","2"=>"Resell Rights License","3"=>"Master Resell Rights License","4"=>"PLR License","5"=>"PLR Branding License","100"=>"Other...");

$LG_HF_DIM_ARR=array("750","100");

$THEME_ARR=array("gray"=>"secondary","blue"=>"info","azure"=>"info","green"=>"success","orange"=>"warning","red"=>"danger","purple"=>"primary");
$ECG_PDF_ARR=array("cdb"=>"http://clonedatabase.com","mb"=>"http://mojobase.com/cdb");

$WS_MOD_ARR=array("ecg"=>"Graphics Generator","lg"=>"License Generator","art"=>"Articles","bonus"=>"Bonuses");

$OPT_IN_1 = ['174', '176', '177'];
$OPT_IN_2 = ['173', '175', '178', '191', '192'];

$DFY_1 = ['53', '55', '56', '76'];
$DFY_2 = ['52', '54', '57', '58', '59', '74', '75'];

$AR_LIST = [
    'aweber' => [
        'key' => 'aweber',
		'name' => 'AWeber',
		'client_id' => '',
		'secret_key' => '',
		'redirect_uri' => '',
		'auth_code' => '',
		'access_token' => '',
		'refresh_token' => '',
		'account_id' => '',
		'list' => []
    ],
    'getresponse' => [
        'key' => 'getresponse',
        'name' => 'Get Response',
        'api_key' => '',
        'account' => '',
        'list' => []
    ],
    'mailchimp' => [
        'key' => 'mailchimp',
        'name' => 'Mailchimp',
        'server' => '',
        'api_key' => '',
        'account' => '',
        'list' => []
    ],
    'convertkit' => [
        'key' => 'convertkit',
        'name' => 'Convert Kit',
        'api_key' => '',
        'api_secret' => '',
        'account' => '',
        'list' => []
    ],
    'sendlane' => [
        'key' => 'sendlane',
        'name' => 'Sendlane',
        'api' => '',
        'hash' => '',
        'account' => '',
        'list' => []
    ],
	'activecampaign' => [
        'key' => 'activecampaign',
        'name' => 'Active Campaign',
        'api_key' => '',
        'acc_url' => '',
        'account' => '',
        'list' => []
    ],
	'hubspot' => [
        'key' => 'hubspot',
        'name' => 'Hub Spot',
        'api' => '',
        'account' => '',
        'list' => []
    ],
	'constantcontact' => [
        'key' => 'constantcontact',
		'name' => 'Constant Contact',
		'client_id' => '',
		'secret_key' => '',
		'redirect_uri' => '',
		'auth_code' => '',
		'access_token' => '',
		'refresh_token' => '',
		'list' => []
    ],
    'sendiio' => [
        'key' => 'sendiio',
		'name' => 'Sendiio',
        'api_token' => '',
        'api_secret' => '',
        'account' => '',
        'list' => []
    ]
];

$SHARING_PLATFORMS = [
    [
        'name' => 'Twitter',
        'key' => 'twitter',
        'color' => '#00aced',
        'data' => [
            'title' => '',
            'url' => '',
            'via' => '',
            'hashtags' => ''
        ]
    ],
    [
        'name' => 'Facebook',
        'key' => 'facebook',
        'color' => '#3b5998',
        'data' => [
            'url' => '',
            'hashtags' => '',
        ]
    ],
    [
        'name' => 'Linkedin',
        'key' => 'linkedin',
        'color' => '#007ab5',
        'data' => [
            'url' => '',
        ]
    ],
    [
        'name' => 'Email',
        'key' => 'email',
        'color' => '#444',
        'data' => [
            'title' => '',
            'url' => '',
            'to' => '',
            'subject' => '',
        ]
    ],
    [
        'name' => 'Whatsapp Web',
        'key' => 'whatsapp',
        'color' => '#4dc247',
        'data' => [
            'title' => '',
            'url' => '',
            'web' => '',
        ]
    ],
    [
        'name' => 'Whatsapp App',
        'key' => 'whatsapp',
        'color' => '#4dc247',
        'data' => [
            'title' => '',
            'url' => '',
        ]
    ],
    [
        'name' => 'Telegram',
        'key' => 'telegram',
        'color' => '#34ade1',
        'data' => [
            'title' => '',
            'url' => '',
            'to' => '',
        ]
    ],
    [
        'name' => 'Viber',
        'key' => 'viber',
        'color' => '#7c529e',
        'data' => [
            'title' => '',
            'url' => '',
        ]
    ],
    [
        'name' => 'Pinterest',
        'key' => 'pinterest',
        'color' => '#cb2029',
        'data' => [
            'url' => '',
            'image' => '',
            'description' => ''
        ]
    ],
    [
        'name' => 'Tumblr',
        'key' => 'tumblr',
        'color' => '#32506d',
        'data' => [
            'url' => '',
            'title' => '',
            'caption' => ''
        ]
    ],
    [
        'name' => 'Hackernews',
        'key' => 'hackernews',
        'color' => '#ff6700',
        'data' => [
            'url' => '',
            'title' => '',
        ]
    ],
    [
        'name' => 'Reddit',
        'key' => 'reddit',
        'color' => '#cee3f8',
        'data' => [
            'url' => '',
        ]
    ],
    [
        'name' => 'VK.com',
        'key' => 'vk',
        'color' => '#45668e',
        'data' => [
            'url' => '',
            'title' => '',
            'image' => '',
            'caption' => '',
        ]
    ],
    [
        'name' => 'Buffer',
        'key' => 'buffer',
        'color' => '#46abed',
        'data' => [
            'url' => '',
            'title' => '',
            'via' => '',
            'picture' => '',
        ]
    ],
    [
        'name' => 'Xing',
        'key' => 'xing',
        'color' => '#006464',
        'data' => [
            'url' => '',
            'title' => ''
        ]
    ],
    [
        'name' => 'Line',
        'key' => 'line',
        'color' => '#1dcd00',
        'data' => [
            'url' => '',
            'title' => ''
        ]
    ],
    [
        'name' => 'Instapaper',
        'key' => 'instapaper',
        'color' => '#aaa',
        'data' => [
            'url' => '',
            'title' => '',
            'description' => ''
        ]
    ],
    [
        'name' => 'Pocket',
        'key' => 'pocket',
        'color' => '#ee4056',
        'data' => [
            'url' => ''
        ]
    ],
    [
        'name' => 'Digg',
        'key' => 'digg',
        'color' => '#1b568e',
        'data' => [
            'url' => ''
        ]
    ],
    [
        'name' => 'StumbleUpon',
        'key' => 'stumbleupon',
        'color' => '#eb4924',
        'data' => [
            'url' => '',
            'title' => ''
        ]
    ],
    [
        'name' => 'Flipboard',
        'key' => 'flipboard',
        'color' => '#e02828',
        'data' => [
            'url' => '',
            'title' => ''
        ]
    ],
    [
        'name' => 'Weibo',
        'key' => 'weibo',
        'color' => '#e6162d',
        'data' => [
            'url' => '',
            'title' => '',
            'image' => ''
        ]
    ],
    [
        'name' => 'Renren',
        'key' => 'renren',
        'color' => '#005eac',
        'data' => [
            'url' => ''
        ]
    ],
    [
        'name' => 'Myspace',
        'key' => 'myspace',
        'color' => '#111',
        'data' => [
            'url' => '',
            'title' => '',
            'description' => ''
        ]
    ],
    [
        'name' => 'Blogger',
        'key' => 'blogger',
        'color' => '#eb8104',
        'data' => [
            'url' => '',
            'title' => '',
            'description' => ''
        ]
    ],
    [
        'name' => 'Baidu',
        'key' => 'baidu',
        'color' => '#2319dc',
        'data' => [
            'url' => '',
            'title' => ''
        ]
    ],
    [
        'name' => 'Ok.ru',
        'key' => 'okru',
        'color' => '#ee8208',
        'data' => [
            'url' => '',
            'title' => ''
        ]
    ],
    [
        'name' => 'Evernote',
        'key' => 'evernote',
        'color' => '#07aa33',
        'data' => [
            'url' => '',
            'title' => ''
        ]
    ],
    [
        'name' => 'Skype',
        'key' => 'skype',
        'color' => '#28a8ea',
        'data' => [
            'url' => '',
            'title' => ''
        ]
    ]
];

?>
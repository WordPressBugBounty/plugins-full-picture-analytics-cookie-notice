<?php $fupi_modules = [
    // order is important - modules will show up in this order in the side menu (except the 1st one which is hardcoded to always go 1st)!
    // [
    //     'id' => 'home',
    //     'title' => 'Home',
    //     'is_avail' => true, // can be used in current plan
    //     'is_premium' => false,
    //     'has_settings_page' => true,
    //     'is_addon' => false,
    //     'type' => 'settings',
    // ],
    [
        'id' => 'tools',
        'title' => esc_attr__('Modules', 'full-picture-analytics-cookie-notice' ),
        'is_avail' => true,
        'is_premium' => false,
        'has_settings_page' => true,
        'is_addon' => false,
        'type' => 'settings',
    ],
    [ 
        'id' => 'main', 
        'title' => esc_attr__('General settings', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'settings',
    ],
    [ 
        'id' => 'cegg', 
        'requires' => ['script_src'],
        'title' => 'Crazy Egg', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true,
        'is_addon' => false,
        'type' => 'integr',
        'check_gdpr' => true,
        'pp' => 'https://support.crazyegg.com/hc/en-us/articles/360054306594-Crazy-Egg-Privacy-Data-Practice',
    ],
    [ 
        'id' => 'gads',
        'requires' => ['id'],
        'title' => 'Google Ads', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr',
        'check_gdpr' => true,
        'pp' => 'https://business.safety.google/privacy/'
    ],
    [ 
        'id' => 'ga41',
        'requires' => ['id'],
        'title' => 'Google Analytics', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr',
        'check_gdpr' => true,
        'pp' => 'https://business.safety.google/privacy/'
    ],
    [ 
        'id' => 'ga42',
        'requires' => ['id'],
        'title' => 'Google Analytics #2', 
        'is_avail' => fupi_fs()->can_use_premium_code(), 
        'is_premium' => true, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://business.safety.google/privacy/'
    ],
    [ 
        'id' => 'hotj',
        'requires' => ['id'],
        'title' => 'Hotjar',
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://help.hotjar.com/hc/en-us/articles/360009206234-General-Data-Protection-Regulation-GDPR',
    ],
    [ 
        'id' => 'insp',
        'requires' => ['id'],
        'title' => 'Inspectlet', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://docs.inspectlet.com/hc/en-us/articles/360002994432-Privacy-Impact-Assessment-under-GDPR',
    ],
    [ 
        'id' => 'linkd',
        'requires' => ['id'],
        'title' => 'LinkedIn Insight', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://www.linkedin.com/legal/privacy-policy#data',
    ],
    [ 
        'id' => 'mato',
        'requires' => ['id', 'url'],
        'title' => 'Matomo', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://matomo.org/matomo-cloud-privacy-policy/',
    ],
    [ 
        'id' => 'fbp1',
        'requires' => ['pixel_id'],
        'title' => 'Meta Pixel', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://www.facebook.com/privacy/policy/',
    ],
    [ 
        'id' => 'mads',
        'requires' => ['id'],
        'title' => 'Microsoft Advertising', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://help.ads.microsoft.com/apex/index/3/en/60212',
    ],
    [ 
        'id' => 'clar',
        'requires' => ['id'],
        'title' => 'Microsoft Clarity', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr',
        'check_gdpr' => true,
        'pp' => 'https://learn.microsoft.com/en-us/clarity/setup-and-installation/privacy-disclosure',
    ],
    [ 
        'id' => 'pin',
        'requires' => ['id'],
        'title' => 'Pinterest Tag', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://policy.pinterest.com/en-gb/privacy-policy-2018',
    ],
    [ 
        'id' => 'pla',
        'title' => 'Plausible', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
    ],
    [ 
        'id' => 'posthog',
        'requires' => ['api_key'],
        'title' => 'PostHog', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://posthog.com/handbook/company/security',
    ],
    [ 
        'id' => 'simpl',
        'title' => 'Simple Analytics', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr',
        'check_gdpr' => true,
    ],
    [ 
        'id' => 'tik',
        'requires' => ['id'],
        'title' => 'TikTok Pixel', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://www.tiktok.com/legal/page/eea/privacy-policy/en',
    ],
    [ 
        'id' => 'twit',
        'requires' => ['id'],
        'title' => 'X Ads (Twitter Ads)', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'integr', 
        'check_gdpr' => true,
        'pp' => 'https://x.com/en/privacy',
    ],
    [ 
        'id' => 'gtm',
        'requires' => ['id'],
        'title' => 'Google Tag Manager', 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'tagman', 
        'check_gdpr' => true,
        'pp' => 'https://business.safety.google/privacy/'
    ],
    [ 
        'id' => 'cscr', 
        'title' => esc_attr__('Custom scripts', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'tagman',
        'check_gdpr' => true,
    ],
    [ 
        'id' => 'status',
        'title' => esc_attr__('GDPR setup helper', 'full-picture-analytics-cookie-notice' ),
        'is_avail' => true,
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'priv',
        'content' => 'info',
    ],
    [ 
        'id' => 'cook', 
        'title' => esc_attr__('Consent banner', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'priv', 
        'check_gdpr' => true,
    ],
    [ 
        'id' => 'iframeblock', 
        'title' => esc_attr__('Iframes manager', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'priv', 
        'check_gdpr' => true,
    ],
    [ 
        'id' => 'safefonts', 
        'title' => esc_attr__('Safe fonts', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true,
        'is_premium' => false,
        'has_settings_page' => false,
        'is_addon' => false,
        'type' => 'priv', 
        'check_gdpr' => true,
    ],
    [ 
        'id' => 'privex', 
        'title' => esc_attr__('Privacy policy extras', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'priv', 
        'check_gdpr' => true,
    ],
    [ 
        'id' => 'blockscr', 
        'title' => esc_attr__('Tracking tools manager', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'priv', 
        'check_gdpr' => true,
    ], 
    [ 
        'id' => 'woo', 
        'title' => esc_attr__('WooCommerce tracking', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'ext',
    ],
    [ 
        'id' => 'reports', 
        'title' => esc_attr__('Reports & statistics', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true,
        'is_addon' => false,
        'type' => 'ext',
    ],
    [ 
        'id' => 'atrig', 
        'title' => esc_attr__('Advanced triggers', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => fupi_fs()->can_use_premium_code(), 
        'is_premium' => true, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'ext', 
    ],
    [ 
        'id' => 'trackmeta', 
        'title' => esc_attr__('Metadata tracking', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => true, 
        'has_settings_page' => true, 
        'is_addon' => false ,
        'type' => 'ext',
    ],
    [ 
        'id' => 'geo', 
        'title' => esc_attr__('Geolocation', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => fupi_fs()->can_use_premium_code(),
        'is_premium' => true, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'ext',
    ],
    [ 
        'id' => 'labelpages', 
        'title' => esc_attr__('Page labels', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => true, 
        'has_settings_page' => false,
        'is_addon' => false,
        'type' => 'ext',
    ],
    [ 
        'id' => 'track404', 
        'title' => esc_attr__('Broken links tracking', 'full-picture-analytics-cookie-notice' ), 
        'is_avail' => true, 
        'is_premium' => false, 
        'has_settings_page' => true, 
        'is_addon' => false,
        'type' => 'ext',
    ],
];
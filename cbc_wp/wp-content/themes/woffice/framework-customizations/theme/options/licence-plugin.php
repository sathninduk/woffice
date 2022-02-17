<?php if (!defined('FW')) {
    die('Forbidden');
}

$woffice_wo_cpt = false;
$isinstalled_woffice_cpt = WP_PLUGIN_DIR . '/custom-post-type-support-for-woocommerce/custom-post-type-support-for-woocommerce.php';
$get_db_woffice_cpt_key = fw_get_db_settings_option('woffice_cpt_key');
$woffice_cpt_key = 'woffice_cpt_key';

if (class_exists('WofficeCustomPostTypesupportforWooCommerce')) {
    
    $status  = get_option('Woffice_Wo_CPT_license_status');

    $woffice_wo_cpt['woffice_cpt_key'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice_plugin_license woffice-wo-cpt-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button..', 'woffice'),
    );
    $woffice_wo_cpt['woffice_cpt_key_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wo-cpt-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'woffice_cpt_key\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_wo_cpt['woffice_cpt_key_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wo-cpt-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'woffice_cpt_key\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_wo_cpt['woffice_cpt_key_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wo-cpt-status'),
        'value' => $status,
    );
} elseif (file_exists($isinstalled_woffice_cpt)) {

    $woffice_wo_cpt['woffice_cpt_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wo-cpt-not-active'),
        'label' => __('Woffice Woo Custom Post Type', 'woffice'),
        'html'  =>  'Please activate <span class="highlight"> Woffice Woocommerce Custom Post Type</span> to create post product. <a href="../wp-admin/plugins.php">Click Here</a>',
    );
} else {
    $woffice_wo_cpt['woffice_cpt_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wo-cpt-not-active'),
        'label' => __('Woffice Woo Custom Post Type', 'woffice'),
        'html'  =>  'Please download <span class="highlight"> Woffice Woocommerce Custom Post Type</span> to create post product. <a href="https://woffice.io/downloads/custom-post-type-support-for-woocommerce/" target="_blank">Click Here</a>',
    );
}

$woffice_wo_cpt['woffice_cpt_key_activate_message'] = array(
    'type'  => 'hidden',
    'attr'  => array('class' => 'woffice-wo-cpt-activate-message'),
    'value' => '',
    'desc'  => __('Activating..', 'woffice'),
);
$woffice_wo_cpt['woffice_cpt_key_deactivate_message'] = array(
    'type'  => 'hidden',
    'attr'  => array('class' => 'woffice-wo-cpt-deactivate-message'),
    'value' => '',
    'desc'  => __('Deactivating..', 'woffice'),
);
$woffice_wo_cpt['woffice_cpt_loading_extra_message'] = array(
    'type'  => 'hidden',
    'attr'  => array('class' => 'woffice-wo-cpt-activate-message'),
    'value' => '',
    'desc'  => __('This may take a few moments.', 'woffice'),
);

// woofice advaned email
$woffice_advanced_email = false;
$isinstalled_woffice_woae = WP_PLUGIN_DIR . '/woffice-advanced-email/woffice-advanced-email.php';
$get_db_woffice_woae = fw_get_db_settings_option('woffice_woae_key');
$woffice_wpae_key = 'woffice_woae_key';

if (class_exists('WOAE')) {
    
    $woffice_woae_status  = get_option('Woffice_woae_license_status');

    $woffice_advanced_email['woffice_woae_key'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-woae-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_advanced_email['woffice_woae_key_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-woae-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'woffice_woae_key\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_advanced_email['woffice_woae_key_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-woae-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'woffice_woae_key\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_advanced_email['woffice_woae_key_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-woae-status'),
        'value' => $woffice_woae_status,
    );
} elseif (file_exists($isinstalled_woffice_woae)) {

    $woffice_advanced_email['woffice_woae_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-woae-not-active'),
        'label' => __('Woffice Advanced email', 'woffice'),
        'html'  =>  'Please activate <span class="highlight"> Woffice Advanced Email</span> to create post product. <a href="../wp-admin/plugins.php">Click Here</a>',
    );
} else {
    $woffice_advanced_email['woffice_woae_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-woae-not-active'),
        'label' => __('Woffice Advanced email;', 'woffice'),
        'html'  =>  'Please download <span class="highlight"> Woffice Advanced Email</span> to create email template. <a href="https://woffice.io/downloads/woffice-advanced-email/" target="_blank">Click Here</a>',
    );
}

// woofice subscription
$woffice_subscription = false;
$isinstalled_woffice_wosubscription = WP_PLUGIN_DIR . '/woffice-subscription/woffice-subscription.php';
$get_db_woffice_woae = fw_get_db_settings_option('woffice_wosubscribe_key');
$woffice_wosubscribe_key = 'woffice_wosubscribe_key';

if (class_exists('WOFFICE_SUBSCRIPTION')) {
    
    $woffice_wosubscribe_status  = get_option('Woffice_wosubscribe_license_status');

    $woffice_subscription['woffice_wosubscribe_key'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wosubscribe-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_subscription['woffice_wosubscribe_key_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wosubscribe-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'woffice_wosubscribe_key\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_subscription['woffice_wosubscribe_key_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wosubscribe-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'woffice_wosubscribe_key\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_subscription['woffice_wosubscribe_key_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wosubscribe-status'),
        'value' => $woffice_wosubscribe_status,
    );
} elseif (file_exists($isinstalled_woffice_wosubscription)) {

    $woffice_subscription['woffice_wosubscribe_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wosubscribe-not-active'),
        'label' => __('Woffice Subscription', 'woffice'),
        'html'  =>  'Please activate <span class="highlight"> Subscriptions for WooCommerce & Woffice </span> to create post product. <a href="../wp-admin/plugins.php">Click Here</a>',
    );
} else {
    $woffice_subscription['woffice_wosubscribe_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wosubscribe-not-active'),
        'label' => __('Woffice Subscription', 'woffice'),
        'html'  =>  'Please download <span class="highlight"> Subscriptions for WooCommerce & Woffice </span> to create email template. <a href="https://woffice.io/downloads/woffice-subscription/" target="_blank">Click Here</a>',
    );
}

// Woffice WP JOB MANAGER Company Listing

$woffice_wpjob_companylisting = false;
$isinstalled_wpjob_companylisting = WP_PLUGIN_DIR . '/wp-job-manager-company-listings/afj-company-listings.php';
$get_db_woffice_wpjob_companylisting = fw_get_db_settings_option('afj_company_listings_status');
$woffice_wpjob_companylisting_key = 'afj_company_listings_key';

if(class_exists('WP_Job_Manager_Company_Listings')) {
    $woffice_wpjob_companylisting_status  = get_option('afj_company_listings_status');

    $woffice_wpjob_companylisting['afj_company_listings'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjob-companylisting-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_wpjob_companylisting['afj_company_listings_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjob-companylisting-key-file'),
        'value' => $isinstalled_wpjob_companylisting,
    );
    $woffice_wpjob_companylisting['afj_company_listings_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-companylisting-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'afj_company_listings\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_wpjob_companylisting['afj_company_listings_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-companylisting-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'afj_company_listings\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_wpjob_companylisting['afj_company_listings_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjob-companylisting-status'),
        'value' => $woffice_wpjob_companylisting_status,
    );    
} elseif (file_exists($isinstalled_wpjob_companylisting)) {

    $woffice_wpjob_companylisting['afj_company_listings_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-companylisting-not-active'),
        'label' => __('Woffice - Company Listings for WP Job Manager', 'woffice'),
        'html'  =>  'Please activate <span class="highlight"> Woffice - Company Listings for WP Job Manager </span> Outputs a list of all companies that have submitted jobs with links to their listings and profile. <a href="../wp-admin/plugins.php">Click Here</a>',
    );
} else {
    $woffice_wpjob_companylisting['afj_company_listings_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-companylisting-not-active'),
        'label' => __('Woffice - Company Listings for WP Job Manager', 'woffice'),
        'html'  =>  'Please download <span class="highlight"> Woffice - Company Listings for WP Job Manager </span> Outputs a list of all companies that have submitted jobs with links to their listings and profile. <a href="https://woffice.io/downloads/company-listings/" target="_blank">Click Here</a>',
    );
}

// Woffice WP JOB MANAGER Company Review

$woffice_wpjob_review = false;
$isinstalled_wpjob_review = WP_PLUGIN_DIR . '/wp-job-manager-reviews/wp-job-manager-reviews.php';
$get_db_woffice_wpjob_review = fw_get_db_settings_option('wpjm_review_status');
$woffice_wpjob_review_key = 'wpjm_review_key';

if(class_exists('WP_Job_Manager_Reviews')) {
    $woffice_wpjob_review_status  = get_option('wpjm_review_status');

    $woffice_wpjob_review['wpjm_review'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjob-review-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_wpjob_review['wpjm_review_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjob-review-key-file'),
        'value' => $isinstalled_wpjob_review,
    );
    $woffice_wpjob_review['wpjm_review_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-review-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'wpjm_review\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_wpjob_review['wpjm_review_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-review-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'wpjm_review\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_wpjob_review['wpjm_review_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjob-review-status'),
        'value' => $woffice_wpjob_review_status,
    );    
} elseif (file_exists($isinstalled_wpjob_review)) {

    $woffice_wpjob_review['wpjm_review_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-review-not-active'),
        'label' => __('Woffice review for WP Job Manager', 'woffice'),
        'html'  =>  'Please activate <span class="highlight"> Woffice review for WP Job Manager </span> Leave reviews for listings in WP Job Manager. Define review categories and choose the number of stars available. <a href="../wp-admin/plugins.php">Click Here</a>',
    );
} else {
    $woffice_wpjob_review['wpjm_review_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-review-not-active'),
        'label' => __('Woffice review for WP Job Manager', 'woffice'),
        'html'  =>  'Please download <span class="highlight"> Woffice review for WP Job Manager </span> Leave reviews for listings in WP Job Manager. Define review categories and choose the number of stars available. <a href="https://woffice.io/downloads/wp-job-manager-reviews/" target="_blank">Click Here</a>',
    );
}

// Woffice WP JOB MANAGER Auto Suggest

$woffice_wpjm_autosuggest = false;
$isinstalled_wpjm_autosuggest = WP_PLUGIN_DIR . '/wp-job-manager-job-suggest/afj-auto-job-suggest.php';
$get_db_woffice_wpjm_autosuggest = fw_get_db_settings_option('wpjm_autosuggest_status');
$woffice_wpjm_autosuggest_key = 'wpjm_autosuggest_key';

if(class_exists('AFJ_Auto_Job_Suggest')) {
    $woffice_wpjm_autosuggest_status  = get_option('wpjm_autosuggest_status');

    $woffice_wpjm_autosuggest['wpjm_autosuggest'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjm-afj-autosuggest-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_wpjm_autosuggest['wpjm_autosuggest_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjm-afj-autosuggest-key-file'),
        'value' => $isinstalled_wpjm_autosuggest,
    );
    $woffice_wpjm_autosuggest['wpjm_autosuggest_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-afj-autosuggest-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'wpjm_autosuggest\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_wpjm_autosuggest['wpjm_autosuggest_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-afj-autosuggest-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'wpjm_autosuggest\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_wpjm_autosuggest['wpjm_autosuggest_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjm-afj-autosuggest-status'),
        'value' => $woffice_wpjm_autosuggest_status,
    );    
} elseif (file_exists($isinstalled_wpjm_autosuggest)) {

    $woffice_wpjm_autosuggest['wpjm_autosuggest_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-review-not-active'),
        'label' => __('Woffice - Auto Job Suggest for WP Job Manager', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'> Woffice - Auto Job Suggest for WP Job Manager </span> Add autocomplete search functionality to WP Job Manager's 'All Jobs' search field. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_wpjm_autosuggest['wpjm_autosuggest_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-review-not-active'),
        'label' => __('Woffice - Auto Job Suggest for WP Job Manager', 'woffice'),
        'html'  =>  "Please download <span class='highlight'> Woffice - Auto Job Suggest for WP Job Manager </span>  Add autocomplete search functionality to WP Job Manager's 'All Jobs' search field. <a href='https://woffice.io/downloads/auto-job-suggest/' target='_blank'>Click Here</a>",
    );
}

// Woffice WP JOB MANAGER Job Style

$woffice_afj_job_style = false;
$isinstalled_afj_job_style = WP_PLUGIN_DIR . '/wp-job-manager-job-styles/afj-job-styles.php';
$get_db_woffice_afj_job_style = fw_get_db_settings_option('afj_job_style_status');
$woffice_afj_job_style_key = 'afj_job_style_key';

if(function_exists('afj_job_styles_css')) {
    $woffice_afj_job_style_status  = get_option('afj_job_style_status');

    $woffice_afj_job_style['afj_job_style'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-afj-job-style-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_afj_job_style['afj_job_style_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-afj-job-style-key-file'),
        'value' => $isinstalled_afj_job_style,
    );
    $woffice_afj_job_style['afj_job_style_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-afj-job-style-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'afj_job_style\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_afj_job_style['afj_job_style_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-afj-job-style-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'afj_job_style\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_afj_job_style['afj_job_style_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-afj-job-style-status'),
        'value' => $woffice_afj_job_style_status,
    );    
} elseif (file_exists($isinstalled_afj_job_style)) {

    $woffice_afj_job_style['afj_job_style_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-review-not-active'),
        'label' => __('Woffice - Job Designer for WP Job Manager', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'> Woffice - Job Designer for WP Job Manager </span> Adds the ability to define custom styles for your WP Job Manager plugin. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_afj_job_style['afj_job_style_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjob-review-not-active'),
        'label' => __('Woffice - Job Designer for WP Job Manager', 'woffice'),
        'html'  =>  "Please download <span class='highlight'> Woffice - Job Designer for WP Job Manager </span>  Adds the ability to define custom styles for your WP Job Manager plugin. <a href='https://woffice.io/downloads/job-styles/' target='_blank'>Click Here</a>",
    );
}

// Woffice WP JOB MANAGER Job Listing Lables

$woffice_wpjm_listing_labels = false;
$isinstalled_wpjm_listing_labels = WP_PLUGIN_DIR . '/wp-job-manager-listing-labels/wp-job-manager-listing-labels.php';
$get_db_woffice_wpjm_listing_labels = fw_get_db_settings_option('wpjm_listing_labels_status');
$woffice_wpjm_listing_labels_key = 'wpjm_listing_labels_key';

if(defined('WPJM_LISTING_LABELS_SLUG')) {
    $woffice_wpjm_listing_labels_status  = get_option('wpjm_listing_labels_status');

    $woffice_wpjm_listing_labels['wpjm_listing_labels'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjm-listing-labels-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_wpjm_listing_labels['wpjm_listing_labels_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjm-listing-labels-key-file'),
        'value' => $isinstalled_wpjm_listing_labels,
    );
    $woffice_wpjm_listing_labels['wpjm_listing_labels_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-listing-labels-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'wpjm_listing_labels\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_wpjm_listing_labels['wpjm_listing_labels_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-listing-labels-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'wpjm_listing_labels\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_wpjm_listing_labels['wpjm_listing_labels_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjm-listing-labels-status'),
        'value' => $woffice_wpjm_listing_labels_status,
    );    
} elseif (file_exists($isinstalled_wpjm_listing_labels)) {

    $woffice_wpjm_listing_labels['wpjm_listing_labels_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-listing-labels-not-active'),
        'label' => __(' Woffice Listing Labels for WP Job Manager', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'>  Woffice Listing Labels for WP Job Manager </span> Add labels to listings to further organize site structure. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_wpjm_listing_labels['wpjm_listing_labels_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-listing-labelsnot-active'),
        'label' => __(' Woffice Listing Labels for WP Job Manager', 'woffice'),
        'html'  =>  "Please download <span class='highlight'>  Woffice Listing Labels for WP Job Manager </span> Add labels to listings to further organize site structure. <a href='https://woffice.io/downloads/wp-job-manager-listing-labels/' target='_blank'>Click Here</a>",
    );
}

// Woffice WP JOB MANAGER Job Listing Payment

$woffice_wpjm_listing_payment = false;
$isinstalled_wpjm_listing_payment = WP_PLUGIN_DIR . '/wp-job-manager-listing-payments/wp-job-manager-listing-payments.php';
$get_db_woffice_wpjm_listing_payment = fw_get_db_settings_option('wpjm_listing_payment_status');
$woffice_wpjm_listing_payment_key = 'wpjm_listing_payment_key';

if(function_exists('astoundify_wpjmlp_get_user_packages')) {
    $woffice_wpjm_listing_payment_status  = get_option('wpjm_listing_payment_status');

    $woffice_wpjm_listing_payment['wpjm_listing_payment'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjm-listing-labels-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_wpjm_listing_payment['wpjm_listing_payment_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjm-listing-payment-key-file'),
        'value' => $isinstalled_wpjm_listing_payment,
    );
    $woffice_wpjm_listing_payment['wpjm_listing_payment_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-listing-payment-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'wpjm_listing_payment\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_wpjm_listing_payment['wpjm_listing_payment_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-listing-payment-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'wpjm_listing_payment\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_wpjm_listing_payment['wpjm_listing_payment_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-listing-payment-labels-status'),
        'value' => $woffice_wpjm_listing_payment_status,
    );    
} elseif (file_exists($isinstalled_wpjm_listing_payment)) {

    $woffice_wpjm_listing_payment['wpjm_listing_payment_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-listing-payment-not-active'),
        'label' => __(' Woffice Listing Payments for WP Job Manager', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'> Woffice Listing Payments for WP Job Manager </span> Sell listings via WooCommerce. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_wpjm_listing_payment['wpjm_listing_payment_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-listing-payment-not-active'),
        'label' => __(' Woffice Listing Payments for WP Job Manager', 'woffice'),
        'html'  =>  "Please download <span class='highlight'> Woffice Listing Payments for WP Job Manager </span> Sell listings via WooCommerce. <a href='https://woffice.io/downloads/wp-job-manager-listing-payments/' target='_blank'>Click Here</a>",
    );
}

// Woffice WP JOB MANAGER PRODUCTS

$woffice_wpjm_product = false;
$isinstalled_wpjm_product = WP_PLUGIN_DIR . '/wp-job-manager-products/wp-job-manager-products.php';
$get_db_woffice_wpjm_product = fw_get_db_settings_option('wpjm_product_status');
$woffice_wpjm_product_key = 'wpjm_product_key';

if(class_exists('WP_Job_Manager_Products')) {
    $woffice_wpjm_product_status  = get_option('wpjm_product_status');

    $woffice_wpjm_product['wpjm_product'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjm-product-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_wpjm_product['wpjm_product_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjm-product-key-file'),
        'value' => $isinstalled_wpjm_product,
    );
    $woffice_wpjm_product['wpjm_product_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-product-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'wpjm_product\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_wpjm_product['wpjm_product_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-product-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'wpjm_product\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_wpjm_product['wpjm_product_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-product-labels-status'),
        'value' => $woffice_wpjm_product_status,
    );    
} elseif (file_exists($isinstalled_wpjm_product)) {

    $woffice_wpjm_product['wpjm_product_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-product-not-active'),
        'label' => __('Woffice Products for WP Job Manager', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'> Woffice Products for WP Job Manager </span> Allows you to assign products created in WooCommerce to be associated with listings. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_wpjm_product['wpjm_product_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-product-not-active'),
        'label' => __(' Woffice Products for WP Job Manager', 'woffice'),
        'html'  =>  "Please download <span class='highlight'> Woffice Products for WP Job Manager </span> Allows you to assign products created in WooCommerce to be associated with listings. <a href='http://woffice.io/downloads/wp-job-manager-products' target='_blank'>Click Here</a>",
    );
}

// Woffice WP JOB MANAGER STATS

$woffice_wpjm_stat = false;
$isinstalled_wpjm_stat = WP_PLUGIN_DIR . '/wp-job-manager-stats/wp-job-manager-stats.php';
$get_db_woffice_wpjm_stat = fw_get_db_settings_option('wpjm_stat_status');
$woffice_wpjm_stat_key = 'wpjm_stat_key';

if(function_exists('wpjms_stats')) {
    $woffice_wpjm_stat_status  = get_option('wpjm_stat_status');

    $woffice_wpjm_stat['wpjm_stat'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjm-product-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_wpjm_stat['wpjm_stat_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wpjm-product-key-file'),
        'value' => $isinstalled_wpjm_stat,
    );
    $woffice_wpjm_stat['wpjm_stat_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-product-activate-button'),
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'wpjm_stat\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_wpjm_stat['wpjm_stat_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-product-deactivate-button'),
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'wpjm_stat\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_wpjm_stat['wpjm_stat_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-product-labels-status'),
        'value' => $woffice_wpjm_stat_status,
    );    
} elseif (file_exists($isinstalled_wpjm_stat)) {

    $woffice_wpjm_stat['wpjm_stat_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-product-not-active'),
        'label' => __(' Woffice Stats for WP Job Manager', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'> Woffice Stats for WP Job Manager </span> Capture and display stats of the listings. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_wpjm_stat['wpjm_stat_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wpjm-product-not-active'),
        'label' => __(' Woffice Stats for WP Job Manager', 'woffice'),
        'html'  =>  "Please download <span class='highlight'> Woffice Stats for WP Job Manager </span> Capture and display stats of the listings. <a href=' https://woffice.io/downloads/wp-job-manager-stats/' target='_blank'>Click Here</a>",
    );
}

// Woffice Kanban

$woffice_wokss_kanban = false;
$isinstalled_wokss_kanban = WP_PLUGIN_DIR . '/woffice-kanban-style-shorting/woffice-kanban-style-shorting.php';
$get_db_woffice_wokss_kanban = fw_get_db_settings_option('wokss_kanban_status');
$woffice_wokss_kanban_key = 'wokss_kanban_key';

if(class_exists('WOKSS_KANBAN')) {
    $woffice_wokss_kanban_status  = get_option('wokss_kanban_status');

    $woffice_wokss_kanban['wokss_kanban'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjm-product-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_wokss_kanban['wokss_kanban_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wokss-kanban-key-file'),
        'value' => $isinstalled_wokss_kanban,
    );
    $woffice_wokss_kanban['wokss_kanban_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wokss-kanban-activate-button'),
        'label' => '',
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'wokss_kanban\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_wokss_kanban['wokss_kanban_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wokss-kanban-deactivate-button'),
        'label' => '',
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'wokss_kanban\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_wokss_kanban['wokss_kanban_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wokss-status'),
        'value' => $woffice_wokss_kanban_status,
    );    
} elseif (file_exists($isinstalled_wokss_kanban)) {

    $woffice_wokss_kanban['wokss_kanban_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wokss-not-active'),
        'label' => __(' Woffice Kanban Style Shorting', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'> Woffice Kanban Style Shorting </span> Woffice Kanban Style Shorting allows you to short out your Tasks in a better Kanban style view. Have the options to switch at different states and help organize your view better. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_wokss_kanban['wokss_kanban_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wokss-not-active'),
        'label' => __(' Woffice Kanban Style Shorting', 'woffice'),
        'html'  =>  "Please download <span class='highlight'> Woffice Kanban Style Shorting </span> Woffice Kanban Style Shorting allows you to short out your Tasks in a better Kanban style view. Have the options to switch at different states and help organize your view better. <a href='https://woffice.io/downloads/woffice-kanban/' target='_blank'>Click Here</a>",
    );
}

// Woffice Woffice Advanced Tasks

$woffice_advanced_tasks = false;
$isinstalled_advanced_tasks_for_woffice = WP_PLUGIN_DIR . '/advanced-tasks-for-woffice/advanced-tasks-for-woffice.php';
$get_db_woffice_advanced_tasks_for_woffice = fw_get_db_settings_option('advanced_tasks_for_woffice');
$woffice_advanced_tasks_for_woffice_key = 'advanced_tasks_for_woffice_key';

if(class_exists('Woffice_Advanced_Tasks')) {
    $woffice_advanced_tasks_status  = get_option('advanced_tasks_for_woffice');

    $woffice_advanced_tasks['advanced_tasks_for_woffice'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjm-product-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_advanced_tasks['advanced_tasks_for_woffice_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wokss-kanban-key-file'),
        'value' => $isinstalled_advanced_tasks_for_woffice,
    );
    $woffice_advanced_tasks['advanced_tasks_for_woffice_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wokss-kanban-activate-button'),
        'label' => '',
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'advanced_tasks_for_woffice\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_advanced_tasks['advanced_tasks_for_woffice_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-advanced-tasks-for-woffice-deactivate-button'),
        'label' => '',
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'advanced_tasks_for_woffice\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_advanced_tasks['advanced_tasks_for_woffice_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-wokss-status'),
        'value' => $woffice_advanced_tasks_status,
    );    
} elseif (file_exists($isinstalled_advanced_tasks_for_woffice)) {

    $woffice_advanced_tasks['advanced_tasks_for_woffice_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wokss-not-active'),
        'label' => __(' Advanced tasks for woffice ', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'> Advanced tasks for woffice </span> Advanced Tasks for Woffice is a Woffice Plugin that enhance the UI of Woffice tasks and adding more features for the end user. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_advanced_tasks['advanced_tasks_for_woffice_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-wokss-not-active'),
        'label' => __(' Advanced tasks for woffice', 'woffice'),
        'html'  =>  "Please download <span class='highlight'> Advanced tasks for woffice </span> Advanced Tasks for Woffice is a Woffice Plugin that enhance the UI of Woffice tasks and adding more features for the end user. <a href='https://woffice.io/downloads/advanced-tasks-for-woffice/' target='_blank'>Click Here</a>",
    );
}
// Woffice docs_to_wiki

$woffice_docs_to_wiki = false;
$isinstalled_docs_to_wiki = WP_PLUGIN_DIR . '/docs-to-wiki/docs-to-wiki.php';
$get_db_woffice_docs_to_wiki = fw_get_db_settings_option('docs_to_wiki_status');
$woffice_docs_to_wiki_key = 'docs_to_wiki_key';

if(class_exists('docs_to_wiki')) {
    $woffice_docs_to_wiki_status  = get_option('docs_to_wiki_status');

    $woffice_docs_to_wiki['docs_to_wiki'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-wpjm-product-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_docs_to_wiki['docs_to_wiki_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-docs-to-wiki-key-file'),
        'value' => $isinstalled_docs_to_wiki,
    );
    $woffice_docs_to_wiki['docs_to_wiki_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-docs-to-wiki-activate-button'),
        'label' => '',
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'docs_to_wiki\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_docs_to_wiki['docs_to_wiki_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-docs-to-wiki-deactivate-button'),
        'label' => '',
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'docs_to_wiki\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_docs_to_wiki['docs_to_wiki_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-docstowiki-status'),
        'value' => $woffice_docs_to_wiki_status,
    );    
} elseif (file_exists($isinstalled_docs_to_wiki)) {

    $woffice_docs_to_wiki['docs_to_wiki_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-docstowiki-not-active'),
        'label' => __(' Woffice Doc To Wiki', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'> Woffice Doc To Wiki </span> it allows you to display google doc on your site. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_docs_to_wiki['docs_to_wiki_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-docstowiki-not-active'),
        'label' => __(' Woffice Doc To Wiki', 'woffice'),
        'html'  =>  "Please download <span class='highlight'> Woffice Doc To Wiki </span> it allows you to display google doc on your site. <a href='https://woffice.io/downloads/docs-to-wiki/' target='_blank'>Click Here</a>",
    );
}
// Woffice Timeline
$woffice_timeline = false;
$isinstalled_woffice_timeline = WP_PLUGIN_DIR . '/woffice-timeline/woffice-timeline.php';
$get_db_woffice_woffice_timeline = fw_get_db_settings_option('woffice_timeline_status');
$woffice_timeline_key = 'woffice_timeline_key';

if(class_exists('Woffice_Timeline')) {
    $woffice_timeline_status  = get_option('woffice_timeline_status');

    $woffice_timeline['woffice_timeline'] = array(
        'label' => __('Licence Key', 'woffice'),
        'attr'  => array('class' => 'woffice-plugin-license woffice-timeline-active', 'autocomplete' => 'false'),
        'type'         => 'text',
        'value' => '',
        'desc' =>   __('Enter Licence Key. After Activating/Deactivating please click on "Save Changes" Button.', 'woffice'),
    );
    $woffice_timeline['woffice_timeline_file'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-timeline-key-file'),
        'value' => $isinstalled_woffice_timeline,
    );
    $woffice_timeline['woffice_timeline_activate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-timeline-activate-button'),
        'label' => '',
        'desc' =>   __('Click to activate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptLicenceActivate(\'woffice_timeline\');" class="button-large submit-button-save woffice_licence_activate_btn">Activate</button>',
    );
    $woffice_timeline['woffice_timeline_deactivate'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-timeline-deactivate-button'),
        'label' => '',
        'desc' =>   __('Click to deactivate licence', 'woffice'),
        'html'  =>  '<button type="button" onclick="WocptDeActivateLicenceKey(\'woffice_timeline\');" class="button-large submit-button-save woffice_licence_deactivate_btn">Deactivate</button>',
    );
    $woffice_timeline['woffice_timeline_status'] = array(
        'type'  => 'hidden',
        'attr'  => array('class' => 'woffice-timeline-status'),
        'value' => $woffice_timeline_status,
    );    
} elseif (file_exists($isinstalled_woffice_timeline)) {

    $woffice_timeline['woffice_timeline_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-timeline-not-active'),
        'label' => __(' Woffice Timeline', 'woffice'),
        'html'  =>  "Please activate <span class='highlight'> Woffice Timeline </span> it allows you to display google doc on your site. <a href='../wp-admin/plugins.php'>Click Here</a>",
    );
} else {
    $woffice_timeline['woffice_timeline_key_message_key'] = array(
        'type'  => 'html',
        'attr'  => array('class' => 'woffice-timeline-not-active'),
        'label' => __(' Woffice Timeline', 'woffice'),
        'html'  =>  "Please download <span class='highlight'> Woffice Timeline </span> it allows you to display google doc on your site. <a href='https://woffice.io/downloads/woffice-timeline/' target='_blank'>Click Here</a>",
    );
}


$options = array(
    'plugin-licence' => array(
        'title'   => __('Plugins', 'woffice'),
        'type'    => 'tab',
        'options' => array(
            'woffice-timeline-box' => array(
                'title'   => __('Woffice Timeline <span class="new-item-badge">New Launch</span>', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_timeline,
                )
            ),
            'woffice-docs-to-wiki-box' => array(
                'title'   => __('Woffice Docs To Wiki <span class="new-item-badge">New Launch</span>', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_docs_to_wiki,
                )
            ),
            'woffice-advanced-task-box' => array(
                'title'   => __('Advanced Tasks For Woffice <span class="new-item-badge">New Launch</span>', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_advanced_tasks,
                )
            ),
            'woffice-wokss-kanban-box' => array(
                'title'   => __('Woffice Kanban Style Shorting', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_wokss_kanban,
                )
            ),
            'woffice-plugins-box' => array(
                'title'   => __('Woo Custom Post Type Manager', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_wo_cpt,
                )
            ),
            'woffice-woae-box' => array(
                'title'   => __('Woffice Advanced Email', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_advanced_email,
                )
            ),
            'woffice-wosubscribe-box' => array(
                'title'   => __('Subscriptions for WooCommerce & Woffice', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_subscription,
                )
            ),
            'woffice-wpjob-companylisting-box' => array(
                'title'   => __('Woffice - Company Listings for WP Job Manager', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_wpjob_companylisting,
                )
            ),
            'woffice-wpjob-review-box' => array(
                'title'   => __('Woffice review for WP Job Manager', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_wpjob_review,
                )
            ),
            'woffice-wpjm-afj-autosuggest-box' => array(
                'title'   => __('Woffice - Auto Job Suggest for WP Job Manager', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_wpjm_autosuggest,
                )
            ),
            'woffice-afj-job-style-box' => array(
                'title'   => __('Woffice - Job Designer for WP Job Manager', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_afj_job_style,
                )
            ),
            'woffice-wpjm-listing-labels-box' => array(
                'title'   => __('Woffice Listing Labels for WP Job Manager', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_wpjm_listing_labels,
                )
            ),
            'woffice-wpjm-listing-payment-box' => array(
                'title'   => __('Woffice Listing Payments for WP Job Manager', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_wpjm_listing_payment,
                )
            ),
            'woffice-wpjm-product-box' => array(
                'title'   => __('Woffice Products for WP Job Manager', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_wpjm_product,
                )
            ),
            'woffice-wpjm-stat-box' => array(
                'title'   => __('Woffice Stats for WP Job Manager', 'woffice'),
                'type'    => 'box',
                'options' => array(
                    $woffice_wpjm_stat,
                )
            ),
        )
    )
);

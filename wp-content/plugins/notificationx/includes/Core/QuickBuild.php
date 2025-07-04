<?php

/**
 * Register Global Fields
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Core;

use NotificationX\Admin\Admin;
use NotificationX\Core\Rules;
use NotificationX\Core\Database;
use NotificationX\Core\Locations;
use NotificationX\GetInstance;
use NotificationX\Core\Modules;
use NotificationX\Extensions\GlobalFields;
use NotificationX\NotificationX;
use NotificationX\Types\TypeFactory;

/**
 * @method static QuickBuild get_instance($args = null)
 */
class QuickBuild {
    /**
     * Instance of QuickBuild
     *
     * @var QuickBuild
     */
    use GetInstance;

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'menu'], 27);

    }

    /**
     * This method is responsible for Admin Menu of
     * NotificationX
     *
     * @return void
     */
    public function menu() {
        add_submenu_page('nx-admin', __('Quick Builder', 'notificationx'), __('Quick Builder', 'notificationx'), 'edit_notificationx', 'nx-builder', [Admin::get_instance(), 'views'], 5);
    }

    public function tabs($configs = []) {
        if(empty($configs)){
            $configs = GlobalFields::get_instance()->tabs();
        }

        $tabs = [
            'source_tab'  => $configs['tabs']['source_tab'],
            'design_tab'  => $configs['tabs']['design_tab'],
            'content_tab' => [],
            'manager_tab' => $configs['tabs']['manager_tab'],
            'display_tab' => $configs['tabs']['display_tab'],
            'finalize'    => [
                'label'   => "Finalize",
                'id'      => "finalize",
                'icon'    => [
                    'type' => 'tabs',
                    'name' => 'source'
                ],
                'classes' => "finalize",
                'fields'  => [
                    'finalize_message' => [
                        'type' => 'action',
                        'action' => 'nx_quick_build_finalize',
                    ],
                ],

            ],
        ];

        if(!empty($configs['tabs']['content_tab']['fields']['content']['fields']['custom_contents'])){
            $tabs['content_tab'] = [
                'label' => "Content",
                'id'    => "content_tab",
                'name'  => "content_tab",
                'icon'  => [
                    'type' => 'tabs',
                    'name' => 'content'
                ],
                'classes' => "content_tab",
                'rules' => Rules::includes('source', ['custom_notification', 'custom_notification_conversions']),
                'fields' => [
                    'content' => [
                        'label'    => __("Content", 'notificationx'),
                        'name'     => "content",
                        'type'     => "section",
                        'priority' => 90,
                        'fields'   => [
                            'custom_contents' => $configs['tabs']['content_tab']['fields']['content']['fields']['custom_contents'],
                        ],
                    ],
                ]
            ];

            unset($configs['tabs']['content_tab']['fields']['content']['fields']['custom_contents']);
        }
        else{
            unset($tabs['content_tab']);
        }

        $tabs['source_tab']['fields'] = array_merge($tabs['source_tab']['fields'], $configs['tabs']['content_tab']['fields']);
        $tabs['display_tab']['fields'] = array_merge($tabs['display_tab']['fields'], $configs['tabs']['customize_tab']['fields']);

        return apply_filters('nx_quick_builder_tabs', [
            'id'            => 'notificationx_metabox_quick_builder_wrapper',
            'redirect'      => !current_user_can( 'edit_notificationx' ),
            'title'         => __('NotificationX', 'notificationx'),
            'is_pro_active' => NotificationX::get_instance()->is_pro(),
            'config'        => [
                'active'  => "source_tab",
                'completionTrack' => true,
                'sidebar' => false,
                'step' => [
                    'show' => true,
                    'buttons' => [
                        'prev' => 'Previous',
                        'next' => 'Next',
                        'quick-builder-publish' => [
                            'name' => 'quick-builder-publish',
                            'type' => 'action',
                            'action' => 'nx_quick_build_launch',
                        ],
                    ]
                ],
            ],
            'submit' => [
                'show' => false,
            ],
            'tabs'         => $tabs,
            'quickBuilder' => true,
            'show' => [
                'source_error',
                'finalize_message',
                'youtube_channel_id',
                'youtube_video_id',
                'type_section',
                'themes_section',
                'for_desktop',
                'for_mobile',
                'is_mobile_responsive',
                'responsive_themes',
                'desktop_themes',
                'type',
                'source_section',
                'source',
                'themes',
                'gdpr_theme',
                'gdpr_position',
                'gdpr_title',
                'gdpr_message',
                'cookies_list_section',
                'content',
                'combine_multiorder',
                'freemius_item_type',
                'freemius_themes',
                'freemius_plugins',
                'wp_reviews_product_type',
                'wp_reviews_slug',
                'wp_stats_product_type',
                'wp_stats_slug',
                'press_content',
                'form_list',
                'mailchimp_list',
                'convertkit_form',
                'custom_contents',
                'title',
                'post_title',
                'post_comment',
                'username',
                'name',
                'first_name',
                'last_name',
                'email',
                'city',
                'country',
                'sales_count',
                'donation_count',
                'image',
                'link',
                'rated',
                'plugin_name',
                'plugin_review',
                'rating',
                'today',
                'last_week',
                'all_time',
                'active_installs',
                'timestamp',
                'appearance',
                'position',
                'visibility',
                'show_on',
                'all_locations',
                'show_on_display',
                
                'google_reviews_place_data',
                'google_reviews_custom_place_id',
                
                'ft_theme_one_icons',
                'ft_theme_one_message',
                'ft_theme_three_line_one',
                'ft_theme_three_line_two',
                'ft_theme_four_line_two',
                'close_button',
                'close_button_tab',
                'close_button_mobile',
                'hide_on_desktop',
                'hide_on_tab',
                'hide_on_mobile',
                'notification-template',
                'first_param',
                'custom_first_param',
                'second_param',
                'third_param',
                'custom_third_param',
                'fourth_param',
                'custom_fourth_param',
                'fifth_param',
                'custom_fifth_param',
                'sixth_param',
                'custom_sixth_param',
                'activecampaign_form',
                'announcement_entries',
                'offer_title',
                'offer_discount',
                'image',
                'expire_time',
                'offer_description',
            ],
            'types_title' => apply_filters( 'nx_source_types_title', array(
                'notification_bar' => __('Notification Bar', 'notificationx'),
                'comments'         => __('Comments', 'notificationx'),
                'conversions'      => __('Sales Notification', 'notificationx'),
                'video'            => __('Video','notificationx'),
                'reviews'          => __('Reviews', 'notificationx'),
                'download_stats'   => __('Download Stats', 'notificationx'),
                'elearning'        => __('eLearning', 'notificationx'),
                'donation'         => __('Donation', 'notificationx'),
                'form'             => __('Contact Form', 'notificationx'),
                'inline'           => __('Growth Alert', 'notificationx'),
                'gdpr'             => __('Cookie Notice', 'notificationx'),
            )),
        ]);
    }

}


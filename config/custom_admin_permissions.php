<?php

return [
    'default' => [
        [
            'rank' => 1,
            'name' => 'Booking Management',
            'permissions' => [
                'view_booking',
                'add_booking',
                'edit_booking',
                'delete_booking',

                'view_status',
                'view_booking_calendar',

                'view_payslip',
                'add_payslip',
                'edit_payslip',
                'delete_payslip',
            ],
        ],

        [
            'rank' => 2,
            'name' => 'Invoice Management',
            'permissions' => [
                'view_invoice',
                'add_invoice',
                'edit_invoice',
                'delete_invoice',

                'view_extra_invoice',
                'add_extra_invoice',
                'edit_extra_invoice',
                'delete_extra_invoice',
            ],
        ],

        [
            'rank' => 3,
            'name' => 'Item Management',
            'permissions' => [
                'view_item_category',
                'add_item_category',
                'edit_item_category',
                'delete_item_category',

                'view_item_sub_category',
                'add_item_sub_category',
                'edit_item_sub_category',
                'delete_item_sub_category',

                'view_item',
                'add_item',
                'edit_item',
                'delete_item',

                'view_room_layout',
                'add_room_layout',
                'edit_room_layout',
                'delete_room_layout',

                'view_room_schedule',
                'add_room_schedule',
                'edit_room_schedule',
                'delete_room_schedule',

                'view_room_plan',
                'add_room_plan',
                'edit_room_plan',
                'delete_room_plan',

                'view_room_schedule_detail',
            ],
        ],

        [
            'rank' => 4,
            'name' => 'Contact Message Management',
            'permissions' => [
                'view_message',
                'add_message',
                'edit_message',
                'delete_message',
            ],
        ],

        [
            'rank' => 5,
            'name' => 'Other Service Management',
            'permissions' => [
                'view_other_service_category',
                'add_other_service_category',
                'edit_other_service_category',
                'delete_other_service_category',

                'view_other_service_item',
                'add_other_service_item',
                'edit_other_service_item',
                'delete_other_service_item',
            ],
        ],

        [
            'rank' => 6,
            'name' => 'Dynamic Pricing Management',
            'permissions' => [
                'view_discount',
                'add_discount',
                'edit_discount',
                'delete_discount',

                'view_extra_bed_price',
                'add_extra_bed_price',
                'edit_extra_bed_price',
                'delete_extra_bed_price',

                'view_earlylatecheck',
                'add_earlylatecheck',
                'edit_earlylatecheck',
                'delete_earlylatecheck',
            ],
        ],

        [
            'rank' => 7,
            'name' => 'User Management',
            'permissions' => [
                'view_user',
                'add_user',
                'edit_user',
                'delete_user',

                'view_account_type',
                'add_account_type',
                'edit_account_type',
                'delete_account_type',

                'view_user_nrc_image',
                'add_user_nrc_image',
                'edit_user_nrc_image',
                'delete_user_nrc_image',

                'view_user_credit',
                'add_user_credit',
                'edit_user_credit',
                'delete_user_credit',

                'view_payment_card',
                'add_payment_card',
                'edit_payment_card',
                'delete_payment_card',
            ],
        ],

        [
            'rank' => 8,
            'name' => 'Other Management',
            'permissions' => [
                'view_tax',
                'add_tax',
                'edit_tax',
                'delete_tax',

                'view_checkin_deposit',
                'add_checkin_deposit',
                'edit_checkin_deposit',
                'delete_checkin_deposit',

                'view_slider',
                'add_slider',
                'edit_slider',
                'delete_slider',
            ],
        ],

        [
            'rank' => 9,
            'name' => 'Admin User Management',
            'permissions' => [
                'view_admin',
                'add_admin',
                'edit_admin',
                'delete_admin',

                'view_admin_user_role',
                'add_admin_user_role',
                'edit_admin_user_role',
                'delete_admin_user_role',

            ],
        ],

        [
            'rank' => 10,
            'name' => 'Permissioin Management',
            'permissions' => [
                'view_permission',
                'add_permission',
                'edit_permission',
                'delete_permission',
            ],
        ],

        [
            'rank' => 11,
            'name' => 'Notification Management',
            'permissions' => [
                'view_sendNotification',
                'add_sendNotification',
                'edit_sendNotification',
                'delete_sendNotification',
            ],
        ],

        // 'view roles',
        // 'add role',
        // 'edit role',
        // 'delete role',

        // 'view_category',
        // 'add_category',
        // 'edit_category',
        // 'delete_category',

    ],
];

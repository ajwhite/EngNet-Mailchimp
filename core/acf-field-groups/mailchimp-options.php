<?php

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_mailchimp-options',
		'title' => 'MailChimp Options',
		'fields' => array (
			array (
				'key' => 'field_5321d9c37c86e',
				'label' => 'MailChimp Accounts',
				'name' => 'mailchimp_accounts',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_5321d9d47c86f',
						'label' => 'Name',
						'name' => 'name',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_5321d9da7c870',
						'label' => 'API Key',
						'name' => 'api_key',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
				),
				'row_min' => 0,
				'row_limit' => '',
				'layout' => 'row',
				'button_label' => 'Add Account',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'acf-options',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

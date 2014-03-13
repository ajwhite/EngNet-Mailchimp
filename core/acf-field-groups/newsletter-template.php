<?php


if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_newsletter-template-fields',
		'title' => 'Newsletter Template Fields ',
		'fields' => array (
			array (
				'key' => 'field_531f94e3ef3c9',
				'label' => 'Template',
				'name' => 'template',
				'type' => 'post_object',
				'required' => 1,
				'post_type' => array (
					0 => 'newsletter_markup',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5220de92446b0',
				'label' => 'Featured Post 1',
				'name' => 'featured_post_1',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5220deb3446b1',
				'label' => 'Featured Post 2',
				'name' => 'featured_post_2',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5220dec5446b2',
				'label' => 'Standard Post 1',
				'name' => 'standard_post_1',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5220deee446b7',
				'label' => 'Standard Post 2',
				'name' => 'standard_post_2',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5220deed446b6',
				'label' => 'Standard Post	3',
				'name' => 'standard_post_3',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5220deed446b5',
				'label' => 'Standard Post 4',
				'name' => 'standard_post_4',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5220deec446b4',
				'label' => 'Standard Post 5',
				'name' => 'standard_post_5',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5220deeb446b3',
				'label' => 'Standard Post 6',
				'name' => 'standard_post_6',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5220df1a446b8',
				'label' => 'Banner Image',
				'name' => 'banner_image',
				'type' => 'image',
				'save_format' => 'object',
				'preview_size' => 'large',
				'library' => 'all',
			),
			array (
				'key' => 'field_5220df32446b9',
				'label' => 'Banner Link URL',
				'name' => 'banner_link_url',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'newsletter_template',
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

<?php


if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_newsletter-markups',
		'title' => 'Newsletter Markups',
		'fields' => array (
			array (
				'key' => 'field_53209b3083e89',
				'label' => 'HTML Template ',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_531f91fd28717',
				'label' => 'HTML Template',
				'name' => 'html_template',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'formatting' => 'html',
			),
			array (
				'key' => 'field_531f924228718',
				'label' => 'HTML Template Tags',
				'name' => '',
				'type' => 'message',
				'message' => '<h4>HTML Template Tags:</h4>
	{{NEWSLETTER_TITLE}}
	{{NEWSLETTER_PERMALINK_URL}}
	{{BANNER_HTML}}
	{{FEATURED_ARTICLE_1_HTML}}
	{{FEATURED_ARTICLE_2_HTML}}
	{{STANDARD_ARTICLE_1_HTML}}
	{{STANDARD_ARTICLE_2_HTML}}
	{{STANDARD_ARTICLE_3_HTML}}
	{{STANDARD_ARTICLE_4_HTML}}
	{{STANDARD_ARTICLE_5_HTML}}
	{{STANDARD_ARTICLE_6_HTML}}',
			),
			array (
				'key' => 'field_53209b3f83e8a',
				'label' => 'Text Template',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_531f925e28719',
				'label' => 'Text Template',
				'name' => 'text_template',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'formatting' => 'none',
			),
			array (
				'key' => 'field_53209ab931161',
				'label' => 'Text Template Tags',
				'name' => '',
				'type' => 'message',
				'message' => '<h4>Text Template Tags:</h4>
	{{NEWSLETTER_TITLE}}
	{{NEWSLETTER_PERMALINK_URL}}
	{{BANNER}}
	{{FEATURED_ARTICLE_1}}
	{{FEATURED_ARTICLE_2}}
	{{STANDARD_ARTICLE_1}}
	{{STANDARD_ARTICLE_2}}
	{{STANDARD_ARTICLE_3}}
	{{STANDARD_ARTICLE_4}}
	{{STANDARD_ARTICLE_5}}
	{{STANDARD_ARTICLE_6}}',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'newsletter_markup',
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

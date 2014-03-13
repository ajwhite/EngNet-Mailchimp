<?php


class EngNet_MailChimp
{
	var $MC; 
	
	function __construct(){
		add_filter('add_meta_boxes', array($this, 'mailchimp_add_metabox'));
		add_action( 'wp_ajax_getMailchimpLists', array($this, 'ajax_getMailchimpLists'));
		add_action( 'wp_ajax_createMailchimpCampaign', array($this, 'ajax_createCampaign'));
		add_action( 'admin_menu', array($this, 'mailchimp_options_page_menu') );
		$this->MC = new MailChimpAPI();
	}
	
	
	
	function mailchimp_options_page_menu(){
		add_options_page( 'MailChimp' );
		add_submenu_page(
			'edit.php?post_type=newsletter_template',
			'MailChimp Settings',
			'MailChimp Settings',
			'edit_posts',
			'newsletter_template_mailchimp_settings',
			array($this, 'mailchimp_options_page')
		);
	}
	
	function mailchimp_options_page(){
		?>
		<div id="wrap">
			<h2>MailChimp Settings</h2>
			<script type="text/javascript">
			window.location = "/wp-admin/admin.php?page=acf-options";
			</script>
		</div>
		<?php
	}
	
	
	function ajax_createCampaign(){
		$apiKey 		= $_POST['api_key'];
		$action			= $_POST['mailchimpAction'];
		$list	 		= $_POST['list'];
		$newsletter 	= $_POST['newsletter'];
		$from_email		= $_POST['from_email'];
		$from_name		= $_POST['from_name'];
		$to_name		= $_POST['to_name'];
		$subject		= $_POST['subject'];

		
		// Templater & Mailchimp
		$Templater = new EngNet_Newsletter_Templater();
		$this->MC->setApiKey($apiKey);
		
		
		// Title
		$newsletterPost = get_post($newsletter);
		$title = get_field('template', $newsletter);
		$title = $title->post_title . ' - ' . $newsletterPost->post_title;
		
		
		// Templates
		$htmlTemplate = $Templater->getNewsletterHtml($newsletter);
		$textTemplate = $Templater->getNewsletterText($newsletter);
		
		
		// Create campaign
		$campaign = $this->MC->createCampaign($list, $subject, $title, $from_email, $from_name, $to_name, $htmlTemplate, $textTemplate);
		
		
		if (isset($campaign['status']) && $campaign['status'] == 'error'){
			echo json_encode(array('status' => 500, 'result' => $campaign));
			exit(0);
		}
		
		
		if ($action == 'create'){
			// Create only
			add_post_meta($newsletter, 'mailchimp_status', 'created', true) || update_post_meta($newsletter, 'mailchimp_status', 'created');
		} else if ($action == 'schedule'){
			// Schedule Campaign
			add_post_meta($newsletter, 'mailchimp_status', 'sent', true) || update_post_meta($newsletter, 'mailchimp_status', 'sent');
		} else if ($action == 'send'){
			// Send Campaign
			add_post_meta($newsletter, 'mailchimp_status', 'sent', true) || update_post_meta($newsletter, 'mailchimp_status', 'sent');
			$this->MC->sendCampaign($campaign['id']);
		}
		
		// Attach identifiers
		add_post_meta($newsletter, 'mailchimp_campaign', $campaign['id'], true) || update_post_meta($newsletter, 'mailchimp_campaign', $campaign['id']);
		add_post_meta($newsletter, 'mailchimp_list', $campaign['list_id'], true) || update_post_meta($newsletter, 'mailchimp_list', $campaign['list_id']);
add_post_meta($newsletter, 'mailchimp_campaign_web_id', $campaign['web_id'], true) || update_post_meta($newsletter, 'mailchimp_campaign_web_id', $campaign['web_id']);
		
		echo json_encode(array('status' => 200, 'result' => $campaign));
		exit(0);
		
	}
	
	
	function ajax_getMailchimpLists(){
		$apiKey = $_POST['api_key'];
		$this->MC->setApiKey($apiKey);
		$lists = $this->MC->getLists();
		if ($lists){
			echo json_encode($lists);
		} else {
			echo json_encode(array());
		}
		exit(0);
	}
	
	
	function mailchimp_add_metabox(){
		add_meta_box(
			'newsletter_template_mailchimp',
			'MailChimp Access',
			array($this, 'mailchimp_metabox'),
			'newsletter_template',
			'side'
		);
	}
	
	function mailchimp_metabox($post){
		$accounts = get_field('mailchimp_accounts','option');
		$mailchimpStatus = get_post_meta($post->ID, 'mailchimp_status', true);
		if (empty($mailchimpStatus)) $mailchimpStatus = false;
		
		if (!$mailchimpStatus){
			include('view/mailchimp/metabox-edit.php');
		} else {
			include('view/mailchimp/metabox-view.php');
		}
	}
}
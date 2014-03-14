<?php


class EngNet_MailChimp
{
	var $MC; 
	
	function __construct(){
		add_filter('add_meta_boxes', array($this, 'mailchimp_add_metabox'));
		add_action( 'wp_ajax_getMailchimpLists', array($this, 'ajax_getMailchimpLists'));
		add_action( 'wp_ajax_createMailchimpCampaign', array($this, 'ajax_createCampaign'));
		add_action( 'wp_ajax_getMailchimpFolders', array($this, 'ajax_getMailchimpFolders'));
		add_action( 'admin_menu', array($this, 'mailchimp_options_page_menu') );
		$this->MC = new MailChimpAPI();
	}
	
	

	
	function createCampaign($apiKey, $list, $list_name, $account_name, $newsletter, $subject, $from_email, $from_name, $to_name, $folder){
	
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
		$campaign = $this->MC->createCampaign($list, $subject, $title, $from_email, $from_name, $to_name, $htmlTemplate, $textTemplate, $folder);
		
		if ($this->isMailchimpError($campaign)){
			return false;
		}
		
		// Mark Created
		add_post_meta($newsletter, 'mailchimp_status', 'created', true) || update_post_meta($newsletter, 'mailchimp_status', 'created');
		
		// Attach identifiers
		add_post_meta($newsletter, 'mailchimp_campaign', $campaign['id'], true) || update_post_meta($newsletter, 'mailchimp_campaign', $campaign['id']);
		add_post_meta($newsletter, 'mailchimp_campaign_name', $campaign['title'], true) || update_post_meta($newsletter, 'mailchimp_campaign_name', $campaign['title']);
		add_post_meta($newsletter, 'mailchimp_campaign_web_id', $campaign['web_id'], true) || update_post_meta($newsletter, 'mailchimp_campaign_web_id', $campaign['web_id']);


		add_post_meta($newsletter, 'mailchimp_list', $campaign['list_id'], true) || update_post_meta($newsletter, 'mailchimp_list', $campaign['list_id']);
		add_post_meta($newsletter, 'mailchimp_list_name', $list_name, true) || update_post_meta($newsletter, 'mailchimp_list_name', $list_name);
		
		add_post_meta($newsletter, 'mailchimp_account_name', $account_name, true) || update_post_meta($newsletter, 'mailchimp_account_name', $account_name);
		return $campaign;
	}
	
	function sendCampaign($newsletter, $campaignID){
		$send = $this->MC->sendCampaign($campaignID);
		
		if ($this->isMailchimpError($send)){
			return false;
		}
		
		update_post_meta($newsletter, 'mailchimp_status', 'sent');
		return $send;
	}
	
	
	function createCampaignSchedule($newsletter, $campaignID, $time){
		$schedule = $this->MC->createCampaignSchedule($campaignID, $time);
		
		if ($this->isMailchimpError($schedule)){
			return false;
		}
		update_post_meta($newsletter, 'mailchimp_status', 'scheduled');
		update_post_meta($newsletter, 'mailchimp_schedule', $time);
	}
	
	function createCampaignScheduleBatch($newsletter, $campaignID, $time, $batches, $intervals){
		$schedule = $this->MC->createCampaignScheduleBatch($campaignID, $time, $batches, $intervals);
		
		if ($this->isMailchimpError($schedule)){
			return false;
		}
		update_post_meta($newsletter, 'mailchimp_status', 'scheduled');
		update_post_meta($newsletter, 'mailchimp_schedule', $time);
		update_post_meta($newsletter, 'mailchimp_batch_batches', $batches);
		update_post_meta($newsletter, 'mailchimp_batch_intervals', $intervals);
		return $schedule;
	}
	
	
	
	
	function ajax_createCampaign(){
		$apiKey 		= $_POST['api_key'];
		$action			= $_POST['mailchimpAction'];
		$list	 		= $_POST['list'];
		$list_name		= $_POST['list_name'];
		$account_name	= $_POST['account_name'];
		$newsletter 	= $_POST['newsletter'];
		$from_email		= $_POST['from_email'];
		$from_name		= $_POST['from_name'];
		$to_name		= $_POST['to_name'];
		$subject		= $_POST['subject'];
		$folder			= $_POST['folder'];
		
		// Schedules
		$schedule_date	= $_POST['schedule_date'];
		$schedule_hour	= $_POST['schedule_hour'];
		$schedule_min	= $_POST['schedule_min'];
		$schedule_a		= $_POST['schedule_a'];

		// Batch Schedules
		$batch			= $_POST['batch_active'];
		$batch_number	= $_POST['batch_number'];
		$batch_interval	= $_POST['batch_interval'];
		
		
		
		
		
		if ( ($campaign = $this->createCampaign($apiKey, $list, $list_name, $account_name, $newsletter, $subject, $from_email, $from_name, $to_name, $folder)) == false){
			echo json_encode(array('status' => 500, 'result' => $campaign, 'message' => 'Could not create campaign', 'action' => 'none'));
			exit(0);
		}
		
		if ($action == 'schedule'){
			$schedule_min = sprintf("%02s", $schedule_min);
			$scheduleTime = gmdate("Y-m-d H:i:s", strtotime("$schedule_date $schedule_hour:$schedule_min$schedule_a + 4 hours"));
			if (!empty($batch)){
				if($this->createCampaignScheduleBatch($newsletter, $campaign['id'], $scheduleTime, $batch_number, $batch_interval) == false){
					echo json_encode(array('status' => 500, 'message' => 'Campaign created, but could not create batch schedule', 'action' => 'created'));
					exit(0);
				}
			} else {
				if ($this->createCampaignSchedule($newsletter, $campaign['id'], $scheduleTime) == false){
					echo json_encode(array('status' => 500, 'message' => 'Campaign created, but could not create schedule', 'action' => 'created'));
					exit(0);
				}
				
			}
		} else if ($action == 'send'){
			$this->sendCampaign($newsletter, $campaign['id']);
		}
		
		echo json_encode(array('status' => 200, 'result' => $campaign));
		exit(0);
	}
	
	
	
	private function isMailchimpError($response){
		return isset($response['status']) && $response['status'] == 'error';
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
	
	function ajax_getMailchimpFolders(){
		$apiKey = $_POST['api_key'];
		$this->MC->setApiKey($apiKey);
		$folders = $this->MC->getFolders();
		if ($folders){
			echo json_encode($folders);
		} else {
			echo json_encode(array());
		}
		exit(0);
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
			$accountName 	= get_post_meta($post->ID, 'mailchimp_account_name', true);
			$listName		= get_post_meta($post->ID, 'mailchimp_list_name', true);
			$campaignName	= get_post_meta($post->ID, 'mailchimp_campaign_name', true);
			$webid			= get_post_meta($post->ID, 'mailchimp_campaign_web_id', true);
			$scheduleDate	= get_post_meta($post->ID, 'mailchimp_schedule', true);
		
		
			include('view/mailchimp/metabox-view.php');
		}
	}
}
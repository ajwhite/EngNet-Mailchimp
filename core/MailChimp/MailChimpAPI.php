<?php
/**
 * Interface for MailChimp API
 * 
 */
	
	require_once('MailChimpAPIWrapper.php');
	
	
	class MailChimpAPI extends MailChimpAPIWrapper
	{
		function __construct($apiKey = false){
			parent::__construct($apiKey);
		}
		
		public function getLists(){
			$listData = $this->call('lists/list');
			if (!isset($listData['data'])) return false;
			return $listData['data'];
		}
		
		public function createCampaign($list, $subject, $title, $from_email, $from_name, $to_name, $htmlTemplate, $textTemplate, $folder=false){
			$campaign = $this->call('campaigns/create', array(
				'type' => 'regular',
				'options' => array(
					'list_id' 		=> $list,
					'subject' 		=> $subject,
					'from_email'	=> $from_email,
					'from_name'		=> $from_name,
					'to_name'		=> $to_name,
					'title' 		=> $title,
					'folder_id'		=> $folder
				),
				'content' => array(
					'html' => $htmlTemplate,
					'text' => $textTemplate
				)
			));
			return $campaign;
		}
		
		public function sendCampaign($campaignID){
			$this->call('campaigns/send', array('cid' => $campaignID));
		}
		
		public function testCampaign($campaignID, $receiver){
			if (!is_array($receiver)){
				$receiver = array($receiver);
			}
			$this->call('campaigns/send-test', array(
				'cid' => $campaignID,
				'test_emails' => $receiver
			));
		}
		
		public function createCampaignSchedule($campaignID, $time){
			$campaign = $this->call('campaigns/schedule', array(
				'cid' => $campaignID,
				'schedule_time' => $time
			));
			return $campaign;
		}
		
		public function createCampaignScheduleBatch($campaignID, $time, $batches, $intervals){
			$campaign = $this->call('campaigns/schedule-batch', array(
				'cid' => $campaignID,
				'schedule_time' => $time,
				'num_batches' => $batches,
				'stagger_mins' => $intervals
			));
			return $campaign;
		}
		
		public function getFolders(){
			$folders = $this->call('folders/list', array('type' => 'campaign'));
			return $folders;
		}
	}
	
?>
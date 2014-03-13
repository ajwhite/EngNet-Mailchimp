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
		
		public function createCampaign($list, $subject, $title, $htmlTemplate, $textTemplate){
			$campaign = $this->call('campaigns/create', array(
				'type'	=> 'regular',
				'options' => array(
					'list'	=> $list,
					'subject' => $subject,
					'title' => $title
				),
				'content' => array(
					'html' => $htmlTemplate,
					'text' => $textTemplate
				)
			));
			return $campaign['data']['cid'];
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
		
		public function createAndSendCampaign(){
			$cid = $this->createCampaign();
			$this->sendCampaign($cid);
		}
	}
	
?>
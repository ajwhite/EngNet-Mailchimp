<div class="main_meta_content">
	<ul id="mailchimp-campaign-labels">
		<li>
			<strong>MailChimp Account</strong><br/>
			<?php echo $accountName; ?>
		</li>
		<li>
			<strong>MailChimp List</strong><br/>
			<?php echo $listName; ?>
		</li>
		<li>
			<strong>Mailchimp Campaign</strong><br/>
			<?php echo $campaignName; ?>
		</li>
		<?php if(isset($scheduleDate)): ?>
		<li>
			<strong>Mailchimp Scheduled</strong><br/>
			<?php echo date("F d, Y h:ia", strtotime($scheduleDate . " - 4 hours")); ?>
		</li>
		<?php endif; ?>
	</ul>
	
	
</div>
<style type="text/css">

</style>


<div class="bottom_meta_content">
	<div id="delete-action">
		<?php if (!$mailchimpStatus): ?>
			<span class="mailchimp-status not-sent">Not Sent</span>
		<?php else: ?>
			<span class="mailchimp-status <?php echo $mailchimpStatus; ?>"><?php echo ucfirst($mailchimpStatus); ?></span>
		<?php endif; ?>
	</div>
	<div id="publishing-action">	
		<a href="https://admin.mailchimp.com/campaigns/show?id=<?php echo $webid; ?>" target="_blank">View Campaign</a> |
		<a href="https://admin.mailchimp.com/reports/summary?id=<?php echo $webid; ?>" target="_blank">View Report</a>
	</div>
	<div class="clear"></div>
</div>


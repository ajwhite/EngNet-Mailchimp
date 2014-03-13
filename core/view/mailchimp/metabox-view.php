<div class="main_meta_content">
	<div>
		<strong>MailChimp Account</strong><br/>
		-- Mailchimp Account --
	</div>
	
	<div>
		<strong>MailChimp List</strong><br/>
		-- Maichimp List --
	</div>
	
	<div>
		<strong>Mailchimp Campaign</strong><br/>
		--- Mailchimp Campaign ----
	</div>
	
	-- IF SCHEDULED --
	<div>
		<strong>Mailchimp Scheduled</strong><br/>
		MM/dd/YYYY H:i a
	</div>
	
	
</div>
<style type="text/css">
#newsletter_template_mailchimp .main_meta_content > div { margin: 10px 0; }
</style>


<div class="bottom_meta_content">
	<div id="delete-action">
		<?php if (!$mailchimpStatus): ?>
			<span class="mailchimp-status not-sent">Not Sent</span>
		<?php elseif ($mailchimpStatus == 'created'): ?>
			<span class="mailchimp-status created">Created</span>
		<?php elseif ($mailchimpStatus == 'sent'): ?>
			<span class="mailchimp-status sent">Sent</span>
		<?php endif; ?>
	</div>
	<div id="publishing-action">
		<a href="#">View Campaign</a> |
		<a href="#">View Report</a>
	</div>
	<div class="clear"></div>
</div>


<?php if ($post->post_status != 'publish'): ?>
<div class="main_meta_content">
	Please save your newsletter to access MailChimp.
</div>
<?php else: ?>

<div class="main_meta_content">
	<ul id="mailchimp-accounts">
		<li><strong>Select MailChimp Account</strong></li>
		<li>
			<select id="mailchimp-account">
				<option value="">-- Accounts --</option>
				<?php foreach($accounts as $account): ?>
				<option value="<?php echo $account['api_key']; ?>"><?php echo $account['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</li>
	</ul>
	
	<ul id="mailchimp-lists"></ul>
	
	<ul id="mailchimp-defaults">
		<li>
			<label>Default From Email</label><br/>
			<input type="text" class="text" id="mailchimp-default-from-email" />
		</li>
		<li>
			<label>Default From Name</label><br/>
			<input type="text" class="text" id="mailchimp-default-from-name" />
		</li>
		<li>
			<label>Default To Name</label><br/>
			<input type="text" class="text" id="mailchimp-default-to-name" />
		</li>
		<li>
			<label>Subject</label><br/>
			<input type="text" class="text" id="mailchimp-default-subject" />
		</li>
	</ul>
	
	<ul id="mailchimp-actions">
		<li><strong>Actions</strong></li>
		<li>
			<label><input type="radio" name="mailchimp-action" value="schedule" />Create &amp; Schedule Newsletter</label>
			
			<ul id="mailchimp-schedule-section">
				<li><input type="text" id="mailchimp-schedule-date" class="text" placeholder="Date" /></li>
				<li>
					<select id="mailchimp-schedule-hour">
						<?php for ($i=1; $i<=12; $i++): ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
					:
					<select id="mailchimp-schedule-minute">
						<?php for ($i=0; $i<=45; $i+=15): ?>
						<option value="<?php echo $i; ?>"><?php echo sprintf("%02s", $i); ?></option>
						<?php endfor; ?>
					</select>
					<select id="mailchimp-schedule-a">
						<option value="AM">AM</option>
						<option value="PM">PM</option>
					</select>
				</li>
			</ul>
		</li>
		<li>
			<label><input type="radio" name="mailchimp-action" value="send" />Create &amp; Send</label>
		</li>
		<li>
			<label><input type="radio" name="mailchimp-action" value="create"/>Create</label>
		</li>
	</ul>
	
</div>


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
		<span class="spinner mailchimp-spinner"></span>
		<input type="button" class="button button-primary button-large" id="create-mailchimp-campaign" value="Create" />
	</div>
	<div class="clear"></div>
</div>


<script type="text/javascript">
(function($){

	var MailChimpAccess = {
		config: {
			api_key : false,
			postID: '<?php echo $post->ID; ?>',
			post_name: '<?php echo $post->post_title; ?>',
			status: '<?php echo $mailchimpStatus; ?>'
		},
		
		initialize: function(){
			this.events();	
		},
		
		
		getMailChimpLists: function(cb){
			var self = this;
			$.post('/wp-admin/admin-ajax.php',{
				action			: 'getMailchimpLists',
				api_key			: self.config.api_key
			}, cb);
		},
		createCampaign: function(listID, action, subject, from_email, from_name, to_name, cb){
			var self = this;
			$.post('/wp-admin/admin-ajax.php',{
				action			: 'createMailchimpCampaign',
				api_key			: self.config.api_key,
				newsletter		: self.config.postID,
				mailchimpAction : action,
				list			: listID,
				subject			: subject,
				from_email		: from_email,
				from_name		: from_name,
				to_name			: to_name
			}, cb);
		},
		
		getCampaignFields: function(){
			return {
				mailchimpAction : $("input[name=mailchimp-action]:checked").val(),
				list			: $("input[name=mailchimp-list]:checked").val(),
				subject			: $("#mailchimp-default-subject").val(),
				from_email		: $("#mailchimp-default-from-email").val(),
				from_name		: $("#mailchimp-default-from-name").val(),
				to_name			: $("#mailchimp-default-to-name").val(),
				schedule_date	: $("#mailchimp-schedule-date").val(),
				schedule_time_h	: $("#mailchimp-schedule-hour").val(),
				schedule_time_m : $("#mailchimp-schedule-minute").val(),
				schedule_time_a : $("#mailchimp-schedule-a").val()
			};
		}
		
		events: function(){
			var self = this;
			

			$('#mailchimp-schedule-date').datepicker({
		        dateFormat : 'mm-dd-yy',
		        minDate: new Date()
		    });

			$("#mailchimp-account").change(function(){
				$("#mailchimp-lists").empty();
				$("#mailchimp-defaults").hide();
				if ($(this).val().length == 0) {
					self.config.api_key = false;
					return;
				}
				
				
				self.config.api_key = $(this).val();
				
				self.getMailChimpLists(function(resp){
					if (resp.length > 0){
						var lists = $.parseJSON(resp);
						for (var i=0; i<lists.length; i++){
							var list = lists[i];
							var checkbox = $("<input type='radio' name='mailchimp-list' />")
								.val(list.id)
								.data('default_subject', list.default_subject);
							var label = $("<label />").text(list.name + " (" + list.stats.member_count + " members)").prepend(checkbox);
							$("<li/>").append(label).appendTo("#mailchimp-lists");
							
							(function(cb, l){
								cb.click(function(){
									$("#mailchimp-defaults").show();
									$("#mailchimp-default-from-email").val(l.default_from_email);
									$("#mailchimp-default-from-name").val(l.default_from_name);
									$("#mailchimp-default-to-name").val();
									$("#mailchimp-default-subject").val(l.default_subject + " - " + self.config.post_name);
								});	
							})(checkbox, list);
							
						}
						$("#mailchimp-lists").prepend("<li><strong>Lists</srong></li>");
					}
				});
			});
			
			
			$("input[name=mailchimp-action]").click(function(){
				var action = $(this).val();
				
				$("#mailchimp-schedule-section").hide();
				
				if (action == 'schedule'){
					$("#mailchimp-schedule-section").show();
					
				} else if (action == 'create'){
					
				} else if (action == 'send'){
					
				}
			});
			$("#create-mailchimp-campaign").click(function(){
				var list = $("input[name=mailchimp-list]:checked").val();
				var subject = $("#mailchimp-default-subject").val();
				var from_email = $("#mailchimp-default-from-email").val();
				var from_name = $("#mailchimp-default-from-name").val();
				var to_name = $("#mailchimp-default-to-name").val();
				var action = $("input[name=mailchimp-action]:checked").val();
				
				
				var $button = $(this);
				$(".mailchimp-spinner").show();
				$button.attr('disabled','disabled');
				self.createCampaign(list, action, subject, from_email, from_name, to_name, function(resp){
					console.log(resp);
					$(".mailchimp-spinner").hide();
					$button.removeAttr('disabled');
				});
			});
			
		}
		
	};
	
	$(document).ready(function(){
		MailChimpAccess.initialize();
	});


})(jQuery);
</script>

<?php endif; ?>
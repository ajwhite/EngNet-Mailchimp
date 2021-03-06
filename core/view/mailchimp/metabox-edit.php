<?php if ($post->post_status != 'publish'): ?>
<div class="main_meta_content">
	Please save your newsletter to access MailChimp.
</div>
<?php else: ?>
<div class="main_meta_content" id="mailchimp-success"></div>
<div class="main_meta_content" id="mailchimp-forms">
	<ul id="mailchimp-accounts">
		<li><strong>Select MailChimp Account</strong></li>
		<li>
			<select id="mailchimp-account" name="mailchimp-account">
				<option value="">-- Accounts --</option>
				<?php foreach($accounts as $account): ?>
				<option value="<?php echo $account['api_key']; ?>"><?php echo $account['name']; ?></option>
				<?php endforeach; ?>
			</select>
			<span class="spinner mailchimp-lists-spinner"></span>
			<div class="clear"></div>
		</li>
	</ul>
	<ul id="mailchimp-lists"></ul>
	
	<ul id="mailchimp-folders">
		<li><strong>Folder</strong> (optional)</li>
		<li>
			<select id="mailchimp-folder" name="mailchimp-folder">
				<option value="">No Folder</option>
			</select>
		</li>
	</ul>
	
	<ul id="mailchimp-defaults">
		<li>
			<label>Default From Email</label><br/>
			<input type="text" class="text" name="mailchimp-default-from-email" id="mailchimp-default-from-email" />
		</li>
		<li>
			<label>Default From Name</label><br/>
			<input type="text" class="text" name="mailchimp-default-from-name" id="mailchimp-default-from-name" />
		</li>
		<li style="display:none;">
			<label>Default To Name</label><br/>
			<input type="text" class="text" name="mailchimp-default-to-name" id="mailchimp-default-to-name" />
		</li>
		<li>
			<label>Subject</label><br/>
			<input type="text" class="text" name="mailchimp-default-subject" id="mailchimp-default-subject" />
		</li>
	</ul>
	
	<ul id="mailchimp-actions">
		<li><strong>Actions</strong></li>
		<li>
			<label><input type="radio" name="mailchimp-action" value="schedule" />Create &amp; Schedule Newsletter</label>
			
			<ul id="mailchimp-schedule-section">
				<li><input type="text" id="mailchimp-schedule-date" name="mailchimp-schdule-date" class="text" placeholder="Date" /></li>
				<li>
					<select id="mailchimp-schedule-hour" name="mailchimp-schedule-hour">
						<?php for ($i=1; $i<=12; $i++): ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
					:
					<select id="mailchimp-schedule-minute" name="mailchimp-schedule-minute">
						<?php for ($i=0; $i<=45; $i+=15): ?>
						<option value="<?php echo $i; ?>"><?php echo sprintf("%02s", $i); ?></option>
						<?php endfor; ?>
					</select>
					<select id="mailchimp-schedule-a" name="mailchimp-schedule-a">
						<option value="AM">AM</option>
						<option value="PM">PM</option>
					</select>
				</li>
				<li class="batches">
					<input type="checkbox" id="mailchimp-batch-enabled" name="mailchimp-batch-enabled" />
					<select id="mailchimp-batch-number" name="mailchimp-batch-number">
						<?php for($i=2; $i<=22; $i++): ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
					batches every
					<select id="mailchimp-batch-interval" name="mailchimp-batch-interval">
						<option value="5">5 minutes</option>
						<option value="10">10 minutes</option>
						<option value="15">15 minutes</option>
						<option value="20">20 minutes</option>
						<option value="25">25 minutes</option>
						<option value="30">30 minutes</option>
						<option value="60">1 hour</option>
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
		
		getMailchimpFolders: function(cb){
			var self = this;
			$.post('/wp-admin/admin-ajax.php',{
				action			: 'getMailchimpFolders',
				api_key			: self.config.api_key
			}, cb);
		},
		
		createCampaign: function(cb){
			var options = this.getCampaignFields();
			options.api_key = this.config.api_key;
			options.newsletter = this.config.postID;
			options.action = 'createMailchimpCampaign';

			$.post('/wp-admin/admin-ajax.php', options, cb);
		},
		
		getCampaignFields: function(){
			return {
				mailchimpAction : $("input[name=mailchimp-action]:checked").val(),
				account_name	: $("#mailchimp-account option:selected").text(),
				list			: $("input[name=mailchimp-list]:checked").val(),
				list_name		: $("input[name=mailchimp-list]:checked").data('name'),
				subject			: $("#mailchimp-default-subject").val(),
				from_email		: $("#mailchimp-default-from-email").val(),
				from_name		: $("#mailchimp-default-from-name").val(),
				to_name			: $("#mailchimp-default-to-name").val(),
				
				// scheduled campaigns
				schedule_date	: $("#mailchimp-schedule-date").val(),
				schedule_hour	: $("#mailchimp-schedule-hour").val(),
				schedule_min 	: $("#mailchimp-schedule-minute").val(),
				schedule_a 		: $("#mailchimp-schedule-a").val(),
				
				// scheduled batch campaigns
				batch_active	: $("#mailchimp-batch-enabled").is(":checked"),
				batch_number	: $("#mailchimp-batch-number").val(),
				batch_interval	: $("#mailchimp-batch-interval").val(),
				
				// folder
				folder			: $("#mailchimp-folder").val()
			};
		},
		
		validate: function(){
			var fields = this.getCampaignFields();
			// Account check
			if ($("#mailchimp-account").val().length == 0){
				alert("Please select a Mailchimp account");
				return false;
			}
			
			// List check
			if ($("input[name=mailchimp-list]:checked").length == 0){
				alert("Please select a Mailchimp list");
				return false;
			}
			
			if (fields.from_email.length == 0 || fields.from_name.length == 0 || fields.subject.length == 0){
				alert("Please fill in the default fields");
				return false;
			}
			
			if ($("input[name=mailchimp-action]:checked").length == 0){
				alert("Please select an action");
				return false;
			}
			
			if (fields.mailchimpAction == 'schedule'){
				if (fields.schedule_date.length == 0){
					alert("Please select a schedule date");
					return false;
				}
			}
			
			return true;
			
		},
		
		events: function(){
			var self = this;

			$('#mailchimp-schedule-date').datepicker({
		        dateFormat : 'yy-mm-dd',
		        minDate: new Date()
		    });

			$("#mailchimp-account").change(function(){
				$("#mailchimp-lists").empty();
				$("#mailchimp-defaults").hide();
				$("#mailchimp-folders").hide();
				$("#mailchimp-folder option[value!='']").remove();
				if ($(this).val().length == 0) {
					self.config.api_key = false;
					return;
				}
				$(".mailchimp-lists-spinner").show();
				
				self.config.api_key = $(this).val();
				
				self.getMailChimpLists(function(resp){
					$(".mailchimp-lists-spinner").hide();
					var lists = $.parseJSON(resp);
					if (lists.length > 0){
						for (var i=0; i<lists.length; i++){
							var list = lists[i];
							var checkbox = $("<input type='radio' name='mailchimp-list' />")
								.val(list.id)
								.data('name', list.name);
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
					} else {
						alert("Unable to find any available lists, please check your API Keys or add a new list");
					}
				});
				
				self.getMailchimpFolders(function(resp){
					if (resp.length > 0){
						var folders = $.parseJSON(resp);
						if (folders.length > 0){
							$("#mailchimp-folders").show()
							for (var i=0; i<folders.length; i++){
								$("<option/>").val(folders[i].folder_id).html(folders[i].name).appendTo("#mailchimp-folder");
							}
						}
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
				if (!self.validate()) return false;
				
				var $button = $(this);
				$(".mailchimp-spinner").show();
				$button.attr('disabled','disabled');
				self.createCampaign(function(resp){
					var result = $.parseJSON(resp);
					$(".mailchimp-spinner").hide();
					if (result.status == 500){
						if (result.action == 'created'){
							self.campaignSuccess('create');
						} else {
							$button.removeAttr('disabled');							
						}
						alert(result.message);
					} else {
						$button.text('Created');
						self.campaignSuccess();
					}
				});
			});
			
		},
		
		campaignSuccess: function(action){
			var action = action ? action : $("input[name=mailchimp-action]:checked").val()
			$("#mailchimp-forms").slideUp();
			$("#mailchimp-success").show();
			$(".mailchimp-status").removeClass('not-sent');
			
			if (action == 'create'){
				$("#mailchimp-success").html("<p>Your campaign has been successfully created</p>");
				$("#create-mailchimp-campaign").val("Created");
				$(".mailchimp-status").text("Created").addClass('created');
			} else if (action == 'send'){
				$("#mailchimp-success").html("<p>Your campaign has been successfully sent</p>");
				$("#create-mailchimp-campaign").val("Sent");
				$(".mailchimp-status").text("Sent").addClass('sent');
			} else if (action == 'schedule'){
				$("#mailchimp-success").html("<p>Your campaign has been successfully scheduled</p>");
				$("#create-mailchimp-campaign").val("Scheduled");
				$(".mailchimp-status").text("Scheduled").addClass("scheduled");
			}
		}
		
	};
	
	$(document).ready(function(){
		MailChimpAccess.initialize();
	});


})(jQuery);
</script>

<?php endif; ?>
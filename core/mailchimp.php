<?php


class EngNet_MailChimp
{
	var $MC; 
	
	function __construct(){
		add_filter('add_meta_boxes', array($this, 'mailchimp_add_metabox'));
		add_action( 'wp_ajax_getMailchimpLists', array($this, 'ajax_getMailchimpLists'));
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
	
	function mailchimp_metabox(){
		$accounts = get_field('mailchimp_accounts','option');
		?>
		<div class="main_meta_content">
			<select id="mailchimp-account">
				<option value="">Select MailChimp Account</option>
				<?php foreach($accounts as $account): ?>
				<option value="<?php echo $account['api_key']; ?>"><?php echo $account['name']; ?></option>
				<?php endforeach; ?>
			</select>
			<ul id="mailchimp-lists"></ul>
		</div>
		<div class="bottom_meta_content">
			<?php if ($post->post_status == 'publish'): ?>
			<div id="delete-action">
				<span class="mailchimp-status not-sent">Not Sent</span>
			</div>
			
			<div id="publishing-action">
				<span class="spinner"></span>
				<input type="button" class="button button-primary button-large" value="Create" />
				<input name="save" type="button" class="button button-primary button-large" value="Create & Send">
			</div>
			<div class="clear"></div>
			<?php else: ?>
			Please publish your newsletter
			<?php endif; ?>
		</div>
		
		
		
		
		
		
		<script type="text/javascript">
		(function($){
			$("#mailchimp-account").change(function(){
				if ($(this).val().length == 0) return;
				
				$.post('/wp-admin/admin-ajax.php', {
					action: 'getMailchimpLists',
					api_key: $(this).val()
				}, function(resp){
					$("#mailchimp-lists").empty();
					if (resp.length > 0){
						var lists = $.parseJSON(resp);
						for (var i=0; i<lists.length; i++){
							var list = lists[i];
							var checkbox = $("<input type='radio' name='mailchimp-list' />")
								.val(list.id)
								.data('default_subject', list.default_subject);
							var label = $("<label />").text(list.name).prepend(checkbox);
							$("<li/>").append(label).appendTo("#mailchimp-lists");
						}
					}
				});
			});
		})(jQuery);
		</script>
		<?php
	}
}
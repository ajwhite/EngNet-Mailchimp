<?php

/**
 * Plugin Name: EngNet Mailchimp Integration
 * Plugin URI: http://engnetglobal.com
 * Description: Mailchimp newsletter campaign integration
 * Version: 0.0.1
 * Author: Atticus White
 * Author URI: http://www.atticuswhite.com
 *
 */


if (!class_exists("EngNet_MailChimpPlugin")){
	class EngNet_MailChimpPlugin{
	
		var $MC;
		var $newsletter;
		
		var $dependencies = array(
			array(
				'class' => 'acf',
				'message' => '<a href="http://www.advancedcustomfields.com/">Advanced Custom Fields</a> must be installed for this plugin to work'
			),
			array(
				'class' => 'acf_repeater_plugin',
				'message' => 'ACF Repeater Plugin must be installed for this plugin to work'
			),
			array(
				'class' => 'acf_options_page_plugin',
				'message' => 'ACF Options Page plugin must be installed for this plugin to work'
			)
		);
	
	
	
		function __construct(){
			if ($this->checkDependencies()){
				$this->include_before_theme();
				
				if (is_admin()){
					$this->MC = new EngNet_MailChimp();
				}
				$this->newsletter = new EngNet_Newsletter();
			} else {
				add_action( 'admin_notices', array($this, 'dependencyNotice'));
			}
		}
		
		
		function include_before_theme(){
			if (is_admin()){
				include_once('core/MailChimp/MailChimpAPI.php');
				include_once('core/mailchimp.php');
			}
			include_once('core/newsletter.php');
			include_once('core/newsletter_templater.php');
		}
		
		
		
		function checkDependencies(){
			foreach($this->dependencies as $key=>$dependency){
				if (!class_exists($dependency['class'])){
					return false;
				}
			}
			return true;
		}
		
		function dependencyNotice(){
			foreach($this->dependencies as $dependency):
				if (!class_exists($dependency['class'])):
				?>
				<div class="error">
					<p><strong>EngNet Mailchimp</strong> - <?php echo $dependency['message']; ?></p>
				</div>
				<?php
				endif;
			endforeach;
		}
	}
	
	
}


if (class_exists("EngNet_MailChimpPlugin")){
	$engnet_MailChimp = new EngNet_MailChimpPlugin();
}
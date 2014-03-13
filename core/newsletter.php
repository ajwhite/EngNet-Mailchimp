<?php


class EngNet_Newsletter
{

	var $templater;

	function __construct(){
		$this->templater = new EngNet_Newsletter_Templater();
		add_action( 'init', array($this, 'register_custom_post_types') );
		add_action( 'template_redirect', array($this, 'template_redirect') );
		

		add_filter('manage_newsletter_template_posts_columns', array($this, 'newsletter_template_columns'));
		add_filter('manage_newsletter_template_posts_custom_column', array($this, 'newsletter_template_custom_column'), 10, 2);
		add_action('admin_print_footer_scripts', array($this, 'newsletter_template_print_footer_scripts'));

		add_filter('parse_query', array($this, 'newsletter_template_filter_posts'));
		add_filter('post_row_actions', array($this, 'newsletter_template_action_links'));
		add_action('restrict_manage_posts', array($this, 'newsletter_template_restrict_manage_posts'));

		
		
		add_filter('init', array($this, 'newsletter_text_rewrite'));
		
	}
	
	
	
	
	
	
	
	function template_redirect(){
		global $wp, $post, $wp_query;
		
		if ($wp->query_vars['post_type'] == 'newsletter_template' || $wp->query_vars['post_type'] == 'newsletter_markup'){
			if (have_posts()){
				$this->generateTemplate($wp->query_vars['post_type'] == 'newsletter_markup');
			} else {
				$wp_query->is_404 = true;
			}
		}
	}
	
	
	private function generateTemplate($preview = false){
		global $wp;
		if (isset($wp->query_vars['text_only'])){
			header('HTTP/1.1 200 OK');
			header('Content-Type: text/plain; charset=utf-8');
			echo $this->templater->getNewsletterText(get_the_ID());
		} else {
			if ($preview){
				echo $this->templater->getNewsletterHtmlPreview(get_the_ID());
			} else {
				echo $this->templater->getNewsletterHtml(get_the_ID());				
			}

		}
		exit(0);
	}
	
	
	private function do_theme_redirect(){
		global $post, $wp_query;
		if (have_posts()){
			echo "<h1>PLUGIN GENERATED</h1>";
			if (!isset($wp->query_vars['text_only'])){
				echo $this->templater->getNewsletterHtml(get_the_ID());
			} else {
			}
			exit(0);
		} else {
			$wp_query->is_404 = true;
		}
	}	
	
	
	
	function register_custom_post_types(){
		$labels = $this->populate_post_type_or_taxonomy_labels(array(
			'name' 			=> 'Newsletters',
			'singular_name' => 'Newsletter'
		));
		$args = array(
			'labels'				=> $labels,
			'public'				=> true,
			'exclude_from_search'	=> true,
			'show_in_nav_menus'		=> true,
			'supports'				=> array('title', 'revisions'),
			'rewrite'				=> array('slug' => 'newsletter')
		);
		register_post_type('newsletter_template', $args);
		
		$labels = $this->populate_post_type_or_taxonomy_labels(array(
			'name'			=> 'Newsletter Templates',
			'singular_name'	=> 'Newsletter Template'
		));
		$args = array(
			'labels'				=> $labels,
			'public'				=> true,
			'exclude_from_search'	=> true,
			'show_in_nav_menus'		=> true,
			'supports'				=> array('title', 'revisions'),
			'rewrite'				=> array('slug' => 'newsletter_markup')
		);
		register_post_type('newsletter_markup', $args);
	}
	
	


	function newsletter_template_columns($columns){
		return array(
			'cb' => $columns['cb'],
			'title' => $columns['title'],
			'template' => "Template",
			'mailchimp' => "MailChimp Status",
			'date' => $columns['date']
		);
		$columns['Mailing List'] = "Mailing List";
		return $columns;	
	}

	
	function newsletter_template_custom_column($column, $postID=false){
		if ($column == 'template'){
			$template = get_field('template', $postID);
			echo $template->post_title;
		} else if ($column == 'mailchimp'){
			$status = get_post_meta($postID, 'mailchimp_status', true);
			$webid = get_post_meta($postID, 'mailchimp_campaign_web_id', true);
			
			if (!empty($status)): 
			?>
			<strong><span class="mailchimp-status <?php echo $status; ?>"><?php echo ucfirst($status); ?></span></strong><br/>
			
			<a href="https://admin.mailchimp.com/campaigns/show?id=<?php echo $webid; ?>" target="_blank">View Campaign</a> |
			<a href="https://admin.mailchimp.com/reports/summary?id=<?php echo $webid; ?>" target="_blank">View Report</a>

			<?php 
			endif; 
		}
	}
	
	function newsletter_template_restrict_manage_posts(){
		if ($_GET['post_type'] != 'newsletter_template') return;
		$markupQuery = new WP_Query(array(
			'numberposts' => -1,
			'post_type' => 'newsletter_markup'
		));
		
		?>
		<select name="template">
			<option value="">All Templates</option>
		<?php
		while($markupQuery->have_posts()){
			$markupQuery->the_post();
		?>
			<option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
		<?php
		}
		?>
		</select>
		<?php
		
		wp_reset_postdata();
	}
	
	function newsletter_template_filter_posts($query){
		global $pagenow;
		if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'newsletter_template' && isset($_GET['template']) && !empty($_GET['template'])){
			$query->query_vars['meta_key'] = 'template';
			$query->query_vars['meta_value'] = $_GET['template'];
		}
	}
	
	
	function newsletter_template_action_links($actions, $post) {
		if (get_post_type() == 'newsletter_template'){
	
			unset($actions['inline hide-if-no-js']);
			unset($actions['view']);
			$actions['html_preview'] = "<a href='" . get_permalink($post->ID) . "' target='_blank'>HTML Preview</a>";
			$actions['text_preview'] = "<a href='" . get_permalink($post->ID) . "/text_only.txt' target='_blank'>Plaintext View</a>";
			$actions['source'] = "<a href='view-source:" . get_permalink($post->ID) . "' target='_blank'>View Source</a>";
		} else if (get_post_type() == 'newsletter_markup'){
			unset($actions['inline hide-if-no-js']);
			unset($actions['view']);
			$actions['html_preview'] = "<a href='" . get_permalink($post->ID) . "' target='_blank'>Preview</a>";
		}
		return $actions;
	}
	
	function newsletter_template_print_footer_scripts(){
	?>
		<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$(".row-actions .html_preview, .row-actions .text_preview").click(function(){
					var href = $(this).find('a').attr('href');
					window.open(href,'1394589493708','width=700,height=600,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
					return false;
				});
			});
		})(jQuery);
		
		</script>
	<?php
	}
	
	function newsletter_text_rewrite(){
		add_rewrite_tag('%text_only%','([^&]+)');
		add_rewrite_rule('^newsletter/([^/]*)/text_only?', 'index.php?post_type=newsletter_template&name=$matches[1]&text_only=true','top');
	
		//flush_rewrite_rules();
	}
	














	private function populate_post_type_or_taxonomy_labels($labels) {
	    if (!is_array($labels) && !is_string($labels)) {
	        return null;
	    }
	    if (is_string($labels)) {
	        $labels = array($labels);
	    }
	    
	    $name = '';
	    $name_lower = '';
	    $singular_name = '';
	    $singular_name_lower = '';
	    
	    if (isset($labels['name'])) {
	        $name = $labels['name'];
	        $name_lower = strtolower($name);
	        
	        if (!isset($labels['search_items'])) {
	            $labels['search_items'] = "Search $name";
	        }
	        if (!isset($labels['popular_items'])) {
	            $labels['popular_items'] = "Popular $name";
	        }
	        if (!isset($labels['all_items'])) {
	            $labels['all_items'] = "All $name";
	        }
	        if (!isset($labels['not_found'])) {
	            $labels['not_found'] = "No $name_lower found";
	        }
	        if (!isset($labels['not_found_in_trash'])) {
	            $labels['not_found_in_trash'] = "No $name_lower found in trash";
	        }
	        if (!isset($labels['menu_name'])) {
	            $labels['menu_name'] = $name;
	        }
	        if (!isset($labels['separate_items_with_commas'])) {
	            $labels['separate_items_with_commas'] = "Separate $name_lower with commas";
	        }
	        if (!isset($labels['add_or_remove_items'])) {
	            $labels['add_or_remove_items'] = "Add or remove $name_lower";
	        }
	        if (!isset($labels['choose_from_most_used'])) {
	            $labels['choose_from_most_used'] = "Choose from most used $name_lower";
	        }
	    }
	    
	    if (isset($labels['singular_name'])) {
	        $singular_name = $labels['singular_name'];
	        $singular_name_lower = strtolower($singular_name);
	        
	        if (!isset($labels['add_new'])) {
	            $labels['add_new'] = "Add New";
	        }
	        if (!isset($labels['add_new_item'])) {
	            $labels['add_new_item'] = "Add New $singular_name";
	        }
	        if (!isset($labels['edit_item'])) {
	            $labels['edit_item'] = "Edit $singular_name";
	        }
	        if (!isset($labels['update_item'])) {
	            $labels['update_item'] = "Update $singular_name";
	        }
	        if (!isset($labels['new_item'])) {
	            $labels['new_item'] = "New $singular_name";
	        }
	        if (!isset($labels['new_item_name'])) {
	            $labels['new_item_name'] = $labels['new_item'];
	        }
	        if (!isset($labels['view_item'])) {
	            $labels['view_item'] = "View $singular_name";
	        }
	        if (!isset($labels['parent_item'])) {
	            $labels['parent_item'] = "Parent $singular_name";
	        }
	        if (!isset($labels['parent_item_colon'])) {
	            $labels['parent_item_colon'] = $labels['parent_item'] . ':';
	        }
	    }
	    
	    return $labels;
	}

}
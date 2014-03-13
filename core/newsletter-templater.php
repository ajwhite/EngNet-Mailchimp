<?php


class EngNet_Newsletter_Templater
{


	
	
		
	function _getNewsletterHTML($templateID, $tagReplacements){
		$templateHTML = get_field('html_template', $templateID);
		$tags = array(
			'{{NEWSLETTER_TITLE}}',
			'{{NEWSLETTER_PERMALINK_URL}}',
			'{{FEATURED_ARTICLE_1_HTML}}',
			'{{FEATURED_ARTICLE_2_HTML}}',
			'{{STANDARD_ARTICLE_1_HTML}}',
			'{{STANDARD_ARTICLE_2_HTML}}',
			'{{STANDARD_ARTICLE_3_HTML}}',
			'{{STANDARD_ARTICLE_4_HTML}}',
			'{{STANDARD_ARTICLE_5_HTML}}',
			'{{STANDARD_ARTICLE_6_HTML}}',
			'{{BANNER_HTML}}',
		);
		return str_replace($tags, $tagReplacements, $templateHTML);
	}
	function _getNewsletterText($templateID, $tagReplacements){
		$templateText = get_field('text_template', $templateID);
		$tags = array(
			'{{NEWSLETTER_PERMALINK_URL}}',
			'{{FEATURED_ARTICLE_1}}',
			'{{FEATURED_ARTICLE_2}}',
			'{{STANDARD_ARTICLE_1}}',
			'{{STANDARD_ARTICLE_2}}',
			'{{STANDARD_ARTICLE_3}}',
			'{{STANDARD_ARTICLE_4}}',
			'{{STANDARD_ARTICLE_5}}',
			'{{STANDARD_ARTICLE_6}}',
			'{{BANNER}}',
			'[&hellip;]',
			'&#8220;',
			'&#8221;',
			'&#8217;',
			'&#8218;',
			'&#8216;',
			'&nbsp;'	
		);
		$output = str_replace($tags, $tagReplacements, $templateText);
		$output = str_replace(" »", " » ", $output);
		$output = trim($output);
		return $output;
	}

	
	
	
	function getNewsletterHtmlPreview($templateID){
		$query = new WP_Query(array(
			'post_type' => 'post',
			'numberposts' => 8,
			'orderby' => 'rand'
		));
			
		while($query->have_posts()){
			$query->the_post();
			$ids[] = get_the_ID();
		}
		
		$tagReplacements = array(
			'PREVIEW',
			get_permalink($templateID),
			$this->get_article_html($ids[0], true),
			$this->get_article_html($ids[1], true),
			$this->get_article_html($ids[2], false),
			$this->get_article_html($ids[3], false),
			$this->get_article_html($ids[4], false),
			$this->get_article_html($ids[5], false),
			$this->get_article_html($ids[6], false),
			$this->get_article_html($ids[7], false),
			'<a href="http://www.superiorscales.com/"><img src="http://industrytap.engnet.atticuswhite.com/wp-content/uploads/2014/03/SuperiorScalesADnews.png"></a>'
		);
		
		wp_reset_postdata();
		
		return $this->_getNewsletterHTML($templateID, $tagReplacements);
		
	}
	
		
		
	function getNewsletterHtml($newsletterID){
		$template = get_field('template', $newsletterID);
		$tagReplacements = array(
			get_the_title($newsletterID),
			get_permalink($newsletterID),
			$this->get_article_html(get_field('featured_post_1', $newsletterID)->ID, true),
			$this->get_article_html(get_field('featured_post_2', $newsletterID)->ID, true),
			$this->get_article_html(get_field('standard_post_1', $newsletterID)->ID, false),
			$this->get_article_html(get_field('standard_post_2', $newsletterID)->ID, false),
			$this->get_article_html(get_field('standard_post_3', $newsletterID)->ID, false),
			$this->get_article_html(get_field('standard_post_4', $newsletterID)->ID, false),
			$this->get_article_html(get_field('standard_post_5', $newsletterID)->ID, false),
			$this->get_article_html(get_field('standard_post_6', $newsletterID)->ID, false),
			$this->get_banner_html(get_field('banner_image', $newsletterID), get_field('banner_link_url', $newsletterID))
		);
		return $this->_getNewsletterHTML($template->ID, $tagReplacements);
	}
		
		
		
		
	function getNewsletterText($newsletterID){
		$template = get_field('template', $newsletterID);
		$tagReplacements = array(
			get_permalink(),
			$this->get_article_text(get_field('featured_post_1', $newsletterID)->ID, true),
			$this->get_article_text(get_field('featured_post_2', $newsletterID)->ID, true),
			$this->get_article_text(get_field('standard_post_1', $newsletterID)->ID, true),
			$this->get_article_text(get_field('standard_post_2', $newsletterID)->ID, true),
			$this->get_article_text(get_field('standard_post_3', $newsletterID)->ID, true),
			$this->get_article_text(get_field('standard_post_4', $newsletterID)->ID, true),
			$this->get_article_text(get_field('standard_post_5', $newsletterID)->ID, true),
			$this->get_article_text(get_field('standard_post_6', $newsletterID)->ID, true),
			get_field('banner_link_url'),
			' ',
			'"',
			'"',
			"'",
			',',
			"'",
			' '
		);
		return $this->_getNewsletterText($template->ID, $tagReplacements);
	}
		
		
			
			
	function get_article_text($post_id, $featured = false) {
		global $post;
		$old_post = $post;
		$post = get_post($post_id);
		setup_postdata($post);
		
		$title = get_the_title();
		$permalink = get_permalink();
		if ($featured == true){
			$excerpt = get_the_excerpt();
		}
		$post = $old_post;
		$text = '';
		if ($featured == true){
			$text .= $title."\n".$permalink."\n"."\n".$excerpt."[...]\n"."\n".'Read More »'.$permalink;
		}
		else {
			$text .= $title."\n".$permalink;
		}
		return $text;
	}
			
		
	function get_article_html($post_id, $featured = false) {
		global $post;
		$old_post = $post;
		$post = get_post($post_id);
		setup_postdata($post);
		
		$title = get_the_title();
		$permalink = get_permalink();
		$excerpt = get_the_excerpt();
		$thumbnail_size = $featured ? 'newsletter-featured-article-image' : 'newsletter-standard-article-image';
		$thumbnail_id = get_post_thumbnail_id($post_id);
		$thumbnail_data = wp_get_attachment_image_src($thumbnail_id, $thumbnail_size);
		if ($thumbnail_data) {
			$thumbnail_url = $thumbnail_data[0];
			$thumbnail_width = $featured ? 640 : 182;
			$thumbnail_height = $featured ? 152 : 127;
			
			$thumbnail = '<img src="' . $thumbnail_url . '" width="' . $thumbnail_width . '" height="' . $thumbnail_height . '" />';
		} else {
			$thumbnail = get_the_post_thumbnail($post_id, $thumbnail_size);
		}
		
		$post = $old_post;
		$html = '';
		
		if ($featured) {
			//$html .= '<td align="center">';
				$html .= '<table width="650" border="0" cellspacing="0" cellpadding="5" class="templateNewsMain">';
					$html .= '<tbody>';
						if ($thumbnail) {
							$html .= '<tr>';
								$html .= '<td>';
									$html .= '<a href="' . $permalink . '">' . $thumbnail . '</a>';
								$html .= '</td>';
							$html .= '</tr>';
						}
						$html .= '<tr>';
							$html .= '<td>';
								$html .= '<h1>';
									$html .= '<a href="' . $permalink . '">' . $title . '</a>';
								$html .= '</h1>';
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td>';
								$html .= $excerpt;
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td align="right">';
								$html .= '<a href="' . $permalink . '">Read More »</a>';
							$html .= '</td>';
						$html .= '</tr>';
					$html .= '</tbody>';
				$html .= '</table>';
			//$html .= '</td>';
		} else {
			//$html .= '<td>';
				$html .= '<table width="100%" border="0" cellspacing="0" cellpadding="3">';
					$html .= '<tbody>';
						if ($thumbnail) {
							$html .= '<tr>';
								$html .= '<td>';
									$html .= '<a href="' . $permalink . '">' . $thumbnail . '</a>';
								$html .= '</td>';
							$html .= '</tr>';
						}
						$html .= '<tr>';
							$html .= '<td>';
								$html .= '<a href="' . $permalink . '">' . $title . '</a>';
							$html .= '</td>';
						$html .= '</tr>';
					$html .= '</tbody>';
				$html .= '</table>';
			//$html .= '</td>';
		}
		
		return $html;
	}		

	function get_banner_html($image, $link){
		$banner = "<img src='{$image['sizes']['newsletter-featured-article-image']}' />";
		
		if ($link){
			return "<a href='$link'>$banner</a>";
		}
		return $banner;
	}
	


}
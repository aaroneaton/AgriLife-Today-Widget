<?php
/*
 * Plugin Name: AgriLife Today Widget
 * Plugin URI: https://github.com/channeleaton/AgriLife-Today-Widget
 * Description: Creates a widget to display stories from today.agrilife.org. Code is pulled from the AgriFlex theme.
 * Version: 1.1
 * Author: J. Aaron Eaton
 * Author URI: http://channeleaton.com
 */

/**
 * Widget: AgriLife Today Feeds
 * Three widgets in one with thoughtful defaults in case of absentee user.
 */

class Agrilife_Today_Widget extends WP_Widget {
	private $feeds = array(
    array('All AgriLife Today','http://today.agrilife.org/feed/'),
    array('College','http://today.agrilife.org/agency/college-of-agriculture-and-life-sciences/feed/'),
    array('Extension','http://today.agrilife.org/agency/texas-agrilife-extension-service/feed/'),
    array('Research','http://today.agrilife.org/agency/texas-agrilife-research/feed/'),
    array('TVMDL','http://today.agrilife.org/agency/texas-veterinary-medical-diagnostics-laboratory/feed/'),
    array('Category: Business &amp; Finance','http://today.agrilife.org/category/business/feed/'),
    array('Category: Environment','http://today.agrilife.org/category/environment/feed/'),
    array('Category: Farm &amp; Ranch','http://today.agrilife.org/category/farm-ranch/feed/'),
    array('Category: Lawn &amp; Garden','http://today.agrilife.org/category/lawn-garden/feed/'),
    array('Category: Life &amp; Health','http://today.agrilife.org/category/life-health/feed/'),
    array('Category: Science &amp; Tech','http://today.agrilife.org/category/science-and-technology/feed/'),
    array('Sub-Cat: 4-H','http://today.agrilife.org/tag/4h-youth/feed/'),
    array('Sub-Cat: AgriLife Personnel','http://today.agrilife.org/tag/personnel/feed/'),
    array('Sub-Cat: Gardening','http://today.agrilife.org/tag/gardening-landscaping/feed/'),
    array('Sub-Cat: Energy','http://today.agrilife.org/tag/biofuel-energy/feed/'),
  );

	function __construct() {

		//Constructor
		$widget_ops = array('classname' => 'widget agrilifetoday', 'description' => 'Show the latest AgriLife Today updates.' );
		$this->WP_Widget('Agrilife_Today_Widget_RSS', 'AgriLife: Agrilife Today News Feed', $widget_ops);		

	} // __construct

	function widget($args, $instance) {

		// prints the widget
		if ( isset($instance['error']) && $instance['error'] )
			return;
		
		extract($args, EXTR_SKIP);	

 		// RSS Processing
 		$myfeeds 			= $this->feeds;
 		$feed_link_index	= (int) $instance['feed_link_index'];
 		$agrilife_feed_link = $myfeeds[$feed_link_index][1]; //'http://agrilife.org/today/feed/';
		$rss = fetch_feed($agrilife_feed_link);
		//$title = $instance['title'];
		
		$desc = '';
		
		if ( ! is_wp_error($rss) ) {
			//$agrilife_feed_title= esc_attr(strip_tags(@html_entity_decode($rss->get_title(), ENT_QUOTES, get_option('blog_charset'))));
			$title = apply_filters('widget_title', empty($instance['title']) ? __('AgriLife Today') : $instance['title'], $instance, $this->id_base);
			
			if ( empty($title) )
				$title = esc_html(strip_tags($rss->get_title()));
			$link = esc_url(strip_tags($rss->get_permalink()));
			while ( stristr($link, 'http') != $link )
				$link = substr($link, 1);
			$podcast_site_link = $link;
		}
		
		// show the widget
		echo $before_widget; ?>
		<div class="watchreadlisten-bg widget">
			<?php if ( $title ) echo $before_title . $title . $after_title; ?>
			<?php agrilife_widget_agrilifetoday_rss_output( $rss, $instance ); ?>		
		</div>
		<?php echo $after_widget;

	} // widget

	function update($new_instance, $old_instance) {

		//save the widget
		$instance = $old_instance;
		$instance['feed_link_index'] = strip_tags($new_instance['feed_link_index']);
		$instance['show_summary'] = strip_tags($new_instance['show_summary']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;

	} // update

	function form($instance) {

		//widgetform in backend
		$instance 			= wp_parse_args( (array) $instance, array('items' => '5', 'feed_link_index' => '0', 'show_summary' => true) );
		
		$items  			= $instance['items'];
		$feed_link_index	= (int) $instance['feed_link_index'];
		$myfeed 			= $this->feeds;
		$show_summary   	= (int) $instance['show_summary'];
		
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('How many items would you like to display?'); ?></label>
		<select id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>">
		<?php
				for ( $i = 1; $i <= 10; ++$i )
					echo "<option value='$i' " . ( $items == $i ? "selected='selected'" : '' ) . ">$i</option>";
		?>
		</select></p>
		<p><label for="<?php echo $this->get_field_id('feed_link_index'); ?>"><?php _e('What category do you want to display?'); ?></label>
		<select id="<?php echo $this->get_field_id('feed_link_index'); ?>" name="<?php echo $this->get_field_name('feed_link_index'); ?>">
		<?php			
			for ($i=0; $i<count($myfeed); $i++) {
				echo "<option value=\"".$i."\" " . ( $feed_link_index == $i ? "selected='selected'" : '' ) . ">".$myfeed[$i][0]."</option>";
			}
		?>
		</select></p>
	
		<p>
			<input id="<?php echo $this->get_field_id('show_summary'); ?>" name="<?php echo $this->get_field_name('show_summary'); ?>" type="checkbox" value="1" <?php checked( $show_summary ); ?> />
			<label for="<?php echo $this->get_field_id('show_summary'); ?>"><?php _e('Display article excerpts?'); ?></label>
		</p>
		<?php

	} // form

} // class AgriLife_Today_Widget

/**
 * Display the RSS feed from AgriLife Today and include image
 *
 * @since 2.5.0
 *
 * @param string|array|object $rss RSS url.
 * @param array $args Widget arguments.
 */
function agrilife_widget_agrilifetoday_rss_output( $rss, $args = array() ) {
	if ( is_string( $rss ) ) {
		$rss = fetch_feed($rss);
	} elseif ( is_array($rss) && isset($rss['url']) ) {
		$args = $rss;
		$rss = fetch_feed($rss['url']);
	} elseif ( !is_object($rss) ) {
		return;
	}

	if ( is_wp_error($rss) ) {
		if ( is_admin() || current_user_can('manage_options') )
			echo '<p>' . sprintf( __('<strong>RSS Error</strong>: %s'), $rss->get_error_message() ) . '</p>';
		return;
	}

	$default_args = array( 'items' => 5, 'feed_link_index' => 0, 'show_summary' => 0  );
	$args = wp_parse_args( $args, $default_args );
	extract( $args, EXTR_SKIP );

	$items = (int) $items;
	if ( $items < 1 || 20 < $items )
		$items = 10;
	$show_summary  = (int) $show_summary;

	if ( !$rss->get_item_quantity() ) {
		echo '<ul><li>' . __( 'An error has occurred; the feed is probably down. Try again later.' ) . '</li></ul>';
		$rss->__destruct();
		unset($rss);
		return;
	}

	echo '<ul>';
	foreach ( $rss->get_items(0, $items) as $item ) {
		$link = $item->get_link();
		while ( stristr($link, 'http') != $link )
			$link = substr($link, 1);
		$link = esc_url(strip_tags($link));
		$title = esc_attr(strip_tags($item->get_title()));
		if ( empty($title) )
			$title = __('Untitled');

		$desc = str_replace( array("\n", "\r"), ' ', esc_attr( strip_tags( @html_entity_decode( $item->get_description(), ENT_QUOTES, get_option('blog_charset') ) ) ) );
		//$desc = wp_html_excerpt( $desc, 360 );
		
		// Append ellipsis. Change existing [...] to [&hellip;].
		if ( '...' == substr( $desc, -3 ) )
			$desc = substr( $desc, 0, -5 ) . '&hellip;';
		elseif ( 'Read More...' == substr( $desc, -12 ) )
			$desc = substr( $desc, 0, -13 ).'&hellip;';

		$desc = trim(esc_html( $desc ));
		
		if ( $show_summary ) {
			$summary = "<p class='rss-excerpt'>$desc</p>";
		} else {
			$summary = '';
		}
		
		// default
		$image = '<img class="rssthumb" src="'.get_bloginfo('stylesheet_directory') . '/images/agrilifetodaythumb.jpg?v=100'.'" alt="'.$title.'" />';

		$date = $item->get_date( 'U' );

		// Allow developers to choose the date format
		$format = apply_filters( 'agrilife_today_date_format', 'M d' );

		if ( $date ) {
			$date = ' <span class="rss-date">' . date_i18n( $format, $date ) . '</span>';
		}

		// SimplePie Bug:
		// get_enclosures only returns one enclosure
		// http://tech.groups.yahoo.com/group/simplepie-support/message/2994	
		if ($enclosure = $item->get_enclosure()) {		
			if(	$enclosure->get_extension() == 'jpg' || $enclosure->get_extension() == 'png' || $enclosure->get_extension() == 'gif') {
			  	$image = '<img class="rssthumb" src="'.$enclosure->get_link().'" alt="'.$title.'" />';
			 } else {
			 	$image = '<img class="rssthumb" src="'.get_bloginfo('stylesheet_directory') . '/images/agrilifetodaythumb.jpg?v=100'.'" alt="'.$title.'" />';
			 }
		}
		
		// Link the image	
		$image = '<a class="rss-img-link" href="'.$link.'" >'.$image.'</a>';
		
	    echo "<li>".'<span class="rss-title"><a class="rss-title-link" href="'.$link.'" >'.$title."</a></span><div class='rss-content'>{$date}{$image}{$summary}</div></li>";

	}
	echo '</ul>';
	$rss->__destruct();
	unset($rss);

} // agrilife_widget_agrilifetoday_rss_output

function init_ag_today_widget() {

  register_widget( 'Agrilife_Today_Widget');

} // init_ag_today_widget
add_action( 'widgets_init', 'init_ag_today_widget' );

// Load up the default css
function agrilife_today_load_styles() {

  wp_register_style( 'today-style', plugins_url( 'style.css', __FILE__ ) );
  wp_enqueue_style( 'today-style' );

} // agrilife_today_load_styles
add_action( 'wp_enqueue_scripts', 'agrilife_today_load_styles' );

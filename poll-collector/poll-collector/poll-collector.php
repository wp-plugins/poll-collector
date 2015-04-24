<?php
/**
 * Plugin Name: Poll Collector
 * Description: Count clicks associated with a post or page
 * Version: 1.0
 * Author: Corey Maass, Gelform Inc
 * Author URI: http://gelform.com
 */



include __DIR__ . '/options.php';



// for flash messages
if( !session_id() ) session_start();



class PollCollector 
{
	static $options = array(
		'poll_show_alerts' => TRUE,
		'poll_ip_check' => FALSE,
		'poll_template_not_clickable' => '{{answer}}<div class="progress">
<div class="progress-bar" role="progressbar" aria-valuenow="{{percentage}}" aria-valuemin="1" aria-valuemax="{{max}}" style="width: {{percentage}}%;">
{{count}}
</div>
</div>',
'poll_template_clickable' => '<a href="{{permalink}}" class="poll-item">
<div class="poll-answer">
{{answer}}
</div>
<div class="progress">
<div class="progress-bar" role="progressbar" aria-valuenow="{{percentage}}" aria-valuemin="1" aria-valuemax="{{max}}" style="width: {{percentage}}%;">
{{count}}
</div>
</div>
</a>',
'poll_template_wrapper' => '<div class="poll clearfix">{{poll}}</div>',
'poll_template_alert' => '<div class="alert alert-success">{{alert}}</div>',
'poll_alert_success' => 'Thanks, your vote has been counted!',
'poll_alert_fail_ip' => 'Whoops, looks like you already voted.'
	);



	static private $default_templates = array (

	);



	static function setup ()
	{
		add_action('wp', array(__CLASS__, 'process_get'));

		// add shortcode for displaying poll
		add_shortcode( 'poll_collector', array(__CLASS__, 'shortcode_poll_collector') );

		// add options page
		add_action( 'admin_menu', array(__CLASS__ . 'Options', 'add_plugin_menu' ));
	}



	static function shortcode_poll_collector( $atts )
	{
		// check for clickable
		if ( isset($atts['clickable']) )
		{
			if ( $atts['clickable'] == '0' || $atts['clickable'] == 'false' || $atts['clickable'] == 'no' )
			{
				$atts['clickable'] = FALSE;
			}
		}

		// set defaults
		$atts = shortcode_atts(
			array (
				'post' => '',
				'answers' => 'True, False',
				'clickable' => TRUE
			),
			$atts
		);



		// if post is specified, load it
		if ( $atts['post'] > 0 )
		{
			$post = get_post($atts['post']); 
		}
		else
		{

			global $post;
		}

		// check if post is loaded
		if ( !isset($post->ID) || $post->ID == 0 )
		{
			return false;
		}



		// get all options
		$ops = self::get_options();

		// break out our answers
		$answersArr = explode(',', $atts['answers']);



		// get values, for highest
		$answersWithCountsArr = array();
		$maxCount = 0;
		foreach ($answersArr as $answer)
		{
			$answer = trim($answer);

			$answerCount = (int) get_post_meta( $post->ID, 'poll-answer-' . strtolower($answer), TRUE );

			$answersWithCountsArr[] = (object) array(
				'answer' => $answer,
				'count' => $answerCount
			);

			if ( $answerCount > $maxCount )
			{
				$maxCount = $answerCount;
			}
		}

		// multiple tiles 100, so we don't end up with an empty bar
		$maxCount = $maxCount*10;



		$pollHtml;
		foreach ($answersWithCountsArr as $answer)
		{
			// multiple tiles 100, so we don't end up with an empty bar
			$percentage = $maxCount > 0 ? ($answer->count/$maxCount)*100*10 : 0;
			if ( $percentage < 10 ) $percentage = 10;

			// build permalink
			$permalink = get_permalink($post->ID);

			if (strpos($permalink, '?') === false) 
			{
				$permalink .= '?';
			}
			else
			{
				$permalink .= '&amp;';
			}

			// add answer
			$permalink .= 'poll-answer=' . urlencode($answer->answer);

			// add nonce
			$nonce = wp_create_nonce(get_permalink($post->ID));
			$permalink .= '&amp;poll-nonce=' . $nonce;

			// make it a link
			if ( $atts['clickable'] )
			{
				$html = stripslashes(html_entity_decode($ops['poll_template_clickable']));
			}
			else
			{
				$html = stripslashes(html_entity_decode($ops['poll_template_not_clickable']));
			}

			// populate templates
			$html = str_replace('{{permalink}}', $permalink, $html);
			$html = str_replace('{{answer}}', $answer->answer, $html);
			$html = str_replace('{{percentage}}', $percentage, $html);
			$html = str_replace('{{max}}', $maxCount, $html);
			$html = str_replace('{{count}}', $answer->count, $html);

			$pollHtml .= $html;
		}		



		// get alerts, which clears them out, regardless of whether we show them
		$flash = self::flash('poll');

		if ( $flash && $ops['poll_show_alerts'] )
		{
			$html = stripslashes(html_entity_decode($ops['poll_template_alert']));
			$html = str_replace('{{alert}}', $flash, $html);

			$pollHtml = $html . $pollHtml;
		}



		// wrap it all 
		if ( !empty($ops['poll_template_wrapper']) )
		{
			$html = stripslashes(html_entity_decode($ops['poll_template_wrapper']));
			$pollHtml = str_replace('{{poll}}', $pollHtml, $html);
		}



		// and return our html
		return $pollHtml;
	} // shortcode_poll_collector



	static function process_get ()
	{
		// check for 
		if ( empty($_GET['poll-answer']) || empty($_GET['poll-nonce']) ) return;




		// try to inherit post 
		global $post;

		// get the post from the URL
		if ( $post->ID )
		{
			$post_id = $post->ID;
		}
		else
		{
			$post_id = url_to_postid($pageURL);
		}

		$permalink = get_permalink($post->ID);		



		// verify none
		if ( !wp_verify_nonce($_GET['poll-nonce'], $permalink) ) 
		{
			return;
		}



		// get all options
		$ops = self::get_options();



		if ( $ops['poll_ip_check'] )
		{
			// get user ip address to test for duplicate votes
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
			{
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} 
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
			{
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} 
			else 
			{
				$ip = $_SERVER['REMOTE_ADDR'];
			}



			// test if IP address has voted
			$ipHasVoted = get_post_meta( $post_id, 'poll-ip-' . $ip, TRUE );

			if ( $ipHasVoted ) 
			{
				if ( $ops['poll_show_alerts'] ) 
				{
					self::flash('poll', 'IP already voted');
				}

				// redirect without nonce to prevent double voting
				wp_redirect( $permalink ); exit;
			}
		}



		// get the previous count
		$answerCount = get_post_meta( $post_id, 'poll-answer-' . strtolower($_GET['poll-answer']), TRUE );

		// store the vote
		update_post_meta($post_id, 'poll-answer-' . strtolower($_GET['poll-answer']), $answerCount+1, $answerCount);

		// store the ip address
		update_post_meta($post_id, 'poll-ip-' . $ip, date('Y-m-d H:i:s'));

		// hooray!
		if ( $ops['poll_show_alerts'] )
		{
			self::flash('poll', 'Vote counted');
		}

		// redirect without nonce to prevent double voting
		wp_redirect( $permalink ); exit;
	}



	/**
	 * @link http://www.phpdevtips.com/2013/05/simple-session-based-flash-messages/
	 * Function to create and display error and success messages
	 * @access public
	 * @param string session name
	 * @param string message
	 * @param string display class
	 * @return string message
	 */
	static function flash( $name = '', $message = '', $class = 'success fadeout-message' )
	{
	    //We can only do something if the name isn't empty
	    if( !empty( $name ) )
	    {
	        //No message, create it
	        if( !empty( $message ) && empty( $_SESSION[$name] ) )
	        {
	            if( !empty( $_SESSION[$name] ) )
	            {
	                unset( $_SESSION[$name] );
	            }
	            if( !empty( $_SESSION[$name.'_class'] ) )
	            {
	                unset( $_SESSION[$name.'_class'] );
	            }
	 
	            $_SESSION[$name] = $message;
	            $_SESSION[$name.'_class'] = $class;
	        }
	        //Message exists, display it
	        elseif( !empty( $_SESSION[$name] ) && empty( $message ) )
	        {
	            $class = !empty( $_SESSION[$name.'_class'] ) ? $_SESSION[$name.'_class'] : 'success';
	            $return = $_SESSION[$name];
	            unset($_SESSION[$name]);
	            unset($_SESSION[$name.'_class']);

	            return $return;
	        }
	    }
	}



	static function get_options()
	{
		$options = self::$options;
		foreach ($options as $option => $default)
		{
			$saved = get_option($option);
			if ( !empty($saved) )
			{
				$options[$option] = $saved;
			}
			// $$option = stripslashes($options[$option]);
		}


		// get plugin options
		// $ops = array();
		// foreach (self::$options as $option)
		// {
		// 	$ops[$option] = get_option($option);
		// }

		return $options;
	}
}



PollCollector::setup();




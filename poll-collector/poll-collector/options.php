<?php 

class PollCollectorOptions extends PollCollector
{
	static function add_plugin_options() 
	{
		// make sure they're an admin
		if ( !current_user_can( 'manage_options' ) )  
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}



		// save the form options
		if (isset($_POST["update_settings"])) 
		{
			foreach (parent::$options as $option => $ignore)
			{
				$$option = esc_attr($_POST[$option]);
				update_option($option, $$option);
				$$option = stripslashes($$option);
			}

			echo '<div id="message" class="updated">Settings saved</div>';
		}

		// load all options as page variables for display
		$options = parent::$options;

		foreach ($options as $option => $default)
		{
			$saved = get_option($option);

			if ( !empty($saved) )
			{
				$options[$option] = $saved;
			}
			$$option = stripslashes($options[$option]);
		}
		?>



		<div class="wrap">
			<?php screen_icon('themes'); ?>
			<h2>
				Poll Collector
			</h2>


<h2 class="nav-tab-wrapper">
		<a href="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>?page=<?php echo __CLASS__ ?>" class="nav-tab <?php echo empty($_GET['instructions']) && empty($_GET['builder']) ? 'nav-tab-active' : '' ?>">Settings</a>
		<a href="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>?page=<?php echo __CLASS__ ?>&amp;instructions=1" class="nav-tab <?php echo !empty($_GET['instructions']) ? 'nav-tab-active' : '' ?>">Instructions</a>
		<a href="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>?page=<?php echo __CLASS__ ?>&amp;builder=1" class="nav-tab <?php echo !empty($_GET['builder']) ? 'nav-tab-active' : '' ?>">Builder</a>
</h2>



<?php if ( !empty($_GET['instructions']) ) : ?>
			<h3>About the plugin</h3>

			<p>
				Poll Collector is for running polls on your website. 
			</p>
			<p>
				Each poll is associated with a single post or page.
				They can be used with custom post types, too (since custom post types are just posts).
				Only one poll can be associated with each post.
			</p>
			<p>
				Poll Collector uses shortcodes. 
				To create a poll, create a shortcode as directed below and paste it into your post or page.
			</p>

			<p>
				Poll data is stored as post meta data using update_post_meta(). 
				Each answer has a database record with the total count of votes. 
				For example, if you're poll has two answers, &quot;yes&quot; and &quot;no&quot;, the post meta data would have two records:<br>
				poll-answer-yes = 10<br>
				post-answer-no = 5
			</p>

			<h3>Instructions</h3>

			<ol>
				<li>
					Install the plugin.
				</li>
				<li>
					Go to the plugin settings by visiting <b>Settings</b> &gt; <a href="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>?page=<?php echo __CLASS__ ?>"><b>Poll Collector</b></a> in your WordPress admin.
				</li>
				<li>
					Update the template html you would like rendered to your pages or posts. 
					(The default templates will render nicely on sites using Bootstrap 3.)
					Change any settings you'd like.
				</li>
				<li>
					Visit the <a href="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>?page=<?php echo __CLASS__ ?>&amp;builder=1">Builder</a> page for help building a shortcode.
					here you'll see how to define the poll answers, and whether a poll is open (visitors can vote) or closed.
				</li>
				<li>
					Copy the rendered shortcode and paste it in your post or page. Then save your post or page.
				</li>
				<li>
					Visit your post or page, and you should see your poll!
				</li>
			</ol>

			<h3>Shortcode options</h3>

			<p>
				The shortcode is based off of [poll_collector]
			</p>

			<dl>
				<dt>answers</dt>
				<dd>
					<p>
						The poll options. This might be a list of politicians, favorite colors, or just &quot;yes&quot; and &quot;no&quot;.
					</p>
					<p>
						Example: <i>[poll_collector answers="Bill Clinton, George W Bush, Barack Obama"]</i>
					</p>
				</dd>
				<dt>clickable</dt>
				<dd>
					<p>
						Denotes whether visitors can vote in the poll or not. 
						To close a poll, leave out this option, or set it to &quot;no&quot;, &quot;false&quot; or &quot;0&quot;.
						To open a poll, simple set this option (&quot;[poll_collector clickable]&quot;) or set it to &quot;yes&quot;, &quot;true&quot; or &quot;1&quot;.
					</p>
					<p>
						Example: <i>[poll_collector clickable="yes"]</i>
					</p>
				</dd>
				<dt>post</dt>
				<dd>
					<p>
						The ID of the post to associate with the poll. 
						You shouldn't ever need this, unless you want to display a poll somewhere other than on the same page as its post.
					</p>
					<p>
						Example: <i>[poll_collector post="132"]</i>
					</p>
				</dd>
			</dl>

			<style>
			dt {
				font-weight: bold;
			}
			</style>
<?php endif // instructions ?>



<?php if ( !empty($_GET['builder']) ) : ?>
			<h3>Shortcode builder</h3>

			<p>
				Modify the options below, and the shortcode will update.
				When you're done, copy and paste the updated shortcode into your posts.
			</p>

			<div class="postbox" id="poll_shortcode_builder">
				<div class="inside">
					<p>
						<input type="text" id="poll_shortcode" value="[poll_collector answers=&quot;yes, no&quot; clickable=&quot;yes&quot;]" readonly>
					</p>

					<h4>Poll Answers</h4>
					<p class="poll_value">
						<span class="poll_value_remove">&times;</span>
						<input type="text" value="yes">
					</p>
					<p class="poll_value">
						<span class="poll_value_remove">&times;</span>
						<input type="text" value="no">
					</p>
					<p>
						<button type="button" id="poll_add_value" class="button button-primary button-large">Add another poll choice</button>
					</p>
					<h4>Options</h4>
					<p>
						<label>
							<input type="checkbox" id="poll_clickable" checked> Visitors can vote
						</label>
					</p>				
				</div><!-- inside -->
			</div><!-- postbox -->



			<script>
			jQuery(function($){

				function updateShortcode()
				{
					var valArr = [];
					$('.poll_value input').each(function(){
						valArr.push($(this).val());
					});

					var clickable = $('#poll_clickable').prop('checked') ? 'yes' : 'no';

					var text = '[poll_collector answers="' + valArr.join(', ') + '" clickable="' + clickable + '"]';
					$('#poll_shortcode').val(text);
				}; // updateShortcode

				$('#poll_shortcode_builder').on(
					'keyup',
					'.poll_value input',
					function()
					{
						updateShortcode();
					}
				);

				$('#poll_shortcode_builder').on(
					'click',
					'.poll_value_remove',
					function()
					{
						$(this).closest('.poll_value').remove();
						updateShortcode();
					}
				);

				$('#poll_add_value').on(
					'click',
					function()
					{
						$('<p class="poll_value"><span class="poll_value_remove">&times;</span><input type="text" value="something"></p>').insertAfter('.poll_value:last');
						updateShortcode();
					}
				);

				$('#poll_clickable').on(
					'change',
					function()
					{
						updateShortcode();
					}
				);
			});
			</script>

			<style>
			.postbox [type=text] {
				padding: .236em .618em;
				font-size: 1.7em;
				line-height: 100%;
				height: 1.7em;
				width: 100%;
				outline: 0;
				margin: 0;
				background-color: #fff;
			}

			.poll_value {
				position: relative;
			}
			.poll_value_remove {
				background: #C00;
				border-radius: 1em;
				color: #FFF;
				cursor: pointer;
				font-size: 2em;
				height: 1em;
				line-height: 1em;
				width: 1em;
				position: absolute;
				right: -.5em;
				text-align: center;
				top: -.5em;
			}
			</style>
<?php endif // builder ?>



<?php if ( empty($_GET['instructions']) && empty($_GET['builder']) ) : ?>
			<form method="POST" action="">
				
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="<?= 'poll_show_alerts' ?>">
								Show alerts:
							</label> 
						</th>
						<td>
							<p>
								<label>
									<input type="radio" name="<?= 'poll_show_alerts' ?>" value="1" <?= ${'poll_show_alerts'} ? 'checked="checked"' : '' ?> />
									Yes
								</label><br>
								<label>
									<input type="radio" name="<?= 'poll_show_alerts' ?>" value="0" <?= !${'poll_show_alerts'} ? 'checked="checked"' : '' ?> />
									No
								</label>
							</p>
							<p><i>Alerts will show a message when a visitor trys to vote.</i></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="<?= 'poll_ip_check' ?>">
								One vote per IP:
							</label> 
						</th>
						<td>
							<p>
								<label>
									<input type="radio" name="<?= 'poll_ip_check' ?>" value="1" <?= ${'poll_ip_check'} ? 'checked="checked"' : '' ?> />
									Yes
								</label><br>
								<label>
									<input type="radio" name="<?= 'poll_ip_check' ?>" value="0" <?= !${'poll_ip_check'} ? 'checked="checked"' : '' ?> />
									No
								</label>
							</p>
							<p><i>Blocks the same IP address from voting multiple times. WARNING! Sometimes offices or schools all use one IP address.</i></p>
						</td>
					</tr>
				</table>

				<h3>Templates</h3>

				<p>
					Templates define how the poll and results will be rendered on your site. Variables allow you to place the poll data within the template.
				</p>

				<table>
					<tr valign="top">
						<th scope="row">
							<label for="<?= 'poll_template_not_clickable' ?>">
								Not clickable:
							</label> 
						</th>
						<td>
							<textarea name="<?= 'poll_template_not_clickable' ?>"><?= ${'poll_template_not_clickable'} ?></textarea>
						</td>
						<td>
							Variables:<br>
							{{percentage}} 
							{{max}}
							{{answer}}
							{{count}}
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="<?= 'poll_template_clickable' ?>">
								Clickable:
							</label> 
						</th>
						<td>
							<textarea name="<?= 'poll_template_clickable' ?>"><?= ${'poll_template_clickable'} ?></textarea>
						</td>
						<td>
							Variables:<br>
							{{percentage}} 
							{{max}}
							{{answer}}
							{{count}}
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="<?= 'poll_template_wrapper' ?>">
								Wrapper:
							</label> 
						</th>
						<td>
							<textarea name="<?= 'poll_template_wrapper' ?>"><?= ${'poll_template_wrapper'} ?></textarea>
						</td>
						<td>
							Variables:<br>
							{{poll}} 
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="<?= 'poll_template_alert' ?>">
								Alert:
							</label> 
						</th>
						<td>
							<textarea name="<?= 'poll_template_alert' ?>"><?= ${'poll_template_alert'} ?></textarea>
						</td>
						<td>
							Variables:<br>
							{{alert}} 
						</td>
					</tr>
				</table>

				<h3>Alerts</h3>

				<table>
					<tr valign="top">
						<th scope="row">
							<label for="<?= 'poll_alert_success' ?>">
								Success message:
							</label> 
						</th>
						<td>
							<textarea name="<?= 'poll_alert_success' ?>"><?= ${'poll_alert_success'} ?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="<?= 'poll_alert_fail_ip' ?>">
								fail message:<br>
								(IP already voted)
							</label> 
						</th>
						<td>
							<textarea name="<?= 'poll_alert_fail_ip' ?>"><?= ${'poll_alert_fail_ip'} ?></textarea>
						</td>
					</tr>
				</table>

				<p>
					<input type="submit" value="Save settings" class="button-primary"/>
					<input type="hidden" name="update_settings" value="Y" />
				</p>
			</form>
		</div><!-- wrap -->

		<style>
		td textarea {
			height: 6em;
			width: 40em;
		}
		</style>


		<?php endif; // instructions ?>
		<?php 
	}



	static function add_plugin_menu() 
	{
		add_options_page( 'Poll Collector Options', 'Poll Collector', 'manage_options', __CLASS__, array(__CLASS__, 'add_plugin_options') );
	}



} // PollCollectorOptions



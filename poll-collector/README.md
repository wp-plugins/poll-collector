=== Poll Collector Wordpress Plugin ===
Contributors: Gelform
Tags: poll, questionnaire, feedback
Requires at least: 4.0.1
Tested up to: 4.1.2
Stable tag: 1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Run polls on your WordPress posts and pages, simply.

== Description ==

Run polls on your WordPress posts and pages, simply.

Bootstrap Example:
![Poll Collector Bootstrap example](/assets/screenshot-post-bootstrap.png)

Twenty Fourteen Example:
![Poll Collector Twenty Fourteen example](/assets/screenshot-post-2014.png)

Poll Collector uses shortcodes. To create a poll, create a shortcode as directed below and paste it into your post or page.

== Installation ==

1. Install the plugin.
1. Go to the plugin settings by visiting Settings > Poll Collector in your WordPress admin. ![Poll Collector settings page](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-settings.png)
1. Update the template html you would like rendered to your pages or posts. (The default templates will render nicely on sites using Bootstrap 
1. Change any settings you'd like.
1. Visit the Builder page for help building a shortcode (or follow the guide below and create your own). The builder shows you how to define the poll answers, and whether a poll is open (visitors can vote) or closed. ![Poll Collector builder page](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-builder.png)
1. Copy the shortcode and paste it in your post or page. Then save your post or page. ![Poll Collector shortcode example](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-admin-shortcode.png)
1. Visit your post or page, and you should see your poll! ![Poll Collector Bootstrap example](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-post-bootstrap.png)

== Frequently Asked Questions ==

= How is the data stored =

Each poll is associated with a single post or page. They can be used with custom post types, too (since custom post types are just posts). Only one poll can be associated with each post.

Poll data is stored as post meta data using update_post_meta(). Each answer has a database record with the total count of votes. For example, if you're poll has two answers, "yes" and "no", the post meta data would have two records:
```
poll-answer-yes = 10
post-answer-no = 5
```

== Screenshots ==

Bootstrap Example:
![Poll Collector Bootstrap example](/assets/screenshot-post-bootstrap.png)

Twenty Fourteen Example:
![Poll Collector Twenty Fourteen example](/assets/screenshot-post-2014.png)

== Changelog ==

= 1.0.1 =
* The first stable release.

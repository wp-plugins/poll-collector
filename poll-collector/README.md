Poll Collector Wordpress Plugin
=====

Run polls on your WordPress posts and pages, simply.

Bootstrap Example:
![Poll Collector Bootstrap example](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-post-bootstrap.png)

Twenty Fourteen Example:
![Poll Collector Bootstrap example](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-post-2014.png)

Poll Collector uses shortcodes. To create a poll, create a shortcode as directed below and paste it into your post or page.

Instructions
-----

1. Install the plugin.
1. Go to the plugin settings by visiting Settings > Poll Collector in your WordPress admin. ![Poll Collector settings page](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-settings.png)
1. Update the template html you would like rendered to your pages or posts. (The default templates will render nicely on sites using Bootstrap 
1. Change any settings you'd like.
1. Visit the Builder page for help building a shortcode (or follow the guide below and create your own). The builder shows you how to define the poll answers, and whether a poll is open (visitors can vote) or closed. ![Poll Collector builder page](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-builder.png)
1. Copy the shortcode and paste it in your post or page. Then save your post or page. ![Poll Collector shortcode example](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-admin-shortcode.png)
1. Visit your post or page, and you should see your poll! ![Poll Collector Bootstrap example](https://raw.githubusercontent.com/gelform/poll-collector-wordpress-plugin/master/poll-collector/img/screenshot-post-bootstrap.png)



About the plugin
-----

Poll Collector is for running polls on your website.

Each poll is associated with a single post or page. They can be used with custom post types, too (since custom post types are just posts). Only one poll can be associated with each post.

Poll data is stored as post meta data using update_post_meta(). Each answer has a database record with the total count of votes. For example, if you're poll has two answers, "yes" and "no", the post meta data would have two records:
```
poll-answer-yes = 10
post-answer-no = 5
```


Shortcode options
-----

The shortcode is based off of [poll_collector]

### answers
The poll options. This might be a list of politicians, favorite colors, or just "yes" and "no".

Example: 
```
[poll_collector answers="Bill Clinton, George W Bush, Barack Obama"]
```

### clickable
Denotes whether visitors can vote in the poll or not. Voting is allowed, by default.

To close a poll, set the "clickable" attribute to "no", "false" or "0". 

To open a poll, leave off this attribute, set this option without a value ("[poll_collector clickable]"), or set it to "yes", "true" or "1".

Example: 
```
[poll_collector clickable="yes"]
```

### post
The ID of the post to associate with the poll. You shouldn't ever need this, unless you want to display a poll somewhere other than on the same page as its post.

Example: 
```
[poll_collector post="132"]
```

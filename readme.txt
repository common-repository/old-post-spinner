=== OPS Old Post Spinner === 
Contributors: 1manfactory
Donate link: http://1manfactory.com/donate 
Tags: post, promotion, SEO, rss, plugin, posts, unique, content, spin, spinner, spinning, article spinning 
Requires at least: 2.9 
Tested up to: 3.2
Stable tag: trunk

Create a complete unique new post on a random old one and promote it to the top of your blog. 

== Warnings == 

**DO NOT USE IF YOUR PERMALINK STRUCTURE HAS DATES!!!** 

== Description == 

All you need for SEO article spinning: Duplicate an (random) existing posts, make them unique automatically via 'spinning' and promote to the top of your blog. 

** DO NOT USE IF YOUR PERMALINK STRUCTURE HAS DATES!!! ** 

Compared to other plugins which do pretty much the same my plugin uses the build in functionality of Wordpress’ own "cronjob". This makes the whole part much more stable and (what is even more important) bypasses workload away from the server. 

Check out my other [Wordpress Plugins](http://wordpress.org/extend/plugins/profile/1manfactory) 

== Installation == 

1. Upload Old Post Spinner to the `/wp-content/plugins/` directory 
2. Activate the plugin through the 'Plugins' menu in WordPress 
3. Adjust settings and make log sub folder writeable. You will be promted to do so only if necessary. 

== Remove plugin == 

1. Deactivate plugin through the 'Plugins' menu in WordPress 
2. Delete plugin through the 'Plugins' menu in WordPress 

It's best to use the build in delete function of wordpress. That way all the stored data will be removed and no orphaned data will stay. 

== Screenshots == 

1. Basic Settings 
2. Log view 
3. Normal content edit 
4. Spin content edit 

== Frequently Asked Questions == 

= Is there anything I should be aware of? Any limitations?  = 

Yes! This plugin should not be used with permalink structures that include dates. 

= Please explain me the settings' values = 

Old Post Spinner (OPS) checks if a certain amount of time has passed. If so it promotes (or duplicates, depending on the settings) a random post to any position you like. 

E.G. take this values: 

* Minimum interval between old post promotions: 2000 minutes 

* Random maximum interval (added to minimum interval): 500 minutes

Now OPS will every 2000 + X minutes (where X lies somewhere random between 0 and 500) pick up one old post and promote (or duplicate) it to the defined position. 

OPS will take into account which posting categories you never want to promote 

You can also specify an minimum age for a post to be considered. 

What will happen? Let's take 10 posts ordered by age with No. 1 the as the youngest one. OPS picks the, let's say, sixth post and give it the current date to promote it to position number one. 

If you want a post to be promoted to position 3 OPS will place it, regarding the date, exactly in the middle between number 2 and (the old) number 3, which now will drop to position 4. 

= What is the difference between duplicating and promoting? = 

Promoting will move the post to a new position, whereas duplicating will copy a post to the new position, so the latter generates a new post. 

= What are the best practice values? = 

They are installed as default values. Minimum interval: 2000 minutes, Random interval: 500 minutes, Post age: 14 days. But maybe you should experiment with your own values. It's up to you. 

= What do you mean with spinning? = 

Spinning means the creation of a new "spinned" content. That will create a new and unique post every time it gets promoted (or duplicated). 

= How do I spin the title and the content? = 

That requires a bit of handwork. You can manually add multiple synonym versions of any sentence and any word you want. That is done by using the characters |, { and }. I call this spinning instructions. I give you an example: 

`Hello, my name is {Jack|Jim} and I love {Tennis|Golf}.` 

####Using this spinning instructions will generate this four different result sentences every time your article is promoted: 

* Hello, my name is Jack and I love Tennis. 
* Hello, my name is Jack and I love Golf. 
* Hello, my name is Jim and I love Tennis. 
* Hello, my name is Jim and I love Golf. 

So, with a little bit of extra afford you are able to create a complete unique post every time. Just add as many synonym sentences as you like between the curly brackets  ”{“, “}” and separate each synonym sentence by Pipe “|”. 

####You can nest your sentences as well: 

`Hello, my name is {Jim|{Handsome|Ugly}Joe}.` 

####Will result in 

* Hello, my name is Jim. 
* Hello, my name is Handsome Joe. 
* Hello, my name is Ugly Joe. 

IMPORTANT: Please post this spinning instruction sentences in the extra Old-Post-Spinner meta box of Wordpress (see the screenshot) and NOT into the normal text and content form fields 

= What will happen if I only want to spin the title or the content? = 

No problem. If you only fill in spinning instructions on the title, then only the title will be spinned. The content won't change. Same with the content, if you only want the content to be spinned, than leave the spinning instructions on the title empty. 

= What date will the promoted posts get? = 

Posts promoted to first position will get the current date. Posts below first position will be placed right in the middle of the enclosing posts. 

= Why should I use a category to ignore from promotion? = 

Some posts are simply not meant to be spinned. Especially if they are date-related. E.g. you should place posts with news into an omitted category. 

= But how do I hide the category from my blog? = 

If you don't want this category to appear in you blog you can use the "AVH Extended Categories" plugin. 

= What is the best position to promote (or duplicate) to? = 

I would advice you to always promote to position 1. Using any other position will after some periods leads to a cluster of post old placed around the very same date and time, this is because a new post is always placed exactly in the middle between two old posts. 

= How can I recover the original dates? = 

All original dates are stored as custom fields. The name is "ops_original_pub_date". 

= How can I support you? = 

That is nice of you. Donation link: http://1manfactory.com/donate 

= How can I promote my newly dated posts to twitter now? = 

I think you should consider to use a service like twitterfeed.com. It will pickup your promoted posts and send status messages to twitter. 

= What is the plugin page?  = 

[Old Post Spinner](http://1manfactory.com/ops "Old Post Spinner") 

= Do you have other plugins?  = 

Check out my other [Wordpress Plugins](http://wordpress.org/extend/plugins/profile/1manfactory) 

= Where do I post my feedback? = 

Post it at the plugin page: [Old Post Spinner](http://1manfactory.com/ops "Old Post Spinner") 

== Changelog == 

= 2.4.0 (06.07.2011) = 
* testing with Wordpress 3.2 -> OK
* small security bugfix

= 2.3.3 (10.06.2011) = 
* testing with Wordpress 3.1.3 -> OK
* removing schedule cron value when deactivating as well

= 2.3.2 (12.04.2011) = 
* small security bugfix

= 2.3.1 (25.03.2011) = 
* bugs fixed on test routine, when entering wrong values

= 2.3.0 (15.03.2011) = 
* using minutes instead of hours

= 2.2.1 (26.02.2011) = 
* testing with Wordpress 3.1 -> ok

= 2.2 (19.02.2011) = 
* testing with 3.0.5 
* more FAQs 
* new screenshots 
* more log entries to check for 
* more translations

= 2.15 (04.12.2010) = 
* tested with Wordpress 3.02 

= 2.14 (11.08.2010) = 
* tested with 3.01 
* new screenshots 

= 2.13 (11.07.2010) = 
* Fresher design of meta boxes 
* new wording 
* correcting typo errors 

= 2.12 (11.07.2010) = 
* creating a brand new permalink when duplicating 
* fixing a bug where not unique permalinks could get deleted 

= 2.11 (10.07.2010) = 
* now detects an existing "Google XML Sitemaps Generator for WordPress"-Plugin and starts creating the sitemap on request 

= 2.10 (09.07.2010) = 
* tuning the visual design 

= 2.02 (08.07.2010) = 
* small bugfix where on rare occasions (special PHP/server configuration) OPS creates an error 

= 2.01 (07.07.2010) = 
* adding more logging messages 

= 2.00 (06.07.2010) = 
* Now you can even duplicate a unique post instead of only promoting it 

= 1.21 (05.07.2010) = 
* delete spinning instructions as well when removing of plugin 

= 1.20 (04.07.2010) = 
* NOW WITH ABILITY TO CREATE UNIQUE CONTENT (Read FAQs on that, please) 
* fixed little date bug 

= 1.10 (18.06.2010) = 
* first multi language version 
* German added 

= 1.09 (18.06.2010) = 
* tested for Wordpress 3.0 - ok 
* log folder error message now appears inside admin header 

= 1.08 (17.06.2010) = 
* fixed unnecessary warning messages on development systems 

= 1.07 (16.06.2010) = 
* moving log folder to wp-content path to face some problems when updating 

= 1.06 (15.06.2010) = 
* fixing problems some might have with the creation of log folder 

= 1.05 (12.06.2010) = 
* new FAQs 
* checking of implausible values 
* new screenshots 

= 1.04 (08.06.2010) = 

* better logview (check for still not existing log file) 

= 1.03 (07.06.2010) = 

* small bugfixes 

= 1.02 (06.06.2010) = 

* using build in Wordpress Cronjob 
* better logfile view 

= 1.01 (05.06.2010) = 

* little bugfixes, wording 
* including of deinstalation routine to delete all data 
* implementing a better log file viewer 

= 1.00  (04.06.2010) = 

* first Version 

== Upgrade Notice == 

Just do a normal upgrade. 

== To do == 

More translations. Does someone wants to help?
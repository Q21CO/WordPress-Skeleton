=== Open Links Directory ===

name: Open Directory Links (ODLinks) Wordpress plugins version 1.4.1-a
Contributors: Mohammad Forgani
Donate link: http://www.forgani.com/
Tags: websitedirectory,opendirectory,linkdirectory,link directory,websites directory,dmoz,classifieds,website submitting, website, odlinks
Requires at least: 2.6
Tested up to: 2.6
Stable tag: 0.6

Build A Webpage directory
The plugin will help you to build a website directory and allow the site visitors to submit links by themselves.


== Description ==

Open Directory Links (ODLinks) help you to build a websites directory and allow your site visitors to submit links by themselves and enabling visitors to search for a website.

This plugin is under active development. If you experience problems, please first make sure you have the latest version installed. 
Feature requests, bug reports and comments can be submitted [here](http://www.forgani.com/root/opendirectorylinks/).


== Installation ==

1. Unzip the downloaded file and upload the odlinks folder to your Wordpress plugins folder (/wp-content/plugins/)
2. Log into your WordPress admin panel 
3. So go ahead and activate the ODLINKS plugin, which should appear in the list of installed plug-ins.
4. "ODLinks" will now be displayed in your admin panel.
5. For first step instructions, go to Options "Settings" of ODLINKS

You will need to make the Smarty cache and template_c folders writeable (chmod 777):
Use your FTP client to change its permissions to 777.
* odlinks/includes/Smarty/cache
* odlinks/includes/Smarty/templates_c

Once you have changed the folder permissions, return to your browser, and refresh it. 
Now you can submit your settings. After submission, installer process will create the needed database tables automatically.

6. Creating/Editing a category and subcategory 
- Before deploying, you should create and set-up the categories and subcategories for your Website.
- Go aheead and choose "Categories" from the ODLinks drop down menu and insert the category name and category description and then submit it.
- You can insert the subcategories by clicking on 'Add Category' and choosing any category name from the list in parent category. If you select root as parent category, then you create a main category.
- Create a subcategory is mandatory and required! 


7. Please, you should to keep the default title [[ODLINKS]] of page to make it work...


== Frequently Asked Questions ==

How can we display the latest xx ads in the sidebar?

You copy the following code and place it anywhere in the sidebar.php located in active theme folder.
<?php lastODLinksSidebar(8) ?>


== Upgrade ==

You will need to make the Smarty cache folders writeable (chmod 777):

* odlinks/includes/Smarty/cache
* odlinks/includes/Smarty/templates_c

== Frequently Asked Questions ==

Where to customize font and background colors?

You may modify the style sheet odlinks.css in wp-content/plugins/odlinks/themes/default/css/ folder in any way you wish to sets the background color of the layout area or fonts


== Screenshots ==

demo: http://www.odlinks.com


== History ==


== Changelog ==

= 1.4.1 =
Last Changes: Feb 29/11/2014
- Fixed Security Vulnerabilities

= 1.4.0 =
Last Changes: Feb 01/03/2013
- implement page navigation
- fixed to Thumbshots works properly.
- bugfix: admin style

= 1.3.0-d =
Last Changes: Sep 22/09/2012
- implementing of posts by admin user

= 1.3.0-c =
Last Changes: Oct 22/10/2011
- Bug Fix: fix pagerank issues
- update to check the character

= 1.3.0-b =
Last Changes: Mar 30/03/2011
- added/changed new skin theme & added some further admin interface 
- made some tiny changes to fixe for wp 3.1 problems..

= 1.2.0-d =
Changes 1.1.2-d - Aug 29/08/2010
- implement category's link in footer

= 1.2.0-c =
Changes 1.1.2-c - May 25/05/2010
- fixed for Wordpress 3.0

= 1.2.0-a =
Changes 1.1.2-a - May 25/05/2010
- new captcha routine. The previous methods have got problem with firefox
- updated to show ComboBox with subcategory names

Changes 1.1.1-a - Jan 20/01/2010
- implemented english language file

Changes 1.1-a - Jan 19/01/2010
- update the search process and templates

Changes 1.0.2-a - Oct 25/10/2009
- Fixed bug with auto-install on wordpress 2.8.5

Changes 1.0.1-a - Mar 17/03/2009
- Implement the search function.

Changes 1.0.0-a - Jan 25/01/2009
- It covers changes between WordPress Version 2.6 and Version 2.7

Changes Nov 12/2008 version v. 07
- implement the banned list
- added google pagerank


Changes Nov 12/2008 version v. 05
- implement the bookmark & sent to his friend’s button
- edit/move categories

- implement the conformation code (captcha)

Changes Oct 10/2008
- admin email notification
- include the Google AdSense 



== To Do ==

== Arbitrary section ==

== A brief Markdown Example ==

have fun
Mohammad Forgani
mMarch 15 2013

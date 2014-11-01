=== Unique Headers ===
Contributors: ryanhellyer
Tags: header, headers, image, header-image, header-images, taxonomy, tag, category, posts, pages, taxonomies, post, page, unique
Donate link: http://geek.ryanhellyer.net/donate/
Requires at least: 3.9
Stable tag: 1.2


Adds the ability to use unique custom headers on individual pages, posts or categories or tags.

== Description ==

The <a href="http://geek.ryanhellyer.net/products/unique-headers/">Unique Headers Plugin</a> adds the ability to use unique custom headers on
individual pages, posts or categories. This plugin makes use of the excellent <a href="http://wordpress.org/extend/plugins/taxonomy-metadata/">Taxonomy Metadata plugin</a> for handling header images for categories and tags.


== Installation ==

After you've downloaded and extracted the files:

1. Upload the complete 'unique-headers' folder to the '/wp-content/plugins/' directory OR install via the plugin installer
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Install and activate the <a href="http://wordpress.org/extend/plugins/taxonomy-metadata/">Taxonomy Metadata plugin</a> to add support for changing images for category listings.
4. And yer done!

Now you will see sections for changing your custom headers on the posts, pages and category listings.

Visit the <a href="http://geek.ryanhellyer.net/products/unique-headers/">Unique Headers Plugin</a> for more information.


== Frequently Asked Questions ==

= I can't change the image on my categories/tags, what's wrong? =

You need to install the <a href="http://wordpress.org/extend/plugins/taxonomy-metadata/">Taxonomy Metadata plugin</a>. WordPress does not support
the taxonomy meta data which this plugin needs to use to store the custom header image URL. As soon as you install that plugin, the category/tag image
functionality should begin working.


= I set an image, but now I can't change it :/ =
This is caused by a bug in a code library we've used. To work around this, you need to remove the image before adding a new one.


= Where's the settings page? =

There isn't one.


= Does it work in older versions of WordPress? =

Probably, but we only support the latest version of WordPress.


= I need custom functionality. Can we pay you to build it for us? =

No, I'm too busy. Having said that, if you are willing to pay me a small fortune then I could <a href="http://geek.ryanhellyer.net/contact/">probably be persuaded</a>.


== Screenshots ==

1. The custom image uploader for posts/pages, when using the <a href="http://geek.ryanhellyer.net/products/unique-headers/">Unique Headers Plugin</a>
2. The custom image uploader for categories, when using the <a href="http://geek.ryanhellyer.net/products/unique-headers/">Unique Headers Plugin</a> and <a href="http://wordpress.org/extend/plugins/taxonomy-metadata/">Taxonomy Metadata plugin</a>


== Changelog ==

Version 1.3: Total rewrite to use custom built in system for media uploads. Also adapted taxonomies to use ID's and added support for extra post-types.<br />
Version 1.2: Converted to use the class from the Multiple Featured Images plugin<br />
Version 1.1: Added support for tags <br />
Version 1.0.4: Added support for displaying a category specific image on the single post pages<br />
Version 1.0.3: Correction for $new_url for categories<br />
Version 1.0.2: Bug fix to allow default header to display when no category specified<br />
Version 1.0.1: Bug fixes for post/page thumbnails<br />
Version 1.0: Initial release<br />


= Credits =

Thanks to the following for help with the development of this plugin:<br />
* <a href="http://onmytodd.org">Todd</a> - Assistance with implementing support for tags<br />
* <a href="http://www.dss.dep.no/">Norwegian Government Administration Services (G.A.S.)</a><br />

=== Unique Headers ===
Contributors: ryanhellyer
Tags: custom-header, header, headers, images, page, post, plugin, image, images, categories, gallery, media, header-image, header-images, taxonomy, tag, category, posts, pages, taxonomies, post, page, unique, custom
Donate link: https://geek.hellyer.kiwi/donate/
Requires at least: 4.3
Tested up to: 4.7
Stable tag: 1.7.2



Adds the ability to use unique custom header images on individual pages, posts or categories or tags.

== Description ==

= Features =
The <a href="https://geek.hellyer.kiwi/products/unique-headers/">Unique Headers Plugin</a> adds a custom header image box to the post/page edit screen. You can use this to upload a unique header image for that post, or use another image from your WordPress media library. When you view that page on the front-end of your site, the default header image for your site will be replaced by the unique header you selected.

This functionality also works with categories and tags.

= Requirements =
You must use a theme which utilizes the built-in custom header functionality of WordPress. If your theme implement it's own header functionality, then this plugin will not work with it.

= Paid WordPress development =
If you would like to pay for assistance, additional features to be added to the plugin or are just looking for general WordPress development services, please contact me via <a href="https://ryan.hellyer.kiwi/contact/">my contact form</a>.


== Installation ==

After you've downloaded and extracted the files:

1. Upload the complete 'unique-headers' folder to the '/wp-content/plugins/' directory OR install via the plugin installer
2. Activate the plugin through the 'Plugins' menu in WordPress
4. And yer done!

Now you will see a new custom header image uploader whilst editing posts, pages, tags or categories on your site.

Visit the <a href="https://geek.hellyer.kiwi/products/unique-headers/">Unique Headers Plugin</a> for more information.


== Frequently Asked Questions ==

= I upgraded to WordPress 4.4 and the taxonomy meta plugin broke. What should I do? =
Older versions of WordPress required the taxonomy meta data plugin to add support for categories and tags. However, that functionality was rolled into the core of WordPress 4.4 and the old plugin stopped working with no upgrade path. You can simply delete the plugin, and your site will behave as normal, but the old header images for categories and tags will be missing. To work around this problem, please <a href="https://wordpress.org/support/topic/wordpress-44-fatal-error?replies=9#post-7762404">follow the instructions in this helpful support thread</a> for that plugin. Please note that I am not connected with the taxonomy meta data plugin and can not provide any assistance with it.

= I set a category header image, but why are my individual posts not showing that header image? =
Setting a category (or other taxonomy) header image, only causes that header image to show on the category page itself. It does not make the header image show on the single posts of that category.

To add this functionality, please install the <a href="https://geek.hellyer.kiwi/plugins/unique-headers-single-posts/">Unique Headers single posts extension plugin</a>.

= Your plugin doesn't work =
Actually, it does work ;) The problem is likely with your theme. Some themes have "custom headers", but don't use the built-in WordPress custom header system and will not work with the Unique Headers plugin because of this. It is not possible to predict how other custom header systems work, and so those can not be supported by this plugin. To test if this is the problem, simply switch to one of the default themes which come with WordPress and see if the plugin works with those, if it does, then your theme is at fault.

= My theme doesn't work with your plugin, how do I fix it? =
This is a complex question and not something I can teach in a short FAQ. I recommend hiring a professional WordPress developer for assistance, or asking the developer of your theme to add support for the built-in WordPress custom header system.

= Does it work with custom post-types? =

Yes, as of version 1.5, support for publicly viewable custom post-types was added by default.

= Does it work with taxonomies? =

Yes, as of version 1.5 of the Unique Headers plugin and version 4.4 of WordPress, support for all publicly viewable custom taxonomies was added by default.


= Where's the plugin settings page? =

There isn't one.


= Other plugins work out the width and height of the header and serve the correct sized header. Why doesn't your plugin do that? =

I prefer to allow you to set the width and height yourself by opening a correct sized image. This allows you to provide over-resolution images to cater for "retina screen" and zoomed in users. Plus, it allows you to control the compression and image quality yourself. Neither route is better in my opinion. If you require this functionality, please let me know though, as if most people prefer the other route, then I may change how the plugin works. I suspect most people won't care either way though.


= Does it work in older versions of WordPress? =

Mostly, but I only actively support the latest version of WordPress. Support for older versions is purely by accident. Versions prior to 4.4 will definitely not work with categories and tags.


= I need custom functionality. Can we pay you to build it for us? =

Yes. Just send me a message via <a href="https://ryan.hellyer.kiwi/contact/">my contact form</a> with precise information about what you require.



== Screenshots ==

1. The new meta box as added to the posts/pages screen
2. The custom header image uploader for adding new header images
3. The new meta box for categories and tags.


== Changelog ==

= 1.7.2 =
* Bug fix for custom taxonomies

= 1.7.1 =
* Bug fix to make srcset work correctly on regular header images

= 1.7 =
* Added support for srcset.
* Confirmed support for TwentySixteen theme.

= 1.6.1 =
* Added checks in file to see if WordPress is loaded.
* Hooking class instantiation in later, due to taxonomies sometimes not being loaded in time.

= 1.6 =
* Removed admin notice from everywhere but the plugins page.

= 1.5.3 =
* Fixing flawed bug fix from version 1.5.2.

= 1.5.2 =
* Fixing bug reported by multiple users, which caused PHP errors on some setups.

= 1.5.1 =
* Overhauled outdated FAQ section of readme.

= 1.5 =
* Introduced unlimited taxonomy support.
* When using a blog page set to a static page URL, the image from the static pages custom header will be used.
* Adding support for all publicly viewable post-types.
* Adding support for all publicly viewable taxonomies.

= 1.4.8 =
* Fixing a bug triggered by WordPress assigning non-URL's as the URL.

= 1.4.7
* Setting a more sane plugin review time.

= 1.4.6 =
* Fixing bug with handling taxonomies. Added plugin review notice back, but without the non-existent MONTH_IN_SECONDS constant.

= 1.4.5 =
* Removing plugin review notice due to unsolvable errors.

= 1.4.4 =
* Adding plugin review class back, with correct time stamp set.

= 1.4.3 =
* Temporarily removing plugin review class until bugs are fixed.

= 1.4.2 =
* Adding a plugin review class.

= 1.4.1 =
* Instantiating the plugin later (allows for adding additional post-types in themes).

= 1.4 =
* Adding backwards compatibility to maintain header images provided by the Taxonomy metadata plugin.

= 1.3.12 =
* Added French language translation.

= 1.3.11 =
* Moved instantiation and localization code into a class.

= 1.3.10 =
* Added Deutsch (German) language translation.

= 1.3.9 =
* Fixing error which caused header images to disappear on upgrading (data was still available just not accessed correctly).

= 1.3.8 =
* Modification translation system to work with changes on WordPress.org.

= 1.3.7 =
* Addition of Spanish translation.
 
= 1.3.1 =
* Adjustment to match post meta key to other plugins, for compatibilty reasons.

= 1.3 =
* Total rewrite to use custom built in system for media uploads. Also adapted taxonomies to use ID's and added support for extra post-types and taxonomies.

= 1.2 =
* Converted to use the class from the Multiple Featured Images plugin.

= 1.1 =
* Added support for tags.

= 1.0.4 =
* Added support for displaying a category specific image on the single post pages.

= 1.0.3 =
* Correction for $new_url for categories.

= 1.0.2 =
* Bug fix to allow default header to display when no category specified.

= 1.0.1 =
* Bug fixes for post/page thumbnails.

= 1.0 =
* Initial release.


= Credits =

Thanks to the following for help with the development of this plugin:<br />
* <a href="http://www.redactsolutions.co.uk">redactuk - Assistance with debugging.
* <a href="http://www.datamind.co.uk/">crabsallover - Assitance with debugging.
* <a href="http://onmytodd.org">Todd</a> - Assistance with implementing support for tags.
* <a href="http://westoresolutions.com/">Mariano J. Ponce</a> - Spanish translation.
* <a href="http://www.graphicana.de/">Tobias Klotz</a> - Deutsch (German) language translation.
* <a href="http://nakri.co.uk/">Nadia Tokerud</a> - Proof-reading of Norsk Bokmål (Norwegian) translation.
* <a href="http://bjornjohansen.no/">Bjørn Johansen</a> - Proof-reading of Norwegian Bokmål translation.
* <a href="https://www.facebook.com/kaljam/">Karl Olofsson</a> - Proof-reading of Swedish translation.
* <a href="http://www.jennybeaumont.com/">Jenny Beaumont</a> - French translation.

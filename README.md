# Unique Headers

Add unique custom header images to individual pages, posts, categories, or tags.


## Installation

After you've downloaded and extracted the files:

1. Upload the complete 'unique-headers' folder to the '/wp-content/plugins/' directory OR install via the plugin installer
2. Activate the plugin through the 'Plugins' menu in WordPress
4. And yer done!

Now you will see a new custom header image uploader whilst editing posts, pages, tags or categories on your site.

Visit the <a href="https://geek.hellyer.kiwi/products/unique-headers/">Unique Headers Plugin</a> for more information.


## Development

This section is only relevant for developers building the plugin from source (e.g. cloning from GitHub). If you installed via WordPress.org, the plugin is ready to use — no build steps required.

The plugin uses Composer for PHP autoloading and dependencies, npm for JavaScript tooling, and TypeScript for admin JavaScript.

To set up a development environment:

1. Install PHP dependencies: <code>composer install</code>
2. Install JavaScript dependencies: <code>npm install</code>
3. Compile TypeScript to JavaScript: <code>npm run build</code>

To run tests and analysis:

- PHPCS (PSR-12 coding standards): <code>composer phpcs</code>
- Auto-fix PHPCS violations: <code>composer phpcbf</code>
- PHP-CS-Fixer (dry-run): <code>composer cs</code>
- PHP-CS-Fixer (auto-fix): <code>composer cs:fix</code>
- PHPStan static analysis: <code>composer phpstan</code>
- PHPUnit unit tests: <code>composer test</code>
- TypeScript type checking: <code>npm run typecheck</code>

TypeScript source files are in <code>src/ts/</code>. The compiled output is written to <code>assets/admin.js</code> and is what WordPress loads on the admin side.

### Credits

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


## Frequently Asked Questions

### I set a category header image, but why are my individual posts not showing that header image?

Setting a category (or other taxonomy) header image, only causes that header image to show on the category page itself. It does not make the header image show on the single posts of that category.

To add this functionality, please install the <a href="https://geek.hellyer.kiwi/plugins/unique-headers-single-posts/">Unique Headers single posts extension plugin</a>.

### Your plugin doesn't work

Actually, it does work ;) The problem is likely with your theme. Some themes have "custom headers", but don't use the built-in WordPress custom header system and will not work with the Unique Headers plugin because of this. It is not possible to predict how other custom header systems work, and so those can not be supported by this plugin. To test if this is the problem, simply switch to one of the default themes which come with WordPress and see if the plugin works with those, if it does, then your theme is at fault.

### My theme doesn't work with your plugin, how do I fix it?

This is a complex question and not something I can teach in a short FAQ. I recommend hiring a professional WordPress developer for assistance, or asking the developer of your theme to add support for the built-in WordPress custom header system.

### Does it work with custom post-types?

Yes, as of version 1.5, support for publicly viewable custom post-types was added by default.

### Does it work with taxonomies?

Yes, as of version 1.5 of the Unique Headers plugin, support for all publicly viewable custom taxonomies was added by default.

### Where's the plugin settings page?

There isn't one.

### Other plugins work out the width and height of the header and serve the correct sized header. Why doesn't your plugin do that?

I prefer to allow you to set the width and height yourself by opening a correct sized image. This allows you to provide over-resolution images to cater for "retina screen" and zoomed in users. Plus, it allows you to control the compression and image quality yourself. Neither route is better in my opinion. If you require this functionality, please let me know though, as if most people prefer the other route, then I may change how the plugin works. I suspect most people won't care either way though.

### Does it work in older versions of WordPress?

Mostly, but I only actively support the latest version of WordPress. Support for older versions is purely by accident.

### I need custom functionality. Can we pay you to build it for us?

Yes. Just send me a message via <a href="https://ryan.hellyer.kiwi/contact/">my contact form</a> with precise information about what you require.


## Architecture

The plugin uses:

- **PSR-4 autoloading** - classes in `src/` are autoloaded via Composer under the `RyanHellyer\UniqueHeaders` namespace.
- **Inpsyde Modularity** - the plugin is structured as two modules implementing `ExecutableModule`, bootstrapped via the library's `Package` class.
- **TypeScript** - admin JavaScript is written in TypeScript and compiled to ES5 with esbuild.

```
.
+-- .github/workflows/ci.yml     # GitHub Actions CI
+-- assets/
|   +-- admin.css                # Admin styles
|   +-- admin.js                 # Compiled JS (from src/ts/admin.ts)
+-- bin/generate-readme.php      # README generator
+-- composer.json
+-- index.php                    # Plugin entry point, boots Modularity Package
+-- phpcs.xml.dist               # PHP_CodeSniffer configuration
+-- phpstan.neon                 # PHPStan configuration
+-- readme.txt                   # WordPress.org plugin readme
+-- src/
|   +-- AdminModule.php          # Admin meta box module
|   +-- AttachmentHelper.php     # Attachment helper service
|   +-- DisplayModule.php        # Front-end display module
|   +-- DotorgPluginReview.php   # Review notice class
|   +-- ts/
|       +-- admin.ts             # TypeScript source
+-- tests/
|   +-- SmokeTest.php            # Smoke tests
|   +-- bootstrap.php            # WordPress function stubs
+-- views/
    +-- meta-box.php             # Shared partial template for image picker
```

## Quality

| Tool | Command | Purpose |
|---|---|---|
| PHP_CodeSniffer | `composer phpcs` | Sniffs for PSR-12 violations |
| PHP_CodeSniffer | `composer phpcbf` | Auto-fixes PSR-12 violations |
| PHP-CS-Fixer | `composer cs` | Dry-run style check |
| PHP-CS-Fixer | `composer cs:fix` | Auto-fixes style issues |
| PHPStan | `composer phpstan` | Static analysis at level 6 |
| PHPUnit | `composer test` | Unit tests |
| TypeScript | `npm run build` | Compile admin.ts to admin.js |
| TypeScript | `npm run typecheck` | Type-check admin.ts |

All PHP code uses `declare(strict_types=1)` and follows PSR-12.

## Contributing

1. Clone the repository
2. Run `composer install` and `npm install`
3. Make your changes in `src/` and/or `src/ts/`
4. Run the quality tooling:

   ```bash
   composer phpcs
   composer phpstan
   composer test
   npm run typecheck
   ```

5. Run `npm run build` if you changed the TypeScript
6. Run `composer generate-readme` to regenerate this file
7. Submit a pull request

## Description

This plugin has been actively maintained since 2012 and is used on over 20,000 websites worldwide.

### Features

The <a href="https://geek.hellyer.kiwi/products/unique-headers/">Unique Headers Plugin</a> adds a custom header image box to the post/page edit screen. You can use this to upload a unique header image for that post, or use another image from your WordPress media library. When you view that page on the front-end of your site, the default header image for your site will be replaced by the unique header you selected.

This functionality also works with categories and tags.

### Requirements

You must use a theme which utilizes the built-in custom header functionality of WordPress. If your theme implement it's own header functionality, then this plugin will not work with it.

### Paid WordPress development

If you would like to pay for assistance, additional features to be added to the plugin or are just looking for general WordPress development services, please contact me via <a href="https://ryan.hellyer.kiwi/contact/">my contact form</a>.


## Screenshots

### 1. The new meta box as added to the posts/pages screen

[missing image]

### 2. The custom header image uploader for adding new header images

[missing image]

### 3. The new meta box for categories and tags.

[missing image]


## Changelog

### 2.1.3 - 2026-05-10

* Fix: Resolved issue where custom header images would not display on WooCommerce product category pages due to taxonomy caching at plugin init time
* Fix: Restored ability to upload video headers in the customizer

### 2.1.2 - 2026-05-08

* Fix: Removed strict string type declarations from postHeaderImageFilter and taxonomyHeaderImageFilter to prevent TypeError when WordPress Customizer passes an array through the theme_mod_header_image filter

### 2.1.1 - 2026-05-07

* Fixing version number
* Shortening readme description to meet WordPress.org requirements

### 2.1 - 2026-05-07

* Fixed PSR-4 container namespacing bug

### 2.0.1 - 2026-05-01

* CI: restricted PHP version matrix to 8.4 and 8.5
* CI: fixed lock file resolution issue for different PHP versions
* Docs: added plugin longevity note (since 2012, 20,000+ sites)
* Docs: reorganized README sections for better user experience

### 2.0 - 2026-05-01

* Major architectural overhaul: migrated to PSR-4 autoloading with Composer and Inpsyde Modularity
* Replaced legacy classes with AdminModule, DisplayModule, and AttachmentHelper service
* Rewrote admin JavaScript as ES6 class without jQuery
* Migrated admin JavaScript to TypeScript with esbuild build pipeline
* Added shared view partial for image meta box
* Added PHP_CodeSniffer (PSR-12), PHPStan (level 6), PHP-CS-Fixer, and PHPUnit tooling
* Added unit tests with WordPress function stubs
* Added GitHub Actions CI workflow (PHP 8.2-8.5)
* Security: added capability checks to savePost and storeTaxonomyData
* Security: changed attachment ID sanitization from sanitize_text_field to absint
* Performance: scoped admin asset enqueues to post and term edit screens only
* Performance: consolidated taxonomy attachment lookup into shared helper
* Bug fix: prevented Shortcode UI plugin crash by setting wpActiveEditor dummy
* Bug fix: corrected broken URL check in admin.js media uploader

### 1.9.4 - 2026-04-29

* Version number bump

### 1.9.3 - 2023-10-26

* Fixed a bug in the DotOrg_Plugin_Review() class. The switch to === broke the no debug check, so this has been fixed.

### 1.9.2 - 2023-10-26

* Fixed a bug in the nonce system for taxonomy terms.

### 1.9.1 - 2023-10-26

* Temporarily preventing the DotOrg_Plugin_Review() class from loading due to a bug report relating to it. It will be re-added later.

### 1.9 - 2023-10-20

* Updated WordPress coding standards support

### 1.8.3 - 2023-09-14

* Confirmed support for newer WordPress versions
* Added Composer support

### 1.8.2 - 2022-10-14

* Bug fix for when array value doesn't exist

### 1.8.1 - 2022-01-16

* Bug fix for offset value error

### 1.8 - 2021-04-11

* Bug fix for "WP_Scripts::localize was called" notice

### 1.7.12 - 2021-04-06

* Bug fix for when no object set in Unique_Headers_Taxonomy_Header_Images::modify_header_image_data()

### 1.7.11 - 2020-03-08

* Version bump to force dot org update

### 1.7.10 - 2017-12-07

* Correctly checking for presence of object before setting width and height

### 1.7.9 - 2017-07-16

* Checking for presence of object before setting width and height

### 1.7.8 - 2017-03-28

* Fixing filter bug

### 1.7.7 - 2017-03-28

* Fixing filter bug

### 1.7.6 - 2017-03-28

* Fixing bug in taxonomy setup

### 1.7.5 - 2017-03-27

* Fixing bug in taxonomy setup

### 1.7.4 - 2017-03-27

* Fixing bug in taxonomy setup

### 1.7.3 - 2017-03-25

* Bug fix for srcset with taxonomies
* Changed to class autoloader
* Moved instantiation class to it's own file
* Added extendible core class

### 1.7.2 - 2017-03-25

* Bug fix for custom taxonomies

### 1.7.1 - 2017-03-25

* Bug fix to make srcset work correctly on regular header images

### 1.7 - 2017-03-25

* Added support for srcset.
* Confirmed support for TwentySixteen theme.

### 1.6.1 - 2016-10-26

* Added checks in file to see if WordPress is loaded.
* Hooking class instantiation in later, due to taxonomies sometimes not being loaded in time.

### 1.6 - 2016-10-26

* Removed admin notice from everywhere but the plugins page.

### 1.5.3 - 2016-06-19

* Fixing flawed bug fix from version 1.5.2.

### 1.5.2 - 2016-06-19

* Fixing bug reported by multiple users, which caused PHP errors on some setups.

### 1.5.1 - 2016-04-15

* Overhauled outdated FAQ section of readme.

### 1.5 - 2016-03-22

* Introduced unlimited taxonomy support.
* When using a blog page set to a static page URL, the image from the static pages custom header will be used.
* Adding support for all publicly viewable post-types.
* Adding support for all publicly viewable taxonomies.

### 1.4.8 - 2016-03-20

* Fixing a bug triggered by WordPress assigning non-URL's as the URL.

### 1.4.7 - 2015-12-13

* Setting a more sane plugin review time.

### 1.4.6 - 2015-10-31

* Fixing bug with handling taxonomies. Added plugin review notice back, but without the non-existent MONTH_IN_SECONDS constant.

### 1.4.5 - 2015-10-29

* Removing plugin review notice due to unsolvable errors.

### 1.4.4 - 2015-10-28

* Adding plugin review class back, with correct time stamp set.

### 1.4.3 - 2015-10-28

* Temporarily removing plugin review class until bugs are fixed.

### 1.4.2 - 2015-10-27

* Adding a plugin review class.

### 1.4.1 - 2015-10-26

* Instantiating the plugin later (allows for adding additional post-types in themes).

### 1.4 - 2015-08-21

* Adding backwards compatibility to maintain header images provided by the Taxonomy metadata plugin.

### 1.3.12 - 2015-03-24

* Added French language translation.

### 1.3.11 - 2015-02-19

* Moved instantiation and localization code into a class.

### 1.3.10 - 2015-02-18

* Added Deutsch (German) language translation.

### 1.3.9 - 2015-01-04

* Fixing error which caused header images to disappear on upgrading (data was still available just not accessed correctly).

### 1.3.8 - 2014-12-21

* Modification translation system to work with changes on WordPress.org.

### 1.3.7 - 2014-12-20

* Addition of Spanish translation.

### 1.3.1 - 2014-12-19

* Adjustment to match post meta key to other plugins, for compatibilty reasons.

### 1.3 - 2014-11-03

* Total rewrite to use custom built in system for media uploads. Also adapted taxonomies to use ID's and added support for extra post-types and taxonomies.

### 1.2 - 2014-07-13

* Converted to use the class from the Multiple Featured Images plugin.

### 1.1 - 2014-04-19

* Added support for tags.

### 1.0.4 - 2013-02-14

* Added support for displaying a category specific image on the single post pages.

### 1.0.3 - 2012-12-09

* Correction for $new_url for categories.

### 1.0.2 - 2012-12-02

* Bug fix to allow default header to display when no category specified.

### 1.0.1 - 2012-11-07

* Bug fixes for post/page thumbnails.

### 1.0 - 2012-08-22

* Initial release.

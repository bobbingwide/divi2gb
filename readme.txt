=== divi2gb ===
Contributors: bobbingwide
Donate link: https://www.oik-plugins.com/oik/oik-donate/
Tags: Divi, Gutenberg, converter
Requires at least: 6.0.1
Tested up to: 6.0.1
Stable tag: 0.0.0

Divi to Gutenberg converter.

== Description ==
Use the Divi to Gutenberg converter to help you to migrate content from a Divi site to a block based theme.

The plugin converts Divi's shortcodes to plain HTML, with some blocks. 

- To get started install and activate the divi2gb plugin.
- Install and activate a block based full site editing theme.
- Visit https://example.com/wp-admin/admin-ajax.php?action=divi2gb
- Select the page you want to convert.

For each page to convert: 

- Click on the Convert link 
- Choose View Source on the generated page ( the AJAX output )
- Copy the HTML you need
- Go back to the original AJAX page
- Click on the Edit link for the page you chose to convert
- Remove the original Classic block
- Paste the HTML as plain text
- Convert the pasted content to blocks

While it's possible to convert a site in situ it's much better to leave the original site unchanged
and to perform the conversion on a new site.
Use the WordPress importer to import the pages to the new site.

== Installation ==
1. Upload the contents of the divi2gb plugin to the `/wp-content/plugins/divi2gb' directory
1. Activate the divi2gb plugin through the 'Plugins' menu in WordPress
1. Visit the URL https://example.com/wp-admin/admin-ajax.php?action=divi2gb
1. It should list the pages in the site, some of which may have been built with Divi.

== Screenshots ==
1. Page selection
2. Converted page 
3. View source of the converted page 

== Upgrade Notice ==
= 0.0.0 = 
Prototype code as proof of concept.


== Changelog ==
= 0.0.0 = 
* Added: Create a simple Divi to Gutenberg converter #1
* Tested: With WordPress 6.0.1
* Tested: With PHP 8.0

== Further reading ==
For more information see the GitHub repository https://github.com/bobbingwide/divi2gb
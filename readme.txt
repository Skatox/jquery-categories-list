=== JS Categories List Widget ===
Contributors: skatox
Donate link: https://skatox.com/blog/jquery-categories-list-widget/
Tags: javascript, categories, gutenberg, block, widget
Requires at least: 6.1
Tested up to: 6.5
Stable tag: 4.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple Gutenberg block and JS widget (can be called from posts) for displaying categories in a list with some effects.

== Description ==

This plugin provides a widget and a filter to display a collapsible list of categories in your sidebar or posts by using vanilla Javascript.

= Features =
 1. Support for Gutenberg blocks. Add it to any FSE theme or Gutenberg compatible theme.
 1. Display a collapsed list of your archives to reduce space.
 1. Uses vanilla JS to add effects and to be compatible with all browsers.
 1. Select the symbol for expanding/collapsing categories
 1. Select categories to exclude, so it shows only the categories you want.
 1. Autoexpand selected category (including it parent or child).
 1. Support for multiple instances.
 1. Simple layout configuration
 1. Shortcode support  *[JsCategoriesList]*
 1. Translated to Spanish, Italian Russian, Slovak, Czech.
 1. Compatible with most JS cache and minify plugins.
 1. And more to come...

== Installation ==

1. Make a directory `jquery-categories-list` under `/wp-content/plugins/`
1. Upload  all downloaded files to `/wp-content/plugins/jquery-categories-list/`
1. Activate plugin at the plugins section.
1. Go to Presentation -> Widgets and drag the JS Categories List to your sidebar and configure it, if you want to display it inside a post then write [JsCategoriesList] at the location where it will be shown and save it.

== Configuration ==

* Title: title of the widget.
* Trigger Symbol:  characters to be displayed as a bullet.
* Symbol position: where to put the expand/collapse symbol.
* Effect: JS's effect to use.
* Order by: display order of categories.
* Expand: if list should be expanded or callapsed by default.
* Show number of posts: display how many post are published in the category.
* Show empty categories: display categories with no posts.
* Categories to include: categories to be included from the list.
* Categories to exclude: categories to be excluded from the list.

== Frequently Asked Questions ==

= Why there are 2 widgets? =

Since version 4.0 the widget was migrated to a Gutenberg block. So there will be a Gutenberg block with the most modern code and compatibility and a legacy version that is 100% made in PHP to keep compatibility with older installations.

= Why this plugin is not working? =

By support experience, like 99% of problems are due to:

* There's a Javascript error caused by other plugin and it stops any further code execution, check your browser's logs to find the problem and deactivate the conflict plugin.
* Your template doesn't have a wp_footer() function, this plugin requires this function to load JS code at the end of the website to improve speed.
* You're using a plugin that removes Wordpress' JS version and inserts an old one.

= How I can send you a translation? =

Send me the translated .mo file to migueluseche(a)skatox.com and indicate the language, I can read english or spanish, so please write me on these languages.

= Can I use images as bullets or trigger symbols? =

Yes, select 'Empty Space' as trigger symbol and Save, then you can add any custom background using CSS,
just play with the widget's classes .jaw_category, .jcl_symbol, .jcl_link.

= Can I show this list inside posts? =

Yes, only write [jQueryCategoriesList] anywhere inside a post or page's contest and it will be
replaced for the archive list when rendering the content.  You can add the following parameters to change its behavior:

1. **showcount** ( boolean ): Select if you want to show the count post inside category's name
1. **'layout'** ( "right", "left" ): where to display the symbol
1. **ex_sym**: the expansion symbol.
1. **con_sym**: the collapse symbol.
1. **effect** ("none", "slide", "fade"): the JS effect to implement.
1. **orderby** ("name","id", "count"): how to order categories.
1. **orderdir** ("ASC", "DESC"): the direction to order categories.
1. **show_empty'** ( boolean ): show categories with no posts.
1. **expand** ("all", "none"): expand or collapase all categories.
1. **exclude**: IDs (comma separated) of the categories to exclude.

= How can i add multiples instances? =

Since 1.2 there's a trick to do it, just add a new Text widget only with  [jQueryCategoriesList] as content (without quotes) then
when looking the site it will have a new copy of the widget. For different configuration for each instance, just pass the following parameters to control it:
1. showcount (boolean): Select if the number of posts should be displayed
1. orderby ( "name", "ID", "count") : Select the field to sort the categories: by name, by its id or by the number of posts.
1. orderdir (asc or desc): The direction to sort the categories.

For example, if you write:
*[jQueryCategoriesList showcount=1 orderby=count order=desc  ex_sym=+ con_sym=- fx_in=fadeIn exclude=22,32] *

It will show the categories with the count of posts, order by its number of post in a descendent direction, the simbols are + and - and the effect is fadeIn, categories with IDs 22 and 32 will be excluded. You can check source code for more information.

= How I contribute to this plugin? =

By using it, recommending it to other users, giving it 5 starts at plugin's wordpress page, suggesting features or coding new features and finally by **DONATING** using plugin's website's donate link,
with the donation I can spend more time on this project.

= How can i add multiples instances? =

Since 2.0 you can add as many instances as you want, but there's another way to do it, just add a new Text widget only with the shortcode [jQueryCategoriesList] then it will have a new copy of the widget.

= Can I have different instances with different configuration? =

Since 2.0 it's possible. Each instance has its own configuration. Shortcode widgets are controlled by shortcode attributes.


== Screenshots ==

1. Here you can see a list of categories, those with sub-categories will habe a link on the right to expand its childs.
2.  Here you can see a list of categories and its sub-categories expanded.

== Change Log ==

= 4.0.1 =
* Added support for WordPress Playground.
* Updating tests to the latest react-testing-library version.
* Updating JS dependencies to latest version. This will fix security issues and improve performance.
* Legacy version: added `jcl_widget_title` hook to allow changing the widget's title (helpful for translations).

= 4.0.0 =
* Code migrated to Gutenberg block. Now you can use this plugin as a Gutenberg block on any modern theme.
* The block version reads the categories dynamically. It saves bandwidth and reduces old DOM size on old sites.
* JS code is only loaded if there’s a widget or block is added to page. No more always loading JS and CSS code.
* Deprecated old php version. It will only be on maintenance mode and new Gutenberg block will be supported.
* Improved automated tests and code, to make future versions easier to develop.

= 3.0 =
* Solved accordion bug with Intenet Explorer 8

= 2.2.2 =
* Solved expansion/contraction bug when using sub-categories.
* Added support for HTTPS, now the plugin generates the correct link if HTTPS is being used, thanks to **bridgetwes** for the patch.
* Added ordering by category's slug.


= 2.2.1 =
* Fixed the problem of a warning message being shown when a post had a single category.
* Added a CSS class to **li** elements with child categories.
* Improved category HTML generation code to make future modifications easier.

= 2.2. =
* Fixed category's post count when excluding some categories, now it uses the same algorithm as the standard WP widget.
* Finally the list expands to the opened/click category! It remembers where you clicked! Just select the expand option to 'Clicked Category'.
* Wrapped post count (if activated) inside a **span** to apply CSS rules.
* Added a active class for current category.
* Fixed some spanish translations.
* Plugin translated to Russian by Матвеев Валерий.

= 2.1 =
* Added option to exclude categories when using shortcodes, just add categorie's ID separated by commas in the exclude attribute.
* Solved bug of not including JS file when using a filter without any widget.
* Solved bug of not including JS in some WP installlations under Windows.

= 2.0 =
* Huge update thanks to donations! If you upgrade to this version you'll NEED to configurate the widget AGAIN, due to architecture rewriting configuration may get lost.
* Added support for multiples instances, finally you can have as many widgets as you want without any hack :)
* Added support for dynamic widgets
* Added an option to not have any effect when expanding or collapsing.
* Added an option to select if you want the symbol on the left or on the right.
* Added an option to autoexpand all categories by default.
* Removed dynamic generation of the JS file, now you don't need write permissions on the folder.
* Rewroted JS code, now it is a single JS file for all instances, improved perfomance and compatible with all cache plugins.
* Updated translation files for Spanish, Czech, Slovak and Italian.

= 1.3.2 =
* Fixed some several bugs, thanks to Marco Lizza who reviewed the code and fixed them. Plugin should be more stable and won't throw errors when display_errors is on.
* Added italian translation.

= 1.3.1 =
* Added option to show or hide empty categories.
* Improved shortcode, now parameters accepts yes, no, 1, or 0 as input.

= 1.3 =
* Improved Javascript code (please save again the configuration to take effect)
* Better shortcode/filter support. now it has attributes for different behavior on instances. (There's no support for effect and symbol because it is managed through the JS filse )

= 1.2.5 =
* Fixing i18n bug due to new wordpress changes, now it loads your language (if it was translated) correctly.

= 1.2.4 =
* JS code is not generated dynamically, now it is generated in a separated file. For better performance and to support any minify plugins.

= 1.2.3 =
* Improved generated HTML code to be more compatible when JS is off, also helps to search engines to navigate through archives pages.
* Added Slovak translation.
* Cleaned code and make it more readable for future hacks from developers.

= 1.2.2 =
* Changed plugin's JS file loading to the footer, so it doesn't affect your site's loading speed.
* Added default value for widget's title. And it is included in translation files, so this can be used in multi-language sites.
* Plugin translated to Czech (CZ) thanks to Tomas Valenta.

= 1.2.1 =
* Solving sorting bug, now you can choose the categories display order.

= 1.2 =
* Added support for multiples instances (by writing [jQuery Categories List] on any Text widget).
* Fixed a bug when no categories were selected to be filtered.
* Improved compatibility with Wordpress 3.x.

= 1.1 =
* Added support to exclude categories when listing.

= 1.0 =
* Initial public version.

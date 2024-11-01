=== WoW Guild Retrieve ===
Contributors: manatrance
Tags: WoW,warcraft,guild,roster,mmorpg
Requires at least: 2.8
Tested up to: 4.1.0
Stable tag: 1.3.1

Show a roster of your World of Warcraft guild’s members on any page.

== Description ==

I was building a website for a World of Warcraft guild on the Wordpress platform. I needed to display a roster
of all the guild’s members. Rather than have the guild leader manually enter new members in a unique database,
I decided to create a plugin to handle the display of a roster automatically using WoW Armory data.

WoW Guild Retrieve asks for a realm name and guild name. Because it’s a shortcode, you can have multiple guild lists
even on the same page!

= Getting Started =

To use the plugin in any page or post, just include the following text in that page:

[wgr guildname="" realmname=""]

Between the quotes, add the appropriate names, like so:

[wgr guildname="My Guild Name" realmname="My Realm Name"]

It defaults to US servers. For EU servers, just add this:

[wgr guildname="My Guild Name" realmname="My Realm Name" region="eu"]

= Additional Options =

If you’d like to change the sorting, just use the following options:

`sorttype` - this changes the default column which the plugin sorts by. May be a number between 0 and 4.
`sortorder` - this changes the order in which the plugin sorts. May be either "asc" or "desc".

Also, there is this option:

`restrict` - if set to true, this will restrict the guild list to only level 90 players.

The last option:

`tablesize` - if set, this changes the default number of rows displayed.

An example of a shortcode that uses all of the options is here:

[wgr guildname="My Guild" realmname="My Realm" region="eu" sorttype="1" sortorder="desc" restrict="true" tablesize="25"]

= Changing Rank Names =

Also, you can change the names of the ranks displayed. This is done in the Options page, which also will explain the process. The Options page can be found under your Settings
tab in Wordpress.

At this time, custom names are universal, so if you have multiple guild rosters on one website they will all have the same rank names.

= Further Questions =

Note: if you are upgrading from a version earlier than 1.3.0, you will need to deactivate and then reactivate the plugin.

If you have any questions, don’t hesitate to drop by the plugin website here:

http://www.benovermyer.com/wow-guild-retrieve

== Changelog ==

= v1.3.1 =

* Changed roster to longtext to fix a bug

= v1.3.0 =

* Removed non-functioning analytics
* Cleaned up the code and fixed an obscure bug related to fetching Armory data
* Added basic caching

= v1.2.2 =

* Fixed Pandaren race identification (Blizzard changed their ID number)

= v1.2.1 =

* Fixed Pandaren image files

= v1.2.0 =

* Increased "restrict" from level 85 to 90
* Updated links to go to the new plugin website
* Added behind-the-scenes anonymous usage logging as a precursor to a new feature
* Updated documentation to be clearer

= v1.1.1 =

* Added the Pandaren race
* Added the Monk class

= v1.1.0 =

* Changed the data source to pull from the new Armory
* Added Achievement Points and guild level display

= v1.0.6 =

* Changed the plugin to point at the new Armory

= v1.0.5 =

* Added support for Cataclysm level limit and races

= v1.0.4 =

* Added options-based custom rank names

= v1.0.3 =

* Added custom rank names
* Added ability to change the default number of rows displayed

= v1.0.1 =

* Fixed a bug that displayed "Guild Leader" as "Rank 0"
* Fixed a bug that displayed incorrect or broken images

= v1.0.0 =

* Rebuilt the plugin to use shortcodes
* Now supports multiple guild lists
* Upgraded dataTables jQuery plugin to v1.6 from v1.4 beta 7

= v0.9.7 =

* Fixed bug in options that was preventing data display

= v0.9.6 =

* Added option to choose default sorting
* Added ability to restrict the roster to only level 80 players

= v0.9.5 =

* Added option to choose a light or dark styling
* Fixed a couple CSS bugs

= v0.9.4 =

* Fixed a bug with the WoW Armory lookup

= v0.9.3 =

* Added images and new styling.
* Added new div-based structure to aid in CSS styling.
* Fixed a bug with the CSS and javascript.

== Installation ==

1. Upload the contents of the zip to the ’/wp-content/plugins/wow-guild-retrieve’ directory
2. Activate the plugin through the ’Plugins’ menu in WordPress
3. Enter the shortcode for your guild on the page you want the roster to display (details in the Description page)

== Frequently Asked Questions ==

= When will you support caching? =

In version 2.0. It’s coming, I promise.

= Can I sort these columns? =

Yes! Just click on the column headers.

= Can I style the table with my own CSS? =

Of course. The id for the containing div is `guild-data-div`.

== Screenshots ==

1. A shot of the plugin in action

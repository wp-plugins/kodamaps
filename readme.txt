=== Kodamaps ===

Contributors: Tomoka Baba (@robox.org)
Tags: google maps, map, shortcode, address, post
Requires at least: 4.1.1
Tested up to: 4.1.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is possible to post the location information, and can be embedded google maps.

== Description ==

This plugin is possible to post the location information, and can be embedded google maps that set up a marker at the location by shortcode.

First, let's edit the post.
Enter an address or latitude and longitude to input area of Kodamaps.



How to embed is as follows:

`[kodamaps]`

You can decide whether put markers of all the posts or a marker of single post by specifying the type.

If you want to display all the posts, you can specify the center of the map:

`[kodamaps type="all" addr="San Francisco, California"]`

You can also set width, height and zoom:

`[kodamaps type="all" lat="37.77493" lng="-122.41942" width="300" height="200" zoom="11"]`

[This plugin is maintained on GitHub.](https://github.com/RoboxOrg/kodamaps)

= Arguments =

* type: "single" or "all". Default value is "single".
* width: Width of the map (px). Default value is "100%".
* height: Height of the map (px). Default value is "450px".
* zoom: Zoom of the map. Default value is "16".
* addr: Address of the map center. Can be used only type is "all".
* lat: Latitude of the map center. Can be used only type is "all".
* lng: Longitude of the map center. Can be used only type is "all".

== Installation ==

* Go to the plugins administration screen in your WordPress admin, click on Add New, search for "Kodamaps" and click on Install Now.

== Screenshots ==

1. Post the location information.
2. Embed the map.

== Changelog ==

= 0.1.0 =
The first release.

== Contact ==
GitHub Issues: https://github.com/RoboxOrg/kodamaps/issues

Email: baba@robox.org

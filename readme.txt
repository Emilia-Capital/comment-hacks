=== Yoast Comment Hacks ===
Contributors: joostdevalk, yoast
Tags: comments, spam, emails
Requires at least: 4.5
Tested up to: 4.8
Stable tag: 1.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make comments management easier by applying some of the simple hacks the Yoast team uses.

== Description ==

Make comments management easier by applying some of the simple hacks the Yoast team uses.

This plugin adds some small hacks around core WordPress comments to make them more bearable:

* Cleaner comment notification emails.
* The option to disallow comments below and above a certain length.
* The option to redirect first time commenters to a thank you page.
* An input field on the comment edit screen to change the comment parent ID.
* Links in the admin comments section to email individual commenters.
* A button in the WP toolbar to email all the commenters on a post.
* Adds a comment routing option. This adds a dropdown in a post's discussion settings, allowing the routing of comment emails to another user.

See the screenshots to get an even better idea of the plugins functionality.

== Installation ==

**Install through your backend**

1. Search for "yoast comment hacks", click install.
1. You're done.

**Install manually**

1. Download and unzip the plugin.
1. Upload the `yoast-comment-hacks` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Configure your settings on the Settings &rarr; Comment Hacks screen.

== Screenshots ==

1. Screenshot of a clean comment notification email.
2. The comment parent edit box.
3. The plugins admin settings.
4. The button on the frontend to email all the commenters on a post.
5. The link in the backend to email an individual commenters on a post.

== Changelog ==

= 1.4 =

* Enhancements:
    * Limit the roles shown in the comment notifications dropdown to roles that normally exist and can write. Introduces a new filter to allow expanding them.
    
= 1.3 =

* Enhancements:
    * Add option to restrict comments that are too long, next to too small.
    * Add `reply-to` header to comment notification and moderation emails, pointing to the post author.
    * Preserve the active tab when saving settings.
    * Remove `[...]` from pingback / trackback excerpt in cleaned emails as that's already included by core.
    * Replace link to ARIN with link to [ip-lookup.net](http://ip-lookup.net) for details about the IP.
    * Refactored code for readability and code quality.
    * Refactor upgrade routine to do less DB queries.

= 1.2 =

Fixes several issues:

* Differentiate between AJAX request and normal POST for nonce checking, fixes #7.
* Make sure comment type isn't empty in cleaned email, fixes #8.
* Allow setting the comment parent to 0, fixes #10.
* Prevents defaults from being reinstated, fixes #14.

Also:

* Adds translator comments to all strings with `sprintf` / `printf`.
* Updates to new version of Yoast i18n, in the process switching from `translate.yoast.com` to `translate.wordpress.org` and removing packaged translations.
* Added `yarn.lock` and removed no longer needed i18n grunt tasks.

= 1.1.1 =

* Add text domain so the plugin can be translated.

= 1.1 =

* Add comment routing option, adds a dropdown in a post's discussion settings, allowing the routing of comment emails to another user.

= 1.0 =

* Initial version.

=== Comment Hacks ===
Contributors: joostdevalk
Tags: comments, spam, emails
Text Domain: yoast-comment-hacks
Requires at least: 5.9
Tested up to: 6.4
Stable tag: 1.9.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires PHP: 7.4

Make comments management easier by applying the simple hacks Joost has gathered over the years.

== Description ==

Make comments management easier by applying the simple hacks Joost has gathered over the years.

This plugin adds some small hacks around core WordPress comments to make them more bearable:

* Cleaner comment notification emails.
* The option to enforce a comment policy: just create a comment policy page, toggle the option on and select it, and
commenters will have to accept your comment policy before being able to comment.
* The option to forward comments to an email address (for instance for your support team) and then trash them.
* The option to disallow comments below and above a certain length.
* The option to redirect first time commenters to a "thank you" page.
* An input field on the comment edit screen to change the comment parent ID.
* Links in the admin comments section to email individual commenters.
* A button in the WP toolbar to email all the commenters on a post.
* Adds a comment routing option. This adds a dropdown in a post's discussion settings, allowing the routing of comment emails to another user.

See the screenshots to get an even better idea of the plugins' functionality.

=== Have you found an issue? ===

If you have bugs to report, please go to [the plugin's GitHub repository](https://github.com/jdevalk/comment-hacks). For security issues, please use our [vulnerability disclosure program](https://patchstack.com/database/vdp/yoast-comment-hacks), which is managed by PatchStack. They will assist you with verification, CVE assignment, and, of course, notify us.


== Installation ==

**Install through your backend**

1. Search for "comment hacks", click install.
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

= 1.9.4 =

* Fix fatal due to wrong class import, props [@andizer](https://profiles.wordpress.org/andizer/).

= 1.9.3 =

* Fix bug where comment reroute recipient would not save.
* Added an option to disable the "Email all commenters" admin bar button.

= 1.9.2 =

* Fix missing autoloader

= 1.9 =

* Introduces a new option to the plugin: adding a comment policy was never easier than this: just create a comment
policy page, toggle the option on and select it, and commenters will have to accept your comment policy before being
able to comment.
* Fixes a bug where editing a comment on the quick edit screen would cause that comment to lose its parent.
* Enhances performance by preventing too frequent option updates.
* Remove all direct DB queries in favor of using WordPress core functions.

= 1.8.1 =

* Fixed a couple of PHP 7.4 related issues.

= 1.8 =

* Changed namespace to `JoostBlog`.
* Removed Yoast branding.
* Updated plugin to require PHP 7.4.

= 1.7 =

* Bugfixes:
    * Fixed: the "Email commenters" link would not be displayed in the WordPress admin bar and in the Comments list.
    * Fixed: the "Email commenters" link in the front end admin bar wouldn't work when jQuery wasn't enqueued.
    * Fixed: the notification emails for new comments would have incorrect content for the Author line and the text displayed before the comment.

= 1.6 =

* Fix language packs.

= 1.5 =

* Bugfixes:
    * Fixed: comment recipient dropdown would reset on reload of the page.
    * Fixed: admin bar CSS showing when no admin bar is showing.

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

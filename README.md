# Pressbooks HfH Plugin

This WordPress plugin extends the Pressbooks plugin with additional features used by the HfH Zuugs platform.

## Features

### Book Posts Password

This feature allows authors to easily set a single password for all of their book posts at `Organize > Book Posts Password`.

Implemented in `components/book_posts_password.php`.

### Course Progress

This feature allows users to track their reading progress. At the end of every chapter a button is displayed that allows the user to mark the chapter as read.
In the table of contents progress indicators are added to show which chapters have been marked. Additionally, a new progress page is added to the main menu with an overview of the reading progress across the entire book.

This feature can be activated at `Hfh > Pressbooks` by checking the `Show learning progress` checkbox.

Implemented in `components/course_progress.php`.

### Chapter Categories

This feature adds the default Wordpress Category page for posts of the type `chapter`. The category page is located at `Organize > Chapter Categories`. It also adds the metaboxes for category selection to the chapter editor page.

Implemented in `components/chapter_categories.php`.

### Editor Language Button

This feature adds a button to the Wordpress Text Editor that lets users specify a language for the selected part of their text.

Implemented in `components/quicktags.php`.

### Subscriber Management

Using the [Shibboleth plugin](https://wordpress.org/plugins/shibboleth/), users belonging to a certain organization can be granted subscriber permissions automatically. The groups being able to access a certain book can be selected at `Settings > Privacy`.

For this feature to work properly, a small modification needs to be made to the source code of the Shibboleth plugin.

Implemented in `components/user.php`.

### Custom Shortcodes

- `hfh_parts` and `hfh_chapters` provide a linked visualization of the parts and chapters of the book. The intent is that they are put on top of the page with `hfh_parts` before `hfh_chapters` if both are used.

- `hfh_confetti` adds a burst of confetti when the page is loaded.

Implemented in `components/shortcodes.php`.

### H5P Autosave Default Settings

If the h5p plugin is network activated when a new site is created, the h5p autosaving feature is enabled and the save frequency set to 3 seconds.

Implemented in `components/h5p.php`.

### H5P Details iFrame Resize Fix

If a h5p is inside a closed details element, the iframe's height will not be set correctly in Safari.
We fix this by calling a window resize event whenver a details element is toggled.

Implemented in `components/js/details_h5p_iframe_resize_fix.js`

### Gamipress Notifications H5P Fix

The Gamipress Notifications Plugin does not react to ajax requests made from inside a H5P iframe.
In order to still get live notifications we listen to the H5P ourselves and trigger the Gamipress listener.

Implemented in `components/js/gamipress_notifications_h5p_fix.js`

## Usage

Sass styles can be compiled with `sass components/scss:components/css --no-source-map`.

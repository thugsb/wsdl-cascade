# CSS and JS Setup
The slc.edu website uses [minify][https://code.google.com/p/minify/] to, well, minify the CSS and JS.
The mobile site simply lists each of the CSS and JS files that it includes, whereas the desktop site uses the groupsConfig.php to pull in groups.

The desktop sites' CSS comprises of the files in `/core/v5.0/css/a/`, which have been split up into separate files in order to make them easier to manage.
The mobile site has a single `slc.css` file, which is includes along with the twitter bootstrap files.
It would be good to split this into multiple files in the near future, as it is becoming very long.

Similarly, the desktop sites' JS files are to be found as individual files in `/core/v5.0/js/plugins`, whereas the mobile site has a single `plugins.js` file.
Again, the mobile plugins may want to be separated out in the near future.

# Cron Scripts

At the end of each day, a cron command runs to make sure the latest WSDL scripts have been pulled from github.
However, it should be noted that if there are changes to the github repository, then the scripts that run more frequently than once per day would need git to update manually, as and when the changes are made.
You can do this by running `cd /srv/www/htdocs/--_web-services/ && git pull origin master` from the server.

There are several cron scripts that run several times a day. For example, there are scripts that copy the structured data into metadata for news items and faculty.
The current days events are also imported every hour.

The catalogue course data is copied to metadata daily, and the events are archived daily.
Inactive faculty are archived weekly.
Future events are imported daily, but past events are only imported weekly.

Any of these scripts can be run manually from the server, or from a local clone of the github repo.
The _cron.php script will send an email in output, whereas running the scripts manually will give output in the browser.
Note that in order to run from cron, a credentials argument and file must be supplied, e.g. `c=/home/com/__credentials.php`.

# CSS and JS Setup
The slc.edu website uses [minify][https://code.google.com/p/minify/] to, well, minify the CSS and JS.
The mobile site simply lists each of the CSS and JS files that it includes, whereas the desktop site uses the groupsConfig.php to pull in groups.

The desktop sites' CSS comprises of the files in `/core/v5.0/css/a/`, which have been split up into separate files in order to make them easier to manage.
The mobile site has a single `slc.css` file, which is includes along with the twitter bootstrap files.
It would be good to split this into multiple files in the near future, as it is becoming very long.

Similarly, the desktop sites' JS files are to be found as individual files in `/core/v5.0/js/plugins`, whereas the mobile site has a single `plugins.js` file.
Again, the mobile plugins may want to be separated out in the near future.

# Editing Faculty and Catalogue Courses

After editing (or creating) a faculty asset or catalogue course, you may wish to manually run the `copyFacultyDataToMeta.php`, `editAllCoursesMaintenance.php`, or `editAllGradCourses.php` scripts, in order to make sure that the data has been copied into metadata.
You may wish to edit the WSDL scripts' `pagetest()` functions, in order to make the `preg_match` the page you wish to edit.
You could also enter the ID of the page in the WSDL input field, and change the `<select>` to 'page'.

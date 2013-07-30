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

## Avoiding echo and print

When writing scripts that will be cron-enabled, make sure to avoid `echo` and `print` commands that aren't protected by `if ($cron)`.
Instead, output to cron emails are added via `$o[x] .= 'text'`.
The parts of `$o` are as follows:

* $o[0] = Summary
* $o[1] = Errors
* $o[2] = Edited Assets
* $o[3] = All Assets Processed
* $o[4] = Folders Processed

So the output will look something like this:

    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read folder: '.$asset["path"].'</div>';
    } else {
      echo '<div class="f">Failed to read folder: '.$asset["path"].'</div>';
    }



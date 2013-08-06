# Documentation: Adding a new year of events

This should be done once all the events of the year have disappeared from the feed, probably June or July each year.

Create a new year using the `New->Folders->Event Year Folder` and give an appropriate name and Display Name for the new year.

Rename the /events/index20xx-20xx index block and point it at the new folder. You may wish to make a copy of it for the previous year, pointed to the previous year's folder. Make sure that the original index block is edited/used, as it is used by many pages throughout the site (and yet they won't be listed as subscribers).

Create a new event page (easiest to copy an existing one) into the new 20xx-20xx folder, as a placeholder to be in the event stream over the summer.
E.g. https://cms.slc.edu:8443/entity/open.act?id=e7fe1fc07f000002781205b87e3ee0c0&type=page&#highlight
Make it a public event, but hide the "open to public" label with CSS in news.css:
E.g. #event2013-08-30-opening_day-eid000001 .label {display:none;}

Edit the `$start_asset` ID in the `readEventsXML.php`, `archiveEvents.php` and `deleteEventsRemovedFromXML.php` WSDL scripts, to have the ID of the new 20xx-20xx folder, along with editing the IDs of the _archived and _inactive folders (in `readEventsXML.php` and `archiveEvents.php`).

Edit the crontab on the server (login and use `crontab -e`). You'll want to change the years that each of the scripts look to, and make sure they are running on appropriate days of the week. You'll probably want to edit these at the end of the summer too, to make sure the scripts are running frequently enough (but not too frequently). In general, upcoming months should be running daily, and previous months should be running weekly. The script for today should run hourly.

Once you have 'real' imported events for the new year, you can move the placeholder event into _archived.

Copy the index page from the previous year into the new year (important for breadcrumbs). Then edit the news-events/events/.htaccess file to redirect the index.html to the events stream.

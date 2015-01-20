### News Archives

News articles should be archived each January. Here's how to do it:

1. Create a folder for the previous academic year named `yyyy-yyyy` in the [archived](https://cms.slc.edu:8443/entity/open.act?id=03ead72b7f000002001344a87a1d76ac&type=folder&) folder.
2. Create the two index blocks `newsPages` and `newsWithMeta` in that folder. You can copy these blocks from the previous year and change their "Index Folder". The index blocks should be set to alphabetical ascending for the articles to be in the correct order. Older years can't have this, as there are articles that are not named using the yyyy-mm-dd-title naming convention.
2. Move the News items from the previous academic year into its folder in news-events/archived/yyyy-yyyy.
3. Create a `yyyy-yyyy-slc-news-archive` page in the [news archives site](https://cms.slc.edu:8443/entity/open.act?id=9560e98c7f000002204f9c6d11d14a66&type=folder&) and publish it.
4. Publish the [news stream](https://cms.slc.edu:8443/entity/open.act?id=52d657817f000002001344a8bea6509a&type=page&).
5. Log in to the server on the command line and `cd /srv/www/htdocs/news-events/archived/`. A simple `prince 20xx-20yy-archived-news.html` will then create the appropriate PDF.
6. Still on the server (or via FTP), create a `yyyy-yyyy` folder within the `/archived/` folder and move the files from the previous academic year (that are still in `/news-events/news/`) into that folder.
7. Edit the [archived index page](https://cms.slc.edu:8443/entity/open.act?id=1c1deeec7f00000201f9140f7481740f&type=page&) to add the newly archived year at the start of the list and publish it.
8. Edit the [.htaccess](https://cms.slc.edu:8443/entity/open.act?id=e806772b7f0000021b1f96bd51409823&type=file&) file to mark the archived folder as [G]one (around line 260).


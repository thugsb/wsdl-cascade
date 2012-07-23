# Useful WSDL Scripts for Cascade CMS

See example.php and example_full.php for usage.

## Catalogue docs

There is a WSDL script, `addNextYearsCoursesFolders.php`, which allows you to copy in the 20xx-xx/primary+related folders into all the disciplines.

The $academicYear is set by the www-config/formats/xslt/catalogue/_academicYear, although some files are set manually.

There's also a WSDL that will move the previous years' course folders to _archived, `moveLastYearsCoursesToArchived.php`.

### Maintenance Scripts

There are 4 maintenance scripts that can/should be run daily while working on the catalogue:

* editAllGradCourses.php
* editAllCoursesMaintenance.php
* copyFacultyDataToMeta.php
* moveArchivedFacultyToInactive.php

After running them, publish the slc-cat-ugrad/_reports folder. Note that the XSL for these have the $academicYear set manually so you can preview the next year while the current year is still publishing.

If the course naming plugin stops work, `renamePagesBasedOnTitle.php` should help out, but it may not be perfect.

When you're ready to publish the next year, the script `renameCoursesPageHeadlines.php` will update the years in the page headlines.

### Print Catalogue

There's a WSDL script, `publishCatalogueDisciplinePages.php` that will publish all the /cat/area/disc/index pages. These pages are what the _print/undergradute.html page includes to build up the catalogue.

The _print/toc and faculty pages will also need publishing, once the academicYear is updated. Then, download these 3 .html pages into a single folder, along with _print/styles.css. Now you're ready for Prince.

Assuming prince is installed, open your terminal and go to that folder. Then simply run

    $ prince undergraduate.html -o [output_name].pdf

The PDF created should be more-or-less ready. Check through for any problems with the courses, and make sure the pages are all breaking nicely. You may need to make some minor adjustments, and show or hide some of the notes pages.

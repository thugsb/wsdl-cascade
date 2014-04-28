### Print Catalogue

There's a WSDL script, `publishCatalogueDisciplinePages.php` that will publish all the /cat/area/disc/index pages. These pages are what the _print/undergradute.html page includes to build up the catalogue.

The _print/toc and faculty pages will also need publishing, once the academicYear is updated. Then, download these 3 .html pages into a single folder, along with _print/styles.css. Now you're ready for Prince.

If running prince on the server, run `php undergraduate.html > undergraduate-out.html` in order to create a file with the PHP includes already executed (as prince won't execute PHP includes).

Assuming prince is installed, open your terminal and go to that folder. Then simply run

    $ prince undergraduate.html -o [output_name].pdf

The PDF created should be more-or-less ready. Check through for any problems with the courses, and make sure the pages are all breaking nicely. You may need to make some minor adjustments, and show or hide some of the notes pages.

## Archives

There's an `_print/archives` page that is set up to include all the courses from 2000-2012. This has the corresponding `archives-out.html` and `archives-out.pdf`, and is linked to from the [archives page](https://www.slc.edu/catalogue/archives.html).

Each additional year that needs archiving requires a page to be created named after the academic year, e.g. `_print/2012-2013`. Those pages should automatically include the courses from the previous years, and will need to be output with php, e.g. `php 2012-2013.html > 2012-2013-course-arhives.html` and then converted output as PDF via prince. The `publishCatalogueDisciplinePages.php` script can be modified to publish all the prince configurations of the discipline pages for that year. by modifying the `pagetest` function in the two places that "index" appears.

Each year that is output needs to be linked manually from the [archives page](https://www.slc.edu/catalogue/archives.html).

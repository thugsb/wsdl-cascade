### Print Catalogue

There's a WSDL script, `publishCatalogueDisciplinePages.php` that will publish all the /cat/area/disc/index pages. These pages are what the _print/undergradute.html page includes to build up the catalogue.

The _print/toc and faculty pages will also need publishing, once the academicYear is updated. Then, download these 3 .html pages into a single folder, along with _print/styles.css. Now you're ready for Prince.

Assuming prince is installed, open your terminal and go to that folder. Then simply run

    $ prince undergraduate.html -o [output_name].pdf

The PDF created should be more-or-less ready. Check through for any problems with the courses, and make sure the pages are all breaking nicely. You may need to make some minor adjustments, and show or hide some of the notes pages.
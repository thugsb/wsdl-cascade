# To add a new year to the undergrad catalogue

Follow these steps to get the site ready for adding courses to.

* Disable the www and mobile destinations on the catalogue, grad-catalogue, www-undergrad, www-grad and faculty sites.
* Edit the $lastyear and $nextyear in `copyIndexPages.php`, and run it.
* Make sure that the index/year pages are next to each other in the folder order, so the mobile nav neighbours show up (they should be last, unless new assets were created).
* Change the `$year` in `renameOldProgramLandingPages.php` to the old year and run it.
* Make the newly-created humanities/modern-lang/20xx-20xx not indexable.
* Edit the display name of the cat-admin/_admin/asset-factories/course-folder folder.
* Edit the $lastyear and $nextyear in `addNextYearsCoursesFolders.php`, make sure the $start_asset is the ugrad folder, and run it.
* Reminder: Check it's worked for those modern language folders too!
* Edit the years, $start_asset and foldertest=true in `editAssetFactoryContainer.php` and run it.
* Update the $academicYear.
* Edit undergraduate-courses_ to use the previous $academicYear (until courses are ready).

Now you're ready to add the courses to cascade.

* Edit the $academicYear in each of the catalogue/_reports XSLT, and make sure the publish-set is publishing hourly. Inform the content-creators.
* Change the year in the `editAllCoursesMaintenance.php` and `editAllGradCourses.php` scripts.
* Edit the $old and $new in `renameCoursesPageHeadlines.php` and run it.
* Edit undergraduate-courses_ to use the included $academicYear.

The `moveLastYearsCoursesToArchived.php` script moves the previous years' course folders to _archived, but since 2013 we've not been doing that.

If the course naming plugin stops work, `renamePagesBasedOnTitle.php` should help out, but it may not be perfect.

# To add a new year to the grad catalogue

* Edit the $lastyear and $nextyear in `copyGradIndexPages.php`, and run it.
* Make sure that the index/year pages are next to each other in the folder order, so the mobile nav neighbours show up.
* Make sure the `$year` in `renameOldProgramLandingPages.php` is the old year and run it using the $start_asset `4e9e12a97f000001015d84e03ea3fb26` (the grad-catalogue base folder).
* Change the $start_asset in `addNextYearsCoursesFolders.php` to be the grad folder, and run it.
* Update the title of the grad-cat/index
* Edit the $start_asset and foldertest=false in `editAssetFactoryContainer.php` and run it.

You might be able to see the grad courses on this site: https://my.slc.edu/ics/Academics/Course_Schedules/graduate.jnz

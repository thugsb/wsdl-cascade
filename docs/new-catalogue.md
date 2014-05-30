# To add a new year to the undergrad catalogue

Follow these steps to get the site ready for adding courses to.

1. Disable the www destination on the catalogue and grad-catalogue site. You may also want to disable the www destination for the www-undergrad, www-grad and faculty sites too, although be warned that this might cause problems.
2. Edit the $lastyear and $nextyear in `copyIndexPages.php`, and run it.
3. Make sure that the index/year pages are next to each other in the folder order, so the mobile nav neighbours show up (they should be last, unless new assets were created).
4. Change the `$year` in `renameOldProgramLandingPages.php` to the old year and run it (make sure the $start_asset is `817373157f00000101f92de5bea1554a`).
5. Make the newly-created humanities/modern-lang/20xx-20xx not indexable.
6. Edit the display name of the cat-admin/_admin/asset-factories/course-folder folder.
7. Edit the $lastyear and $nextyear in `addNextYearsCoursesFolders.php`, make sure the $start_asset is the ugrad folder, and run it.
8. Reminder: Check it's worked for those modern language folders too!
9. Edit the years, $start_asset and set foldertest=true in `editAssetFactoryContainer.php` and run it.
10. Update the $academicYear.
11. Edit undergraduate-courses_ to use the previous $academicYear (until courses are ready).

Now you're ready to add the courses to cascade.

12. Edit the $academicYear in each of the catalogue/_reports XSLT, and make sure the publish-set is publishing hourly. Inform the content-creators.
13. Change the years in `copyIndexPages.php` and run it. Fix the folder order to make sure the left navigation is correct. Unfortunately this must be done manually, folder by folder (if we're removing previous years of catalogues, this step isn't required).
13. Change the year in the `editAllCoursesMaintenance.php` and `editAllGradCourses.php` scripts.
14. Edit the $old and $new in `renameCoursesPageHeadlines.php` and run it.
15. Edit undergraduate-courses_ (and graduate-courses_) to use the included $academicYear.
16. If you haven't done so, re-enable the www destinations and publish!

The `moveLastYearsCoursesToArchived.php` script moves the previous years' course folders to _archived, but since 2013 we've not been doing that.

If the course naming plugin stops work, `renamePagesBasedOnTitle.php` should help out, but it may not be perfect.

# To add a new year to the grad catalogue

1. Edit the $lastyear and $nextyear in `copyGradIndexPages.php`, and run it.
2. Make sure that the index/year pages are next to each other in the folder order, so the mobile nav neighbours show up.
3. Make sure the `$year` in `renameOldProgramLandingPages.php` is the old year and run it using the $start_asset `4e9e12a97f000001015d84e03ea3fb26` (the grad-catalogue base folder).
4. Change the $start_asset in `addNextYearsCoursesFolders.php` to be the grad folder, and run it.
5. Update the title of the grad-cat/index
6. Edit the $start_asset and set foldertest=false in `editAssetFactoryContainer.php` and run it.
7. Edit graduate-courses_ to use the included $academicYear (if you haven't already done so).

You might be able to see the grad courses on this site: https://my.slc.edu/ics/Academics/Course_Schedules/graduate.jnz

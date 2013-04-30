# To add a new year to the undergrad catalogue

* Disable the www and mobile destinations on the catalogue, grad-catalogue, www-undergrad, www-grad and faculty sites.
* Edit the $lastyear and $nextyear in copyIndexPages, and run it.
* Make the newly-created humanities/modern-lang/20xx-20xx not indexable.
* Edit the display name of the cat-admin/_admin/asset-factories/course-folder folder.
* Edit the $lastyear and $nextyear in addNextYearsCoursesFolders, make sure the $start_asset is the ugrad folder, and run it.
* Reminder: Check it's worked for those modern language folders too!
* Edit the years, $start_asset and foldertest=true in editAssetFactoryContainer and run it.
* Update the $academicYear.
* Edit undergraduate-courses_ to use the previous $academicYear (until courses are ready).

The above gets the site ready for adding courses to.

* Change the year in the editAllCoursesMaintenance and editAllGradCourses scripts.
* Edit the $old and $new in renameCoursesPageHeadlines and run it.
* Edit undergraduate-courses_ to use the included $academicYear.



# To add a new year to the grad catalogue

* Edit the $lastyear and $nextyear in copyGradIndexPages, and run it.
* Change the $start_asset in addNextYearsCoursesFolders to be the grad folder, and run it.
* Update the title of the grad-cat/index
* Edit the $start_asset and foldertest=false in editAssetFactoryContainer and run it.


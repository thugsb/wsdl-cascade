



# Editing Faculty and Catalogue Courses

After editing (or creating) a faculty asset or catalogue course, you may wish to manually run the `copyFacultyDataToMeta.php`, `editAllCoursesMaintenance.php`, or `editAllGradCourses.php` scripts, in order to make sure that the data has been copied into metadata.
To target a single discipline or program, you can enter the ID of the folder into the WSDL ID input.
In order to target a single asset, you enter the ID of the page (either faculty or course) in the WSDL input field, and change the `<select>` to 'page'.

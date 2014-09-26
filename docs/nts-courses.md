# NTS Courses

The NTS courses are available for purchase [here](http://nts.slc.edu/modules/shop/index.html?action=courseCatalogs).

These courses are brought into cascade via an [XML feed](http://my.slc.edu/nts/coursefeed.aspx) that does some processing.
This feed is controlled by IT at SLC.
It is pulled into cascade via `www.slc.edu+ce/NTS feed`.

That feed block is then pulled into course pages as `External structured content`, and the `Teaser` metadata field is used to link it to the kinds of course that should be shown on that particular page.
The `Teaser` field should contain the `OfferingID`.
To find the `OfferingID`, view a course in nts.slc.edu, e.g. in this case the `Teaser` would be '1591': `http://nts.slc.edu/modules/shop/index.html?action=section&OfferingID=1591&SectionID=3092`.

The `Main Column - NTS Courses_` XSLT should do the rest for you, showing the courses with the description collapsed and a button link to "Registration and Details", like [this](http://d.pr/i/HCD).

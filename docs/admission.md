# Admission

NOTE: These instructions are outdated.

## On The Road

### Counsellor changes

The admission counsellors are listed within two Data Definitions as dropdowns.
If the lists need updating, you need to edit the [Admission counselors](https://cms.slc.edu:8443/entity/open.act?id=97bb16a57f00000101f92de51ae1b8e1&type=structureddatadefinition) and [State](https://cms.slc.edu:8443/entity/open.act?id=302091fb7f0000024ee1d6a35d90ceb1&type=structureddatadefinition) DDs.
Note that removing counsellors from these lists will likely wipe out data from the [States](https://cms.slc.edu:8443/entity/open.act?id=1c07f8bb7f0000024124dc483414e60f&type=folder&).
Also, see below regarding counsellor avatars.

### Updating for a new year

When it's time to add the next year of events, delete the folders+files in `/visit/_travel-admin`, and copy the replacement folders+files from `/_admin/asset-factories/states`.

The events-20xx.csv needs to have LF line terminators, which may not be the way Excel saves the CSV.
To test whether this is the case or not, type this into the command line: `file events-20xx.csv`, and the output will hopefully be either "UTF-8 Unicode English text" or "ASCII English text" and will _not_ say "ASCII English text, with CR line terminators".
If it does have CR line terminators, re-save the file and make sure Line Endings are set to LF (this is the default in Textmate).

The CSV file should be in the following format:

    "Event","City","State","Rep","Start","End"
    "Washington County College Fair","Hagerstown","Maryland","CeCe Belcher",2013-10-09 18:00,2013-10-09 20:00
    "Virginia High School Visits","Various","Virginia","CeCe Belcher",2013-10-15,2013-10-17

It may not be necessary to have the quotations marks, except when there is a comma in the entry.

### Making sure the counsellor avatars appear

[This format](https://cms.slc.edu:8443/entity/open.act?id=3059c2907f00000250905c93ffc2bc40&type=format) formats a page that calls both the index of the event blocks 
`/visit/_travel-admin/[region]/[area]` and the `/admission-counselors` block, 
which contains details about the current admission counsellors.

The page will show the areas in alphabetical order.

For a counsellor to be displayed, that counsellor must be in `/admission-counselors` and have the counsellor (name) dropdown assigned to match the counsellor assigned in the area block. 
This is the key that connects the two blocks, and so the dropdown must be kept identical. 
Unfortunately this means the Data Definitions need to be edited any time a new counsellor is added (or removed).

The Reading Areas assigned in `/admission-counselors` does NOT necessarily correlate with the on-the-road area events.

## Todo

The import script should check to see if the times are set, and if not, should set the "Display Times?" radio button to No.

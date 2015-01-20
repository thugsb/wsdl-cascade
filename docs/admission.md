# Admission Site

The admission site is unlike the other sites on slc.edu. It was created with the intention of moving the entire website towards this design, but became derailed when a more radical branding redesign became the new direction. It is a mobile-first, fully responsive site with good accessibility ratings and progressive enhancement.

## Gallery

Every page on this site has the ability to include a Gallery at the top of the page. This can be edited in the structured data on each page. It uses [jquery cycle2](http://jquery.malsup.com/cycle2/), which is largely activated through `data-xxx` markup in the HTML on the page.

### Options

The Gallery can either be Big (a showcase) or Small (a banner). It can also be turned off, or include a single placeholder image, which is the smaller (banner) version. Slides can be randomized and they can be set up to only display between (or for) certain times. A slide will only be shown is an image is set (or there's an XML override, see below). Showcase should be 1280px by 690px and banner images should be 1280px by 317px.

### XML Override

A gallery slide can also be over-ridden with the XML selector. This can either be done using an XML block or the Gallery Slide DD block. This second option is the recommended option, and has an Asset Factory set up for it. This DD block should be fairly self-explanatory, and (I think) all the fields are optional.

## Homepage Buckets

The homepage has buckets set up for it. These are similar to the Tiles and Cards found elsewhere on the site. They were built to be scalable, but Cards are probably better. Editing the [index-buckets](https://cms.slc.edu:8443/entity/open.act?id=294ae8607f00000239d7a7b3b2f68539&type=block&) asset should be pretty self-explanatory.

## Contact Map

The [map](https://cms.slc.edu:8443/entity/open.act?id=be8199fe7f0000024320e76de0f484f6&type=page&) is generated with an SVG. There's a fallback image for IE8- users, but the image won't be updated.

Counselors have blocks which define their travels and details within the [/_counslors/](https://cms.slc.edu:8443/entity/open.act?id=116c6cab7f00000273c3d061a8384d18&type=folder&) folder. This block needs to be selected as the "External structured content" on their counselor page in the [/counslors/](https://cms.slc.edu:8443/entity/open.act?id=a40b9ee17f0000025b606e1f1e47c179&type=folder&) folder. There are asset factory for both these blocks and pages.

The counselor blocks are indexed by the [counselors-index](https://cms.slc.edu:8443/entity/open.act?id=1172cddb7f00000273c3d06138b5ae15&type=block&) block, which is used by the map page itself.

Each counselor that travels also needs to have a feed block set up for them within the [/_counselor-events/](https://cms.slc.edu:8443/entity/open.act?id=420eb9fd7f0000020c089e41097ca899&type=folder&) folder. The IDs are the IDs of the counselor in Slate. A list taken at the time can be found in [liquid planner](https://app.liquidplanner.com/space/73116/projects/show/17718245) but new counselors will have their own IDs. The link to use for the feed block is `https://www.slc.edu/__web-services/importICalEvents.php?user=xxx` where "xxx" is the (long) ID of the appropriate counselor. As you can see, this utilizes a PHP script, but this is not a WSDL script as it does not actually interact with Cascade or import the events (even though the name implies that).

## Right Sidebar

### Four call-to-action buttons

The four buttons are included via the [apply_inc.html](https://cms.slc.edu:8443/entity/open.act?id=d13d54097f0000020e41a88280c55a6b&type=file&) server side include.

### Right and left sidebar

The right and left sidebars found elsewhere on the site are combined into the right sidebar, with the right sidebar coming before the left sidebar.

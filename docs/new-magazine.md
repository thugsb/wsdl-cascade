# To add a new magazine

1. Use the `New->Magazine Issue` asset factory to create the new folder.
2. Change the folder order of the [www.slc.edu+magazine base folder](https://cms.slc.edu:8443/entity/open.act?id=ab880f697f0000021a23b0063cc5fd6f&type=folder&), to make sure this folder is at the top (they should be in chronological order).
3. Edit the Magazine Folder and put the "Season Year" into the Teaser field, e.g. "Spring 2015". Put the Display Name as the name of the magazine.
4. Rename the features/focus folder to something appropriate for this magazine issue. Be sure to change the Display Name of the focus folder.
5. Put the content into the pages. Additional pages can be created with the `New Magazine Page` asset factory, although the `Related Content -> Related Publication` needs to be assigned to the index page located in the same folder. All pages should have an `Avatar`.
6. Once the content is ready, publish the new magazine.
7. Change the [.htaccess](https://cms.slc.edu:8443/entity/open.act?id=e44751db7f000002255ec1f21949360c&type=file&) to make /magazine/ redirect to the new magazine.
8. Add a new card to the [archives-cards](https://cms.slc.edu:8443/entity/open.act?id=56ebc2637f000002061dc8f093f41457&type=block&) block for the new magazine, and publish the archives page.
9. Change the [search quicklink](http://www.siteimprove.com/searchimprove/setup/pagerankingpretext.aspx?pckid=1405240315&prid=1407367679&kid=1407370176)

Note: The "related" index blocks are used to create the right nav. They should only be attached to the index page of each folder.

## Legacy documentation for the old magazine format

* Use the `New->Magazine Issue` asset factory to create the new folder.
* Changed the folder order of the [www.slc.edu+magazine base folder](https://cms.slc.edu:8443/entity/open.act?id=ab880f697f0000021a23b0063cc5fd6f&type=folder&), to make sure this folder is at the top (they should be in chronological order).
* Edit the content in the folder, such as the index header, the intro block and the content in `/_featured and news`.
* Edit the [News-Magazine asset factory page](https://cms.slc.edu:8443/entity/open.act?id=5d1d937e7f000002310aff0edd2d9e63&type=page&) to point to the correct magazine folder and have the correct thumbnails.
* Add the articles in the news-events site, using the asset-factory.
* Reference those articles in _stories and _more-stories.
* Change the [magazineBlock](https://cms.slc.edu:8443/entity/open.act?id=394aeaf37f00000237022ee59204acb1&type=block_STRUCTUREDDATA) for the news-events homepage.
* Change the magazine [archives page](https://cms.slc.edu:8443/entity/open.act?id=c88649d47f000002005b7025f9b44319&type=page&).
* Update the [covers block](https://cms.slc.edu:8443/entity/open.act?id=1637fca97f000002357a73240dff02f1&type=block), with the most recent magazine as the first group.
* Publish: The new folder (including its media), the archives page, the previous magazine index (so the Archives cover updates), the [archives section SSI](https://cms.slc.edu:8443/entity/open.act?id=5b147b7f7f000002095adf3cf9332b52&type=page), and the [News-Events homepage](https://cms.slc.edu:8443/entity/open.act?id=392f69ce7f00000237022ee5ec6b40d8&type=page&).
* Change the [.htaccess](https://cms.slc.edu:8443/entity/open.act?id=e44751db7f000002255ec1f21949360c&type=file&) to make /magazine/ redirect to the new magazine.
* Change the [search quicklink](http://www.siteimprove.com/searchimprove/setup/pagerankingpretext.aspx?pckid=1405240315&prid=1407367679&kid=1407370176)

# To add a new magazine

* Copy the previous magazine's folder and stop it from being indexed (for now).
* Renamed the display name of the copied folder.
* Changed the folder order of the [www-magazine base folder](https://cms.slc.edu:8443/entity/open.act?id=ab880f697f0000021a23b0063cc5fd6f&type=folder&), to make sure this folder is at the top (they should be in chronological order).
* Edit the content in the folder, such as the index header, the intro block and the content in `/_featured and news`.
* Add the articles in the news-events site.
* Reference those articles in _stories and _more-stories (after wiping out the references that were copied from the previous magazine).
* Change the [magazineBlock](https://cms.slc.edu:8443/entity/open.act?id=394aeaf37f00000237022ee59204acb1&type=block_STRUCTUREDDATA) for the news-events homepage.
* Change the magazine [archives page](https://cms.slc.edu:8443/entity/open.act?id=c88649d47f000002005b7025f9b44319&type=page&).
* Update the [covers block](https://cms.slc.edu:8443/entity/open.act?id=1637fca97f000002357a73240dff02f1&type=block).
* Publish: The new folder (including its media), the archives page, the previous magazine index (so the Archives cover updates), and the [News-Events homepage](https://cms.slc.edu:8443/entity/open.act?id=392f69ce7f00000237022ee5ec6b40d8&type=page&).
* Change the [.htaccess](https://cms.slc.edu:8443/entity/open.act?id=e44751db7f000002255ec1f21949360c&type=file&) to make /magazine/ redirect to the new magazine.

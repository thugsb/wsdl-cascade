# Videos

Embedding videos can be done using the WYSIWYG editor.
Videos can be embedded into any section of the page (main content, sidebars, etc.).
Hannon Hill has provided instructions for [Inserting media](http://www.hannonhill.com/kb/WYSIWYG/index.html#inserting-media), which covers most of the implementation.

## SLC video specifics

_It is important to make sure that videos are embedded inside a &lt;p&gt; (paragraph) within the WYSIWYG editor._
This is only likely to come up if the video is the only content inside a WYSIWYG instance, but could result in a broken layout or page content being hidden.

If you wish the video to start playing as soon as the page is loaded, add the following code to the end of the src URL: `?autoplay=1` (or if the URL already has a question mark `?` in it, append `&autoplay=1` instead — note the `&` instead of the `?`).

## Playlists

If you wish to insert multiple videos, you can do so by creating a playlist in Youtube and embedding the playlist.

### Making a Youtube playlist

To make a playlist, go to the [Youtube video manager playlist page](http://www.youtube.com/view_all_playlists) and click `New Playlist`.
You can then enter a name and description for the playlist (you may wish to put the URL that the playlist will be embedded on in the description) and click `Create Playlist`.
You can then click `Add video by URL` and enter the Youtube video URL and click `Add` for each video you wish to add to the playlist.
Once you've added all the videos, click `Done` and `Play all`, and from there you can click "Share" and "Embed" and copy+paste the [embed code](http://d.pr/i/wb93) into the ["Embed Code" tab](http://d.pr/i/3O1P) that the WYSIWYG editor brings up when you click to insert media.
Before clicking "Insert", add `http:` to the start of the IFRAME 'src' attribute, so that it reads `src="http://www.youtube..."` and not just `src="//www.youtube..."`.

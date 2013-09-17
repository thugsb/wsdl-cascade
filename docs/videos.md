# Videos

Embedding videos can be done using the WYSIWYG editor.
Videos can be embedded into any section of the page (main content, sidebars, etc.).
Hannon Hill has provided instructions for [Inserting media](http://www.hannonhill.com/kb/WYSIWYG/index.html#inserting-media), which covers most of the implementation.

## SLC video specifics

_It is important to make sure that videos are embedded inside a &lt;p&gt; (paragraph) within the WYSIWYG editor._
This is only likely to come up if the video is the only content inside a WYSIWYG instance, but could result in a broken layout or page content being hidden.

If you wish the video to start playing as soon as the page is loaded, add the following code to the end of the src URL: `?autoplay=1` (or if the URL already has a question mark `?` in it, append `&autoplay=1` instead).

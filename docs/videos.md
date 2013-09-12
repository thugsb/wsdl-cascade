# Videos

The library archives site contains a new way to embed videos. 
It is greatly simplified from the previous data definition.
There are now only four inputs: Placement, Ratio, Link and whether or not the video should autoplay.

## Placement

If you choose "above" or "below", the video will automatically be inserted above or below the main content on the page.
If you choose "manual", you will need to put `<div id="video"></div>` somewhere on the page, whether amongst the main content, or in a sidebar.
This code must be entered as HTML code, which can be done by clicking the `HTML` button on the WYSIWYG editor: http://d.pr/i/RTXq
You can then enter the code in the popup window like so: http://d.pr/i/bCKZ

## Ratio and sizing

The video will default to being full-width of the content area.
On a page without a sidebar, this is 720px wide, and on a page with a sidebar it is 450px wide.
A 4:3 ratio video would be 570px and 368px respectively, whereas a 16:9 (widescreen) video is 435px and 283px respectively.
On mobile devices, the video will remain full-width, and its height will scale appropriately.

## Link

The only officially-supported videos are from Youtube, Vimeo and Livestream.
The following formats for the URL are supported:

* http://www.youtube.com/watch?v=1HnZ7FgZb8I (standard Youtube URL)
* http://www.youtube.com/watch?v=1HnZ7FgZb8I&list=PLR8IP-Ezl7uEaMyvoyP7L7mvXFMNf-m3l (standard Youtube playlist URL)
* http://youtu.be/1HnZ7FgZb8I (standard Youtube sharing URL)
* http://www.youtube.com/embed/1HnZ7FgZb8I?list (Youtube embed code URL)
* http://www.youtube.com/embed/1HnZ7FgZb8I?list=PLR8IP-Ezl7uEaMyvoyP7L7mvXFMNf-m3l (Youtube playlist embed code URL)
* http://vimeo.com/17927160 (standard Vimeo URL)
* http://player.vimeo.com/video/74013667 (Vimeo embed code URL)
* http://cdn.livestream.com/embed/occupytoronto?layout=4 (Livestream embed code URL)

They all work starting with just //, http:// or https://.
Other arguments in the URL, such as `&feature=related` might cause the video to fail to play.
_Always check your video works before publishing the page._

It is possible that embed codes from other video providers would work, assuming they are designed to be played within an `IFRAME`.
However, it is recommended to transfer these videos to a supported provider (Youtube or Vimeo).

## Autoplay

Selecting `Yes` for autoplay will cause the video to start playing as soon as the page is loaded.
This generally will not work for mobile devices.
If you use a different provided from the supported three, this is likely to fail.

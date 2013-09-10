# The Config Site

The www_config site contains just about all the templates, XML formats (XSLT and a few Velocity), configuration sets, metadata sets, data definitions, and many XML and feed blocks. It also contains the www Transport, used by all Destinations.
Content Types, Workflow Definitions, Publish Sets, Destinations and Connectors are site-specific, and so are located in their individual site.

## Templates

General information about templates can be found from [HannonHill's kb](http://www.hannonhill.com/kb/Template/index.html) and their [PDF](http://www.hannonhill.com/downloads/pdf/white-papers/Templates_in_a_CMS.pdf).

Templates contain the page's structure, but hopefully no content. Instead, they define regions, each of which can take an XML input and can then be formatted by XSLT or velocity.
The regional assignments on templates 'cascade' down into the configurations that use the template, which in turn cascade down into the pages which use the configurations.
So a generic template, like the `Level 2 HTML` template, can be used by many different configurations, having mostly the same content, yet allowing for configuration-specific regional assignments.
Most of the pages on the v5 version of the site use `Level 2 HTML`, which the other `Level 2 XXX` templates being minor variations of this.

Another template of note is the `JS` template, which actually contains no content, and allows for non-XML output.
When used with `#protect-top` (in the format), this template will only output the content that is protected, and will output nothing else.
For more information on outputting non-XML code, see the [HH kb](http://www.hannonhill.com/kb/Code-Sections/index.html).

## Formats

Most of the slc.edu website uses XSLT formats, with the occasional use of Velocity where XSLT doesn't perform so well (such as string manipulation and escaping content).

The XSLT formats have a naming convention, where the scripts that start with an underscore are scripts that are _included_ by other scripts, and scripts that end with an underscore are scripts that _include_ other scripts.
This is done because the include paths in XSLT are hard-coded, and so moving/renaming the XSLT would result in the scripts breaking if the included/including scripts weren't modified to match.
It is important to follow this naming convention, so that if the need arises to reorganize or rename scripts, this can be more easily done without too much work.
A missing/mis-linked script will give an error, but the error message would probably not be very helpful.

### Main Column

The `/_cms/formats/xslt/main-column/_Main Column` script is used by nearly every page.
It generates the div#content-main, which includes the page `<h1>` heading, as well as the page content.
This script also brings in many other elements from the data definition, such as video, SlideShowPro, and External Structured Content, among other things.

Many other `Main Column - xxx_` formats exist, which include this script. They are references by the `_Main Column` script in the 'array' of `xsl:apply-templates`.

This script also determines whether div#content-main has the .no-content-sidebar class or not, but the content in the sidebar is determined by a different Region and Format.

### Server Side Includes

Some elements of our site are included using PHP Server Side Includes (SSI). 
However, the cascade rendering engine does not honour PHP includes, and there has to be different content shown internally (within cascade's rendering) than externally (on the site).
This is done through the use of `[system-view:internal]` and `[system-view:external]` within the XSLT scripts, with the Internal section having the same content as the externally-included PHP snippet.
The External section tells the rendering engine to run PHP, such as

    <xsl:processing-instruction name="php">
      include($_SERVER['DOCUMENT_ROOT'] . "/_server-side/v5inc/js_inc.html");
    </xsl:processing-instruction>

The actual code that is published is generally located in `www_core`.

Another method used to include code uses the `include via dd block` script, which allows you to specify the include path in the `teaser` metadata field.
It otherwise functions identically to the purpose-built scripts.

Another important point to note is that when you wish to link to something starting with `//`, such as `//fonts.googleapis.com/css?family=Copse`, the Internal rendering must add `https:` at the start of the link, otherwise Cascade will strip a slash and it will end up looking for content at (for example) https://cms.slc.edu/fonts.googleapis.com/css?family=Copse (which obviously won't exist, and would result in missing CSS/JS).

### Xalan

Xalan scripts are JavaScript scripts that can be executed by XSLT.
A commonly used example of such a script is the [_format date](https://cms.slc.edu:8443/entity/open.act?id=fc2d19267f000002005b702508dc5c56&type=format&) script, which performs some magic to convert [UNIX timestamps](https://en.wikipedia.org/wiki/Unix_timestamp) into human-readable dates and times.
However, it is important to note that Cascade can only handle a single `<xalan:script>`, meaning that all desired JavaScript functions must be included inside a single script.
This also means that you cannot include two different Xalan scripts from different XSLT files.
An example of multiple JavaScript functions within a single Xalan script is used by the [News Stream](https://cms.slc.edu:8443/entity/open.act?id=214b67b97f00000210d38f43019dd35b&type=format&).

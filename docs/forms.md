# Creating Forms

This file was last edited on 3rd June, 2015.

# Creating Forms In Cascade

In Cascade, pages have a "Forms" part of their data definition. This includes a single group, that can have multiple instances. Only a single copy of each form can be placed on a page, although multiple forms from multiple sources can be included.

A person can choose where the form is to appear on a page by selecting its placement. If selecting "manual", include a DIV with ID="[lowercase type of form]-[ID of form]", e.g. `<div id="hubspot-12345"></div>` where you want the form to appear on the page. Make sure the LEFT FEATURES XSLT is included on the page. The LEFT FEATURES XSLT is the part of the page that includes the JavaScript that generates the form.

NOTE: Manually-placed forms will only appear when JavaScript is enabled. A warning will appear when JavaScript is turned off. For other placements, Hubspot forms cannot be shown or completed when JavaScript is turned off and a warning will be shown. For non-manual placements, a link will be shown for Slate forms allowing people to fill out the form, and Machform works OK without JavaScript.

Slate forms allow for a redirect to be put in place, so that people who successfully complete the form are taken to a different page. A complete URL (with http:// or https://) should be entered into Cascade for this to be the case. Otherwise, the user will be taken back to the same page after completing the form. See below for informational regarding redirects when using the other form types.

We have implemented embedding for three form types:

* Hubspot
* Slate
* Machform

# Hubspot

Hubspot is designed for inbound marketing.

## Forms

[Forms](https://app.hubspot.com/forms/480743/) can be made easily in Hubspot, with many options of field types. It is possible to create new fields on the fly, or to do so in the [Contacts Settings](https://app.hubspot.com/contacts/480743/settings/properties/). Additionally, Rich Text can be added above any field row, and there are several instances where we have used this to put in a horizontal line to help break up the form (to input such a line, edit the Rich Text and go to the Insert menu).

## Embedding Forms

Forms can be embedded by taking the code from the "Embed" tab. In order to embed a form, a redirect URL must be set for the "Thank You" page. Embedded Hubspot forms will always redirect to the same page, and the redirect URL unfortunately cannot be determined for each embed instance. For embedding in Cascade, see the page on [embedding forms in Cascade](#).

## Workflows

[Workflows](https://app.hubspot.com/contacts/480743/automation/) are used to set up responses to user actions, such as filling out a form. A simple Workflow would be that when someone fills out a form, an auto-response email is sent to them. However, there are many more options available and complex workflows can be created with relative ease. Once ready, make sure to turn them on.

## File Manager

The [File Manager](https://app.hubspot.com/content/480743/file-manager) seems to be used for managing images. Images can be moved, renamed and replaced, but need to be sized correctly before uploading.

## Design Manager

The [Design Manager](https://app.hubspot.com/content/480743/template-builder) is used to create and edit templates and code files, such as CSS and JS. The Design Manager should only be used by those working with code development.

### Templates

Templates are used by Landing Pages, Emails, and potentially other things. Templates can be set up to have many different modules. These can be images, text regions, forms, code regions, and many other elements.

Modules can be placed on each other or next to each other to create Module Groups. Module Groups that are in columns (next to other regions or Module Groups) can be converted into Flexible Columns by using the top-right Gear icon and selecting "Make Flexible Column". Flexible Columns are flexible in the sense that Landing Pages can add additional regions into the Flexible Column.

Most modules can be given specific HTML classes, IDs, CSS styles, and can be wrapped in other HTML tags, by selecting "Edit CSS Declarations" from the Gear dropdown. The BODY can also have classes and CSS styles added to it via the Edit menu dropdown.

Modules can (and generally should) be given default content, such as a default logo image or header text.

Modules can be made Global, which allows them to be available for other templates.

There are many other parts of template-creation that aren't documented here. The [Hubspot documentation for Modules](http://knowledge.hubspot.com/site-pages-user-guide/how-to-use-content-modules) is extensive.

### Code Files

CSS and JS files can be created and edited using the editor.

In our implementation, the `custom/page/css/Hubspot-default-basic.css` and `custom/page/web_page_basic/hs_overrides.css` files are included in all Pages. This can be changed in the [Content Settings](https://app.hubspot.com/content/480743/settings/publishing). However, `hs_overrides.css` contains code that we may want to move out of it at some point, as it was made specifically for the Summer at Sarah landing pages in early 2015.

## Landing Pages

[Landing Pages](https://app.hubspot.com/content/480743/landing-pages) are great ways to create single-use pages. We have mainly used Landing Pages as pages to display forms and "Thank You" pages. A Landing Page must have a template, and then the Modules available can be edited to have the desired content for that page.

Page-specific stylesheets can also be added in the Style tab.

Once ready, make sure to publish the page.

## Emails

[Emails](https://app.hubspot.com/email/480743) can either be sent as blasts or as auto-responders. They use templates, and it is important to set the "From Name" and "From Email Address". The "Message Subject" is potentially the most important part of any email, and should be thought about carefully. The Email Body is also important, as it the "Preview Text", which most email clients now display to people before they open the email, although different clients show different lengths of text.

The "Suggestions" at the bottom-right of the page are helpful, and it's good to pay attention to them.

## Campaigns

Campaigns are useful containers for much of the above. At the time of writing this documentation, they haven't been used very much and we suggest you read the [Hubspot Campaign Documentation](http://knowledge.hubspot.com/campaigns-user-guide/how-to-create-a-campaign).

# Slate

Slate is primarily used by the Admissions department. Similar to [Hubspot](#), Slate allows its users to set up forms (either to be embedded or on a universally-styled landing page), and to send email blasts and auto-responder emails.

There are [Slate forums](https://technolutions.zendesk.com/forums) available to get support, although they require you to be logged in to Slate first.

## Configuration Editor

Slate also allows advanced users to edit the XSLT and CSS used by its landing pages, via the [Configuration Editor](https://apply.slc.edu/manage/database/config). Our current (June 2015) setup actually uses CSS hosted in Cascade, and so the `build.xslt` is likely to be the only file that needs to be edited in order to change the landing page template.

## Emails and Thank You pages

These are set up by editing a form and clicking "Edit Communications". Both emails and Thank You pages are set up by selecting "New Mailing". You may then select whether to set up the auto-responder email, thank-you page, or both. The interface for setting up a Thank You page looks the same as an email, which can be a little confusing. The trigger can also be set, which is Slate's basic equivalent to Hubspot's workflows.

# Machform

Machform is used for non-marketing forms and internal forms, such as the 404/Error Page feedback form and for companies wishing to post an Off-campus job opportunity for students.

Machform has a simple interface, allowing for form fields to be added and customized easily, and entries to be viewed and exported. Notification emails and simple auto-responder emails can also be set up, although if a form requires an auto-responder email then it is likely better to use another platform such as [Hubspot](#) or [Slate](#).

The best place to get information is from the [Machform Support page](http://www.appnitro.com/support-resources) or the [Machform Forums](http://www.appnitro.com/forums/).

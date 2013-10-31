# Finding Aids

On the Library Archives site, there are Finding Aid pages which use a standard XML schema.

## Adding and editing finding aids

The first thing to do is to upload the XML as a new XML block within the [finding-aids/_xml](https://cms.slc.edu:8443/entity/open.act?id=559d382a7f0000022f616fcf9e902bd7&type=folder&) folder.
If the Finding Aid has a PDF, upload it into the [finding-aids/pdf](https://cms.slc.edu:8443/entity/open.act?id=98c184377f00000272fd5ba8c426177b&type=folder&) folder.
Then, create a new Finding Aid page using the asset factory.
Make sure to link both of the inline regions (both called DEFAULT) to the XML you uploaded (this is for the HTML and XML configurations), and make sure the PDF is also linked, [like so](http://d.pr/i/6CX4).
The Display Name and Title are currently not used, but might be used in the future, so you may wish to put in the title that you would desire the page to be labelled with (e.g. if it were to appear in the nav or on an index page that lists the Finding Aids).

Note: The XML structure needs to either follow the "[ead](http://d.pr/i/d7DO)" or the "[ns2](http://d.pr/i/h0Ig)" structure.

## Admin Info

We were able to find and use a [standard XSL](https://cms.slc.edu:8443/entity/open.act?id=0e8c49b57f000002417993456f65e7ff&type=format&) to format to convert them to HTML, although the [XSL has been modified](https://cms.slc.edu:8443/entity/open.act?id=55aa44b47f0000022f616fcf7ea779b1&type=format&) a little to achieve the layout we desired.

The inline Region assignments are set to be inline from the [Config Set](http://d.pr/i/crar).


# Explanation of variables

Unless stated otherwise, variables are optional.

`$title` just sets the title of the page. 
It's useful as a brief explanation of what the script does.

`$start_asset` is the ID of an asset in cascade that will initially appear in the ID input field.
This is useful if you are targeting a particular site/folder/asset and will generally be set.

`$type_override` allows you to set the type of asset that will be initially selected in the select input.
Folder is default.

`$asset_type` and `$asset_children_type` allow you to specify the kinds of assets you are dealing with.
Page and folder are default, but you could, for example, want to target files within folders, or assetFactorys within assetFactoryContainers.
These determine what the `readFolder()`, `indexFolder()` and `readPage()` functions search for.
If you wish to search for multiple asset types (e.g. pages and files) then you will either need to edit the functions, or use two scripts.

# Explanation of functions

These functions are required: `pagetest()`, `foldertest()`, `edittest()`, `changes()`.

The first three of these must return either true or false.
They can simply do that if required, or they can perform some kind of test, likely a `preg_match()`.

`changes()` is the function that determines how an asset is to be changed (assuming the script will be editing something).
In order to target a particular value in the metadata or structured data, this will likely mean running `foreach` loops.
In this function, the variable `$changed` should be global, and should initially be set to false.
Once the script has found something that needs editing, set `$changed` to true, and that asset will get edited. 
If `$changed` is always true, the script will always edit every asset, even when no changes are being made (which not only is wasteful, but also clutters the version history).

The simple `example.php` calls in `header.php`, which in turn calls in `html_header.php` (which calls in `web_services_util.php`).
On the other hand, `example_full.php` directly calls `html_header.php`, as it wants to avoid the functions defined in `header.php`.
The `html_header.php` is the script that calls the function that actually reads the assets (either `readFolder()` or `readPage()`), and so at least one of these is required (generally both).
`readFolder()`, `indexFolder`, `readPage()` and `editPage()` are either defined in `header.php`, `cron.php` or the script that you are making (if you based it on `example_full.php`).


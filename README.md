# Useful WSDL Scripts for Cascade CMS

These scripts provide a browser-based GUI for the WSDL scripts that for use with Hannon Hill's Cascade CMS.

To make new scripts, copy [example.php](example.php) or [example_full.php](example_full.php) and edit it as appropriate. 
Example.php allows for simple edits of specified pages (set by the functions pagetest(), foldertest() and edittest() ), whereas example_full.php gives much greater flexibility, allowing for moving, publishing, and other operations.

The scripts can also be set up to run as [cron](./docs/cron.md) jobs, although it may take a little extra work to set up for that purpose (but not much).

See the [docs](./docs/) for more info about the specific scripts and usage.

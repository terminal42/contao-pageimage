
# terminal42/contao-pageimage

This Contao extension add a new field to assign image files for each page.
Using these images, the _Page Image_ front end module can:

 - output the image as page content (e.g. as a header banner)
 - generate CSS to apply the image as a background to `<body>` or an element.

 **Note:** In version 4, the two separate front end modules have been merged
 and the change from image to CSS happens through custom front end templates!


## Features

 - **Multiple images per page**<br>
   If more than one image is assigned to a page, the front end module
   can decide which n-th image should be shown. This means you can (for example)
   have a header and footer image per page. Make sure to correctly order them
   in the page configuration.

 - **Inherit images from parent page**<br>
   If the current page has no image assigned, images from the parent
   page can optionally be inherited to all child pages.

 - **Support for responsive images**<br>
   In both the `mod_pageimage` or `mod_pageimage_background` templates,
   _Page Image_ fully supports Contao's responsive image settings
   (using `<picture>` or `@media` query respectively).

 - **NEW: metadata support**<br>
   Version 4 adds support for file metadata. Same as with content elements,
   metadata can be defined in the file manager and can be selectively
   overridden in the page configuration.


## Installation

Choose the installation method that matches your workflow!

### Installation via Contao Manager

Search for `terminal42/contao-pageimage` in the Contao Manager and add it
to your installation. Apply changes to update the packages.

### Manual installation

Add a composer dependency for this bundle. Therefore, change in the project root
and run the following:

```bash
composer require terminal42/contao-pageimage
```

Depending on your environment, the command can differ, i.e. starting with
`php composer.phar …` if you do not have composer installed globally.

Then, update the database via the `contao:migrate` command or the Contao install tool.


## License

This bundle is released under the [MIT license](LICENSE)

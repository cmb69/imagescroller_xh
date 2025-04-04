# Imagescroller_XH

Imagescroller_XH facilitates displaying a scrolling slideshow of images
(optionally with links and with a description). It has no back-end
functionality to manage the galleries. Instead the images have to be
uploaded via FTP or the CMSimple_XH file manager. Additional information for
the links and the description has to be entered manually in a special text
file.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

# Requirements

Imagescroller_XH is a plugin for [CMSimple_XH](https://cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0, and PHP ≥ 7.1.0.
Imagescroller_XH also requires the [Plib_XH](https://github.com/cmb69/plib_xh) plugin;
if that is not already installed (see *Settings*→*Info*),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

## Download

The [lastest release](https://github.com/cmb69/imagescroller_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins. See the
[CMSimple_XH Wiki](https://wiki.cmsimple-xh.org/?for-users/working-with-the-cms/plugins)
for further details.

1. **Backup the data on your server.**
1. Unzip the distribution on your computer.
1. Upload the whole folder `imagescroller/` to your server into the `plugins/`
   folder of CMSimple_XH.
1. Set write permissions for the subfolders `config/`, `css/` and `languages/`.
1. Browse to `Plugins` → `Imagescroller` to check if all requirements
   are fulfilled.

## Settings

The configuration of the plugin is done as for many other CMSimple_XH plugins in
the back-end of the Website. Select `Plugins` → `Imagescroller`.

You can change the default settings of Imagescroller_XH under `Config`.
Hints for the options will be displayed when hovering over the help icon
with your mouse.

Localization is done under `Language`. You can translate the character
strings to your own language if there is no appropriate language file
available, or customize them according to your needs.

The look of Imagescroller_XH can be customized under `Stylesheet`.

## Usage

To display an image scroller with all all images in the folder
`userfiles/images/my_gallery/`, insert into a page:

    {{{imagescroller('my_gallery')}}}

To display the image scroller on all pages, you have to enable the `autoload`
configuration option, and insert in the template:

    <?=imagescroller('my_gallery')?>

If you want to link the images or add titles or descriptions, you have to
create a gallery in the back-end.
Under `Plugins` → `Imagescroller` → `Galleries`, a list of available image
`Folders` is presented; choose one and press `Edit gallery`, and
the gallery will be initialized with all supported images from that folder.
Edit the gallery to your liking, and `Save gallery`.
Then the gallery will be presented under `Galleries`,
and the folder is no longer listed under `Folders`.
You can edit the created galerie anytime again.

The gallery definition contains a record for each image you want to show; records are
seperated by a line containing only two percent signs (`%%`).  The records
can have the following fields: `Image`, `URL`, `Title` and `Description`;
only `Image` is required, the other fields are optional.
The fields of the record are written on separate lines which start
with the field name, followed by a colon and the field value.
The order of the lines does not matter.
The filename of the image has to be relative to `userfiles/images/`;
the URL can be relative to the current CMSimple_XH Site or absolute.
An example gallery definition looks like:

    Image: image1.jpg
    URL: http://www.example.com/
    Title: First Photo
    Description: This is the first photo for the image scroller.
    %%
    Image: image37.jpg
    URL: ?A_CMSimple_Page
    %%
    Image: image2.jpg
    URL: ?&amp;mailform
    Title: Contact
    %%
    Image: image3.jpg
    URL: http://3-magi.net/
    Description: My favorite website ;)

To display the image scroller just call `imagescroller()` with the name of the
galerie, e.g.:

    {{{imagescroller('info')}}}

Note that all images should have the same size (i.e. dimensions). Otherwise
they will be resized to the size of the first image in the gallery, and in
the back-end a warning will be shown.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/imagescroller_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Imagescroller_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Imagescroller_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Imagescroller_XH.  If not, see <https://www.gnu.org/licenses/>.


Copyright © Christoph M. Becker

Slovak translation © Dr. Martin Sereday<br>
Estonian translation © Alo Tänavots

## Credits

Imagescroller is powered by [jQuery.serialScroll](https://github.com/flesler/jquery.serialScroll).
Many thanks to Ariel Flesler for publishing this nice jQuery plugin under MIT license.

The plugin logo is designed by [Everaldo Coelho](https://www.everaldo.com/).
Many thanks for publishing this icon under GPL.

This plugin uses Oxygen icons from the [Oxygen Theme](https://github.com/KDE/oxygen-icons).
Many thanks for publishing these icons under LGPLv3.

Many thanks to the community at the [CMSimple_XH Forum](https://www.cmsimpleforum.com/)
for tips, suggestions and testing.

And last but not least many thanks to [Peter Harteg](https://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.

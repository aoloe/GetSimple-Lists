# GetSimple-Lists


This Lists plugin started as a Fork – in the GitHub sense –  of the Item Manager plugin, but – rather quickly – I've noticed that it would have been easier to start from scratch when fixing the bug that disturbed me and adding the new features I was looking for.

Nonetheless, the Lists plugin has been extensively inspired by PyC's work in the Item Manager and uses Content Fields plugin which is very close to the Custom Fields one.

## Features

## Adding the promotional items to the sidebar

In the manager you can mark specific items as promoted.

You can then show them on the main page or on specific pages by:

- In your theme, create a component called "lists", with – at least – the content
        <?php lists_promotion() ?>
  You can set as parameter the id of the list you want to insert, an array of id or leave it empty to show all the promotions for all types of lists.
- Add to the theme's `sidebar.inc.php` file the line
        <?php get_component('lists'); ?>
  At the place where you want the promoted items to be shown.

Of course, this same works for other parts of the template. But you cannot use this method to add the promoted items to the content of a specific page.
For this to happen, you will have to edit the settings and set it as "prepend", "append" or "replace".

## Resources

- [Plugin Creation](http://get-simple.info/wiki/plugins:creation)
- http://get-simple.info/wiki/plugins:tabs_menus
- http://get-simple.info/wiki/plugins:hooks_filters
- http://get-simple.info/extend/plugin/customfields/22/
- http://get-simple.info/extend/plugin/i18n-custom-fields/100/
- http://mvlcek.bplaced.net/get-simple/i18nspecialpages/
- http://get-simple.info/extend/plugin/items-manager/301/ and https://github.com/aoloe/GetSimple-Items-Manager
- https://github.com/aoloe/GetSimple-Lists

other thinks to check
- http://mvlcek.bplaced.net/get-simple/dynpages

## TODO

- next step is geting from i18n_specialpages the way it enforeces mandatory fields( maybe introduce http://parsleyjs.org/ for client side checks)
- setup ww.getsimple.org

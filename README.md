#GetSimple-Lists


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


## How it works




## Resources

- [Plugin Creation](http://get-simple.info/wiki/plugins:creation)
- `http://get-simple.info/wiki/plugins:tabs_menus`
- `http://get-simple.info/wiki/plugins:hooks_filters`
- `http://get-simple.info/extend/plugin/customfields/22/`
- `http://get-simple.info/extend/plugin/i18n-custom-fields/100/`
- `http://mvlcek.bplaced.net/get-simple/i18nspecialpages/`
- `http://get-simple.info/extend/plugin/items-manager/301/ and https://github.com/aoloe/GetSimple-Items-Manager`
- `https://github.com/aoloe/GetSimple-Lists`

other thinks to check
- http://mvlcek.bplaced.net/get-simple/dynpages

## TODO

- rename `Lists_item` to something better!
  `Lists_list` would already be better...
- cleanup the link to `Content_field`!
- remove the `Lists_settings` class



- next step is geting from `i18n_specialpages` the way it enforeces mandatory fields( maybe introduce http://parsleyjs.org/ for client side checks)
- setup ww.getsimple.org
- add an "order by" field (empty for manual, otherwise autoamtic by a field)
- find a "clean" way to attach a filter to the display of the list (on this page, show all the pages with this filter... or let the user activate the filter: show all the entries with year 2011, or simply paginate through the items)
- check if in the list of pages (for the drop down), it is better to use the slug or the page title (most of all when there are subpages... they risk to be more of unique!)

## Notes while implementing the lists list in the edit console

Don't delete the things below once implemented, but transform them in a documentation draft.

We need to be able to list the items from ContentFields without really knowing which fields are in the list.

In the Lists settings we need a field (select among the existing fields) that define which is the field to be used as a label. Theoretically, a `sprintf` of several fields could be used, but it looks a bit overkill! (It can be done, if one day we have, on top of the form, a way to define a settings file with more advanced settings)
If no field is defined as the label, the first one (order) is taken.

It should also be possible to tell if the order is ascending or descending.

A second drop down should be used to define which field determines the order of the the List items. If empty, the items can be manually sorted, as it is done with the Fields definition.

With each entry in the list, we need to store the creation and the change date (+ eventually the two users)

When adding fields to the Settings, we should find a way to mark the page as dirty, so that it asks for confirmation if the user tries to navigate away. The same applies for editing the field definition and, then, also, in the edit console. And add the sentence "The Project/Item has unsaved changes at the bottom of the sidebar (cf. pages editing)

Eventually, we can add an option to prepend or append the content of the page. (one of both... or it's empty)

It would also be interesting to be able to attach a type of list to multiple pages... and show with a different filter on each page.
It may be doable with a single page:
- if there is not parameter show the elements with no tag
- otherwise only show the elements with the specific tag
- and always show the list of tags and allow switching among them.

Add a (non mandatory field) that specify which is the tag (it should be possible to attach several tags to one entry)

Is there a need to show the details on a separate page?
And for search? Any need for a search functionality? Also to be shown on a separate page?

## Forum posts
Find a way to undo the delete (instead of the confirm dialog). ...
One question is, if it's ok to have a delete link... in the past it was clear that a delete must always be a POST to protect yourself from mass-deletion due to spiders (well, since it's in the admin/authenticated part...).

One different problem to solve, is how to cleanly undo concurrent actions (two users deleting in parallel, the same user doing actions on two browser windows)

###What is `check_nonce()`?

It's used to protect from xss... and it compares a hash created with a global salt, the username, the action, the script file executing, the user's ip, the current date/hour.

All but the global salt (which is put in the middle of the string instead of being used as a real hash) are known to the attacker, assuming that they are kwnowingly attacking GS. (or the user is not known?)
is the choice of using sha1 good?
I think that i should have a look at a good way to avoid xss attacks.

# Adding a list to several pages

Each lists can appear only one single page.

If you want to show parts of a list on several page, the easiest solution is to "tag" your entries and to create a filter that shows the selected tags (TBI).

From your templates you can also "manually" load the Lists plugin, load a specific list and display a custom set of entries (TBI).

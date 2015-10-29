---
title:  "Usage"
---

# Usage

The way Pimp My Matrix works is by allowing you to create your block type groups and field layouts in multiple contexts.

Say you have a large Matrix field that drives a lot of the content on your site, you want it to work the same way across most of the control panel but there are often a couple of places you just want to tweak it. You might want an extra block type for a specific section, or to not show certain fields somewhere as they aren’t applicable in that context.

We enable this to happen by making use of contexts. Each time the code runs that manipulates the output of your Matrix fields we check the context of the page to see if there is any specific configuration for that context and if not fall back to any defaults you may have set.

The following contexts are currently supported:

- Entry Types
- Category Groups
- Global Sets
- Users

You can override your defaults for a specific context by going to the field layout designer for each one, clicking the gear icon of any active Matrix field and selecting “Group block types”:

![group block types button](http://s3-eu-west-1.amazonaws.com/supercoolplugins/Pimp-My-Matrix/group-block-types.jpg)

## Setting up defaults

To create default block type groups and field layouts for all your Matrix fields click on the “Pimp My Matrix” tab in the main navigation. Here you will find a list of your current Matrix fields.

Click a field name to launch the block type groups editor. It should look something like this:

![block type groups editor](http://s3-eu-west-1.amazonaws.com/supercoolplugins/Pimp-My-Matrix/block-type-groups-editor.jpg)

Now you can group your block types in the same way that you create a field layout for a section:

![block type groups editor filled in](http://s3-eu-west-1.amazonaws.com/supercoolplugins/Pimp-My-Matrix/block-type-groups-editor-2.jpg)

If you leave any block types off then they won’t be shown.

Once you have some groups you can go one step further and customize the field layout for a particular block - just click the gear icon and select ‘Edit field layout’.

![block type field layout](http://s3-eu-west-1.amazonaws.com/supercoolplugins/Pimp-My-Matrix/block-type-field-layout-editor.jpg)

Thats it! You should now be able to browse to somewhere that uses that field and see your new groups and field layouts in action.

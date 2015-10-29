---
title:  "Hooks"
---

# Hooks

There are two hooks provided that allow plugin developers to enable their own specific contexts for Pimp My Matrix to use.

## loadPimpMyMatrixConfigurator

Gives plugins a chance to load the block type groups editor on their own field layout designer.

It should return an array or `null`.

```php
public function loadPimpMyMatrixConfigurator()
{
  $segments = craft()->request->getSegments();

  if ( count($segments) == 3
       && $segments[0] == 'myplugin'
       && $segments[1] == 'myelementtypegroup'
       && $segments[2] != 'new'
     )
  {
    return array(
      'container' => '#fieldlayoutform',
      'context' => 'myelementtypegroup:'.$segments[2]
    );
  }
}
```

## loadPimpMyMatrixFieldManipulator

Gives plugins a chance to load the field manipulation js on their own pages that use fields.

It should return a string or `null`.

```php
public function loadPimpMyMatrixFieldManipulator()
{
  $segments = craft()->request->getSegments();

  if ( count($segments) == 3 && $segments[0] == 'myelementtype' )
  {
    $myElementGroup = craft()->myPlugin->getMyElementGroupByHandle($segments[1]);
    if ($myElementGroup)
    {
      return 'myelementtypegroup:'.$myElementGroup->id;
    }
  }
}
```

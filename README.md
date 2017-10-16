# EXS-LanderTrackingChaturbateBundle

[![Build Status](https://travis-ci.org/ExSituMarketing/EXS-LanderTrackingChaturbateBundle.svg)](https://travis-ci.org/ExSituMarketing/EXS-LanderTrackingChaturbateBundle)

## What is this bundle doing ?

This bundle is not a standalone bundle and requires `EXS-LanderTrackingHouseBundle`.

It will add an extracter and a formatter to be added to `EXS-LanderTrackingHouseBundle` to manage Chaturbate tracking parameter.

The extracter service searches for parameters :
- `track` which contains a string composed of either `{cmp}~{exid}~{visit}` or `{cmp}~{exid}` or `{cmp}`.

The formatter service will add the parameters if  :
- `track` will contains a string composed of either `{cmp}~{exid}~{visit}` or `{cmp}~{exid}` or `{cmp}`.

## Installation

Download the bundle using composer

```
$ composer require exs/lander-tracking-chaturbate-bundle
```

Enable the bundle, and the _EXS-LanderTrackingHouseBundle_ that is installed as a requirement.

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new EXS\LanderTrackingHouseBundle\EXSLanderTrackingHouseBundle(),
        new EXS\LanderTrackingChaturbateBundle\EXSLanderTrackingChaturbateBundle(),
        // ...
    );
}
```

## Configuration

The `cmp` parameter has a default value configurable with this configuration key :

```yml
# Default values.
exs_lander_tracking_chaturbate:
    default_cmp: 1
```

This default value will be used over `EXS-LanderTrackingHouseBundle`'s `cmp` default value.

## Usage

Example :
```twig
    <a href="{{ 'http://www.test.tld/' | appendTracking('chaturbate') }}">Some link</a>
    <!-- Will generate : "http://www.test.tld/?track=123~987654321~5" -->
    
    <a href="{{ 'http://www.test.tld/?foo=bar' | appendTracking('chaturbate') }}">Some link</a>
    <!-- Will generate : "http://www.test.tld?foo=bar&track=123~987654321~5" -->
```

See [EXS-LanderTrackingHouseBundle's documentation](https://github.com/ExSituMarketing/EXS-LanderTrackingHouseBundle/blob/master/README.md) for more information.

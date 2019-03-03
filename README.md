# MultiHook module for the Zikula Application Framework

![Build Status](http://guite.info:8080/buildStatus/icon?job=Applications_MultiHook/master)


## Documentation

  1. [Introduction](#introduction)
  2. [Requirements](#requirements)
  3. [Installation](#installation)
  4. [Implementing custom entry providers](#entryproviders)
  5. [Implementing custom needles](#needles)
  6. [Changelog](#changelog)
  7. [Questions, bugs and contributing](#contributing)


<a name="introduction" />

## Introduction

MultiHook is a content helper module which can handle automatically replace abbreviations, acronyms, autolinks and censored words.


<a name="requirements" />

## Requirements

This module is intended for being used with Zikula 2.0.12+.


<a name="installation" />

## Installation

The MultiHook module is installed like this:

1. Download the [latest release](https://github.com/zikula-modules/MultiHook/releases/latest).
2. Copy the content of `modules/` into the `modules/` directory of your Zikula installation. Afterwards you should a folder named `modules/Zikula/MultiHookModule/`.
3. Initialize and activate ZikulaMultiHookModule in the extensions administration.


<a name="entryproviders" />

## Implementing custom entry providers

MultiHook can not only manage abbreviations, acronyms, autolinks and censored words itself. Also other modules can contribute additional entries by implementing an *entry provider*. For example a product database could let MultiHook create autolinks for all product numbers automatically.

- An entity provider class name should be suffixed by `Provider` and located in the `ModuleRoot/EntryProvider/` directory. This is not mandatory but a recommended convention.
- Entity provider classes must be registered as a service using the `zikula.multihook_entry_provider` tag.
- Entity provider classes need to implement several methods. At the moment there is no interface for that though to prevent issues when MultiHook is not available in a system.
- As an example the Content module offers a [PageEntryProvider](https://github.com/zikula-modules/Content/blob/master/src/modules/Zikula/ContentModule/EntryProvider/PageEntryProvider.php) to create auto links for all site titles.


<a name="needles" />

## Implementing custom needles

For some (old) information about it see [this](https://github.com/zikula-modules/MultiHook/blob/5.x-old/docs/install.txt) and [that](https://github.com/zikula-modules/MultiHook/blob/5.x-old/docs/needles_howto.txt).

- Needle classes must be registered as a service using the `zikula.multihook_needle` tag.

**TBD**

<a name="changelog" />

## Changelog

### Version 6.0.0

Structural changes:
- Entirely rewritten for Zikula 2.0.x using ModuleStudio.

New features:
- Translatable functionality for multilingual entries.
- New settings for configuring which replacements should be enabled/disabled (#1).
- Checkbox column for mass deletion (#2).

### Older versions

See [old changelog](https://github.com/zikula-modules/MultiHook/blob/5.x-old/docs/changelog.txt).


<a name="contributing" />

## Questions, bugs and contributing

If you want to report something or help out with further development of the Content module please refer
to the corresponding GitHub project at https://github.com/zikula-modules/Content

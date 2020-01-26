# MultiHook module for the Zikula Application Framework

[![](https://github.com/zikula-modules/MultiHook/workflows/Generate%20module/badge.svg)](https://github.com/zikula-modules/MultiHook/actions?query=workflow%3A"Generate+module")
[![](https://github.com/zikula-modules/MultiHook/workflows/Test%20module/badge.svg)](https://github.com/zikula-modules/MultiHook/actions?query=workflow%3A"Test+module")

## Documentation

1. [Introduction](#introduction)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Implementing custom entry providers](#implementing-custom-entry-providers)
5. [Using and implementing needles](#using-and-implementing-needles)
6. [Changelog](#changelog)
7. [Questions, bugs and contributing](#questions-bugs-and-contributing)

## Introduction

MultiHook is a content helper module which can handle automatically replace abbreviations, acronyms, autolinks and censored words. As a filter hook, MultiHook scans text for certain keywords and performs actions depending on what has been defined for them. You can easily create

- autolinks, eg. the word MultiHook can always link to the project site at GitHub
- abbr + acronym tags, eg. EC gets converted to <abbr title="European Community">EC</abbr>
- censors, eg. bad words get converted to *****
- needles (see below for more information about this feature)

## Requirements

The `master` branch of this module is intended for being used with Zikula 3.0.
For Zikula 2.0.x look at [releases](https://github.com/zikula-modules/MultiHook/releases/).

## Installation

The MultiHook module is installed like this:

1. Download the [latest release](https://github.com/zikula-modules/MultiHook/releases/latest).
2. Copy the content of `modules/` into the `modules/` directory of your Zikula installation. Afterwards you should a folder named `modules/Zikula/MultiHookModule/`.
3. Initialize and activate ZikulaMultiHookModule in the extensions administration.

After installation has been completed activate MultiHook for any modules you want to use it with, like Content or News. You can manage the hook enablements from either the subscriber modules administration area or from the provider side, that is the MultiHook administration area.

You can now add links, acronyms, abbreviations and censors in the admin panel or by selecting text while pressing the CTRL-button on every page inside your Zikula installation (*Note:* the latter option is not available [yet](https://github.com/zikula-modules/MultiHook/issues/5)).

### Configuring HTML tags needed for MultiHook

In the SecurityCenter administration you need to allow these tags incl. parameters for the MultiHook:

- a
- abbr
- acronym
- em
- span

## Implementing custom entry providers

MultiHook can not only manage abbreviations, acronyms, autolinks and censored words itself. Also other modules can contribute additional entries by implementing an *entry provider*. For example a product database could let MultiHook create autolinks for all product numbers automatically.

- An entity provider class name should be suffixed by `Provider` and located in the `ModuleRoot/EntryProvider/` directory. This is not mandatory but a recommended convention.
- Entity provider classes need to implement `\Zikula\ExtensionsModule\ModuleInterface\MultiHook\EntryProviderInterface`.
- As an example the Content module offers a [PageEntryProvider](https://github.com/zikula-modules/Content/blob/master/modules/Zikula/ContentModule/EntryProvider/PageEntryProvider.php) to create auto links for all site titles.

## Using and implementing needles

Needles are a clever invention by Oivind Skau and have been implemented in PagEd for the first time. They have been implemented since MultiHook 4.0.

A needle is a quick way to insert a link from one piece of your content to another piece of your content. Instead of typing out a long URL to reference some page or forum post, you could simply type a short string such as `CONTENTPAGE-2` and the link would be automatically created for you. Or maybe you want to link to a specific download, weblink, news article...any module that has a needle (or needles) included, can use the functionality. Another great aspect about needles is that they won't become outdated: if a permalink changes your needle will automatically be replaced by the newest version.

You can simply add any kind of link by writing `NEEDLENAME{params}` if there is a corresponding needle provided. You can see a list of all needles available in your installation at the configuration page inside the MultiHook administration area.

Hints for implementing your own needles:

- A needle class name should be suffixed by `Needle` and located in the `ModuleRoot/Needle/` directory. This is not mandatory but a recommended convention.
- Needle classes need to implement `\Zikula\ExtensionsModule\ModuleInterface\MultiHook\NeedleInterface`.
- Ideally use some caching mechanism in order to avoid consecutive database queries. Have a look at existing needles to learn more about the idea.
- As an example the Content module includes the [PageNeedle](https://github.com/zikula-modules/Content/tree/master/modules/Zikula/ContentModule/Needle).

## Changelog

### Version 6.1.0

Structural changes:

- Upgrades for Zikula 3.0.x.

New features:

- None yet

Bugfixes:

- Cache selected entries to save performance for multiple filter calls on same page

### Version 6.0.0

Structural changes:

- Entirely rewritten for Zikula 2.0.x using ModuleStudio.

New features:

- Translatable functionality for multilingual entries.
- Entry providers for letting other modules contribute additional entries automatically (#7).
- New settings for configuring which replacements should be enabled/disabled (#1).
- Checkbox column for mass deletion (#2).

### Older versions

See [old changelog](https://github.com/zikula-modules/MultiHook/blob/5.x-old/docs/changelog.txt).

## Questions, bugs and contributing

If you want to report something or help out with further development of the Content module please refer
to the corresponding GitHub project at <https://github.com/zikula-modules/Content>.

# CMSimple-XH Audio Plugin

The audio plugin for CMSimple-XH provides a simple method to integrate sounds in the page. The sound files are located in a root directory defined in the plugins configuration. All files are listed using HTML5 audio tags and can directly be played.

Subdirectories are displayed as links to dive in. For navigation both an up button and breadcrumbs are available and can be switched on and off in the configuration.

## Usage

The simplest plugin call is

    {{{audio()}}}

In this case the audio files and subdirectries of the root directory are displayed.

    {{{audio("audio_file.xxx")}}}
    {{{audio("subdir/audio_file.xxx")}}}

This plugin call integrates a single audio file in the root directory with no further navigation. The path can contain subdirectories.

## Memberaccess integration

If the Memberaccess-XH plugin is installed an admin group can be defined in the configuration. With this access right files can be deleted.

# ToDos

## Audio admin functions

* mkdir
* rmdir
* rename of files and dirs
* move of files
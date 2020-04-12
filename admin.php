<?php

/**
 * Internal Filebrowser -- admin.php
 *
 * @category  CMSimple_XH
 * @package   CALDav Calendar
 * @author    Thomas Winkler <thomas.winkler@iggmp.net>
 * @copyright 2018 nibble-arts <http://www.nibble-arts.org>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://cmsimple-xh.org/
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/*
 * Register the plugin menu items.
 */
if (function_exists('XH_registerStandardPluginMenuItems')) {
    XH_registerStandardPluginMenuItems(true);
}

if (function_exists('audio') 
    && XH_wantsPluginAdministration('audio') 
    || isset($audio) && $audio == 'true')
{

    $o .= print_plugin_admin('off');

    switch ($admin) {

        case '':
            $o .= '<h1>Audio</h1>';
    		$o .= '<p>Version 0.9</p>';
    		$o .= '<p>Copyright 2019</p>';
            $o .= '<p><a href="http://www.nibble-arts.org" target="_blank">Thomas Winkler</a></p>';
            $o .= "<p>Mit dem Audio-Plugin lassen sich Tondateien einfach einbinden. Es werden alle Formate unterstützt, die vom Browser ausgegeben werden können.</p>";

            $o .= "<h3>Verwendung</h3>";
            $o .= '<p>Die Audiodateien werden durch einen einfachen Plugineintrag aufgerufen.</p>';

            $o .= "<p><strong>Datei im Audio-Root-Verzeichnis</strong></p>";
            $o .= '<p><code>{{{audio("filename.mp3")}}}</code></p>';

            $o .= "<p><strong>Datei in einem Unterverzeichnis</strong></p>";
            $o .= '<p>Steht die Datei in einem Unterverzeichnis unter dem Rootverzeichnis, das in der Konfiguration eingestellt ist, muss dies ebenfalls angegeben werden.</p>';
            $o .= '<p><code>{{{audio("unterverzeichnis/filename.mp3")}}}</code></p>';

            $o .= "<p><strong>Alle Dateien in einem Unterverzeichnis</strong></p>";
            $o .= '<p>Bei Angabe eines Verzeichnisses im Audio-Root-Pfad werden automatische alle Audiodateien zum Abspielen angeboten.</p>';
            $o .= '<p><code>{{{audio("verzeichnis")}}}</code></p>';

            break;

        default:
            $o .= plugin_admin_common($action, $admin, $plugin);
    }

}
?>

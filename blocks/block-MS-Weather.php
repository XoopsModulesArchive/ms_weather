<?php

/************************************************************************************/
/* MS-Weather:                                                                      */
/* v1.2  28/01/2004 FOR XOOPS v2                                                    */
/* Ported by: Sylvain B. (sylvain@123rando.com)                                     */
/* http://123rando.com                                                              */
/*                                                                                  */
/*                                                                                  */
/* WEATHER BLOCK: block-MS-Weather.php                                              */
/* v3.0c  11-07-2003 FOR PHP-NUKE 6.5                                               */
/*                                                                                  */
/* Copyright © 2002 by: Maty Scripts (webmaster@matyscripts.com)                    */
/* http://www.matyscripts.com                                                       */
/*                                                                                  */
/* This program is free software. You can redistribute it and/or modify             */
/* it under the terms of the GNU General Public License as published by             */
/* the Free Software Foundation; either version 2 of the License.                   */
/*                                                                                  */
/************************************************************************************/
# Cache File Date routine is from:             
# NukeScripts Network (webmaster@nukescripts.com)
# Copyright © 2002, All rights reserved
# http://www.nukescripts.net
/************************************************************************************/
function disp_block_msweather()
{
    global $module_name, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsLogger;

    require_once XOOPS_ROOT_PATH . '/modules/ms_weather/header.php';

    //$language = $xoopsConfig['language'];

    $block = [];
    $block['title'] = _MS_WEATHER_BLOCKTITLE;
    $block['content'] = '';
    $msw = new msweather();

    // Read Preferred city codes
    $result = $xoopsDB->query( 'select mswv1, mswv2 from ' . $xoopsDB->prefix('msweather') . " where type='3'" );
    list( $cityname, $citycode ) = $xoopsDB->fetchRow( $result );

    // Read Preferred Unit ( [0] = Celsius - [1] = Fahrenheid and cache parameters
    $result = $xoopsDB->query( 'select mswv1, mswv2, mswv3 from ' . $xoopsDB->prefix('msweather') . " where type='1'" );
    list( $unit, $cachetype, $cacheinterval ) = $xoopsDB->fetchRow( $result );

    // Determine if Weather data should be cached again ==>
    // Code from: NukeScripts Network (webmaster@nukescripts.com): http://www.nukescripts.net
    $cache_file = XOOPS_ROOT_PATH . '/modules/ms_weather/cache/' . $citycode . '.dat';
    if ( file_exists( "$cache_file" ) ) {
        $cachefiletime = filemtime( $cache_file );
        if ( 0 == $cachetype ) {
            $comparetime = date('Y-m-d H:i', mktime (0,0,0,date('m',$cachefiletime),date('d',$cachefiletime) + $cacheinterval,date('Y',$cachefiletime)));
        } elseif ( 1 == $cachetype ) {
            $comparetime = date('Y-m-d H:i', mktime (date('G',$cachefiletime) + $cacheinterval,date('i',$cachefiletime),0,date('m',$cachefiletime),date('d',$cachefiletime),date('Y',$cachefiletime)));
        } elseif ( 2 == $cachetype ) {
            $comparetime = date('Y-m-d H:i', mktime (date('G',$cachefiletime),date('i',$cachefiletime) + $cacheinterval,0,date('m',$cachefiletime),date('d',$cachefiletime),date('Y',$cachefiletime)));
        }
    }
    // END Code from: NukeScripts Network (webmaster@nukescripts.com): http://www.nukescripts.net

    // If required, cache Weather data again and display block info - eache Cityname can be seperately cached
    $currenttime = date('Y-m-d H:i');
    if ( ( !( file_exists( $cache_file ) ) ) || ( $comparetime < $currenttime ) || ( !( filesize( $cache_file ) ) ) ) {
        // Store and Read all Required data for $cityname/$citycode
        $block['content'] .= $msw->msw_saveblockdata( $cityname, $citycode, $unit, $cache_file, $module_name );
    } else {
        // Read all Required data for $cityname/$citycode from cache file
        $block['content'] .= $msw->msw_readblockdata( $cache_file );
    }

    return $block;
}
?>

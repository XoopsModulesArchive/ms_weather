<?php

/************************************************************************************/
/* MS-Weather:                                                                      */
/* v1.2  28/01/2004 FOR XOOPS v2                                                    */
/* Ported by: Sylvain B. (sylvain@123rando.com)                                     */
/* http://123rando.com                                                              */
/*                                                                                  */
/*                                                                                  */
/* MS-Weather: index.php                                                            */
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
require_once '../../mainfile.php';
require_once 'header.php';

/******************************************************************************/
/* FUNCTION: MSWeatherShowForcast( $cityname, $citycode, $unit )              */
/* Main Start function for Maty Scripts Weather                               */
/******************************************************************************/
function MSWeatherShowForcast( $cityname, $citycode, $unit )
{
    global $xoopsConfig, $xoopsModule, $xoopsDB, $module_name, $xoopsUser;

    $module_name = $xoopsModule->dirname();

    echo "<center><a target=\"_blank\" href=\"http://weather.yahoo.com\"><img border=\"0\" src=\"images/yahoo_weather.bmp\"></a></center><br>\n";

    // Read Module Display Options
    $result = $xoopsDB->query( 'select mswv1, mswv2, mswv3 from ' . $xoopsDB->prefix('msweather') . " where type='2'" );
    list( $showgeneral, $showforecast, $showmore ) = $xoopsDB->fetchRow( $result );

    // When this function is invoked by either Block or Site-Module, then read defaults from database
    if ( !isset( $cityname ) ) {
        // Read Preferred city codes
        $result = $xoopsDB->query( 'select mswv1, mswv2 from ' . $xoopsDB->prefix('msweather') . " where type='3'" );
        list( $cityname, $citycode ) = $xoopsDB->fetchRow( $result );

        // Read Preferred Unit ( [0] = Celsius - [1] = Fahrenheid and cache parameters
        $result = $xoopsDB->query( 'select mswv1 from ' . $xoopsDB->prefix('msweather') . " where type='1'" );
        list( $unit ) = $xoopsDB->fetchRow( $result );
    }
    /******************************************************/
    /*                                                    */
    /* function msw_displaycities() block                 */
    /*                                                    */
    /******************************************************/
    function msw_m_displaycities( $cityname, $module_name )
    {
        global $xoopsDB, $_POST, $_GET;
        if (isset($_POST['pcity'])) {
            $pcity = $_POST['pcity'];
        }
        if (isset($_GET['pcity'])) {
            $pcity = $_GET['pcity'];
        }
        $selcity = '<form method="post" action="' . XOOPS_URL . "/modules/ms_weather/index.php?op=MSWeatherDecode&amp;pcity=$pcity\">";
        $selcity .= '<center>' . _MSW_SELECTCITY . '<select name="pcity" onChange="submit()">';
        $result = $xoopsDB->query( 'select mswv1, mswv2 from ' . $xoopsDB->prefix('msweather') . " where type='4' ORDER BY mswv1 ASC" );
        while ( list( $dbcityname, $dbcitycode ) = $xoopsDB->fetchRow( $result ) ) {
            $dbcityname = stripslashes($dbcityname);
            if ( $dbcityname == $cityname ) {
                $selcity .= '<option selected>' . $dbcityname . '</option>';
            } else {
                $selcity .= '<option>' . $dbcityname . '</option>';
            }
        }
        $selcity .= '</select></center></form>';

        return( $selcity );
    }

    // Display Module Data
    $msw = new msweather();

    OpenTable();
    echo '<center><h4><b>' . $cityname . ' ' . _MSW_TODAY . '</b></h4></center>';
    // Display pulldown menu with city codes
    echo msw_m_displaycities( $cityname, $module_name );
    //CloseTable();
    echo '<br>';

    echo $msw->msw_getmoduledata( $cityname, $citycode, $unit, $module_name, $showgeneral, $showforecast, $showmore );
    echo '<br>';
    echo '<center>' . _ADAPT_FROM . '</center>';
    CloseTable();
    // For Administrator only: Admin Menu
    if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
        $isadmin = true;
    } else {
        $isadmin = false;
    }
    if ($isadmin) {
        echo '<br>';
        OpenTable();
        echo '<center><a href="' . XOOPS_URL . '/modules/ms_weather/admin/msweatheradmin.php?op=MSWeatherAdminGUI"><b>' . _MSW_ADM . '</b></a></center>';
        CloseTable();
    } else {
        echo '<br>';
    }
}

/******************************************************************************/
/* FUNCTION: MSWeatherDecode( $pcity )                                        */
/* Decode the cityname/citycode                                               */
/******************************************************************************/
function MSWeatherDecode( $pcity )
{
    global $xoopsDB;

    // Get codes for selected City
    $result = $xoopsDB->query( 'select mswv1, mswv2 from ' . $xoopsDB->prefix('msweather') . " where type='4' AND mswv1='$pcity'" );
    list( $dbcityname, $dbcitycode ) = $xoopsDB->fetchRow( $result );
    // Get preferred unit
    $result = $xoopsDB->query( 'select mswv1 from ' . $xoopsDB->prefix('msweather') . " where type='1'" );
    list( $unit ) = $xoopsDB->fetchRow( $result );
    // Show weather forecast
    MSWeatherShowForcast( $dbcityname, $dbcitycode, $unit );
}

/***************************************************/
/****************** PROGRAM START ******************/
/***************************************************/
if ( $xoopsConfig['startpage'] == $xoopsModule->dirname() ) {
    $xoopsOption['show_rblock'] = 1;
    require XOOPS_ROOT_PATH . '/header.php';
    if ( empty($_GET['start']) ) {
        make_cblock();
        echo '<br>';
    }
} else {
    $xoopsOption['show_rblock'] = 0;
    require XOOPS_ROOT_PATH . '/header.php';
}

global $_POST,$_GET;
if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}
if (isset($_GET['unit'])) {
    $unit = $_GET['unit'];
}
if (isset($_POST['unit'])) {
    $unit = $_POST['unit'];
}
if (isset($_GET['cityname'])) {
    $cityname = $_GET['cityname'];
}
if (isset($_POST['cityname'])) {
    $cityname = $_POST['cityname'];
}
if (isset($_GET['citycode'])) {
    $citycode = $_GET['citycode'];
}
if (isset($_POST['citycode'])) {
    $citycode = $_POST['citycode'];
}
if (isset($_POST['pcity'])) {
    $pcity = $_POST['pcity'];
}

switch ( $op ) {
   case 'MSWeatherShowForcast':
      MSWeatherShowForcast( $cityname, $citycode, $unit );
   break;
   case 'MSWeatherDecode':
      MSWeatherDecode( $pcity );
   break;
   default:
      MSWeatherShowForcast( $cityname, $citycode, $unit );
}

require_once (XOOPS_ROOT_PATH . '/footer.php');
?>


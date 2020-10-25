<?php

/************************************************************************************/
/* MS-Weather:                                                                      */
/* v1.2  28/01/2004 FOR XOOPS v2                                                    */
/* Ported by: Sylvain B. (sylvain@123rando.com)                                     */
/* http://123rando.com                                                              */
/*                                                                                  */
/*                                                                                  */
/* MS-Weather: class.msweather.php                                                  */
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
$module_name = 'ms_weather';
global $TableBar, $TableBackGround, $TableFontColor, $TempFontColor, $_POST,$_GET;
// Colors that can be changed for the Weather Overview in the Module

include 'msw_config.php';

class msweather
{
    /******************************************************/
    /*                                                    */
    /* Constructor function msweather()                   */
    /*                                                    */
    /******************************************************/
    public function msweather()
    {
    }

    /******************************************************/
    /*                                                    */
    /* Constructor function msw_openpage()                */
    /*                                                    */
    /******************************************************/
    public function msw_openpage( $citycode, $unit )
    {
        // Page to open
        if ( 0 == $unit ) {
            $openfile = 'http://weather.yahoo.com/forecast/' . $citycode . '_c.html';
        } else {
            $openfile = 'http://weather.yahoo.com/forecast/' . $citycode . '_f.html';
        }

        $openerror = 0;
        $mswdata = @implode ( '', file( $openfile ) );
        if ( false === $mswdata ) {
            $mswdata = '' . _MSW_OPENERROR . '';
            $openerror = 1;
        }

        return( $mswdata );
    }

    /******************************************************/
    /*                                                    */
    /* function get_msw_blockdata()                       */
    /*                                                    */
    /******************************************************/
    public function msw_saveblockdata( $cityname, $citycode, $unit, $cache_file, $module_name )
    {
        global $TempFontColor;
        // Open en load Yahoo Weather page
        $rf = $this->msw_openpage( $citycode, $unit );
        if ( $rf != '' . _MSW_OPENERROR . '' ) {
            // Filter ALL Required data for selected City
            eregi( '<!--CURCON-->(.*)<!--END CURCON-->', $rf, $all );

            // Display Information for Weather block
            $MSWblock = '<center><b>' . $cityname . ' ' . _MSW_B_TODAY . '</b></center>';
            $MSWblock .= '<center>' . $all[1] . '</center>';
            $MSWblock .= '<center><b><a href="' . XOOPS_URL . '/modules/ms_weather/index.php?op=MSWeatherShowForcast">' . _MSW_B_FORECAST . '</a></b></center>';

            // Set font size and colors of temperature block
            $MSWblock = $this->msw_settempcolors( $MSWblock, $TempFontColor );
            // Decode Language
            $MSWblock = $this->msw_languagedecode( $MSWblock, 0 );
            // Setup Select City Mechanism
            $MSWblock .= $this->msw_displaycities( $cityname, $module_name );
            // Write filtered information to cache File
            $MSWwrite = @fopen( $cache_file, 'wb' );
            if ( !$MSWwrite ) {
                $errormessage = '' . _MSW_WRITEERROR . '';
            } else {
                fwrite( $MSWwrite, "$MSWblock" );
                fclose( $MSWwrite );
            }
        } else {
            $errormessage = '' . _MSW_OPENERROR . '';
        }

        // Return data for block
        if ( isset( $errormessage ) ) {
            return( $errormessage );
        }

        return( $MSWblock );
    }

    /******************************************************/
    /*                                                    */
    /* function get_msw_readblockdata                     */
    /*                                                    */
    /******************************************************/
    public function msw_readblockdata( $cache_file )
    {
        if ( file_exists( $cache_file) ) {
            $MSWread = fopen( $cache_file, 'rb' );
            $MSWblock = fread( $MSWread, filesize( $cache_file ) );
            fclose( $MSWread );

            return( $MSWblock );
        }

        return( 'Error!' );
    }

    /******************************************************/
    /*                                                    */
    /* function get_msw_getmoduledata()                   */
    /*                                                    */
    /******************************************************/
    public function msw_getmoduledata( $cityname, $citycode, $unit, $module_name, $showgeneral, $showforecast, $showmore )
    {
        global $TableBar, $TableBackGround, $TableFontColor, $TempFontColor;
        // Open en load Yahoo Weather page
        $rf = $this->msw_openpage( $citycode, $unit );

        if ( $rf != '' . _MSW_OPENERROR . '' ) {
            $rf = $this->msw_settempcolors( $rf, $TempFontColor );
            $rf = $this->msw_languagedecode( $rf, 1 );
            $MSWModule = '';
            if ( $showgeneral ) {
                $MSWModule .= "<div align=\"center\"><center><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"50%\"><tr><td width=\"100%\">\n";
                // Display General Overview
                eregi( '<!--CURCON-->(.*)<!--endscale-->', $rf, $all );
                if ( 0 == $unit ) {
                    $MSWModule .= '<center><a href="' . XOOPS_URL . "/modules/ms_weather/index.php?op=MSWeatherShowForcast&amp;cityname=$cityname&amp;citycode=$citycode&amp;unit=1\"><b>F&#186;</b></a> | <b>C&deg;</b></center>";
                } else {
                    $MSWModule .= '<center><b>F&#186;</b> | <a href="' . XOOPS_URL . "/modules/ms_weather/index.php?op=MSWeatherShowForcast&amp;cityname=$cityname&amp;citycode=$citycode&amp;unit=0\"><b>C&deg;</b></a></center>";
                }
                $MSWModule .= '<center>' . $all[1] . '</center><br><br>';
                $MSWModule .= "</td></tr></table></center></div>\n";
            }

            if ( $showforecast ) {
                $idctry = mb_substr($citycode,0,2);
                $idctry = mb_strtolower($idctry);
                if ('gm' == $idctry) {
                    $idctry = str_replace('gm','de',$idctry);
                } elseif ('sp' == $idctry) {
                    $idctry = str_replace('sp','espanol',$idctry);
                } else {
                    $idctry = $idctry;
                }
                // Display Forecast
                eregi( '<!----------------------- FORECAST ------------------------->(.*)<!--ENDFC-->', $rf, $all );
                if ( ('uk' == $idctry) || ('fr' == $idctry) || ('de' == $idctry) || ('espanol' == $idctry) || ('br' == $idctry) ) {
                    $all[1] = str_replace( '<!-- SpaceID=0 robot -->', "<A target=\"_blank\" href=\"http://$idctry.weather.com/weather/extended/" . $citycode . '?par=yahoo&amp;site=www.yahoo.com&amp;promo=forecast">' . _MSW_EXTENDED . '</A>', $all[1] );
                } else {
                    $all[1] = str_replace( '<!-- SpaceID=0 robot -->', '<A target="_blank" href="http://www.weather.com/weather/extended/' . $citycode . '?par=yahoo&amp;site=www.yahoo.com&amp;promo=forecast">' . _MSW_EXTENDED . '</A>', $all[1] );
                }
                $all[1] = str_replace( '=#eeeeee', '=' . $TableBar, $all[1] );
                $all[1] = str_replace( '=#ffffff', '=' . $TableBackGround, $all[1] );
                $all[1] = str_replace( 'face=Arial', 'face=Arial size=2 color=' . $TableFontColor, $all[1] );
                $MSWModule .= '<center>' . $all[1] . '</center><br><br>';
            }

            if ( $showmore ) {
                // Display Current Conditions
                eregi( '<!--MORE CC-->(.*)<!--ENDMORE CC-->', $rf, $all );
                $all[1] = str_replace( '<tr bgcolor=eeeeee>', '<tr bgcolor=' . $TableBar . '>', $all[1] );
                $all[1] = str_replace( '<tr>','<tr bgColor=' . $TableBackGround . '>', $all[1] );
                $all[1] = str_replace( '<tr valign=top>','<tr bgColor=' . $TableBackGround . ' valign=top>', $all[1] );
                $all[1] = str_replace( _MSW_SHOWMORE, '<td bgcolor=' . $TableBar . ' width="100%"><font face="Arial" color=' . $TableFontColor . ' size="2"><center><b>' . _MSW_SHOWMORE . '</b></center></font></td>', $all[1] );
                $all[1] = str_replace( 'face=Arial', 'face=Arial size=2 color=' . $TableFontColor, $all[1] );
                $MSWModule .= '<center>' . $all[1] . '</center>';
            }

            return( $MSWModule );
        }

        return( '' . _MSW_OPENERROR . '' );
    }

    /******************************************************/
    /*                                                    */
    /* function msw_displaycities() block                 */
    /*                                                    */
    /******************************************************/
    public function msw_displaycities( $cityname, $module_name )
    {
        global $xoopsDB, $_POST, $_GET;
        if (isset($_POST['pcity'])) {
            $pcity = $_POST['pcity'];
        }
        if (isset($_GET['pcity'])) {
            $pcity = $_GET['pcity'];
        }
        $selcity = '<form method="post" action="' . XOOPS_URL . "/modules/ms_weather/index.php?op=MSWeatherDecode&amp;pcity=$pcity\">";
        $selcity .= '<center>' . _MSW_B_SELECTCITY . '<select name="pcity" onChange="submit()">';
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

    /******************************************************/
    /*                                                    */
    /* function msw_settempcolors( )                      */
    /* Set font size + color temperature block            */
    /******************************************************/
    public function msw_settempcolors( $rf, $TempFontColor )
    {
        $rf = str_replace( '<font face=Arial size=2>Currently:', '<font color=' . $TempFontColor . ' face=Arial size=2>Currently:', $rf ); // Temp now 
      $rf = str_replace( '<font size="5"', '<font size="5" color=' . $TempFontColor, $rf ); // Temp Now
      $rf = str_replace( '<!-- teswt -->', '<!-- teswt --><font face=Arial size=2 color=' . $TempFontColor . '>', $rf ); // Weather Tekst
      $rf = str_replace( 'Hi:', '</font><font face=Arial color=' . $TempFontColor . ' size=2>Hi:', $rf ); // Temp Hi
      $rf = str_replace( 'Lo:', '</font><font face=Arial color=' . $TempFontColor . ' size=2>Lo:', $rf ); // Temp Lo
      return( $rf );
    }

    /******************************************************/
    /*                                                    */
    /* function msw_languagedecode()                      */
    /*                                                    */
    /******************************************************/
    public function msw_languagedecode( $rf, $BorM )
    {
        $rf = str_replace( 'More Current Conditions', _MSW_MORECURRENT, $rf );
        $rf = str_replace( 'Feels Like', _MSW_FEELSLIKE, $rf );
        $rf = str_replace( 'Barometer', _MSW_BAROMETER, $rf );
        $rf = str_replace( 'Humidity', _MSW_HUMIDITY, $rf );
        $rf = str_replace( 'Visibility', _MSW_VISIBILITY, $rf );
        $rf = str_replace( 'Dewpoint', _MSW_DEWPOINT, $rf );
        $rf = str_replace( 'Sunrise', _MSW_SUNRISE, $rf );
        $rf = str_replace( 'Sunset', _MSW_SUNSET, $rf );
        $rf = str_replace( 'Unavailable', _MSW_UNAVAILABLE, $rf );
        $rf = str_replace( 'Unlimited', _MSW_UNLIMITED, $rf );

        if ( 0 == $BorM ) {
            // Use for the block shorter translations, otherwise the block becomes to wide
            $rf = str_replace( 'Today', _MSW_B_TODAY, $rf );
            $rf = str_replace( 'Currently:', _MSW_NOW, $rf );
            $rf = str_replace( 'Hi:&nbsp;',  _MSW_HI . '<br>', $rf );
            $rf = str_replace( 'Lo:&nbsp;',  _MSW_LO . '<br>', $rf );
            $rf = str_replace( 'Scattered', _MSW_B_SCATTERED, $rf );
            $rf = str_replace( 'Isolated', _MSW_B_ISOLATED, $rf );
            $rf = str_replace( 'Thunderstorms', _MSW_B_THUNDERSTORMS, $rf );
            $rf = str_replace( 'with', _MSW_B_WITH, $rf );
            $rf = str_replace( 'Thunder', _MSW_B_THUNDER, $rf );
            $rf = str_replace( 'Showers', _MSW_B_SHOWERS, $rf );
            $rf = str_replace( 'Shower', _MSW_B_SHOWER, $rf );
            $rf = str_replace( 'Partly', _MSW_B_PARTLY, $rf );
            $rf = str_replace( 'Mostly', _MSW_B_MOSTLY, $rf );
            $rf = str_replace( 'Light', _MSW_B_LIGHT, $rf );
            $rf = str_replace( 'Heavy', _MSW_B_HEAVY, $rf );
            $rf = str_replace( 'Cloudy', _MSW_B_CLOUDY, $rf );
            $rf = str_replace( 'Snow', _MSW_B_SNOW, $rf );
            $rf = str_replace( 'Haze', _MSW_B_HAZE, $rf );
            $rf = str_replace( 'Drizzle', _MSW_B_DRIZZLE, $rf );
            $rf = str_replace( 'Sunny', _MSW_B_SUNNY, $rf );
            $rf = str_replace( 'Fair', _MSW_B_FAIR, $rf );
            $rf = str_replace( 'Rain', _MSW_B_RAIN, $rf );
            $rf = str_replace( 'Wind', _MSW_B_WIND, $rf );
            $rf = str_replace( 'Few', _MSW_B_FEW, $rf );
            $rf = str_replace( 'Vicinity', _MSW_B_VICINITY, $rf );
            $rf = str_replace( 'Clouds', _MSW_B_CLOUDS, $rf );
            $rf = str_replace( 'Early', _MSW_B_EARLY, $rf );
            $rf = str_replace( 'Clearing', _MSW_B_CLEARING, $rf );
            $rf = str_replace( 'Late', _MSW_B_LATE, $rf );
            $rf = str_replace( 'Fog', _MSW_B_FOG, $rf );
            $rf = str_replace( 'Clear', _MSW_B_CLEAR, $rf );
            $rf = str_replace( 'Drifting', _MSW_B_DRIFTING, $rf );
            $rf = str_replace( 'in the', _MSW_B_INTHE, $rf );
        //$rf = str_replace( "Mon", "Mon", $rf );
        } else {
            // For the Modul display there are no restrictions with respect to the length of the names
            $rf = str_replace( 'Today', _MSW_TODAY, $rf );
            $rf = str_replace( 'Currently:', _MSW_CURRENTLY, $rf );
            $rf = str_replace( 'Hi:&nbsp;', _MSW_HIGH . '<br>', $rf );
            $rf = str_replace( 'Lo:&nbsp;', _MSW_LOW . '<br>', $rf );
            $rf = str_replace( 'Scattered', _MSW_SCATTERED, $rf );
            $rf = str_replace( 'Isolated', _MSW_ISOLATED, $rf );
            $rf = str_replace( 'Thunderstorms', _MSW_THUNDERSTORMS, $rf );
            $rf = str_replace( 'with', _MSW_WITH, $rf );
            $rf = str_replace( 'Thunder', _MSW_THUNDER, $rf );
            $rf = str_replace( 'Showers', _MSW_SHOWERS, $rf );
            $rf = str_replace( 'Shower', _MSW_SHOWER, $rf );
            $rf = str_replace( 'Partly', _MSW_PARTLY, $rf );
            $rf = str_replace( 'Mostly', _MSW_MOSTLY, $rf );
            $rf = str_replace( 'Light', _MSW_LIGHT, $rf );
            $rf = str_replace( 'Heavy', _MSW_HEAVY, $rf );
            $rf = str_replace( 'Cloudy', _MSW_CLOUDY, $rf );
            $rf = str_replace( 'Snow', _MSW_SNOW, $rf );
            $rf = str_replace( 'Haze', _MSW_HAZE, $rf );
            $rf = str_replace( 'Drizzle', _MSW_DRIZZLE, $rf );
            $rf = str_replace( 'Sunny', _MSW_SUNNY, $rf );
            $rf = str_replace( 'Fair', _MSW_FAIR, $rf );
            $rf = str_replace( 'Rain', _MSW_RAIN, $rf );
            $rf = str_replace( 'Wind', _MSW_WIND, $rf );
            $rf = str_replace( 'Few', _MSW_FEW, $rf );
            $rf = str_replace( 'Vicinity', _MSW_VICINITY, $rf );
            $rf = str_replace( 'Clouds', _MSW_CLOUDS, $rf );
            $rf = str_replace( 'Early', _MSW_EARLY, $rf );
            $rf = str_replace( 'Clearing', _MSW_CLEARING, $rf );
            $rf = str_replace( 'Late', _MSW_LATE, $rf );
            $rf = str_replace( 'Fog', _MSW_FOG, $rf );
            $rf = str_replace( 'Clear', _MSW_CLEAR, $rf );
            $rf = str_replace( 'Drifting', _MSW_DRIFTING, $rf );
            $rf = str_replace( 'in the', _MSW_INTHE, $rf );
            $rf = str_replace( 'AM', _MSW_AM, $rf );
            $rf = str_replace( 'PM', _MSW_PM, $rf );
            $rf = str_replace( 'High:', _MSW_HIGH, $rf );
            $rf = str_replace( 'Low:', _MSW_LOW, $rf );
            $rf = str_replace( 'Tomorrow', _MSW_TOMORROW, $rf );
            $rf = str_replace( 'Sun', _MSW_SUN, $rf );
            $rf = str_replace( 'Mon', _MSW_MON, $rf );
            $rf = str_replace( 'Tue', _MSW_TUE, $rf );
            $rf = str_replace( 'Wed', _MSW_WED, $rf );
            $rf = str_replace( 'Thu', _MSW_THU, $rf );
            $rf = str_replace( 'Fri', _MSW_FRI, $rf );
            $rf = str_replace( 'Sat', _MSW_SAT, $rf );
            $rf = str_replace( 'Day', _MSW_DAY, $rf );
            $rf = str_replace( 'kph', _MSW_KPH, $rf );
        }

        return( $rf );
    }
} 

?>

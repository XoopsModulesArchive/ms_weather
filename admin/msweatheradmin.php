<?php

/************************************************************************************/
/* MS-Weather:                                                                      */
/* v1.2  28/01/2004 FOR XOOPS v2                                                    */
/* Ported by: Sylvain B. (sylvain@123rando.com)                                     */
/* http://123rando.com                                                              */
/*                                                                                  */
/*                                                                                  */
/* MS-Weather: msweatheradmin.php                                                   */
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

require_once 'admin_header.php';

/******************************************************************************/
/* FUNCTION: MSWeatherAdminGUI()                                              */
/******************************************************************************/
function MSWeatherAdminGUI()
{
    global $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsLogger, $module_name;

    // Read Module Display Options
    $result = $xoopsDB->query( 'select mswv1, mswv2, mswv3 from ' . $xoopsDB->prefix('msweather') . " where type='2'" );
    list( $showgeneral, $showforecast, $showmore ) = $xoopsDB->fetchRow( $result );

    // Read Preferred/Default city codes
    $result = $xoopsDB->query( 'select mswv1, mswv2 from ' . $xoopsDB->prefix('msweather') . " where type='3'" );
    list( $cityname, $citycode ) = $xoopsDB->fetchRow( $result );

    // Read Preferred Unit ( [0] = Celsius - [1] = Fahrenheid and cache parameters
    $result = $xoopsDB->query( 'select mswv1, mswv2, mswv3 from ' . $xoopsDB->prefix('msweather') . " where type='1'" );
    list( $unit, $cachetype, $cacheinterval ) = $xoopsDB->fetchRow( $result );
    xoops_cp_header();
    // Configure Unit + Cache Options
    echo '<br>';

    MSWeatherPlotHeader( _MSW_ADM );

    echo '<form action="msweatheradmin.php" method="post">
   <div align="center"><center>
   <table border="1" cellpadding="2" cellspacing="0" width="80%" class="head">
   <tr >
      <td width="33%" align="center" height="30"><b>' . _MSW_UNIT . '</b></td>
      <td width="33%" align="center" height="30"><b>' . _MSW_CACHETYPE . '</b></td>
      <td width="33%" align="center" height="30"><b>' . _MSW_CACHEINTERVAL . '</b></td>
   </tr>
   <tr>
      <td width="33%" class="even" align="center"><select size="1" name="unit">';
    if ( 0 == $unit ) {
        echo '<option selected>' . _MSW_CELSIUS . '</option><option>' . _MSW_FAHRENHEID . '</option></select></td>';
    } else {
        echo '<option>' . _MSW_CELSIUS . '</option><option selected>' . _MSW_FAHRENHEID . '</option></select></td>';
    }
    echo '<td width="33%" class="even" align="center"><select size="1" name="cachetype">';

    if ( 0 == $cachetype ) {
        echo '<option selected>' . _MSW_CDAYS . '</option><option>' . _MSW_CHOURS . '</option><option>' . _MSW_CMINUTES . '</option></select></td>';
    } else {
        if ( 1 == $cachetype ) {
            echo '<option>' . _MSW_CDAYS . '</option><option selected>' . _MSW_CHOURS . '</option><option>' . _MSW_CMINUTES . '</option></select></td>';
        } else {
            echo '<option>' . _MSW_CDAYS . '</option><option>' . _MSW_CHOURS . '</option><option selected>' . _MSW_CMINUTES . '</option></select></td>';
        }
    }
    echo "<td width=\"33%\" class=\"even\" align=\"center\"><input type=\"text\" name=\"cacheinterval\" value=\"$cacheinterval\" size=\"10\" maxlength=\"10\"></td>
   </tr></table></center></div>";
    echo '<center>' . _MSW_CACHECOMMENTS . '</center><br><br>';

    echo "<div align=\"center\"><center>\n";
    echo "<table border=\"1\" cellpadding=\"2\" cellspacing=\"0\" width=\"80%\" class=\"outer\">\n";
    echo '<tr><th colspan="2"><center><b>' . _MSW_DISPLAY . "</b></center></th></td></tr>\n";
    echo '<tr ><td width="60%" class="head">&nbsp;<b>' . _MSW_SHOWGENERAL . "</b></td>\n";
    echo "<td width=\"40%\" class=\"even\">\n";
    if ( 0 == $showgeneral ) {
        echo '<input type="radio" name="showgeneral" value="0" checked><b>&nbsp;' . _MSW_NO . '</b>&nbsp;&nbsp;
      <input type="radio" name="showgeneral" value="1"><b>&nbsp;' . _MSW_YES . '</b></td>';
    } else {
        echo '<input type="radio" name="showgeneral" value="0" ><b>&nbsp;' . _MSW_NO . '</b>&nbsp;&nbsp;
      <input type="radio" name="showgeneral" value="1" checked><b>&nbsp;' . _MSW_YES . '</b></td>';
    }
    echo "</tr>\n";
    echo '<tr ><td width="60%" class="head">&nbsp;<b>' . _MSW_SHOWFORECAST . "</b></td>\n";
    echo "<td width=\"40%\" class=\"even\">\n";
    if ( 0 == $showforecast ) {
        echo '<input type="radio" name="showforecast" value="0" checked><b>&nbsp;' . _MSW_NO . '</b>&nbsp;&nbsp;
      <input type="radio" name="showforecast" value="1"><b>&nbsp;' . _MSW_YES . '</b></td>';
    } else {
        echo '<input type="radio" name="showforecast" value="0" ><b>&nbsp;' . _MSW_NO . '</b>&nbsp;&nbsp;
      <input type="radio" name="showforecast" value="1" checked><b>&nbsp;' . _MSW_YES . '</b></td>';
    }
    echo "</tr>\n";
    echo '<tr ><td width="60%" class="head">&nbsp;<b>' . _MSWA_SHOWMORE . "</b></td>\n";
    echo "<td width=\"40%\" class=\"even\">\n";
    if ( 0 == $showmore ) {
        echo '<input type="radio" name="showmore" value="0" checked><b>&nbsp;' . _MSW_NO . '</b>&nbsp;&nbsp;
      <input type="radio" name="showmore" value="1"><b>&nbsp;' . _MSW_YES . '</b></td>';
    } else {
        echo '<input type="radio" name="showmore" value="0" ><b>&nbsp;' . _MSW_NO . '</b>&nbsp;&nbsp;
      <input type="radio" name="showmore" value="1" checked><b>&nbsp;' . _MSW_YES . '</b></td>';
    }
    echo "</tr>\n";
    echo '</table></center></div>';

    echo '<input type="hidden" name="op" value="MSWeatherStore2">
   <p align="center"><input type="submit" value="' . _MSW_SAVE . '"></p>
   </form>';
    echo '<br>';

    // Show City Overview + Add/Edit/Delete/Default Options
    echo '<br>';
    echo '<center><a href="msweatheradmin.php?op=MSWeatherMaintain&amp;id=0&amp;dowhat=1"><img border="0" src="../images/edit.gif">&nbsp;<b>' . _MSW_NEWCITYCODE . '</b></a></center>';
    echo '<br><div align="center"><center>
   <table border="1" cellpadding="2" cellspacing="0" width="80%" class="outer">
   <tr >
      <td class="bg3" width="10%" align="center" height="30"><b>' . _MSW_EDIT . '</b></td>
      <td class="bg3"width="15%" align="center" height="30"><b>' . _MSW_DELETE . '</b></td>
      <td class="bg3"width="15%" align="center" height="30"><b>' . _MSW_DEF . '</b></td>
      <td class="bg3"width="30%" align="center" height="30"><b>' . _MSW_CITYNAME . '</b></td>
      <td class="bg3"width="30%" align="center" height="30"><b>' . _MSW_CITYCODE . '</b></td>
   </tr>';

    $BoxCounter = 0;
    $result = $xoopsDB->query( 'select id, mswv1, mswv2 from ' . $xoopsDB->prefix('msweather') . " where type='4' ORDER BY mswv1 ASC" );
    while ( list( $id, $dbcityname, $dbcitycode ) = $xoopsDB->fetchRow( $result ) ) {
        $BoxCounter += 1;
        if ( $BoxCounter % 2 ) {
            $cellcolor = '#C2CDD6';
        } else {
            $cellcolor = '#DEE3E7';
        }
        echo "
      <tr >
         <td width=\"10%\" bgcolor=\"$cellcolor\"><a href=\"msweatheradmin.php?op=MSWeatherMaintain&amp;id=$id&amp;dowhat=0\"><b>" . _MSW_EDIT . "</b></a></td>
         <td width=\"15%\" bgcolor=\"$cellcolor\"><a href=\"msweatheradmin.php?op=MSWeatherDelete&amp;id=$id\"><b>" . _MSW_DELETE . '</b></a></td>';
        $dbcityname = stripslashes($dbcityname);
        if ( $dbcityname == $cityname ) {
            echo '<td width="15%" bgcolor="#E18A00"><center><b>' . _MSW_DEF . '</b></center></td>';
        } else {
            echo "<td width=\"15%\"  bgcolor=\"$cellcolor\"><a href=\"msweatheradmin.php?op=MSWeatherDefault&amp;id=$id\"><b><center>" . _MSW_POSS . '</center></b></a></td>';
        }

        echo "
         <td width=\"30%\" bgcolor=\"$cellcolor\">$dbcityname</td>
         <td width=\"30%\" bgcolor=\"$cellcolor\">$dbcitycode</td>
      </tr>";
    }
    echo '</table></center></div>';
    echo '<br>';
    MSWeatherPlotFooter();
    xoops_cp_footer();
}

/******************************************************************************/
/* FUNCTION: MSWeatherMaintain()                                              */
/* dowhat = 0 : Edit - dowhat = 1 : Add                                       */
/******************************************************************************/
function MSWeatherMaintain( $id, $dowhat )
{
    global $xoopsDB, $module_name;

    $result = $xoopsDB->query( 'select mswv1, mswv2 from ' . $xoopsDB->prefix('msweather') . " where id='$id'" );
    list( $cityname, $citycode ) = $xoopsDB->fetchRow( $result );
    $cityname = stripslashes($cityname);
    xoops_cp_header();
    if ( 0 == $dowhat ) {
        MSWeatherPlotHeader( _MSW_EDIT . ' ' . $dbcityname );
        echo '<br>';
    } else {
        MSWeatherPlotHeader( _MSW_NEWCITYCODE );
        echo '<br>';
    }
    if ( 0 == $dowhat ) {
        ( _MSW_EDIT . ' ' . $dbcityname );
        echo '<br>';
    } else {
        ( _MSW_NEWCITYCODE );
        echo '<br>';
    }
    echo '<form action="msweatheradmin.php" method="post">
   <div align="center"><center>
   <table border="1" cellpadding="2" cellspacing="0" width="80%" class="outer">
   <tr >
      <td class="head" width="50%" align="center" height="30">' . _MSW_CITYNAME . '</td>
      <td class="head" width="40%" align="center" height="30">' . _MSW_CITYCODE . "</td>
   </tr>
   <tr >
      <td width=\"50%\" class=\"even\"><input type=\"text\" name=\"cityname\" value=\"$cityname\" size=\"45\" maxlength=\"100\"></td>
      <td width=\"40%\" class=\"even\"><input type=\"text\" name=\"citycode\" value=\"$citycode\" size=\"45\" maxlength=\"10\"></td>
   </tr>
   </table></center></div>
   <input type=\"hidden\" name=\"id\" value=\"$id\">
   <input type=\"hidden\" name=\"dowhat\" value=\"$dowhat\">
   <input type=\"hidden\" name=\"op\" value=\"MSWeatherStore1\">
   <p align=\"center\"><input type=\"submit\" value=\"" . _MSW_SAVE . '"></p>
   </form>';

    echo '<br><center>[ <a href="javascript:history.go( -1 )"><b>' . _MSW_GOBACK . '</b></a> ]</center><br>';

    MSWeatherPlotFooter();
    xoops_cp_footer();
}

/******************************************************************************/
/* FUNCTION: MSWeatherStore1()                                                */
/* dowhat = 0 : Edit - dowhat = 1 : Add                                       */
/******************************************************************************/
function MSWeatherStore1( $id, $dowhat, $cityname, $citycode )
{
    global $xoopsDB;

    if ( ( '' == $cityname ) || ( '' == $citycode ) ) {
        MSWeatherError ( _MSW_INPUTERROR );
    } else {
        $cityname = addslashes($cityname);
        if ( 1 == $dowhat ) {
            $result = $xoopsDB->query( 'insert into ' . $xoopsDB->prefix('msweather') . " ( type, mswv1, mswv2, mswv3 ) values ( '4', '$cityname', '$citycode', '' )" );
            if ( !$result ) {
                MSWeatherError ( _MSW_INSERTERROR );
            } else {
                MSWeatherAdminGUI();
            }
        } else {
            $result = $xoopsDB->query( 'update ' . $xoopsDB->prefix('msweather') . " set mswv1 = '$cityname', mswv2 = '$citycode', mswv3 = '' where id='$id'" );
            if ( !$result ) {
                MSWeatherError ( _MSW_UPDATEERROR );
            } else {
                MSWeatherAdminGUI();
            }
        }
    }
}

/******************************************************************************/
/* FUNCTION: MSWeatherStore2()                                                */
/******************************************************************************/
function MSWeatherStore2( $unit, $cachetype, $cacheinterval, $showgeneral, $showforecast, $showmore )
{
    global $xoopsDB;

    if ( '' == $cacheinterval ) {
        MSWeatherError ( _MSW_CACHEINTERROR );
    } else {
        if ( _MSW_CELSIUS == $unit ) {
            $unit = 0;
        } else {
            $unit = 1;
        }
        if ( _MSW_CDAYS == $cachetype ) {
            $cachetype = 0;
        } else {
            if ( _MSW_CHOURS == $cachetype ) {
                $cachetype = 1;
            } else ( $cachetype = 2 );
        }

        $result1 = $xoopsDB->query( 'update ' . $xoopsDB->prefix('msweather') . " set mswv1 = '$unit', mswv2 = '$cachetype', mswv3 = '$cacheinterval' where type='1'" );
        $result2 = $xoopsDB->query( 'update ' . $xoopsDB->prefix('msweather') . " set mswv1 = '$showgeneral', mswv2 = '$showforecast', mswv3 = '$showmore' where type='2'" );
        if ( !$result1 || !$result2 ) {
            MSWeatherError ( _MSW_CACHEERROR );
        } else {
            MSWeatherRewriteChacheFile();
            MSWeatherAdminGUI();
        }
    }
}

/******************************************************************************/
/* FUNCTION: MSWeatherRewriteChacheFile()                                     */
/* Rewrite Cache File after making changes                                    */
/******************************************************************************/
function MSWeatherRewriteChacheFile()
{
    global $xoopsDB, $module_name;

    require_once '../class.msweather.php';
    $msw = new msweather();

    // Read Preferred city codes
    $result = $xoopsDB->query( 'select mswv1, mswv2 from ' . $xoopsDB->prefix('msweather') . " where type='3'" );
    list( $cityname, $citycode ) = $xoopsDB->fetchRow( $result );

    // Read Preferred Unit ( [0] = Celsius - [1] = Fahrenheid and cache parameters
    $result = $xoopsDB->query( 'select mswv1, mswv2, mswv3 from ' . $xoopsDB->prefix('msweather') . " where type='1'" );
    list( $unit, $cachetype, $cacheinterval ) = $xoopsDB->fetchRow( $result );

    $cache_file = XOOPS_ROOT_PATH . "modules/$module_name/cache/" . $citycode . '.dat';
    $msw->msw_saveblockdata( $cityname, $citycode, $unit, $cache_file, $module_name );
}

/******************************************************************************/
/* FUNCTION: MSWeatherDelete()                                                */
/******************************************************************************/
function MSWeatherDelete($id)
{
    global $xoopsDB;
    require_once 'admin_header.php';
    $result = 'DELETE FROM ' . $xoopsDB->prefix('msweather') . " WHERE id=$id";
    $GLOBALS['xoopsDB']->queryF($result);
    if ( !$result ) {
        MSWeatherError ( _MSW_DELETEERROR );
    } else {
        MSWeatherAdminGUI();
    }
}

/******************************************************************************/
/* FUNCTION: MSWeatherDefault()                                               */
/******************************************************************************/
function MSWeatherDefault($id)
{
    global $xoopsDB;
    require_once 'admin_header.php';
    // Read city codes of city that should be preferred
    $result = $xoopsDB->query( 'select mswv1, mswv2 from ' . $xoopsDB->prefix('msweather') . " where id='$id'" );
    list( $cityname, $citycode ) = $xoopsDB->fetchRow( $result );
    $result = 'update ' . $xoopsDB->prefix('msweather') . " set mswv1 = '$cityname', mswv2 = '$citycode', mswv3 = '' where type='3'";
    $GLOBALS['xoopsDB']->queryF($result);
    if ( !$result ) {
        MSWeatherError ( _MSW_DEFAULTERROR );
    } else {
        MSWeatherAdminGUI();
    }
}
/******************************************************************************/
/* FUNCTION: MSWeatherError()                                                 */
/******************************************************************************/
function MSWeatherError( $errormessage )
{
    xoops_cp_header();
    OpenTable();
    echo '<h4 align=center><b>' . $errormessage . '</b></h4>';
    CloseTable();
    echo '<br>';
    echo '<center>[ <a href="javascript:history.go( -1 )">' . _MSW_GOBACK . "</a> ]</center>\n";
    xoops_cp_footer();
}

/******************************************************************************/
/* FUNCTION: MSWeatherPlotHeader()                                            */
/******************************************************************************/
function MSWeatherPlotHeader( $msmessage )
{
    global $xoopsDB;

    // Read Version Number
    $result = $xoopsDB->query( 'select mswv1, mswv2, mswv3 from ' . $xoopsDB->prefix('msweather') . " where type='0'" );
    list( $copyright, $mname, $version ) = $xoopsDB->fetchRow( $result );
    OpenTable();
    echo '<center><font color="FB0000"><h3>' . $mname . ' ' . $version . ' - ' . $msmessage . "</font></h3></center>\n";
}

/******************************************************************************/
/* FUNCTION: MSWeatherPlotFooter()                                            */
/******************************************************************************/
function MSWeatherPlotFooter()
{
    global $xoopsDB;

    // Read Version Number
    $result = $xoopsDB->query( 'select mswv1, mswv2, mswv3 from ' . $xoopsDB->prefix('msweather') . " where type='0'" );
    list( $copyright, $mname, $version ) = $xoopsDB->fetchRow( $result );
    echo '<center><font class="tiny">' . $mname . ' ' . $version . ' ' . _BY . ' <a href="http://www.matyscripts.com">' . $copyright . "</a></font></center>\n";
    CloseTable();
}

/***************************************************/
/****************** PROGRAM START ******************/
/***************************************************/
global $_POST,$_GET;

if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}
if (isset($_GET['dowhat'])) {
    $dowhat = $_GET['dowhat'];
}
if (isset($_POST['dowhat'])) {
    $dowhat = $_POST['dowhat'];
}
if (isset($_GET['unit'])) {
    $unit = $_GET['unit'];
}
if (isset($_POST['unit'])) {
    $unit = $_POST['unit'];
}
if (isset($_GET['cachetype'])) {
    $cachetype = $_GET['cachetype'];
}
if (isset($_POST['cachetype'])) {
    $cachetype = $_POST['cachetype'];
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
if (isset($_POST['id'])) {
    $id = $_POST['id'];
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
if (isset($_GET['cacheinterval'])) {
    $cacheinterval = $_GET['cacheinterval'];
}
if (isset($_POST['cacheinterval'])) {
    $cacheinterval = $_POST['cacheinterval'];
}
if (isset($_GET['showgeneral'])) {
    $showgeneral = $_GET['showgeneral'];
}
if (isset($_POST['showgeneral'])) {
    $showgeneral = $_POST['showgeneral'];
}
if (isset($_GET['showforecast'])) {
    $showforecast = $_GET['showforecast'];
}
if (isset($_POST['showforecast'])) {
    $showforecast = $_POST['showforecast'];
}
if (isset($_GET['showmore'])) {
    $showmore = $_GET['showmore'];
}
if (isset($_POST['showmore'])) {
    $showmore = $_POST['showmore'];
}

switch ( $op ) {
    case 'MSWeatherAdminGUI':
       MSWeatherAdminGUI();
    break;
    case 'MSWeatherMaintain':
       MSWeatherMaintain( $id, $dowhat );
    break;
    case 'MSWeatherStore1':
       MSWeatherStore1( $id, $dowhat, $cityname, $citycode );
    break;
    case 'MSWeatherStore2':
       MSWeatherStore2( $unit, $cachetype, $cacheinterval, $showgeneral, $showforecast, $showmore );
    break;
    case 'MSWeatherRewriteChacheFile':
       MSWeatherRewriteChacheFile();
    break;
    case 'MSWeatherDelete':
       MSWeatherDelete( $id );
    break;
    case 'MSWeatherDefault':
       MSWeatherDefault( $id );
    break;
    case 'MSWeatherError':
       MSWeatherError( $errormessage );
    break;
    case 'MSWeatherPlotHeader':
       MSWeatherPlotHeader();
    break;
    case 'MSWeatherPlotFooter':
       MSWeatherPlotFooter();
    break;
   default:
       MSWeatherAdminGUI();
}

?>

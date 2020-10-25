<?php

// ------------------------------------------------------------------------- //
//                           MS-WEATHER for Xoops                            //
//                              Version:  1.2                                //
//***************************************************************************//
// MS-Weather:                                                               //
// v1.2  28/01/2004 FOR XOOPS v2                                             //
// Ported by: Sylvain B. (sylvain@123rando.com)                              //
// http://123rando.com                                                       //
//                                                                           //
//---------------------------------------------------------------------------//
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//---------------------------------------------------------------------------//

include '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsmodule.php';
include XOOPS_ROOT_PATH . '/include/cp_functions.php';
if ( $xoopsUser ) {
    $xoopsModule = XoopsModule::getByDirname('ms_weather');

    if (!$xoopsUser->isAdmin( $xoopsModule->mid() ) ) {
        redirect_header( XOOPS_URL . '/', 3, _NOPERM );

        exit();
    }
} else {
    redirect_header( XOOPS_URL . '/', 3, _NOPERM );

    exit();
} 
if ( file_exists( '../language/' . $xoopsConfig['language'] . '/admin.php' ) ) {
    include '../language/' . $xoopsConfig['language'] . '/admin.php';
} else {
    include '../language/english/admin.php';
} 

?>

<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   Portions of this program are derived from publicly licensed software
 *   projects including, but not limited to phpBB, Magelo Clone,
 *   EQEmulator, EQEditor, and Allakhazam Clone.
 *
 *                                  Author:
 *                           Maudigan(Airwalking)
 *
 *   February 24, 2014 - Added backstab damage (Maudigan c/o Kinglykrab)
 *   February 24, 2014 - Spelling--uncommented (Maudigan c/o Kinglykrab)
 *   February 25, 2014 - added heroic and aug types (Maudigan c/o Kinglykrab)
 *   February 25, 2014 - fixed maxcharges condition (Maudigan c/o Kinglykrab)
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *   October 4, 2014 - Maudigan
 *      fixed call to nonexistent function mycb_message_die
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences.
 *      Implemented new database wrapper.
 *   October 3, 2016 - Maudigan
 *      Made the spell links customizable
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   September 7, 2019 - Kinglykrab
 *      Added a cleaner itemstats list view
 *   April 4, 2020 - Maudigan
 *     cap bag slot count with a constant
 *   April 25, 2020 - Maudigan
 *     relocated GetFieldByQuery to db.php
 *   March 16, 2022 - Maudigan
 *     added item type to item display
 ***************************************************************************/


if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}
include_once(__DIR__ . "/global.php");
include_once(__DIR__ . "/db.php");

function getstancename($val) {
   switch($val) {
      case 1: return "Passive"; break;
		case 2: return "Balanced"; break;
		case 3: return "Efficient"; break;
		case 4: return "Reactive"; break;
		case 5: return "Aggressive"; break;
		case 6: return "Assist"; break;
		case 7: return "Burn"; break;
		case 8: return "Efficient2"; break;
		case 9: return "AEBurn"; break;
      default: return "$val?"; break;
   }
}
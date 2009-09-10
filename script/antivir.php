<?

/***********************************************************************

  Copyright (C) 2007  dimka|ne_zvezda (k_dmitriy@inbox.ru)

  This file is part of up2.0.

  up2.0 is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  up2.0 is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/

include_once '../functions.inc.php';

$datas = get_mysql_array ("SELECT id,location,sub_location FROM up WHERE antivir_checked='0' AND deleted='0' LIMIT 5000");
if (is_array ($datas) && count ($datas) > 0)
{
	foreach ($datas as $rec)
	{
		$file_id = $rec['id'];
		$file = $GLOBALS['upload_dir'].$rec['sub_location'].'/'.$rec['location'];

		$antivir_checked_avira = 0;

		$antivir_checked_avira = antivir_check_file_avira ($file);

		if ($antivir_checked_avira == 1)
		{
			// update db
			dbquery ("UPDATE up SET antivir_checked='1' WHERE id='$file_id'");
		}
		else if ($antivir_checked_avira == 2)
		{
			// update db
			dbquery ("UPDATE up SET antivir_checked='2' WHERE id='$file_id'");
			remove_file_by_id ($file_id, "файл заражен вирусом");
		}
	}

	// clear stat cache
	clear_stat_cache ();
}

exit ();

?>

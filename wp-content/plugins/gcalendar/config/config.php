<?php

	$plugin_cf['gcalendar']['localtime']="fr_FR.utf8";
	$plugin_cf['gcalendar']['google_account']="maisonenjeu@gmail.com";
	$plugin_cf['gcalendar']['html_template']="<div class=\"vevent\">
    <div class=\"summary\">
      <span class=\"title\">#TITLE#</span>
      <abbr class=\"dtstart\" title=\"#TIME_ISO#\"><span class=\"weekday\">#WEEKDAY#</span> <span class=\"daynum\">#DAYNUM#</span> <span class=\"month\">#MONTH#</span>
      </abbr>
      <a class=\"location\" href=\"http://maps.google.fr/maps?hl=fr&q=#LOCATION#+Le+Mans\">#LOCATION#</a>
    </div>
    <div class=\"content\">#CONTENT#</div>
</div>";
	$plugin_cf['gcalendar']['keywords']="time_iso,weekday,daynum,month,time,location,title,content";

?>
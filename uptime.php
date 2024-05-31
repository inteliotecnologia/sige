<?php

echo "<b>Data/hora</b>:". date("d/m/Y H:i:s") ."<br /><br />";

$uptime = trim(exec("cat /proc/uptime"));
/*Dprint_r($uptime);*/
//echo "<br/>";
$uptime = explode(" ", $uptime);
$idletime=$uptime[1];
$uptime=$uptime[0];


$day=86400;
$days=floor($uptime/$day);
echo "<strong>Uptime do servidor:</strong> $days dias e ";
$utdelta=$uptime-($days*$day);

$hour=3600;
$hours=floor($utdelta/$hour);
echo "$hours hora(s), ";
$utdelta-=$hours*$hour;

$minute=60;
$minutes=floor($utdelta/$minute);
echo "$minutes minuto(s) e ";
$utdelta-=round($minutes*$minute,2);

echo number_format($utdelta, 0) ." segundos.";

echo "<br/><b>Carga aproximada do processador:</b> ";
echo round((1-($idletime/$uptime))*100,3);
echo "%";
?>
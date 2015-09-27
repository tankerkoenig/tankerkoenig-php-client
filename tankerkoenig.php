<?php
/*** TANKERKOENIG.DE (http://www.tankerkoenig.de) ***
*** PHP-Script v1.0 by Bayaro ***

Ihr müsst euch nur einen API-Key per E-Mail zuschicken lassen,
dann die entsprechenden Konfigurationen hier am Anfang
des Skriptes eintragen und dann das Skript einmal ausführen.
Es werden, unterhalb des Skriptes dann Kategorien und
Dummy-Module angelegt, in welchen die entsprechenden Daten
der jeweiligen Tankstelle(n) abgespeichert werden.

API Key hier anfordern (kommt sofort per E-Mail):
https://creativecommons.tankerkoenig.de/#register

Dokumentation zur API befindet sich hier:
https://creativecommons.tankerkoenig.de/#techInfo

API Daten stehen unter der Creative-Commons-Lizenz “CC BY 4.0”
https://creativecommons.org/licenses/by/4.0/deed.de
*/


$APIkey = "*********************************";  // API Key, den ihr per E-Mail bekommen habt )


/***** KONFIGURATION FÜR DIE UMKREISSUCHE ********************************************************************/
$lat = 50.26;      // Latitude
$lng = 8.96;       // Longitude
$radius = 5;       // Radius in Kilometern (max. 25km Umkreis)
$sort = "dist";    // Sortieren nach Preis oder Distanz (price, dist)
$type = "diesel";  // Spritsorte (e5, e10, diesel)
/*************************************************************************************************************/
Tankerkoenig_Umkreissuche($APIkey, $lat, $lng, $radius, $sort, $type);  // Auskommentieren, wenn nicht gewünscht


/***** KONFIGURATION FÜR DIE DETAILABFRAGE *******************************************************************/
// ID(s) eurer Tankstelle(n) eintragen (z.B. über Umkreissuche auslesen lassen)
// Diese 3 Eintrage sind nur als Beispiele gedacht. Ihr könnt 1 oder mehr solcher Einträge/Zeilen anlegen.
$Tankstellen[] = "c596bf7e-7845-4b1d-9755-d339692560b0";
$Tankstellen[] = "c1b456c8-b782-41d8-a960-466c4088a463";
$Tankstellen[] = "5ff6dfba-0932-4407-ac6e-9c5b96324005";
/*************************************************************************************************************/
Tankerkoenig_Detailabfrage($APIkey, $Tankstellen);  // Auskommentieren, wenn nicht gewünscht





/******** AB HIER NICHTS MEHR ÄNDERN ********/
function Tankerkoenig_Umkreissuche($APIkey, $lat, $lng, $radius, $sort, $type) {
	// API abfragen und dekodieren
	$json = file_get_contents("https://creativecommons.tankerkoenig.de/json/list.php?lat=$lat&lng=$lng&rad=$radius&sort=$sort&type=$type&apikey=$APIkey");
	if ($json === false) {
	   echo "FEHLER - Die Tankerkoenig-API konnte nicht abgefragt werden!";
	   return;
	}
	$data = json_decode($json);

	// Daten der Tankstellen in Array speichern
	$TankstellenAR = $data->stations;

	// Daten der Tankstellen aus Array auslesen und HTML-Code generieren
	$HTML = '<html><b><u>Tankerkoenig.de - Umkreissuche (PHP Script by Bayaro)</u></b><br><br>';
	$i = 0;
	foreach ($TankstellenAR as $TankstelleAR) {
		$TankstelleName = utf8_decode($TankstellenAR[$i]->name);
		$TankstelleMarke = utf8_decode($TankstellenAR[$i]->brand);
		$TankstelleDistanz = (float)utf8_decode($TankstellenAR[$i]->dist);
		$TankstellePreis = (float)utf8_decode($TankstellenAR[$i]->price);
		$TankstelleID = utf8_decode($TankstellenAR[$i]->id);
		$TankstelleStrasse = utf8_decode($TankstellenAR[$i]->street);
		$TankstelleHausnummer = utf8_decode($TankstellenAR[$i]->houseNumber);
		$TankstellePLZ = utf8_decode($TankstellenAR[$i]->postCode);
		$TankstelleOrt = utf8_decode($TankstellenAR[$i]->place);
		$TankstelleAnschrift = $TankstellePLZ." ".$TankstelleOrt.", ".$TankstelleStrasse." ".$TankstelleHausnummer;

		// HTML-Code generieren
		$HTML .= '<table border="1">';
		$HTML .= '<tr><th colspan="2" align="left">'.$TankstelleName.'</th></tr>';
		$HTML .= '<tr><td>Marke</td><td>'.$TankstelleMarke.'</td></tr>';
		$HTML .= '<tr><td>Distanz</td><td>'.$TankstelleDistanz.'</td></tr>';
		$HTML .= '<tr><td>Preis</td><td>'.$TankstellePreis.'</td></tr>';
		$HTML .= '<tr><td>ID</td><td>'.$TankstelleID.'</td></tr>';
		$HTML .= '<tr><td>Strasse</td><td>'.$TankstelleStrasse.'</td></tr>';
		$HTML .= '<tr><td>Hausnummer</td><td>'.$TankstelleHausnummer.'</td></tr>';
		$HTML .= '<tr><td>PLZ</td><td>'.$TankstellePLZ.'</td></tr>';
		$HTML .= '<tr><td>Ort</td><td>'.$TankstelleOrt.'</td></tr>';
		$HTML .= '<tr><td>Anschrift</td><td>'.$TankstelleAnschrift.'</td></tr>';
		$HTML .= '</table><br><br>';
		$i++;
	}
	echo $HTML;
}


function Tankerkoenig_Detailabfrage($APIkey, $TankstellenAR) {
	// Daten der Tankstelle(n) auslesen und HTML-Code generieren
	$HTML = '<html><b><u>Tankerkoenig.de - Detailsuche (PHP Script by Bayaro)</u></b><br><br>';

	foreach ($TankstellenAR as $TankstelleID) {
	  $json = file_get_contents("https://creativecommons.tankerkoenig.de/json/detail.php?id=$TankstelleID&apikey=$APIkey");
	  if ($json === false) {
		   echo "FEHLER - Die Tankerkoenig-API konnte nicht abgefragt werden!";
		   return;
		}
		$Tankstelle = json_decode($json);
		$TankstelleName = utf8_decode($Tankstelle->station->name);
		$TankstelleMarke = utf8_decode($Tankstelle->station->brand);
		$TankstellePreisE5 = (float)utf8_decode($Tankstelle->station->e5);
		$TankstellePreisE10 = (float)utf8_decode($Tankstelle->station->e10);
		$TankstellePreisDIESEL = (float)utf8_decode($Tankstelle->station->diesel);
		$TankstelleGeoffnet = (boolean)utf8_decode($Tankstelle->station->isOpen);
		$TankstelleGeoffnetVon = utf8_decode($Tankstelle->station->openingTimes[0]->start);
		$TankstelleGeoffnetBis = utf8_decode($Tankstelle->station->openingTimes[0]->end);
		$TankstelleID = utf8_decode($Tankstelle->station->id);
		$TankstelleStrasse = utf8_decode($Tankstelle->station->street);
		$TankstelleHausnummer = utf8_decode($Tankstelle->station->houseNumber);
		$TankstellePLZ = utf8_decode($Tankstelle->station->postCode);
		$TankstelleOrt = utf8_decode($Tankstelle->station->place);
		$TankstelleAnschrift = $TankstellePLZ." ".$TankstelleOrt.", ".$TankstelleStrasse." ".$TankstelleHausnummer;

    // HTML-Code generieren
    $HTML .= '<table border="1">';
		$HTML .= '<tr><th colspan="2" align="left">'.$TankstelleName.'</th></tr>';
		$HTML .= '<tr><td>Marke</td><td>'.$TankstelleMarke.'</td></tr>';
		$HTML .= '<tr><td>Preis E5</td><td>'.$TankstellePreisE5.'</td></tr>';
		$HTML .= '<tr><td>Preis E10</td><td>'.$TankstellePreisE10.'</td></tr>';
		$HTML .= '<tr><td>Preis Diesel</td><td>'.$TankstellePreisDIESEL.'</td></tr>';
		$HTML .= '<tr><td>Geöffnet</td><td>'.$TankstelleGeoffnet.'</td></tr>';
		$HTML .= '<tr><td>Geöffnet von</td><td>'.$TankstelleGeoffnetVon.'</td></tr>';
		$HTML .= '<tr><td>Geöffnet bis</td><td>'.$TankstelleGeoffnetBis.'</td></tr>';
		$HTML .= '<tr><td>ID</td><td>'.$TankstelleID.'</td></tr>';
		$HTML .= '<tr><td>Strasse</td><td>'.$TankstelleStrasse.'</td></tr>';
		$HTML .= '<tr><td>Hausnummer</td><td>'.$TankstelleHausnummer.'</td></tr>';
		$HTML .= '<tr><td>PLZ</td><td>'.$TankstellePLZ.'</td></tr>';
		$HTML .= '<tr><td>Ort</td><td>'.$TankstelleOrt.'</td></tr>';
		$HTML .= '<tr><td>Anschrift</td><td>'.$TankstelleAnschrift.'</td></tr>';
		$HTML .= '</table><br><br>';
	}
	echo $HTML;
}

echo '</html>';
?>

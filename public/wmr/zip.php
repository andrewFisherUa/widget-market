<?php

$temp_filename = "info.zip";

$fp = fopen($temp_filename, "w");
fputs($fp, file_get_contents("http://www.bestchange.ru/bm/info.zip"));
fclose($fp);

$zip = new ZipArchive;
if (!$zip->open($temp_filename)) exit("error");
$currencies = array();
foreach (explode("\n", $zip->getFromName("bm_cy.dat")) as $value) {
  $entry = explode(";", $value);
  $currencies[$entry[0]] = $entry[2];
}
ksort($currencies);
$exchangers = array();
foreach (explode("\n", $zip->getFromName("bm_exch.dat")) as $value) {
  $entry = explode(";", $value);
  $exchangers[$entry[0]] = $entry[1];
}
ksort($exchangers);
$rates = array();
foreach (explode("\n", $zip->getFromName("bm_rates.dat")) as $value) {
  $entry = explode(";", $value);
  $rates[$entry[0]][$entry[1]][$entry[2]] = array("rate"=>$entry[3] / $entry[4], "reserve"=>$entry[5]);
}
$zip->close();
unlink($temp_filename);

$from_cy = 2;//WMR
$to_cy = 93;//WMZ

echo("Курсы по направлению " . $currencies[$from_cy] . "->" . $currencies[$to_cy] . ":<br>");
uasort($rates[$from_cy][$to_cy], function ($a, $b) {
  if ($a["rate"] > $b["rate"]) return 1;
  if ($a["rate"] < $b["rate"]) return -1;
  return(0);
});
foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
  echo("<a href=\"https://www.bestchange.ru/info.php?id=" . $key . "\">" . $exchangers[$key] . "</a> - " . ($entry["rate"] < 1 ? 1 : $entry["rate"]) . " " . $currencies[$from_cy] . " на " . ($entry["rate"] < 1 ? 1 / $entry["rate"] : 1) . " " . $currencies[$to_cy] . " - резерв " . $entry["reserve"] . " " . $currencies[$to_cy] . "<br>");
}

echo("<br>Список валют:<br>");
foreach ($currencies as $key=>$value) echo($key . " - " . $value . "<br>");

echo("<br>Список обменников:<br>");
foreach ($exchangers as $key=>$value) echo($key . " - " . $value . "<br>");

?>
<?php
$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=report', 'market', 'katamaran_boiler');
$datetime=$_POST["datetime"];
$usd=$_POST["usd"];
$btc=$_POST["btc"];
$eth=$_POST["eth"];
$ltc=$_POST['ltc'];
$uah=$_POST['uah'];
$eur=$_POST['eur'];
$date=$_POST['date'];

$sql="insert into courses (date,datetime,usd,btc,eth,ltc,uah,eur)
	select ?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM courses WHERE date=?)";
$sthInsert=$pdo->prepare($sql);
$sql="update courses set datetime=?,usd=?,btc=?,eth=?,ltc=?,uah=?,eur=?
	WHERE date=?";
$sthUpdate=$pdo->prepare($sql);
$sql="insert into courses_tmp (datetime,usd,btc,eth,ltc,uah,eur)
	select ?,?,?,?,?,?,?";
$sthInsertt=$pdo->prepare($sql);
$sthUpdate->execute([$datetime,$usd,$btc,$eth,$ltc,$uah,$eur,$date]);
$sthInsert->execute([$date,$datetime,$usd,$btc,$eth,$ltc,$uah,$eur,$date]);
$sthInsertt->execute([$datetime,$usd,$btc,$eth,$ltc,$uah,$eur]);
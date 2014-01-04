<?php
// Create connection
$con=mysqli_connect("cs336-48.cs.rutgers.edu","csuser","cs268f75","bars_beer");

// Check connection
if (mysqli_connect_errno($con))
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
//get options for dropdown menu
$option = mysqli_query($con, "SELECT DISTINCT City FROM Bars ORDER BY City ASC");
$resopt = '<option value="all">all</option>';
while($rowopt1= mysqli_fetch_array($option))
{
	$resopt .= '<option value ="'.$rowopt1['City'].'">' . $rowopt1['City'] . '</option>';
}

//first query
//About City
ini_set('max_execution_time', 400);
if (isset($_REQUEST['listOfOptions'])){
if($_GET['listOfOptions']=='AC' && $_GET['bar']=="default" && $_GET['Beer']=="default" && $_GET['Drinker']=="default"&& $_GET['city'] == 'nocity'){
$query = mysqli_query($con, "SELECT Cities FROM bars_beer.Cities
WHERE Cities NOT IN(Select City from bars_beer.Bars);");//you have this
	if(!$query){
	echo "Could not successfully fun query ($query) from DB: " . mysql_error();
	exit;
	}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Cities Available', 'type' => 'string'),

);
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' => $r['Cities']); // you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
	
}
else if($_GET['listOfOptions']=='AC' && $_GET['bar']=="default" && $_GET['Beer']=="default" && $_GET['Drinker']=="default" && $_GET['city'] == 'poponfre'){
$query = mysqli_query($con, "");//i'll do this
	if(!$query){
	echo "Could not successfully fun query ($query) from DB: " . mysql_error();
	exit;
	}
	while($result =  mysqli_fetch_array($query)){}
}
else if($_GET['listOfOptions']=='AC' && $_GET['bar']=="default" && $_GET['Beer']=="default" && $_GET['Drinker']=="default"&& $_GET['city'] == 'crimerate'){
$query = mysqli_query($con, "SELECT  `Name` ,  `City` ,  `CrimeRate` ,  `drinksPerWeek` 
FROM  `Drinkers` JOIN  `Cities` ON  `Drinkers`.`City` =  `Cities`.`Cities`
 WHERE  `Cities`.`CrimeRate` > ( SELECT AVG( CrimeRate ) FROM  `Cities` )
AND drinksPerWeek>( SELECT AVG( drinksPerWeek ) FROM  `Drinkers` )
Group by  `CrimeRate` DESC");//i'll do this
	if(!$query){
	echo "Could not successfully fun query ($query) from DB: " . mysql_error();
	exit;
	}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Drinkers Name', 'type' => 'string'),
		array('label' => 'City', 'type' => 'string'),
		array('label' => 'Crime Rate', 'type' => 'string'),
		array('label' => 'Drinkers Per Week', 'type' => 'int'),
);
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' =>  $r['Name']);// you will probably need to transform this into the Date object format
		$temp[] = array('v' =>  $r['City']);
		$temp[] = array('v' =>  (float)$r['CrimeRate']);
		$temp[] = array('v' =>  $r['drinksPerWeek']);
		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
}
else if($_GET['listOfOptions']=='AC' && $_GET['bar']=="default" && $_GET['Beer']=="default" && $_GET['Drinker']=="default"&& $_GET['city'] == 'ratepro'){
$query = mysqli_query($con, "(SELECT   `City` , Count(`BName`) as Number ,  MAX(`EmploymentRate`) as HighAndLow,  AVG(`WeeklyProfit`) as WeeklyProfit
FROM  `Bars` JOIN  `Cities` ON  `Cities`.`Cities` =  `Bars`.`City` 
WHERE WeeklyProfit>(Select AVG(WeeklyProfit) FROM `Bars`))


UNION 
(SELECT    `City` , Count(`BName`) as Number ,  EmploymentRate as HighAndLow,  AVG(`WeeklyProfit`) as WeeklyProfit
FROM  `Bars` JOIN  `Cities` ON  `Cities`.`Cities` =  `Bars`.`City` 
WHERE WeeklyProfit<(Select AVG(WeeklyProfit) FROM `Bars`))");//unemployment affects on bar profit $jsonBTable
	if(!$query){
	echo "Could not successfully fun query ($query) from DB: " . mysql_error();
	exit;
	}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Type of bar', 'type' => 'string'),
		array('label' => 'Number of Bars', 'type' => 'number'),
		array('label' => 'Highest and Lowest Employment Rate', 'type' => 'number'),
		array('label' => 'Weekly Profit', 'type' => 'number'),


);
	$flag=0;
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		if($flag ==0)
			$temp[] = array('v' =>  'Bars in High Crime Rate areas');
		else
			$temp[] = array('v' =>  'Bars in Low Crime Rate areas');
		$temp[] = array('v' =>  $r['Number']); // you will probably need to transform this into the Date object format
		$temp[] = array('v' =>  (float)$r['HighAndLow']);
		$temp[] = array('v' =>  (float)$r['WeeklyProfit']);
		$rows[] = array('c' => $temp);
		$flag=1;
};

	$table['rows'] = $rows;
	$jsonBTable = json_encode($table);
	
}
//college
else if($_GET['listOfOptions']=='ABar' && $_GET['bar']=="college" && $_GET['Beer']=="default" && $_GET['city'] == 'default' && $_GET['Drinker']=="default"){
$query = mysqli_query($con,"(Select City as Name, AVG(WeeklyProfit) as WeeklyProfit FROM bars_beer.Bars
WHERE City ='New Brunswick' OR  City='Glassboro' 
OR City= 'Ewing'
 OR City= 'Princeton'
	OR City='West Long Branch')
UNION 
(Select City, AVG(WeeklyProfit) FROM bars_beer.Bars
WHERE City <>'New Brunswick' AND  City<>'Glassboro' 
AND City<> 'Ewing'
 AND City<> 'Princeton'
	AND City<>'West Long Branch')
");
if (!$query) {
    echo "Could not successfully run query ($query) from DB: " . mysql_error();
    exit;
}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Type of Town', 'type' => 'string'),
		array('label' => 'Weekly Profit', 'type' => 'number'),

);
	$flag=0;
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		if($flag ==0)
			$temp[] = array('v' =>  'Bars in College towns');
		else
			$temp[] = array('v' =>  'Bars in non College towns');
		$temp[] = array('v' => (float)$r['WeeklyProfit']); // you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
};

	$table['rows'] = $rows;

	$jsonTable = json_encode($table);
	$jsonGTable=NULL;
	$jsonBTable=NULL;
}
else if($_GET['listOfOptions']=='ABar' && $_GET['bar']=="most" && $_GET['Beer']=="default" && $_GET['city'] == 'default' && $_GET['Drinker']=="default"){
	$query = mysqli_query($con,"(SELECT b.BName, COUNT(f.drinker) as Frequents FROM bars_beer.Bars b JOIN bars_beer.frequents f ON f.Bar = b.BName
GROUP BY b.BName
HAVING COUNT(f.drinker)   >= ALL 
 (SELECT COUNT(f.drinker)
 FROM bars_beer.Bars b
JOIN frequents f ON f.bar = b.BName
GROUP BY b.BName))");
$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Bars', 'type' => 'string'),
		array('label' => 'Frequents', 'type' => 'string'),
);
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' =>  $r['BName']);// you will probably need to transform this into the Date object format
		$temp[] = array('v' =>  $r['Frequents']);
		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
}
else if($_GET['listOfOptions']=='ABar' && $_GET['bar']=="mostAnd" && $_GET['Beer']=="default" && $_GET['Drinker']=="default"&& $_GET['city'] == 'default'){
	$query = mysqli_query($con,"(SELECT b.BName, COUNT(f.drinker) as Frequents FROM bars_beer.Bars b JOIN bars_beer.frequents f ON f.Bar = b.BName
GROUP BY b.BName
HAVING COUNT(f.drinker)   >= ALL 
 (SELECT COUNT(f.drinker)
 FROM bars_beer.Bars b
JOIN frequents f ON f.bar = b.BName
GROUP BY b.BName)
AND b.BName in(Select Bars FROM bars_beer.Sells WHERE Beer IN(
SELECT l.beer as Most_Likes
	FROM bars_beer.Likes l
	GROUP BY l.beer
	HAVING COUNT(l.beer) >=ALL(Select COUNT(l.beer)
FROM bars_beer.Likes l GROUP BY l.beer))))");
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Bars', 'type' => 'string'),
		array('label' => 'Frequents', 'type' => 'string'),
);
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' =>  $r['BName']);// you will probably need to transform this into the Date object format
		$temp[] = array('v' =>  $r['Frequents']);
		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
}
/*http://localhost:1337/test.php?listOfOptions=ABar&bar=happy&Beer=default&city=default&Drinker=default*/
else if($_GET['listOfOptions']=='ABar' && $_GET['bar']=="happy" && $_GET['Beer']=="default" && $_GET['Drinker']=="default"&& $_GET['city'] == 'default'){
	$query = mysqli_query($con,"SELECT b.BName
FROM bars_beer.Bars b JOIN bars_beer.Sells s ON s.Bars = b.BName
GROUP BY b.BName
HAVING MIN(HappyHourStart)<=ALL(SELECT b.HappyHourStart
FROM bars_beer.Bars b JOIN bars_beer.Sells s ON s.Bars = b.BName
GROUP BY b.BName)
AND 
b.BName IN(SELECT s.Bars FROM bars_beer.Sells s
GROUP BY s.Bars
HAVING AVG(s.Price)   <= ALL 
 (SELECT AVG(s.Price)  
FROM bars_beer.Sells s 
GROUP BY s.Bars))
");
if (!$query) {
    echo "Could not successfully run query ($query) from DB: " . mysql_error();
    exit;
}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Bars', 'type' => 'string'),

);
	
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' =>  $r['BName']); // you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
}
else if($_GET['listOfOptions']=='ABar' && $_GET['bar']=="pricerange" && $_GET['Beer']=="default" && $_GET['Drinker']=="default"&& $_GET['city'] == 'default'){
	$query = mysqli_query($con,"SELECT PriceRange, (COUNT(f.drinker)/ (Select Count(f.drinker) from bars_beer.frequents f) ) as Percentage
FROM bars_beer.Bars b JOIN bars_beer.frequents f ON f.Bar = b.BName
WHERE  PriceRange<'$$'

UNION
(Select PriceRange,(COUNT(f.drinker)/ (Select Count(f.drinker) from bars_beer.frequents f) )
FROM bars_beer.Bars b JOIN bars_beer.frequents f ON f.Bar = b.BName
Where PriceRange>'$$' )
");
if (!$query) {
    echo "Could not successfully run query ($query) from DB: " . mysql_error();
    exit;
}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Price Range', 'type' => 'string'),
		array('label' => 'Percentage', 'type' => 'number'),

);
	
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' =>  $r['PriceRange']);
		$temp[] = array('v' => (float)$r['Percentage']); // you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
};

	$table['rows'] = $rows;

	$jsonTable = json_encode($table);
	$jsonGTable=NULL;

}
//About Beer
else if($_GET['listOfOptions']=='ABeer' && $_GET['bar']=="default" && $_GET['Beer']=="Best" && $_GET['Drinker']=="default" && $_GET['city'] == 'default'){
	$query = mysqli_query($con, "SELECT distinct(`S`.`Beer`), `C`.`cnt` FROM `Likes`  `S` INNER JOIN (SELECT `Beer`, count(`Name`) as `cnt` FROM `Likes` GROUP BY `Beer`) C ON `S`.`Beer` = `C`.`Beer` ORDER BY `C`.`cnt`  DESC");
	if(!$query){
	echo "Could not successfully fun query ($query) from DB: " . mysql_error();
	exit;
	}

	while($result =  mysqli_fetch_array($query)){}
}//end of best


else if($_GET['listOfOptions']=='ABeer' && $_GET['bar']=="default" && $_GET['Beer']=="BLB" && $_GET['Drinker']=="default" && $_GET['city'] == 'default'){
$query = mysqli_query($con, "SELECT distinct(`S`.`Beer`), `C`.`cnt` as quantity FROM `Likes`  `S` INNER JOIN (SELECT `Beer`, count(`Name`) as `cnt` FROM `Likes` GROUP BY `Beer`) C ON `S`.`Beer` = `C`.`Beer` ORDER BY `C`.`cnt`  ASC");
	if(!$query){
	echo "Could not successfully fun query ($query) from DB: " . mysql_error();
	exit;
	}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Beers', 'type' => 'string'),
		array('label' => 'Quantity', 'type' => 'string'),
);
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' =>  $r['Beer']);// you will probably need to transform this into the Date object format
		$temp[] = array('v' =>  $r['quantity']);
		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
}

else if($_GET['listOfOptions']=='ABeer' && $_GET['bar']=="default" && $_GET['city']=="default" && $_GET['Beer']=="nolikes" && $_GET['Drinker']=="default"){
$query = mysqli_query($con, "SELECT Distinct(Beer) from bars_beer.Sells
	WHERE Beer NOT IN(Select Beer FROM bars_beer.Likes)");//bar sells no one likes you have this
	if(!$query){
	echo "Could not successfully fun query ($query) from DB: " . mysql_error();
	exit;
	}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Beers', 'type' => 'string'),

);
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' =>  $r['Beer']);// you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
}


else if($_GET['listOfOptions']=='ABeer' && $_GET['bar']=="default" && $_GET['city']=="default" && $_GET['Beer']=="nosell" && $_GET['Drinker']=="default"){
	$query = mysqli_query($con, "Select BeerName from bars_beer.beer
Where BeerName NOT IN(
Select Beer from bars_beer.Sells
)");//you have this beer no bar sells
	if(!$query){
	echo "Could not successfully fun query ($query) from DB: " . mysql_error();
	exit;
	}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Beers', 'type' => 'string'),

);
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' =>  $r['BeerName']);// you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
}//end of nosell
else if($_GET['listOfOptions']=='ABeer' && $_GET['bar']=="default" && $_GET['city']=="default" && $_GET['Beer']=="likesnosell" && $_GET['Drinker']=="default"){
$query = mysqli_query($con,"SELECT Distinct(Beer) from bars_beer.Likes
	WHERE Beer NOT IN
	(Select Distinct(Beer) FROM bars_beer.Sells)");
	if (!$query) {
    echo "Could not successfully run query ($query) from DB: " . mysql_error();
    exit;
}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Beers', 'type' => 'string'),

);
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' =>  $r['Beer']);// you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
}
//http://localhost:1337/test.php?listOfOptions=ABeer&bar=default&Beer=freqBeer&city=default&Drinker=default
else if($_GET['listOfOptions']=='ABeer' && $_GET['bar']=="default" && $_GET['city']=="default" && $_GET['Beer']=="freqBeer" && $_GET['Drinker']=="default"){
$query = mysqli_query($con,"
	SELECT BeerName from bars_beer.beer
WHERE BeerName IN 
(Select Beer FROM bars_beer.Sells s
WHERE s.Bars IN

(SELECT s.Bars FROM bars_beer.Sells s JOIN bars_beer.frequents f ON f.Bar = s.Bars
GROUP BY s.Bars
HAVING COUNT(f.drinker)   >= ALL 
 (SELECT COUNT(f.drinker)
 FROM bars_beer.Sells s 
JOIN frequents f ON f.bar = s.Bars
GROUP BY s.Bars)))");
if (!$query) {
    echo "Could not successfully run query ($query) from DB: " . mysql_error();
    exit;
}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Beer that is sold by the most frequented bars', 'type' => 'string'),

);
	
	$flag =0;
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' => $r['BeerName']); // you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
	//echo $jsonTable;
	$jsonTable=NULL;
	$jsonBTable=NULL;
	//echo $jsonGTable

}
//About Drinkers
//finished
else if($_GET['listOfOptions']=='AD' && $_GET['bar']=="default" && $_GET['city']=="default" && $_GET['Beer']=="default" && $_GET['Drinker']=="single"){
	$query = mysqli_query($con,"
	(Select isSingle, (Count(distinct(Name))/
(Select Count(distinct(Name)) as Percentage FROM bars_beer.Drinkers WHERE isSingle=1))as Percentage FROM 
	bars_beer.Drinkers, bars_beer.frequents, bars_beer.beer
	WHERE isSingle=1 AND Name=Drinker AND Name IN
	(Select Name FROM bars_beer.Likes
	WHERE Beer IN (Select BeerName FROM bars_beer.beer where
	alcoholContent>(Select AVG(alcoholContent) from bars_beer.beer))
	))
UNION
(Select isSingle, (Count(distinct(Name))/
(Select Count(distinct(Name)) FROM bars_beer.Drinkers WHERE isSingle=0)) FROM 
	bars_beer.Drinkers, bars_beer.frequents, bars_beer.beer
	WHERE isSingle=0 AND Name=Drinker AND Name IN
	(Select Name FROM bars_beer.Likes
	WHERE Beer IN (Select BeerName FROM bars_beer.beer where
	alcoholContent>(Select AVG(alcoholContent) from bars_beer.beer))
	)
)");
	if (!$query) {
    echo "Could not successfully run query ($query) from DB: " . mysql_error();
    exit;
}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Single?', 'type' => 'string'),
		array('label' => 'Percentage', 'type' => 'number'),

);
	
	$flag =0;
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		if($flag ==0)
			$temp[] = array('v' =>  'Single High Alcohol Content');
		else
			$temp[] = array('v' =>  'Not Single High Alcohol Content');
		$temp[] = array('v' => (float)$r['Percentage']); // you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonTable = json_encode($table);
	//echo $jsonTable;
	$jsonGTable=NULL;
	$jsonBTable=NULL;
	//echo $jsonGTable;
}
else if($_GET['listOfOptions']=='ABeer' && $_GET['bar']=="default" && $_GET['Beer']=="default" && $_GET['Drinker']=="agedrinks" && $_GET['city'] == 'default'){
$query = mysqli_query($con,"");//arrange age my drinker i'll do this
	if (!$query) {
    echo "Could not successfully run query ($query) from DB: " . mysql_error();
    exit;
}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Single?', 'type' => 'string'),
		array('label' => 'Percentage', 'type' => 'number'),

);
	$flag = 0;
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		
};

	$table['rows'] = $rows;

	$jsonTable = json_encode($table);
	//echo $jsonTable;
}
/*http://localhost:1337/test.php?listOfOptions=AD&bar=default&Beer=default&city=default&Drinker=old*/
else if($_GET['listOfOptions']=='AD' && $_GET['bar']=="default" && $_GET['Beer']=="default" && $_GET['Drinker']=="old" && $_GET['city'] == 'default'){
$query = mysqli_query($con,"Select d.Name, (Count(Drinker)/(Select Count(Name) FROM bars_beer.Drinkers))as Percentage
FROM bars_beer.Drinkers d JOIN bars_beer.frequents f ON f.Drinker = d.Name
WHERE Bar IN 
(SELECT BName from
bars_beer.Bars where YearEstablished<(Select Avg(YearEstablished) FROM bars_beer.Bars))
AND Age>(Select Avg(Age) FROM bars_beer.Drinkers)
UNION
Select d.name, (Count(Drinker)/(Select Count(Name) FROM bars_beer.Drinkers))
FROM bars_beer.Drinkers d JOIN bars_beer.frequents f ON f.Drinker = d.Name
WHERE Bar IN 
(SELECT BName from
bars_beer.Bars where YearEstablished<(Select Avg(YearEstablished) FROM bars_beer.Bars))
AND Age<(Select Avg(Age) FROM bars_beer.Drinkers)");//i'll do this
	if (!$query) {
    echo "Could not successfully run query ($query) from DB: " . mysql_error();
    exit;
}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Old vs Young', 'type' => 'string'),
		array('label' => 'Percentage', 'type' => 'number'),

);
	$flag = 0;
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		if($flag ==0)
			$temp[] = array('v' =>  'Old People');
		else
			$temp[] = array('v' =>  'Youngsters');
		$temp[] = array('v' => (float)$r['Percentage']); // you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
		$flag =1;
};

	$table['rows'] = $rows;

	$jsonTable = json_encode($table);
	//echo $jsonTable;
}
else if($_GET['listOfOptions']=='AD' && $_GET['bar']=="default" && $_GET['Beer']=="default" && $_GET['Drinker']=="tendTo" && $_GET['city'] == 'default'){
$query = mysqli_query($con,"Select d.Name
FROM bars_beer.Drinkers d JOIN bars_beer.Cities c ON c.Cities = d.City
WHERE EmploymentRate<(Select AVG(EmploymentRate) FROM bars_beer.Cities )
AND Name in (
Select f.Drinker FROM bars_beer.Sells s
JOIN bars_beer.frequents f ON f.Bar = s.Bars
GROUP BY s.Bars
HAVING AVG(s.price)   >= ALL 
 (SELECT AVG(s.price)  
FROM bars_beer.Sells s 
GROUP BY s.Bars)
)");

if (!$query) {
    echo "Could not successfully run query ($query) from DB: " . mysql_error();
    exit;
}
	$rows = array();
	$table = array();

	$table['cols'] = array(
		array('label' => 'Name', 'type' => 'string'),

);
	
	$flag =0;
	$rows = array();
	while($r = mysqli_fetch_assoc($query)){
		$temp = array();
		$temp[] = array('v' => $r['Name']); // you will probably need to transform this into the Date object format

		$rows[] = array('c' => $temp);
};

	$table['rows'] = $rows;

	$jsonGTable = json_encode($table);
	//echo $jsonTable;
	$jsonTable=NULL;
	$jsonBTable=NULL;
	//echo $jsonGTable

}
}
?>

<html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="bootstrap/ico/favicon.png">

    <title>Bars & Beers Database</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">
    <!--my css-->
    <link hrf= "mycss.css" rel ="stylesheet">
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

<body class="page_bg">

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">CS336 Database Project</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

<div class ="container">
<div class="well">
<form action="test.php" method="GET">
What do you want to get?
<select name="listOfOptions" id="list" >
<option value="ABar">About Bar</option>
<option value="ABeer">About Beer</option>
<option value="AC">About Cities</option>
<option value="AD">About Drinker</option>
</select>
<select name="bar" id="bar" method="GET">
<option value="default" id="default">What about Bars?</option>
<option value="most" id="leastBeers">Bars attracts the most drinkers</option>
<option value="mostAnd" id="mostAnd">Bars attracts the most drinkers and sell the most liked beer</option>
<option value="happy" id="happy">Bar(s) that have the earliest happy hours AND sells the cheapest beer</option>
<option value = "pricerange" id="pricerange">Price Range affects on how many people frequent that bar</option>
<option value = "college" id="college">Do College towns have better weekly profits than non-college towns?</option>
</select>
<select name="Beer" id="theB" method="GET">
<option value="default" id="default">What about the Beer?</option>
<option value="BLB" id="leastBeers">Buy least</option>
<option value="Best" id="best">Beers and how many people like it</option>
<option value ="nosell" id="nosell">Beer no bar sells</option>
<option value ="nolikes" id="nolikes">Beer bar sells no one likes </option>
<option value ="likesnosell" id="likesnosell"> Beer people like no bar sells </option>
<option value ="freqBeer" id="freqBeer">Beer that is sold in the most frequented bars(WARNING: TAKES A LONG TIME TO RUN,NOT OPTIMIZED)</option>

</select>
<select name="city" id="theC" method="GET">
<option value="default" id="default">What about the city?</option>
<option value="ratepro" id="leastBeers">City's unemployment rate affect on Bar's Profit</option>
<option value = "nocity" id = "nocity"> City with no bars </option>
<option value = "poponfre" id ="poponfre"> Population affect on how many frequent Bar in that City </option>
<option value = "crimerate" > People who frequent a crime infested city drink more </option>
</select>
<select name="Drinker" id="theD" method="GET">
<option value="default" id="default">What about the Drinker?</option>
<option value="single" id="single">Singles Who Like High Alcohol in their Beer </option>
<option value="agedrinks" id="employed">Age affects Drinks Per Week</option>
<option value="old" id="old">The older the drinker, the more likely they'll frequent the bars that are old?</option>
<option value="tendTo" id="TendTo">Drinkers who live in a town that has a good employment rate tend to by expenisve beers?</option>

</select>
<div style ="padding-left:100px; padding-top:25px"><input style ="display:block" class = "btn btn-large" type= "submit" value ="Search"/></div>
</form>
</div>
</div>
<table>
</br>


<?php
		if(empty($output2)==false){print("Second table<br>$output2");}

?>
</table>
<html>
  <head>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">
	// Load the Visualization API and the piechart package.
	
	
	
   google.load('visualization', '1', {'packages':['corechart']});
      
    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);
	 function drawChart() {
        // Create the data table.
         var data = new google.visualization.DataTable(<?=$jsonTable?>);
        var options = {
		title: 'The Chart',
		is3D: 'true',
		width: 800,
		height: 600
    };

        // Set chart options
        

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      } 

    </script>
  </head>

  <body>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
  </body>
</html>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">

		google.load('visualization', '1', {packages:['table']});
		google.setOnLoadCallback(drawTable);
		function drawTable() {
		if(<?=$jsonGTable != NULL?>){
		var data = new google.visualization.DataTable(<?=$jsonGTable?>);
        var options = {
		title: 'The Chart',
		is3D: 'true',
		width: 800,
		height: 600,
		showRowNumber: true
    }}
	else{
	};
		
		
		var table = new google.visualization.Table(document.getElementById('table_div'));
        table.draw(data, options);


	
	}
	
	    </script>
  </head>

  <body>
<div id="table_div"></div>
  </body>
  <head>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">

		google.load('visualization', '1', {packages:['corechart']});
		google.setOnLoadCallback(drawChart);
		function drawChart() {
		
		if(<?=$jsonBTable != NULL?>){
		
		var data = new google.visualization.DataTable(<?=$jsonBTable?>);
        var options = {
		title: 'Comparison?',
		vAxis: {title: 'Bars',  titleTextStyle: {color: 'red'}},
		hAxis: {title: "Profit"},
    }}
	else{
	};
		
		
		var chart1 = new google.visualization.BarChart(document.getElementById('bar_div'));
        chart1.draw(data, options);


	
	}
	
	    </script>
  </head>

  <body>
<div id="bar_div" style="width: 900px; height: 500px;"></div>
  </body>
</html>

<script type="text/javascript" src="javascripts/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
	 $("#with_bars").click(function() {
                        $("#price").attr("disabled", false);
                        //$("#price").show(); //To Show the dropdown
                    });
                    $("#no_bars").click(function() {
                        $("#price").attr("disabled", true);
                        //$("#price").hide();//To hide the dropdown
                    });
	$(function(){
	$("#theB").hide();
	$("#theD").hide();
	 $("#theC").hide();
	  $("#bar").hide();
   
   });
   
   $("#list").click(function(){
   var val=$("#list").val();
   if (val=="ABeer"){
	$("#theB").show();
	 $("#theD").hide();
	$("#bar").hide();
	 $("#theC").hide();
   }
   else if (val=="AD"){
   $("#theD").show();
   $("#theB").hide();
   $("#bar").hide();
   $("#theC").hide();}
   else if (val=="ABar"){
   $("#bar").show();
   $("#theB").hide();
   $("#theD").hide();
   $("#theC").hide();
   }
   else if(val=="AC"){
   $("#theC").show();
   $("#theB").hide();
   $("#theD").hide();
   $("#bar").hide();
   
   }
   else{
   $("#theB").hide();
   $("#theD").hide();
   $("#bar").hide();
   $("#theC").hide();
   /*$("#city2 option[value=vale]").remove();*/
   
   }
   
   });
   var $selects = $('select');
		$('select').change(function () {
		    $('option:hidden', $selects).each(function () {
		        var self = this,
		            toShow = true;
		        $selects.not($(this).parent()).each(function () {
		            if (self.value == this.value) toShow = false;
		        })
		        if (toShow) $(this).show();
		    });
		    if (this.value != 0) //to keep default option available
		      $selects.not(this).children('option[value=' + this.value + ']').hide();
		});
   /*$('#city').on('change',function() {
        if ($(this).val() == "all"){
            $('#city2').hide();
        } else {
            $('.city2').show();
        } }); */
	
	/*List of patterns
	Cities:
	Range by employement rate or profit->Higher the employment(sp?) rate, the higher the profit per week. <-Good or bad thing?
	More frequent peolpe frequent bars with higher employment rate
	Table that shows population. How many people frequent bars in that city.
	Select 
CASE WHEN 
(Select AVG(WeeklyProfit) FROM bars_beer.Bars
WHERE City ='New Brunswick' OR  City='Glassboro' 
OR City= 'Ewing'
 OR City= 'Princeton'
	OR City='West Long Branch')>
(Select AVG(WeeklyProfit) FROM bars_beer.Bars
WHERE City <>'New Brunswick' AND  City<>'Glassboro' 
AND City<> 'Ewing'
 AND City<> 'Princeton'
	AND City<>'West Long Branch') THEN 'True' ELSE 'FALSE' END AS True_False
 FROM bars_beer.Cities, bars_beer.Bars
	
	Beer:
	Show me the beer that is sold by the most frequented bars:
	SELECT BeerName from bars_beer.beer
WHERE BeerName IN 
(Select Beer FROM bars_beer.Sells s
WHERE s.Bars IN

(SELECT s.Bars FROM bars_beer.Sells s JOIN bars_beer.frequents f ON f.Bar = s.Bars
GROUP BY s.Bars
HAVING COUNT(f.drinker)   >= ALL 
 (SELECT COUNT(f.drinker)
 FROM bars_beer.Sells s 
JOIN frequents f ON f.bar = s.Bars
GROUP BY s.Bars)))
	favorite beer Semi Done
	SELECT l.beer, COUNT(l.beer) as Most_Likes
	FROM bars_beer.Likes l
	GROUP BY l.beer
	HAVING COUNT(l.beer) >9
	>=ALL (
SELECT l.beer FROM bars_beer.Likes l
GROUP BY l.beer DESC)
	The younger you are, the more drinks per week you have(not really a pattern? we can change it to make it)
	
	The higher the alcohol content, the more people like the beer? Give a percentage
	SELECT Count(Beer) From bars_beer.Likes 
	WHERE Beer in (Select BeerName FROM bars_beer.beer
	WHERE alcoholContent>(Select AVG(alcoholContent) from bars_beer.beer))

	Beer that people like but bars don't sell.
	SELECT Distinct(Beer) from bars_beer.Likes
	WHERE Beer NOT IN
	(Select Distinct(Beer) FROM bars_beer.Sells)
	
	
	Bars that sells a beer that people don't like(no one!)
	SELECT Distinct(Beer) from bars_beer.Sells
	WHERE Beer NOT IN(Select Distinct(Beer) FROM bars_beer.Likes)
	Limit 0, 8000
	
	
	Bars
	Cheaper the bar, the more ther frequent it.
	Select Distinct(Bar) FROM bars_beer.frequents, bars_beer.Bars
	WHERE Bar IN (Select BName from bars_beer.Bars
	WHERE PriceRange<'$$')
	
	
	Drinkers:
	isSingle, who like the highest alcohol content frequent more bars than non singles? SEMI DONE
	Select Count(distinct(Name))/1000 FROM 
	bars_beer.Drinkers, bars_beer.frequents, bars_beer.beer
	WHERE isSingle=1 AND Name=bars_beer.frequents.Drinker AND Name IN
	(Select Name FROM bars_beer.Likes
	WHERE Beer IN (Select BeerName FROM bars_beer.beer where
	alcoholContent>(Select AVG(alcoholContent) from bars_beer.beer))
	);
	Hipster(no)
	Do young people drink the most(which is what he described by range)
	
	
	
	Addons:
	Rating?
	*/
	/*$(function(){
		var button = $('$coll');
		button.attr('disabled',true);
		$('input.[name=TownWith]').change(function(e){
		if($(this).val()=='Town with bars'){
			button.removeAttr('disabled');	
		}
		else{
			button.attr('disabled', true);
		
		}
	}
	});*/
	
</script>
</body>
</html>

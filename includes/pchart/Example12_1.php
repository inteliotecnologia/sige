<?php
 /*
     Example12 : A true bar graph
 */

// Standard inclusions   
include("pChart/pData.class");
include("pChart/pChart.class");

// Dataset definition 
$DataSet = new pData;
$DataSet->AddPoint(array(9,8,7),"Serie1");
$DataSet->AddPoint(array("Qualidade da roupa", "Transporte", "Contato com a empresa"),"Serie6"); 

//$DataSet->AddAllSeries();

$DataSet->AddSerie("Serie1");

$DataSet->SetAbsciseLabelSerie("Serie6");
$DataSet->SetSerieName("18 março","Serie1");

// Initialise the graph
$Test = new pChart(800,450);
$Test->setFontProperties("Fonts/tahoma.ttf",9);
$Test->setGraphArea(40,40,700,260);
$Test->drawGraphArea(255,255,255,TRUE);
$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_START0,0,0,0,TRUE,0,2,TRUE);
$Test->drawGrid(4,TRUE,200,200,200,50);

// Draw the bar graph
$Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE,100);

// Finish the graph
$Test->setFontProperties("Fonts/tahoma.ttf",9);
$Test->drawLegend(710,35,$DataSet->GetDataDescription(),255,255,255,100,100,100);
$Test->setFontProperties("Fonts/tahoma.ttf",10);
$Test->drawTitle(50,22,"Example 12",50,50,50,585);
$Test->Stroke("example12.png");

?>
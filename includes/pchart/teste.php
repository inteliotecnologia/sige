<?php
// Standard inclusions
include("pChart/pData.class");
include("pChart/pChart.class");

// Dataset definition
$DataSet = new pData;
$DataSet->AddPoint(array(7,2,3,5,3),"Serie1");
$DataSet->AddPoint(array("Jerky","Fudgey","Mar","Apr","May"),"Serie2");
$DataSet->AddAllSeries();
$DataSet->SetAbsciseLabelSerie("Serie2");

// Initialise the graph
$Test = new pChart(600,400);
$Test->drawFilledRoundedRectangle(7,7,293,193,5,240,240,240);
$Test->drawRoundedRectangle(5,5,295,195,5,230,230,230);

// Draw the pie chart
$Test->setFontProperties("Fonts/tahoma.ttf",8);
$Test->setShadowProperties(2,2,400,200,200);
$Test->drawFlatPieGraphWithShadow($DataSet->GetData(),$DataSet->GetDataDescription(),120,100,60,PIE_PERCENTAGE,10);
$Test->drawPieLegend(230,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);

$Test->Stroke("test.png");

?>

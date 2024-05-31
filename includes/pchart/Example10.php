<?php
 /*
     Example10 : A 3D exploded pie graph
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array(19,2,3,5,3),"Serie1");
 $DataSet->AddPoint(array("January","February","March","April","May"),"Serie2");
 $DataSet->AddAllSeries();
 $DataSet->SetAbsciseLabelSerie("Serie2");

 // Initialise the graph
 $Test = new pChart(800,600);
 //drawFilledRoundedRectangle($X1,$Y1,$X2,$Y2,$Radius,$R,$G,$B) 
 $Test->drawFilledRoundedRectangle(7,7,793,593,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,795,585,5,230,230,230);
 $Test->createColorGradientPalette(195,204,56,223,110,41,5);
 $Test->loadColorPalette('palettes/tones-4.txt','|');

 // Draw the pie chart
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->AntialiasQuality = 1;

//void drawPieGraph($Data,$DataDescription,$XPos,$YPos,$Radius=100,$DrawLabels=PIE_NOLABEL,$EnhanceColors=TRUE,$Skew=60,$SpliceHeight=20,$SpliceDistance=0,$Decimals=0) 
 $Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),180,130,110,PIE_PERCENTAGE_LABEL,FALSE,50,10,5);
 $Test->drawPieLegend(330,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);

 // Write the title
 $Test->setFontProperties("Fonts/MankSans.ttf",10);
 $Test->drawTitle(10,20,"Sales per month",100,100,100);

 $Test->Stroke("example10.png");
?>
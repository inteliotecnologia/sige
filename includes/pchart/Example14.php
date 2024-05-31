<?php
 /*
     Example14: A smooth flat pie graph
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint($valores,"Serie1");
 $DataSet->AddPoint($labels,"Serie2");
 $DataSet->AddAllSeries();
 $DataSet->SetAbsciseLabelSerie("Serie2");

 // Initialise the graph
 $Test = new pChart(800,600);
 $Test->loadColorPalette("Sample/tones.txt");
 $Test->drawFilledRoundedRectangle(7,7,793,593,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,795,595,5,230,230,230);

 //void drawFilledCircle($Xc,$Yc,$Height,$R,$G,$B,$Width=0) 
 //$Test->drawFilledCircle(122,102,70,200,200,200);

 // Draw the pie chart
 $Test->setFontProperties("Fonts/ArialNarrow.ttf",11);
 $Test->AntialiasQuality = 0;
 //void drawBasicPieGraph($Data,$DataDescription,$XPos,$YPos,$Radius=100,$DrawLabels=PIE_NOLABEL,$R=255,$G=255,$B=255,$Decimals=0) 
 $Test->drawBasicPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),300,300,200,PIE_PERCENTAGE,255,255,218);
 
 $Test->setFontProperties("Fonts/ArialNarrow.ttf",14);
 
 //void drawPieLegend($XPos,$YPos,$Data,$DataDescription,$R,$G,$B)
 $Test->drawPieLegend(600,40,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);

 $Test->Stroke("example14.png");
 $Test->Render("example14.png");
?>
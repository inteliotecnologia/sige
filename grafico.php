<?

$GRAFICO_PATH= "includes/pchart/";

// Standard inclusions   
include($GRAFICO_PATH ."pChart/pData.class");
include($GRAFICO_PATH ."pChart/pChart.class");

// Dataset definition 
$DataSet = new pData;
$DataSet->AddPoint(array(1,2,3,4,5,6,7,0,9,10,11,12),"Serie1");
$DataSet->AddPoint(array(11,2,3,1,5,2,7,8,0,10,11,12),"Serie2");
$DataSet->AddPoint(array("Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"),"Serie3");
$DataSet->AddSerie("Serie1");
$DataSet->AddSerie("Serie2");
$DataSet->SetAbsciseLabelSerie("Serie3"); 
$DataSet->SetSerieName("Justificadas","Serie1");
$DataSet->SetSerieName("Não justificadas","Serie2");

// Initialise the graph
$Test= new pChart(800,450);
$Test->setColorPalette(0,35,35,142);
$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrow.ttf",11);
$Test->setGraphArea(75,50,740,300);

$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
$Test->drawGrid(4,TRUE,230,230,230,50);

 $Test->setFontProperties($GRAFICO_PATH ."Fonts/tahoma.ttf",6);
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

// Draw the line graph
$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

// Finish the graph

$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrow.ttf",10);
$Test->drawLegend(620,5,$DataSet->GetDataDescription(),255,255,255);
$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrowBold.ttf",12);
$Test->drawTitle(100,30,"FALTAS ". $tit_depto . $tit_turno ." ". $_POST["ano"],50,50,50,585);
//$ar= gera_auth();
$Test->Stroke("grafico_". $ar .".png");

//if (file_exists("uploads/grafico_". $ar .".png")) {
//	$pdf->Image("uploads/grafico_". $ar .".png", $pdf->GetX()+0.5, $pdf->GetY()+1, 16, 9);
//	unlink("uploads/grafico_". $ar .".png");
//}
?>
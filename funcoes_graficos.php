<?php

/* pChart library inclusions */
include("includes/pChart2/class/pData.class.php");
include("includes/pChart2/class/pDraw.class.php");
include("includes/pChart2/class/pImage.class.php");
include("includes/pChart2/class/pPie.class.php");

function pizza($valores, $labels, $render) {
	
	 /* Create and populate the pData object */
	 $MyData = new pData();   
	 $MyData->addPoints($valores,"ScoreA");  
	 $MyData->setSerieDescription("ScoreA","Application A");
	
	 /* Define the absissa serie */
	 $MyData->addPoints($labels,"Labels");
	 $MyData->setAbscissa("Labels");
	
	 /* Create the pChart object */
	 $myPicture = new pImage(1200,900,$MyData);
	
	 $myPicture->Antialias = TRUE;
	
	 /* Draw a solid background */
	 $Settings = array("R"=>255, "G"=>255, "B"=>255, "Dash"=>0, "DashR"=>0, "DashG"=>0, "DashB"=>0);
	 $myPicture->drawFilledRectangle(0,0,1200,900,$Settings);
		
	 
	 /* Enable shadow computing */ 
	 $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>50));
	
	 /* Create the pPie object */ 
	 $PieChart = new pPie($myPicture,$MyData);
	
	$myPicture->setFontProperties(array("FontName"=>"includes/pChart2/fonts/ArialNarrow.ttf","FontSize"=>10,"R"=>0,"G"=>0,"B"=>0));
	
	 /* Draw an AA pie chart */ 
	 $PieChart->draw2DPie(600,400,array("Radius"=>300,
	 									"DrawLabels"=>true,
	 									"LabelStacked"=>false,
	 									"ValuePadding" => -32,
	 									"Border"=>TRUE,
	 									"WriteValues"=>true,
	 									"ValueR"=>0,"ValueG"=>0,"ValueB"=>0
	 									)
	 					);
	
	 /* Write the legend box */ 
	 $myPicture->setShadow(FALSE);
	 
	 //$myPicture->setFontProperties(array("FontName"=>"includes/pChart2/fonts/ArialNarrow.ttf","FontSize"=>9,"R"=>0,"G"=>0,"B"=>0));
	 //$PieChart->drawPieLegend(600,40,array("Alpha"=>10));
	
	 $nome_arquivo= "uploads/graficos/". rand(1,1000000) .".png";
	 //$Test->Stroke("example14.png");
	 
	 
	 if ($render) {
		 $myPicture->Render($nome_arquivo); 
		 return($nome_arquivo);
	 }
	 else
		 $myPicture->autoOutput($nome_arquivo);  
}

function barra_horizontal($valores, $labels, $render) {
	
	$tamanho= count($valores);
	
	$altura=800;
	
	/* Create and populate the pData object */
	$MyData = new pData();  
	$MyData->addPoints($valores,"Quantidade");
	$MyData->setAxisName(0,"Quantidade");
	$MyData->addPoints($labels,"X");
	$MyData->setSerieDescription("X","X");
	$MyData->setAbscissa("X");
	
	/* Create the pChart object */
	$myPicture = new pImage($altura, 800,$MyData);
	//$myPicture->drawGradientArea(0,0,800,800,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
	//$myPicture->drawGradientArea(0,0,800,800,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
	$myPicture->setFontProperties(array("FontName"=>"includes/pChart2/fonts/ArialNarrow.ttf","FontSize"=>8));
	
	/* Draw the chart scale */ 
	$myPicture->setGraphArea(200,30,$altura-20,780);
	$myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10, "Pos"=>SCALE_POS_TOPBOTTOM)); 
	
	/* Turn on shadow computing */ 
	$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
	
	/* Draw the chart */ 
	$myPicture->drawBarChart(array("DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"Rounded"=>false,"Surrounding"=>0));
	
	/* Write the legend */ 
	//$myPicture->drawLegend(570,215,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
	
	$nome_arquivo= "uploads/graficos/". rand(1,1000000) .".png";
	 
	 if ($render) {
		 $myPicture->Render($nome_arquivo); 
		 return($nome_arquivo);
	 }
	 else
		 $myPicture->autoOutput($nome_arquivo);
}

?>
<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pl", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$GRAFICO_PATH= "includes/pchart/";
	
	// Standard inclusions   
	include("includes/pchart/pChart/pData.class");
	include("includes/pchart/pChart/pChart.class");
	
	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= $_POST["data1"];
		$data2= $_POST["data2"];
	}
	else {
		if ( ($_GET["data1"]!="") && ($_GET["data2"]!="") ) {
			$data1= $_GET["data1"];
			$data2= $_GET["data2"];
		}
	}
	
	if ( ($data1!="") && ($data2!="") ) {
		$data1f= $data1; $data1= formata_data_hifen($data1);
		$data2f= $data2; $data2= formata_data_hifen($data2);
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	else {
		$periodo2= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(22, 0, 0, $periodo2[0], 1, $periodo2[1]);
		$dias_mes= date("t", $data1_mk);
		
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 1.5, 2);
	$pdf->SetAutoPageBreak(true, 2.5);
	$pdf->SetFillColor(235,235,235);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	//$periodo= explode("/", $_POST["periodo"]);
	//$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
	
	//$total_dias_mes= date("t", $periodo_mk);
	
	$i=0;
	while ($_POST["id_cliente"][$i]!="") {
		
		$pdf->AddPage();
	
		$pdf->SetXY(7, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.5, "PESQUISAS DE SATISFAÇÃO", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.5, pega_pessoa($_POST["id_cliente"][$i]), 0, 1, "R");
		
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(4, 0.5, "Data:", 1, 0, 'L', 1);
		
		
		$result_pesquisa= mysql_query("select * from qual_pesquisa
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_cliente = '". $_POST["id_cliente"][$i] ."'
										and   status_pesquisa <> '2'
										and   data_pesquisa >= '$data1'
										and   data_pesquisa <= '$data2'
										order by data_pesquisa asc
										") or die(mysql_error());
		$linhas_pesquisa= mysql_num_rows($result_pesquisa);
		
		if ($linhas_pesquisa>0) $largura= (13/$linhas_pesquisa);
		else $largura= 0;
		
		$j=0;
		while ($rs_pesquisa= mysql_fetch_object($result_pesquisa)) {
			$k= $j+1;
			if ($k==$linhas_pesquisa) $quebra=1;
			else $quebra=0;
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell($largura, 0.5, desformata_data($rs_pesquisa->data_pesquisa), 1, $quebra, 'C', 1);
			
			$id_pesquisa[$j]= $rs_pesquisa->id_pesquisa;
			$data_pesquisa[$j]= $rs_pesquisa->data_pesquisa;
			$duracao[$j]= $rs_pesquisa->duracao;
			
			$j++;
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(4, 0.5, "Duração:", 1, 0, 'L', 1);
		
		for ($d=0; $d<$j; $d++) {
			$k= $d+1;
			if ($k==$linhas_pesquisa) $quebra=1;
			else $quebra=0;
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell($largura, 0.5, $duracao[$d], 1, $quebra, 'C', 1);
		}
		
		$pdf->LittleLn();
		/*
		
		*/
		
		$grafico_dados= array();
		
		$result_categoria= mysql_query("select distinct(qual_pesquisa_categorias.id_pesquisa_categoria), qual_pesquisa_categorias.pesquisa_categoria
										from qual_pesquisa_categorias, qual_pesquisa_itens, qual_pesquisa, qual_pesquisa_notas
										where qual_pesquisa_categorias.id_empresa = '". $_SESSION["id_empresa"] ."' 
										and   qual_pesquisa_categorias.id_pesquisa_categoria = qual_pesquisa_itens.id_pesquisa_categoria
										and   qual_pesquisa_itens.id_pesquisa_item = qual_pesquisa_notas.id_pesquisa_item
										and   qual_pesquisa.id_pesquisa = qual_pesquisa_notas.id_pesquisa
										and   qual_pesquisa.id_cliente = '". $_POST["id_cliente"][$i] ."'
										and   qual_pesquisa.data_pesquisa >= '$data1'
										and   qual_pesquisa.data_pesquisa <= '$data2'
										and   qual_pesquisa_notas.nota <> '-1'
										order by qual_pesquisa_categorias.id_pesquisa_categoria asc
										") or die(mysql_error());
		
		$c=0;
		while ($rs_categoria= mysql_fetch_object($result_categoria)) {
			
			$pdf->SetFillColor(235,235,235);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(4, 0.5, $rs_categoria->pesquisa_categoria, 1, 0, 'L', 1);
			
			$grafico_titulos[$c]= $rs_categoria->pesquisa_categoria;
			
			for ($d=0; $d<$j; $d++) {
				$k= $d+1;
				if ($k==$linhas_pesquisa) $quebra=1;
				else $quebra=0;
				
				$result_media= mysql_query("select avg(nota) as media from qual_pesquisa_notas, qual_pesquisa_itens
											where qual_pesquisa_notas.id_cliente = '". $_POST["id_cliente"][$i] ."'
											and   qual_pesquisa_notas.id_pesquisa = '". $id_pesquisa[$d] ."'
											and   qual_pesquisa_notas.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   qual_pesquisa_notas.id_pesquisa_item = qual_pesquisa_itens.id_pesquisa_item
											and   qual_pesquisa_itens.id_pesquisa_categoria = '". $rs_categoria->id_pesquisa_categoria ."'
											and   qual_pesquisa_notas.nota <> '-1'
											") or die(mysql_error());
				$linhas_media= mysql_num_rows($result_media);
				$rs_media= mysql_fetch_object($result_media);
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->Cell($largura, 0.5, fnum($rs_media->media), 1, $quebra, 'C', 1);
				
				$grafico_dados[$c][$d]= $rs_media->media;
			}
			
			$result_teste= mysql_query("select * from qual_pesquisa_notas
										where id_pesquisa = '". $id_pesquisa ."'
										");
			$linhas_teste= mysql_num_rows($result_teste);
			
			if ($linhas_teste>0)
				$sql_item= "select * from qual_pesquisa_itens, qual_pesquisa_notas
											where qual_pesquisa_itens.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   qual_pesquisa_itens.id_pesquisa_categoria = '". $rs_categoria->id_pesquisa_categoria ."'
											/* and   qual_pesquisa_itens.status_item = '1' */
											and   qual_pesquisa_itens.id_pesquisa_item = qual_pesquisa_notas.id_pesquisa_item
											order by qual_pesquisa_itens.id_pesquisa_item asc
											";
			else
				$sql_item= "select * from qual_pesquisa_itens
											where qual_pesquisa_itens.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   qual_pesquisa_itens.id_pesquisa_categoria = '". $rs_categoria->id_pesquisa_categoria ."'
											and   qual_pesquisa_itens.status_item = '1'
											order by qual_pesquisa_itens.id_pesquisa_item asc
											";
											
			$result_item= mysql_query($sql_item) or die(mysql_error());
			
			while ($rs_item= mysql_fetch_object($result_item)) {
				$pdf->SetFont('ARIALNARROW', '', 7);
				
				$pdf->Cell(4, 0.5, "  ". $rs_item->pesquisa_item, 1, 0, 'L', 0);
				
				for ($d=0; $d<$j; $d++) {
					$k= $d+1;
					if ($k==$linhas_pesquisa) $quebra=1;
					else $quebra=0;
					
					$result= mysql_query("select * from qual_pesquisa_notas
											where id_cliente = '". $_POST["id_cliente"][$i] ."'
											and   id_pesquisa = '". $id_pesquisa[$d] ."'
											and   id_pesquisa_item = '". $rs_item->id_pesquisa_item ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   nota <> '-1'
											") or die(mysql_error());
					$linhas= mysql_num_rows($result);
					$rs= mysql_fetch_object($result);
					
					$pdf->SetFont('ARIALNARROW', '', 8);
					$pdf->Cell($largura, 0.5, fnum($rs->nota), 1, $quebra, 'C', 0);
				}
				
			}
			
			$c++;
		}
		
		$vetor_linhas= count($grafico_dados[0]);
		$vetor_colunas= count($grafico_dados);
		
		// Dataset definition 
		$DataSet = new pData;
		
		for ($m=0; $m<$vetor_linhas; $m++) {
			for ($n=0; $n<=$vetor_colunas; $n++) {
				$grafico_dados2[$m][$n]= $grafico_dados[$n][$m];
			}
			
			@$DataSet->AddPoint($grafico_dados2[$m],"Serie". $m);
			@$DataSet->AddSerie("Serie". $m);
			
			@$DataSet->SetSerieName(desformata_data($data_pesquisa[$m]),"Serie". $m);
		}
		
		@$DataSet->AddPoint($grafico_titulos,"Serie_Titulos"); 
		
		@$DataSet->SetAbsciseLabelSerie("Serie_Titulos");
		
		// Initialise the graph
		@$Test = new pChart(800,450);
		@$Test->setFontProperties($GRAFICO_PATH ."Fonts/tahoma.ttf",9);
		@$Test->setGraphArea(40,40,600,260);
		@$Test->drawGraphArea(255,255,255,TRUE);
		@$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_START0,0,0,0,TRUE,0,2,TRUE);
		@$Test->drawGrid(4,TRUE,200,200,200,50);
		
		// Draw the bar graph
		@$Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE,100);
		
		// Finish the graph
		@$Test->setFontProperties($GRAFICO_PATH ."Fonts/tahoma.ttf",9);
		@$Test->drawLegend(620,50,$DataSet->GetDataDescription(),255,255,255);
		@$Test->setFontProperties($GRAFICO_PATH ."Fonts/tahoma.ttf",10);
		@$Test->drawTitle(50,22,pega_pessoa($_POST["id_cliente"][$i]),50,50,50,585);
		
		$ar= gera_auth();
		@$Test->Render("uploads/grafico_". $ar .".png");
		
		if (file_exists("uploads/grafico_". $ar .".png")) {
			$pdf->Image("uploads/grafico_". $ar .".png", $pdf->GetX()+0.5, $pdf->GetY()+1, 16, 8.75);
			unlink("uploads/grafico_". $ar .".png");
		}
		
		$pdf->Ln();
		
		
		
		
		
		
		$i++;
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("atraso_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>
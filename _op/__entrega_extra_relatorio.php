<?
require_once("conexao.php");
require_once("funcoes.php");

//------------ gráfico

//$GRAFICO_PATH= "includes/pchart/";

// Standard inclusions   
//include($GRAFICO_PATH ."pChart/pData.class");
//include($GRAFICO_PATH ."pChart/pChart.class");

if (pode("pl", $_SESSION["permissao"])) {
	
	//por ano
	if ($_POST["tipo_relatorio"]=="a") {
		define('FPDF_FONTPATH','includes/fpdf/font/');
		require("includes/fpdf/fpdf.php");
		require("includes/fpdf/modelo_paisagem.php");
		
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(1.5, 1.5, 1.5);
		$pdf->SetAutoPageBreak(true, 3);
		$pdf->SetFillColor(210,210,210);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		$pdf->AddPage();
		
		$total_geral_periodo= 0;
		
		$pdf->SetXY(16.9, 1.2);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ENTREGAS EXTRAS", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["periodo"], 0, 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
		$pdf->Cell(3, 0.6, "CLIENTE", 1, 0, 'C', 1);
		
		for ($j=1; $j<13; $j++) {
			$pdf->Cell(1.8, 0.6, substr(traduz_mes($j), 0, 3), 1, 0, 'C', 1);
		}
		
		$pdf->Cell(2, 0.6, "TOTAL", 1, 1, 'C', 1);
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(235,235,235);
		
		$i=0;
		
		while ($_POST["id_cliente"][$i]) {
			if (($i%2)!=0) $fill=1;
			else $fill= 0;
			
			$total_cliente[$i]=0;
		
			$pdf->Cell(3, 0.6, pega_sigla_pessoa($_POST["id_cliente"][$i]), 1, 0, 'C', $fill);
			
			for ($j=1; $j<13; $j++) {
				
				$result_ee= mysql_query("select count(tr_percursos.id_percurso) as total_percursos from tr_percursos, tr_percursos_passos
													where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
													and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
													and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
													and   tr_percursos_passos.passo = '2'
													and   tr_percursos_passos.id_cliente = '". $_POST["id_cliente"][$i] ."'
													and   tr_percursos.tipo = '5'
													") or die(mysql_error());
				$rs_ee= mysql_fetch_object($result_ee);
				
				$total_cliente[$i]+=$rs_ee->total_percursos;
				$total_mes[$j]+=$rs_ee->total_percursos;
				$total+=$rs_ee->total_percursos;
				
				$pdf->Cell(1.8, 0.6, fnumi($rs_ee->total_percursos), 1, 0, 'C', $fill);
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell(2, 0.6, fnumi($total_cliente[$i]), 1, 1, 'C', $fill);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			$i++;
		}
		
		$pdf->LittleLn();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(3, 0.6, "", 0, 0, 'C');
		
		for ($j=1; $j<13; $j++) {
			$pdf->Cell(1.8, 0.6, $total_mes[$j], 1, 0, 'C', !$fill);
		}
		
		$pdf->Cell(2, 0.6, fnumi($total), 1, 1, 'C', !$fill);
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("entrega_extra_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>
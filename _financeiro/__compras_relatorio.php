<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("iq|", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 2.4);
	$pdf->SetFillColor(235,235,235);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	//$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);

	if ($_POST["id_centro_custo_tipo"]!="") {
		$str.= "and   fi_itens.id_centro_custo_tipo = '". $_POST["id_centro_custo_tipo"] ."' ";
		$txt= pega_centro_custo_tipo($_POST["id_centro_custo_tipo"]);
	} else $txt= "GERAL";
	

	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "RELATÓRIO DE COMPRAS", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, $txt, 0 , 1, 'R');
	
	$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	
	
	$result_est= mysql_query("select * from fi_estoque, fi_itens
								where fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   fi_estoque.id_item = fi_itens.id_item
								$str
								order by fi_itens.item asc
								") or die(mysql_error());
	
	if (mysql_num_rows($result_est)>0) {
		$pdf->SetFillColor(200,200,200);
		
		$pdf->Cell(7, 0.6, "PRODUTO", 1, 0, "L", 1);
		$pdf->Cell(2.1, 0.6, "MÍNIMO", 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, "PROVISIONAMENTO", 1, 0, "C", 1);
		$pdf->Cell(2.2, 0.6, "QTDE ATUAL", 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, "A COMPRAR", 1, 1, "C", 1);
		
		$i=0;
		while ($rs_est= mysql_fetch_object($result_est)) {
			
			$result_min= mysql_query("select * from fi_estoque_minimo
										where id_item = '". $rs_est->id_item ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										");
			$rs_min= mysql_fetch_object($result_min);
			
			$a_comprar= $rs_min->provisionamento-$rs_est->qtde_atual;
			
			//if ($rs_est->qtde_atual<$rs_min->qtde_minima) {
			if (($rs_min->provisionamento!="") && ($rs_min->provisionamento!="0") && ($a_comprar>0)) {
				$pdf->SetFillColor(235,235,235);
				
				if (($i%2)==0) $fill=1;
				else $fill=0;
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				
				$pdf->Cell(7, 0.55, $rs_est->item, 1, 0, "L", $fill);
				$pdf->Cell(2.1, 0.55, fnumf($rs_min->qtde_minima) ." ". pega_tipo_apres($rs_est->tipo_apres), 1, 0, "C", $fill);
				$pdf->Cell(3, 0.55, fnumf($rs_min->provisionamento) ." ". pega_tipo_apres($rs_est->tipo_apres), 1, 0, "C", $fill);
				$pdf->Cell(2.2, 0.55, fnumf($rs_est->qtde_atual) ." ". pega_tipo_apres($rs_est->tipo_apres), 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, fnumf($a_comprar) ." ". pega_tipo_apres($rs_est->tipo_apres), 1, 1, "C", $fill);
				
				$i++;
			}
		}
		
		$pdf->Ln();
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("estoque_minimo_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>
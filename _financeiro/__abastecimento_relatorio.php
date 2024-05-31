<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode('u', $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 2, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$i=0;
	
	$data1= $_GET["data1"];
	$data2= $_GET["data2"];
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "CONTROLE DE ABASTECIMENTOS", 0, 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "ENTRE ". desformata_data($data1) ." E ". desformata_data($data2), 0, 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	if ($data1!="") $str .= " and   fi_abastecimentos.data >= '$data1' ";
	if ($data2!="") $str .= " and   fi_abastecimentos.data <= '$data2' ";
	
	if ($_GET["id_veiculo"]!="") $str .= " and   fi_abastecimentos.id_veiculo= '". $_GET["id_veiculo"] ."' ";
	if ($_GET["id_funcionario"]!="") $str .= " and   fi_abastecimentos.id_funcionario= '". $_GET["id_funcionario"] ."' ";
	if ($_GET["id_departamento"]!="") $str .= " and   rh_carreiras.id_departamento= '". $_GET["id_departamento"] ."' ";
	if ($_GET["id_usuario_at"]!="") $str .= " and   fi_abastecimentos.id_usuario_at= '". $_GET["id_usuario_at"] ."' ";

	$result= mysql_query("select fi_abastecimentos.* from fi_abastecimentos, rh_carreiras
									where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_funcionario = fi_abastecimentos.id_funcionario
									and   rh_carreiras.atual = '1'
									". $str ."
									order by data asc
									") or die(mysql_error());
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	
	$pdf->Cell(2, 0.6, "DATA", 1, 0, 'C', 1);
	$pdf->Cell(5.75, 0.6, "FUNCIONÁRIO", 1, 0, 'L', 1);
	$pdf->Cell(5.25, 0.6, "VEÍCULO", 1, 0, 'L', 1);
	$pdf->Cell(2, 0.6, "LITROS", 1, 0, 'C', 1);
	$pdf->Cell(2, 0.6, "VALOR", 1, 1, 'R', 1);
	
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->SetFillColor(240,240,240);
	
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $fill= 0;
		else $fill= 1;
			
		$valor_total += $rs->valor_total;
		$litros_total += $rs->litros;
		
		$veiculo_aqui= pega_veiculo($rs->id_veiculo);
		
		if (strlen($veiculo_aqui)>35) $veiculo_str= substr($veiculo_aqui, 0, 35)."...";
		else $veiculo_str= $veiculo_aqui;
		
		$pdf->Cell(2, 0.6, desformata_data($rs->data), 1, 0, 'C', $fill);
		$pdf->Cell(5.75, 0.6, pega_funcionario($rs->id_funcionario), 1, 0, 'L', $fill);
		$pdf->Cell(5.25, 0.6, $veiculo_str, 1, 0, 'L', $fill);
		$pdf->Cell(2, 0.6, fnum($rs->litros), 1, 0, 'R', $fill);
		$pdf->Cell(2, 0.6, "R$ ". fnum($rs->valor_total), 1, 1, 'R', $fill);
		
		$i++;
	}
	
	$fill= 0;
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(2, 0.6, "", 0, 0, 'L', $fill);
	$pdf->Cell(6, 0.6, "", 0, 0, 'L', $fill);
	$pdf->Cell(5, 0.6, "", 0, 0, 'L', $fill);
	$pdf->Cell(2, 0.6, fnum($litros_total), 1, 0, 'R', $fill);
	$pdf->Cell(2, 0.6, "R$ ". fnum($valor_total), 1, 1, 'R', $fill);
	
	$pdf->Ln();
	
	$pdf->AliasNbPages(); 
	$pdf->Output("abastecimento_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>
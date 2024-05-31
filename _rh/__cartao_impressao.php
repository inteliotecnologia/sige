<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("rm", $_SESSION["permissao"])) {	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	//require("includes/fpdf/modelo_retrato.php");
	
	$valores[1]= 85;
	$valores[0]= 55;
	
	$pdf=new FPDF('L', 'mm', $valores);
	$pdf->SetDisplayMode('real', 'single') ;
	$pdf->SetMargins(5, 5, 5);
	$pdf->SetAutoPageBreak(true, 5);
	$pdf->SetFillColor(230,230,230);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$pdf->AddPage();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	
	$result= mysql_query("select *
								from  pessoas, rh_funcionarios, rh_cartoes, rh_departamentos, rh_carreiras
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_cartoes.id_cartao= '". $_GET["id_cartao"] ."'
								and   pessoas.tipo = 'f'
								and   rh_funcionarios.id_funcionario = rh_cartoes.id_funcionario
								and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by rh_departamentos.departamento asc,
								pessoas.nome_rz asc
								") or die(mysql_error());
	
	if (mysql_num_rows($result)==0) {
		$pdf->Cell(0, 25, "Cartão não encontrado!", 0, 0, 'L', 0);
	}
	else {
		$rs= mysql_fetch_object($result);
		
		if ($rs->tipo_cartao==2) $str= "(S)";
		else $str= "";
		
		$pdf->Line(0, 0, 0, 55);
		$pdf->Line(0, 0, 85, 0);
		$pdf->Line(85, 0, 85, 55);
		$pdf->Line(0, 55, 85, 55);
		
		$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg", 5, 5, 30, 12);
		$pdf->SetX(35);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 12, "CARTÃO PONTO ". $str, 0, 1, 'C', 0);
		
		$pdf->SetY(25);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(0, 5, $rs->nome_rz, 0, 1, 'C', 0);
		
		$pdf->SetY(34);
		$pdf->Cell(0, 10, $pdf->codigo_barras_pdf($rs->numero_cartao, 5), 0, 0, 'C', 0);
	}
	// Geração do PDF na tela.
	$pdf->Output("cartao_". date("d-m-Y_H:i:s") .".pdf", "I");
}

?> 
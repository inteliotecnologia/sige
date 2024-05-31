<?
	require_once("conexao.php");
	require_once("funcoes.php");
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	//require("includes/fpdf/modelo_cartao_todos.php");
		
	$pdf=new FPDF("P", "mm", "A4");
	$pdf->SetMargins(10, 10, 10);
	//$pdf->SetAutoPageBreak(true, 50);
	$pdf->AcceptPageBreak(true);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$pdf->AddPage();
	
	$num1= ($_GET["parte"]*5);
	$num2= $num1+5;
	
	$result= mysql_query("select *
								from  pessoas, rh_funcionarios, rh_cartoes, rh_departamentos, rh_carreiras
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   pessoas.tipo = 'f'
								and   rh_funcionarios.id_funcionario = rh_cartoes.id_funcionario
								and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_funcionarios.status_funcionario = '1'
								and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by rh_departamentos.departamento asc,
								pessoas.nome_rz asc
								limit $num1, 5
								") or die(mysql_error());
	
	$x=0; $y=0;
	$largura= 85; $altura= 55;
	
	while ($rs= mysql_fetch_object($result)) {
		if ($rs->tipo_cartao==2) $str= "(S)";
		else $str= "";
		
		$il= $largura*x;
		$ia= $altura*$y;
		
		$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg", 5, $ia+5, 30, 12);
		$pdf->SetXY(35, $ia+5);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(55, 12, "CARTÃO PONTO ". $str, 0, 1, 'C', 0);
		
		$pdf->SetXY(5, $ia+25);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(85, 5, $rs->nome_rz, 0, 1, 'C', 0);
		
		$pdf->SetXY(5, $ia+34);
		$pdf->Cell(85, 20, $pdf->codigo_barras_pdf($rs->numero_cartao, 5), 0, 0, 'C', 0);
		
		$y++;
		
		if ($y==5) $pdf->Ln();
		//if ($x==3) {
		//	$y++; $x=0;
		//}
	}
	
	$pdf->Output("cartao_todos_". date("d-m-Y_H:i:s") .".pdf", "I");
?>
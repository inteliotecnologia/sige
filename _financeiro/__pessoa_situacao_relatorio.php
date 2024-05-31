<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("rh", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$i=0;
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.5);
	
	if ($_GET["status_pessoa"]!=2) $str .= " and   pessoas.status_pessoa = '". $_GET["status_pessoa"] ."' ";

	$result = mysql_query("select * from pessoas, pessoas_tipos
							where pessoas.id_pessoa = pessoas_tipos.id_pessoa
							and   pessoas_tipos.tipo_pessoa = '". $_GET["tipo_pessoa"] ."'
							$str
							order by 
							pessoas.nome_rz asc
										") or die(mysql_error());
	$linhas= mysql_num_rows($result);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.75, "RELATÓRIO DE ". strtoupper(pega_tipo_pessoa_plural($_GET["tipo_pessoa"])) ."", 0, 1, 'R');
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, $linhas ." REGISTROS", 0, 1, 'R');
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	
	$pdf->Cell(1, 0.6, "MAT.", 1, 0, 'C', 1);
	$pdf->Cell(10, 0.6, "RAZÃO SOCIAL", 1, 0, 'L', 1);
	$pdf->Cell(3, 0.6, "CPF/CNPJ", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.6, "SITUAÇÃO", 1, 1, 'C', 1);
		
	$pdf->SetFont('ARIALNARROW', '', 9);
	$pdf->SetFillColor(240,240,240);
	
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		$j= $i+1;
		
		/*$result_atualiza= mysql_query("update rh_funcionarios
										set num_func= '$j'
										where id_empresa = '". $id_empresa ."'
										and   id_funcionario = '". $rs->id_funcionario."'
										");*/
		
		if (($i%2)==0) $fill= 0;
		else $fill= 1;
		
		$pdf->Cell(1, 0.6, $rs->num_pessoa, 1, 0, 'C', $fill);
		$pdf->Cell(10, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
		$pdf->Cell(3, 0.6, $rs->cpf_cnpj, 1, 0, 'C', $fill);
		$pdf->Cell(3, 0.6, strip_tags(ativo_inativo($rs->status_pessoa)), 1, 1, 'C', $fill);
					
		$i++;
	}
	
	$pdf->Ln();
	
	$pdf->AliasNbPages(); 
	$pdf->Output("funcionario_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>
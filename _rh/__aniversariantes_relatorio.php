<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode("rm", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato_rh.php");
		
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "ANIVERSARIANTES", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, strtoupper(traduz_mes($_POST["periodo"])), 0 , 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$result= mysql_query("select *, DATE_FORMAT(pessoas.data, '%d/%m') as data_aniversario
								from rh_funcionarios, pessoas, rh_carreiras
								where rh_funcionarios.id_pessoa = pessoas.id_pessoa
								and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
								and   rh_carreiras.atual = '1'
								and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
								and   DATE_FORMAT(pessoas.data, '%m') = '". formata_saida($_POST["periodo"], 2) ."'
								order by DATE_FORMAT(pessoas.data, '%d') asc, pessoas.nome_rz asc
								") or die(mysql_error());
	
	$pdf->Cell(6, 0.7, "NOME", 1, 0, "L", 1);
	$pdf->Cell(5, 0.7, "DEPARTAMENTO", 1, 0, "L", 1);
	$pdf->Cell(3, 0.7, "TURNO", 1, 0, "L", 1);
	$pdf->Cell(3, 0.7, "ANIVERSÁRIO", 1, 1, "L", 1);
	
	$pdf->SetFont('ARIALNARROW', '', 9);
	$pdf->SetFillColor(235,235,235);
	
	$j=0;
	while ($rs= mysql_fetch_object($result)) {
		$result_presente= mysql_query("select * from rh_afastamentos, rh_afastamentos_dias
									  	where rh_afastamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_afastamentos.id_funcionario = '". $rs->id_funcionario ."'
										and   (
											  rh_afastamentos.tipo_afastamento = 'p'
										or    rh_afastamentos.tipo_afastamento = 'o'
										or    rh_afastamentos.tipo_afastamento = 'f'
											  )
										and   rh_afastamentos.id_afastamento = rh_afastamentos_dias.id_afastamento
										and   rh_afastamentos_dias.data = '". date("Ymd") ."'
										") or die(mysql_error());
		$linhas_presente= mysql_num_rows($result_presente);
		
		if ( (($_POST["situacao"]==1) && ($linhas_presente==0)) || ($_POST["situacao"]==2) ) {
			if (($j%2)==0) $fill= 0;
			else $fill= 1;
			
			$pdf->Cell(6, 0.7, $rs->nome_rz, 1, 0, "L", $fill);
			$pdf->Cell(5, 0.7, pega_departamento($rs->id_departamento), 1, 0, "L", $fill);
			$pdf->Cell(3, 0.7, pega_turno($rs->id_turno), 1, 0, "L", $fill);
			$pdf->Cell(3, 0.7, $rs->data_aniversario, 1, 1, "L", $fill);
			
			$j++;
		}
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("aniversariantes_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>
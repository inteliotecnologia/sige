<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode("rh", $_SESSION["permissao"])) {
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
	
	$i=0;
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.5);
	
	$result_total_funcionarios = mysql_query("select *
												from rh_funcionarios, pessoas, rh_carreiras
												where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
												and   rh_carreiras.atual = '1'
												and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
												and   rh_funcionarios.status_funcionario = '1'
												") or die(mysql_error());
	$linhas_total_funcionarios= mysql_num_rows($result_total_funcionarios);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.75, "RELATÓRIO DE FUNCIONÁRIOS", 0, 1, 'R');
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "POR ESCOLARIDADE", 0, 1, 'R');
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	
	$vetor= pega_escolaridade('l');
	$k=1;
	while ($vetor[$k]) {
		
		$result_fun= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras, rh_enderecos
											where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_funcionarios.id_pessoa = rh_enderecos.id_pessoa
											and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_funcionarios.status_funcionario <> '2'
											and   rh_funcionarios.status_funcionario = '1'
											and   rh_funcionarios.escolaridade = '". $k ."'
											". $str ."
											order by pessoas.nome_rz asc
											") or die(mysql_error());
		
		$linhas_funcionarios= mysql_num_rows($result_fun);
		
		if ($linhas_funcionarios>0) {
			$percentual= (($linhas_funcionarios*100)/$linhas_total_funcionarios);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, $vetor[$k] ." - ". fnumf($percentual) ."%", 0, 1, 'L', 0);
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(1.5, 0.6, "MAT.", 1, 0, 'C', 1);
			$pdf->Cell(0, 0.6, " NOME", 1, 1, 'L', 1);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->SetFillColor(240,240,240);
			
			$i=0;
			while ($rs_fun= mysql_fetch_object($result_fun)) {
				$j= $i+1;
				
				if (($i%2)==0) $fill= 0;
				else $fill= 1;
				
				$pdf->Cell(1.5, 0.6, $rs_fun->num_func, 1, 0, 'C', $fill);
				$pdf->Cell(0, 0.6, " ". $rs_fun->nome_rz, 1, 1, 'L', $fill);
							
				$i++;
			}
			$pdf->Ln();
		}
		$k++;
	}
	
	$pdf->Ln();
	
	$pdf->AliasNbPages(); 
	$pdf->Output("funcionario_bairro_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>
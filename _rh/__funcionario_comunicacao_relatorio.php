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
	$pdf->Cell(0, 0.6, "RELATÓRIO DE FUNCIONÁRIOS", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	//somente os que estao presentes
	if ($_POST["sexo"]!="") { $str= " and   pessoas.sexo = '". $_POST["sexo"] ."' "; }
	
	$result= mysql_query("select *, pessoas.data as data_nasc from rh_funcionarios, pessoas, rh_carreiras, rh_departamentos
								where rh_funcionarios.id_pessoa = pessoas.id_pessoa
								and   rh_funcionarios.status_funcionario = '1'
								and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
								and   rh_carreiras.atual = '1'
								and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								$str
								order by ". $_POST["ordenacao"] ." asc
								") or die(mysql_error());
	
	$pdf->Cell(6.5, 0.7, "NOME", 1, 0, "L", 1);
	$pdf->Cell(4.5, 0.7, "DEPARTAMENTO", 1, 0, "L", 1);
	$pdf->Cell(3, 0.7, "TURNO", 1, 0, "L", 1);
	$pdf->Cell(3, 0.7, "DATA DE NASC.", 1, 1, "L", 1);
	
	$j=0;
	while ($rs= mysql_fetch_object($result)) {
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(235,235,235);
		
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
		
		$result_filhos= mysql_query("select * from rh_funcionarios_filhos
												where id_funcionario = '". $rs->id_funcionario ."'
												order by data_nasc_filho asc
												");
		$linhas_filhos= mysql_num_rows($result_filhos);
		
		$limite_nascimento= date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y")-18));
		
		$result_filhos18= mysql_query("select * from rh_funcionarios_filhos
												where id_funcionario = '". $rs->id_funcionario ."'
												and   data_nasc_filho > '". $limite_nascimento ."'
												order by data_nasc_filho asc
												");
		$linhas_filhos18= mysql_num_rows($result_filhos18);
		
		if (($_POST["filhos18"]=="") || (($_POST["filhos18"]==1) && ($linhas_filhos18>0)) ) {
		
			if ( (($_POST["filhos"]=="") || ( ($_POST["filhos"]==1) && ($linhas_filhos>0) ) ) ) {
			
				if ( (($_POST["situacao"]==1) && ($linhas_presente==0)) || ($_POST["situacao"]==2) ) {
					if (($j%2)==0) $fill= 0;
					else $fill= 1;
					
					$pdf->Cell(6.5, 0.6, $rs->nome_rz, 1, 0, "L", $fill);
					$pdf->Cell(4.5, 0.6, pega_departamento($rs->id_departamento), 1, 0, "L", $fill);
					$pdf->Cell(3, 0.6, pega_turno($rs->id_turno), 1, 0, "L", $fill);
					$pdf->Cell(3, 0.6, desformata_data($rs->data_nasc), 1, 1, "L", $fill);
					
					if ($_POST["mostrar_filhos"]==1) {
						
						$pdf->SetFont('ARIALNARROW', '', 8);
						$pdf->SetFillColor(245,245,245);
						
						if ($linhas_filhos>0) {
							$fill= 0;
							
							$pdf->Cell(0, 0.1, "", 0, 1, "L", $fill);
							
							while ($rs_filhos= mysql_fetch_object($result_filhos)) {
								$data_nasc_filho= desformata_data($rs_filhos->data_nasc_filho);
								
								$pdf->Cell(0.5, 0.5, "", 0, 0, "L", $fill);
								$pdf->Cell(6, 0.5, $rs_filhos->nome_filho, 0, 0, "L", $fill);
								$pdf->Cell(4.5, 0.5, "", 0, 0, "L", $fill);
								$pdf->Cell(3, 0.5, "", 0, 0, "L", $fill);
								$pdf->Cell(3, 0.5, $data_nasc_filho ." (". strip_tags(calcula_idade($data_nasc_filho)) ." anos)", 0, 1, "L", $fill);
							}
							
							$pdf->Cell(0, 0.2, "", 0, 1, "L", $fill);
						
						}
					}
					
					$j++;
				}//fim situacao
			}//fim filhos
		}//fim filhos18
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("funcionario_comunicacao_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>
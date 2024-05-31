<?
require_once("conexao.php");
require_once("funcoes.php");
require_once("funcoes_espelho.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode_algum("rh", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem.php");
	
	$periodo2= explode('/', $_GET["periodo"]);
	
	$data1_mk= mktime(0, 0, 0, $periodo2[0]-1, 26, $periodo2[1]);
	$data2_mk= mktime(0, 0, 0, $periodo2[0], 25, $periodo2[1]);
	
	$data1= date("d/m/Y", $data1_mk);
	$data2= date("d/m/Y", $data2_mk);
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1.5, 1.5, 1.5);
	$pdf->SetAutoPageBreak(true, 2.5);
	$pdf->SetFillColor(200,200,200);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
		
	$pdf->AddPage();
	
	$pdf->SetXY(16.9, 1.75);
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "RELATÓRIO DE FOLHA", 0, 1, "R");
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0 , 1, "R");
	
	$pdf->Ln();$pdf->Ln();
	
	// ------------- tabela
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	//2
	
	$pdf->Cell(5.5, 1, "FUNCIONÁRIOS", 1, 0, "L", 1);
	$pdf->Cell(1.2, 1, "INSAL.", 1, 0, "C", 1);
	$pdf->Cell(2, 1, "H DIU.", 1, 0, "C", 1);
	$pdf->Cell(2, 1, "H NOT.", 1, 0, "C", 1);
	$pdf->Cell(2, 1, "HORAS RED.", 1, 0, "C", 1);
	$pdf->Cell(2, 0.5, "HE DIURNO", 1, 0, "C", 1);
	$pdf->Cell(2, 0.5, "HE NOTURNO", 1, 0, "C", 1);
	$pdf->Cell(6, 0.5, "DESCONTOS", 1, 0, "C", 1);
	$pdf->Cell(4, 1, "OBS", 1, 0, "L", 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
	
	$pdf->SetXY(14.2, 4.65);
	$pdf->Cell(1, 0.5, "60%", 1, 0, "C", 1);
	$pdf->Cell(1, 0.5, "100%", 1, 0, "C", 1);
	
	$pdf->Cell(1, 0.5, "60%", 1, 0, "C", 1);
	$pdf->Cell(1, 0.5, "100%", 1, 0, "C", 1);
	
	$result_des= mysql_query("select * from rh_motivos
								where tipo_motivo = 't'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								order by motivo asc");
	
	$linhas_desconto= mysql_num_rows($result_des);
	
	$largura= (6/($linhas_desconto+2));
	
	while ($rs_des= mysql_fetch_object($result_des)) {
		$pdf->Cell($largura, 0.5, $rs_des->motivo, 1, 0, "C", 1);
	}
	
	$pdf->Cell($largura, 0.5, "FALTAS (H)", 1, 0, "C", 1);
	$pdf->Cell($largura, 0.5, "FALTAS (D)", 1, 0, "C", 1);
	
	$pdf->SetXY(1.5, 5.15);
	$pdf->SetFillColor(235,235,235);
	$pdf->SetFont('ARIALNARROW', '', 8);
	
	$horas_diurnas_geral=0;
	$horas_noturnas_geral=0;
	$horas_red_geral=0;
	$faltas_geral=0;
	$faltas_geral_dias=0;
	$he_diurno_geral[0]=0;
	$he_diurno_geral[1]=0;
	$he_noturno_geral[0]=0;
	$he_noturno_geral[1]=0;
	
	if ($_GET["id_departamento"]!="") $str_depto= " and   id_departamento = '". $_GET["id_departamento"] ."' ";
	
	$result_depto= mysql_query("select * from rh_departamentos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								". $str_depto ."
								order by departamento asc
								");
	
	while ($rs_depto = mysql_fetch_object($result_depto)) {
		
		$horas_diurnas_depto=0;
		$horas_noturnas_depto=0;
		$horas_red_depto=0;
		$faltas_depto=0;
		$faltas_depto_dias=0;
		$he_diurno_depto[0]=0;
		$he_diurno_depto[1]=0;
		$he_noturno_depto[0]=0;
		$he_noturno_depto[1]=0;
		
		$j=0;
		$result_fun= mysql_query("select *
									from  pessoas, rh_funcionarios, rh_carreiras
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   pessoas.tipo = 'f'
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.atual = '1'
									and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento."'
									and   rh_funcionarios.status_funcionario = '1'
									/* and   rh_funcionarios.id_funcionario = '106' */
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		$linhas_fun= mysql_num_rows($result_fun);
		
		if ($linhas_fun>0) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->Cell(0, 0.2, "", 0, 1, "L", 0);
			$pdf->Cell(0, 0.5, $rs_depto->departamento, 0, 1, "L", 0);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			while ($rs_fun= mysql_fetch_object($result_fun)) {
				
				$retorno= pega_dados_rh($_SESSION["id_empresa"], 0, 0, $rs_fun->id_funcionario, $data1, $data2);
				$novo= explode("@", $retorno);
				
				if (($j%2)==0) $fill=1;
				else $fill= 0;
				
				$pdf->Cell(5.5, 0.5, $rs_fun->nome_rz, 1 , 0, "L", $fill);
				
				if ($rs_fun->insalubridade!=0) $insalubridade= $rs_fun->insalubridade ."%";
				else $insalubridade= "";
				
				$pdf->Cell(1.2, 0.5, $insalubridade, 1, 0, "C", $fill);
				
				$horas_diurnas= $novo[1];
				$horas_noturnas= $novo[2];
				
				$horas_reduzidas= floor((($horas_noturnas*60)/52.3)-$horas_noturnas);
				
				$pdf->Cell(2, 0.5, calcula_total_horas_ss($horas_diurnas), 1, 0, "C", $fill);
				$pdf->Cell(2, 0.5, calcula_total_horas_ss($horas_noturnas), 1, 0, "C", $fill);
				$pdf->Cell(2, 0.5, calcula_total_horas_ss($horas_reduzidas), 1, 0, "C", $fill);
				
				$pdf->Cell(1, 0.5, (calcula_total_horas_ss($novo[4])), 1, 0, "C", $fill);
				$pdf->Cell(1, 0.5, (calcula_total_horas_ss($novo[5])), 1, 0, "C", $fill);
				
				$pdf->Cell(1, 0.5, (calcula_total_horas_ss($novo[6])), 1, 0, "C", $fill);
				$pdf->Cell(1, 0.5, (calcula_total_horas_ss($novo[7])), 1, 0, "C", $fill);
				
				$horas_diurnas_depto+=$horas_diurnas;
				$horas_noturnas_depto+=$horas_noturnas;
				$horas_red_depto+=$horas_reduzidas;
				$faltas_depto+=$novo[3];
				$faltas_depto_dias+=$novo[8];
				$he_diurno_depto[0]+=$novo[4];
				$he_diurno_depto[1]+=$novo[5];
				$he_noturno_depto[0]+=$novo[6];
				$he_noturno_depto[1]+=$novo[7];
				
				$result_des= mysql_query("select * from rh_motivos
											where tipo_motivo = 't'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											order by motivo asc");
				
				$linhas_desconto= mysql_num_rows($result_des);
				
				$largura= (6/($linhas_desconto+2));
				
				while ($rs_des= mysql_fetch_object($result_des)) {
					$result= mysql_query("select * from rh_descontos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_funcionario = '". $rs_fun->id_funcionario ."'
											and   mes = '". $periodo2[0] ."'
											and   ano = '". $periodo2[1] ."'
											and   id_motivo = '". $rs_des->id_motivo ."'
											");
					$rs= mysql_fetch_object($result);
					
					if ($rs_des->qtde_dias==0) $valor= sim_nao_pdf($rs->valor);
					elseif ($rs->valor!=0) $valor= fnum($rs->valor);
					else $valor= "";
					
					$pdf->Cell($largura, 0.5, $valor, 1, 0, "C", $fill);
				}
				
				$pdf->Cell($largura, 0.5, calcula_total_horas_ss($novo[3]), 1, 0, "C", $fill);
				$pdf->Cell($largura, 0.5, $novo[8], 1, 0, "C", $fill);
				$pdf->MultiCell(4, 0.5, "", 1, "L", $fill);
				
				$j++;
			}
			
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(5.5, 0.5, "", 0, 0, "L");
			$pdf->Cell(1.2, 0.5, "", 0, 0, "L");
			
			$pdf->Cell(2, 0.5, calcula_total_horas_ss($horas_diurnas_depto), 1, 0, "C", 1);
			$pdf->Cell(2, 0.5, calcula_total_horas_ss($horas_noturnas_depto), 1, 0, "C", 1);
			$pdf->Cell(2, 0.5, calcula_total_horas_ss($horas_red_depto), 1, 0, "C", 1);
			
			$pdf->Cell(1, 0.5, calcula_total_horas_ss($he_diurno_depto[0]), 1, 0, "C", 1);
			$pdf->Cell(1, 0.5, calcula_total_horas_ss($he_diurno_depto[1]), 1, 0, "C", 1);
			
			$pdf->Cell(1, 0.5, calcula_total_horas_ss($he_noturno_depto[0]), 1, 0, "C", 1);
			$pdf->Cell(1, 0.5, calcula_total_horas_ss($he_noturno_depto[1]), 1, 0, "C", 1);
			
			$total_extra_depto= $he_diurno_depto[0]+$he_diurno_depto[1]+$he_noturno_depto[0]+$he_noturno_depto[1];
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(6-($largura*2), 0.5, "", 0, 0, "C", 0);
			
			$pdf->Cell($largura, 0.5, calcula_total_horas_ss($faltas_depto), 1, 0, "C", 1);
			$pdf->Cell($largura, 0.5, $faltas_depto_dias, 1, 1, "C", 1);
			
			$pdf->Cell(6.7, 0.5, "", 0, 0, "C", 0);
			
			$horas_totais_depto= $horas_diurnas_depto+$horas_noturnas_depto;
			
			$pdf->Cell(4, 0.5, calcula_total_horas_ss($horas_totais_depto), 1, 0, "C", 1);
			
			$pdf->Cell(2, 0.5, "", 0, 0, "C", 0);
			
			$pdf->Cell(4, 0.5, calcula_total_horas_ss($total_extra_depto), 1, 0, "C", 1);
			
			$pdf->SetFillColor(235,235,235);
			
			$horas_diurnas_geral+=$horas_diurnas_depto;
			$horas_noturnas_geral+=$horas_noturnas_depto;
			$horas_red_geral+=$horas_red_depto;
			$faltas_geral+=$faltas_depto;
			$faltas_geral_dias+=$faltas_depto_dias;
			$he_diurno_geral[0]+=$he_diurno_depto[0];
			$he_diurno_geral[1]+=$he_diurno_depto[1];
			$he_noturno_geral[0]+=$he_noturno_depto[0];
			$he_noturno_geral[1]+=$he_noturno_depto[1];
			
			$pdf->Ln();
			
		}
		
	}
	
	$pdf->Ln();$pdf->Ln();
		
	$pdf->SetFillColor(200,200,200);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
	$pdf->Cell(0, 0.2, "", 0, 1, "L", 0);
	$pdf->Cell(0, 0.6, "GERAL:", 0, 1, "L", 0);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	
	//$pdf->Cell(5.5, 0.6, "", 0, 0, "L");
	//$pdf->Cell(1.2, 0.6, "", 0, 0, "C");
	$pdf->Cell(2, 0.6, "HORAS DIU.", 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "HORAS NOT.", 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "HORAS RED.", 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "HE DIURNO", 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "HE NOTURNO", 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "FALTAS (H)", 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "FALTAS (D)", 1, 0, "C", 1);
	$pdf->Cell(10, 0.6, "", 0, 1, "L", 0);
	
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->SetFillColor(235,235,235);
	
	//$pdf->Cell(5.5, 0.6, "", 0, 0, "L");
	//$pdf->Cell(1.2, 0.6, "", 0, 0, "L");
	
	$pdf->Cell(2, 0.6, calcula_total_horas_ss($horas_diurnas_geral), 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, calcula_total_horas_ss($horas_noturnas_geral), 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, calcula_total_horas_ss($horas_red_geral), 1, 0, "C", 1);
	
	$pdf->Cell(1, 0.6, calcula_total_horas_ss($he_diurno_geral[0]), 1, 0, "C", 1);
	$pdf->Cell(1, 0.6, calcula_total_horas_ss($he_diurno_geral[1]), 1, 0, "C", 1);
	
	$pdf->Cell(1, 0.6, calcula_total_horas_ss($he_noturno_geral[0]), 1, 0, "C", 1);
	$pdf->Cell(1, 0.6, calcula_total_horas_ss($he_noturno_geral[1]), 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, calcula_total_horas_ss($faltas_geral), 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, $faltas_geral_dias, 1, 1, "C", 1);
	
	$total_extra_geral= $he_diurno_geral[0]+$he_diurno_geral[1]+$he_noturno_geral[0]+$he_noturno_geral[1];
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	$horas_totais= $horas_diurnas_geral+$horas_noturnas_geral;
	$pdf->Cell(4, 0.6, calcula_total_horas_ss($horas_totais), 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "", 0, 0, "C", 0);
	$pdf->Cell(4, 0.6, calcula_total_horas_ss($total_extra_geral), 1, 0, "C", 1);
	
	$pdf->SetFillColor(235,235,235);
	
	$pdf->AliasNbPages(); 
	$pdf->Output("", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>
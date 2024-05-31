<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode("r", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato_rh.php");
		
	$dataq= explode("/", $_POST["periodo"]);
	$mes_extenso= traduz_mes($dataq[0]);
		
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
	$pdf->Cell(0, 0.6, "RELATÓRIO DE VALE-TRANSPORTE (". strtoupper(pega_tipo_relatorio($_POST["tipo_relatorio"])) .")", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, $mes_extenso ."/". $dataq[1], 0 , 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	//sintético
	if ($_POST["tipo_relatorio"]==1) {
		
		$result_linha= mysql_query("select *
										from  rh_vt_linhas
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										order by id_linha asc
										") or die(mysql_error());
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->SetFillColor(210,210,210);
		
		$pdf->Cell(7, 0.6, "LINHA", 1, 0, "L", 1);
		$pdf->Cell(4.75, 0.6, "VALOR DA PASSAGEM", 1, 0, "L", 1);
		$pdf->Cell(3, 0.6, "QUANTIDADE", 1, 0, "L", 1);
		$pdf->Cell(3, 0.6, "VALOR TOTAL", 1, 1, "L", 1);
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->SetFillColor(235,235,235);
		
		$qtde_total_geral= 0;
		$valor_total_linha_geral= 0;
		
		$j=0;
		while ($rs_linha= mysql_fetch_object($result_linha)) {
			if (($j%2)==0) $fill= 0;
			else $fill= 1;
			
			$qtde_linha_mes= 0;
			$valor_total_linha_mes= 0;
			
			$result_func= mysql_query("select distinct(rh_vt.id_funcionario)
									from  pessoas, rh_funcionarios, rh_vt
									where rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_funcionarios.status_funcionario = '1'
									and   rh_funcionarios.id_funcionario = rh_vt.id_funcionario
									and   pessoas.id_pessoa = rh_funcionarios.id_pessoa
									order by pessoas.nome_rz asc
									") or die(mysql_error());
			
			while ($rs_func= mysql_fetch_object($result_func)) {
				$result_vt= mysql_query("select count(*) as total from rh_vt
										where rh_vt.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_vt.id_linha = '". $rs_linha->id_linha ."'
										and   rh_vt.id_funcionario = '". $rs_func->id_funcionario ."'
										") or die(mysql_error());
				$rs_vt= mysql_fetch_object($result_vt);
				
				$result_dias= mysql_query("select * from rh_escala
											where id_funcionario = '". $rs_func->id_funcionario ."'
											and   trabalha = '1'
											and   DATE_FORMAT(data_escala, '%m/%Y') = '". $_POST["periodo"] ."'
											");
				$qtde= mysql_num_rows($result_dias);
				
				$qtde_linha_mes+= ($rs_vt->total*$qtde);
			}
			
			$valor_total_linha_mes= $qtde_linha_mes*$rs_linha->valor;
			
			$pdf->Cell(7, 0.6, $rs_linha->linha, 1, 0, "L", $fill);
			$pdf->Cell(4.75, 0.6, "R$ ". fnum($rs_linha->valor), 1, 0, "L", $fill);
			$pdf->Cell(3, 0.6, $qtde_linha_mes, 1, 0, "L", $fill);
			$pdf->Cell(3, 0.6, "R$ ". fnum($valor_total_linha_mes), 1, 1, "L", $fill);
			
			$qtde_total_geral+=$qtde_linha_mes;
			$valor_total_linha_geral+=$valor_total_linha_mes;
			
			$j++;
		}
		
		$pdf->Cell(7, 0.6, "", 0, 0, "L");
		$pdf->Cell(4.75, 0.6, "", 0, 0, "L");
		$pdf->Cell(3, 0.6, $qtde_total_geral, 1, 0, "L", $fill);
		$pdf->Cell(3, 0.6, "R$ ". fnum($valor_total_linha_geral), 1, 1, "L", $fill);
		
	}
	//analítico
	if ($_POST["tipo_relatorio"]==2) {
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$result= mysql_query("select distinct(rh_vt.id_funcionario)
								from  pessoas, rh_funcionarios, rh_vt
								where rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   rh_funcionarios.status_funcionario = '1'
								and   rh_funcionarios.id_funcionario = rh_vt.id_funcionario
								and   pessoas.id_pessoa = rh_funcionarios.id_pessoa
								order by pessoas.nome_rz asc
								") or die(mysql_error());
		
		$qtde_mes= 0;
		$total_mes= 0;
		
		while ($rs= mysql_fetch_object($result)) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, pega_funcionario($rs->id_funcionario), "B", 1, "L");
			$pdf->Cell(0, 0.3, "", 0, 1);
			
			$result_vt= mysql_query("select * from rh_vt, rh_vt_linhas
										where rh_vt.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_vt.id_linha = rh_vt_linhas.id_linha
										and   rh_vt.id_funcionario = '". $rs->id_funcionario ."'
										order by  rh_vt.trajeto desc, rh_vt_linhas.id_linha asc
										") or die(mysql_error());
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(5.8, 0.6, "LINHA", 1, 0, "L", 1);
			$pdf->Cell(2.7, 0.6, "TRAJETO", 1, 0, "L", 1);
			$pdf->Cell(2.75, 0.6, "VALOR", 1, 0, "L", 1);
			$pdf->Cell(3, 0.6, "QUANTIDADE", 1, 0, "L", 1);
			$pdf->Cell(2.75, 0.6, "TOTAL", 1, 1, "L", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->SetFillColor(235,235,235);
			
			$qtde_funcionario= 0;
			$total_funcionario= 0;
			
			$result_dias= mysql_query("select * from rh_escala
										where id_funcionario = '". $rs->id_funcionario ."'
										and   trabalha = '1'
										and   DATE_FORMAT(data_escala, '%m/%Y') = '". $_POST["periodo"] ."'
										");
			$qtde= mysql_num_rows($result_dias);
			
			$j=0;
			while ($rs_vt= mysql_fetch_object($result_vt)) {
				if (($j%2)==0) $fill= 0;
				else $fill= 1;
				
				$qtde_funcionario+=$qtde;
	
				$total= $qtde*$rs_vt->valor;
				$total_funcionario+=$total;
				
				$pdf->Cell(5.8, 0.6, $rs_vt->linha, 1, 0, "L", $fill);
				$pdf->Cell(2.7, 0.6, pega_trajeto($rs_vt->trajeto), 1, 0, "L", $fill);
				$pdf->Cell(2.75, 0.6, "R$ ". fnum($rs_vt->valor), 1, 0, "L", $fill);
				$pdf->Cell(3, 0.6, $qtde, 1, 0, "L", $fill);
				$pdf->Cell(2.75, 0.6, "R$ ". fnum($total), 1, 1, "L", $fill);
				
				$j++;
			}
			
			$pdf->Cell(5.8, 0.6, "", 0, 0, "L");
			$pdf->Cell(2.7, 0.6, "", 0, 0, "L");
			$pdf->Cell(2.75, 0.6, "", 0, 0, "L");
			$pdf->Cell(3, 0.6, $qtde_funcionario, 1, 0, "L");
			$pdf->Cell(2.75, 0.6, "R$ ". fnum($total_funcionario), 1, 1, "L");
			
			$qtde_mes+=$qtde_funcionario;
			$total_mes+=$total_funcionario;
		}
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(3.6, 0.6, "QUANTIDADE/MÊS:", 0, 0, "L");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(0, 0.6, $qtde_mes, 0, 1, "L");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(3.6, 0.6, "VALOR TOTAL/MÊS:", 0, 0, "L");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(0, 0.6, "R$ ". fnum($total_mes), 0, 1, "L");
		
	}//fim analítico
	
	$pdf->AliasNbPages(); 
	$pdf->Output("vt_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>
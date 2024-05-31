<?
require_once("conexao.php");
require_once("funcoes.php");
require_once("funcoes_espelho.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode_algum("rh", $_SESSION["permissao"])) {
	
	$str_filtra= " or 1=1 ";
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	if ($_POST["periodo"]!="") {
		$periodo2= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(0, 0, 0, $periodo2[0]-1, 26, $periodo2[1]);
		$data2_mk= mktime(0, 0, 0, $periodo2[0], 25, $periodo2[1]);
		
		$data1= date("d/m/Y", $data1_mk);
		$data2= date("d/m/Y", $data2_mk);
	}
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 2, 2);
	$pdf->SetAutoPageBreak(true, 2.5);
	$pdf->SetFillColor(200,200,200);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
		
	$pdf->AddPage();
	
	//turnover
	if ($_POST["tipo_relatorio"]=="t") {
	
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "TURNOVER", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$periodo3= explode("/", $_POST["periodo"]);
		//$proximo_mes= $periodo2[1] ."-". $periodo2[0] ."-01";
		
		$data_mes_anterior= date("Y-m-d" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		$data_mes_atual= date("Y-m-d" , mktime(14, 0, 0, $periodo3[0], 1, $periodo3[1]));
		
		$m_anterior= date("m" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		$a_anterior= date("Y" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(3, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
		$pdf->Cell(2, 0.6, "ADMISSÕES", 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, "DESLIGAMENTOS", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "DEMITIDOS", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "PEDIDOS", 1, 0, "C", 1);
		$pdf->Cell(3.5, 0.6, "ATIVOS EM ". strtoupper(traduz_mes($m_anterior)) ."/". $a_anterior, 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "TURNOVER", 1, 1, "C", 1);
		
		if ($_POST["id_departamento"]!="") $str_depto= " and   id_departamento = '". $_POST["id_departamento"] ."' ";
		if ($_POST["id_turno"]!="") {
			$str_turno= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
			$tit_turno= " (". pega_turno($_POST["id_turno"]) .")";
		}
		
		$result_depto= mysql_query("select * from rh_departamentos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									". $str_depto ."
									order by departamento asc
									");
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		$geral_total_admissoes= 0;
		$geral_total_desligamentos= 0;
		
		$geral_total_demitidos1= 0;
		$geral_total_demitidos2= 0;
		
		$geral_total_ativos_anterior= 0;
		
		$j=1;
		while ($rs_depto = mysql_fetch_object($result_depto)) {
			
			if (($j%2)==0) $fill=1;
			else $fill= 0;
			
			$result_admissao= mysql_query("select count(rh_carreiras.id_carreira) as total_admissoes from rh_carreiras
											where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
											$str_turno
											and   rh_carreiras.id_acao_carreira = '1'
											and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". $_POST["periodo"] ."'
											") or die(mysql_error());
			$rs_admissao= mysql_fetch_object($result_admissao);
			
			$result_desligamento= mysql_query("select count(rh_carreiras.id_carreira) as total_desligamentos from rh_carreiras
												where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
												$str_turno
												and   rh_carreiras.id_acao_carreira = '2'
												and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". $_POST["periodo"] ."'
											") or die(mysql_error());
			$rs_desligamento= mysql_fetch_object($result_desligamento);
			
			$result_demitidos1= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y-%m-%d') as data_admissao from rh_funcionarios, pessoas, rh_carreiras
												where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
												and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
												$str_turno
												and   rh_carreiras.id_acao_carreira = '2'
												and   rh_carreiras.id_detalhe_carreira = '1'
												and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
												and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". $_POST["periodo"] ."'
												") or die(mysql_error());
			$linhas_demitidos1= mysql_num_rows($result_demitidos1);
			
			$geral_total_demitidos1+=$linhas_demitidos1;
			
			$result_demitidos2= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y-%m-%d') as data_admissao from rh_funcionarios, pessoas, rh_carreiras
												where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
												and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
												$str_turno
												and   rh_carreiras.id_acao_carreira = '2'
												and   rh_carreiras.id_detalhe_carreira = '2'
												and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
												and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". $_POST["periodo"] ."'
												") or die(mysql_error());
			$linhas_demitidos2= mysql_num_rows($result_demitidos2);
			
			$geral_total_demitidos2+=$linhas_demitidos2;
			
			$result_ativos_anterior= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras
													where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
													and   rh_carreiras.atual = '1'
													and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
													and   rh_funcionarios.status_funcionario <> '2'
													and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
													$str_turno
													and   rh_funcionarios.id_funcionario NOT IN
													(
													select id_funcionario from rh_carreiras
													where id_acao_carreira = '2'
													and   data < '". $data_mes_atual ."'
													)
													
													and   rh_funcionarios.id_funcionario IN
													(
													select id_funcionario from rh_carreiras
													where id_acao_carreira = '1'
													and   data <= '". $data_mes_atual ."'
													)
													
													") or die(mysql_error());
			
			$linhas_ativos_anterior= mysql_num_rows($result_ativos_anterior);
			
			if ($linhas_ativos_anterior>0) $turnover= ((($rs_admissao->total_admissoes+$rs_desligamento->total_desligamentos)/2)/$linhas_ativos_anterior)*100;
			else $turnover= "0";
			
			$pdf->Cell(3, 0.6, $rs_depto->departamento . $tit_turno, 1, 0, "L", $fill);
			$pdf->Cell(2, 0.6, $rs_admissao->total_admissoes, 1, 0, "C", $fill);
			$pdf->Cell(2.5, 0.6, $rs_desligamento->total_desligamentos, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, $linhas_demitidos1, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, $linhas_demitidos2, 1, 0, "C", $fill);
			$pdf->Cell(3.5, 0.6, $linhas_ativos_anterior, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, fnum($turnover) ."%", 1, 1, "C", $fill);
			
			$geral_total_admissoes+= $rs_admissao->total_admissoes;
			$geral_total_desligamentos+= $rs_desligamento->total_desligamentos;
			$geral_ativos_anterior+= $linhas_ativos_anterior;
			
			//@mysql_free_result($result_admissao);
			//@mysql_free_result($result_desligamento);
			//@mysql_free_result($result_ativos_anterior);
			
			$j++;
		} //fim while
		
		//@mysql_free_result($result_depto);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		if ($geral_ativos_anterior>0) $geral_turnover= ((($geral_total_admissoes+$geral_total_desligamentos)/2)/$geral_ativos_anterior)*100;
		else $geral_turnover= "0";
		
		$pdf->Cell(3, 0.6, "", 0, 0, "L");
		$pdf->Cell(2, 0.6, $geral_total_admissoes, 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, $geral_total_desligamentos, 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, $geral_total_demitidos1, 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, $geral_total_demitidos2, 1, 0, "C", 1);
		$pdf->Cell(3.5, 0.6, $geral_ativos_anterior, 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, fnum($geral_turnover) ."%", 1, 1, "C", 1);
		
	}//fim turnover
	
	//absenteismo
	if ($_POST["tipo_relatorio"]=="a") {
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ABSENTEÍSMO", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$periodo3= explode("/", $_POST["periodo"]);
		//$proximo_mes= $periodo2[1] ."-". $periodo2[0] ."-01";
		
		$data_mes_atual= date("Y-m-d" , mktime(14, 0, 0, $periodo3[0], 1, $periodo3[1]));
		$data_mes_anterior= date("Y-m-d" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		
		$m_anterior= date("m" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		$a_anterior= date("Y" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(5, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
		$pdf->Cell(3, 0.6, "FALTAS (D)", 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, "HORAS/DIA", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "Nº FUNC.", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "DIAS TRAB.", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "ABSENTEÍSMO", 1, 1, "C", 1);
		
		if ($_POST["id_departamento"]!="") $str_depto= " and   id_departamento = '". $_POST["id_departamento"] ."' ";
		if ($_POST["id_turno"]!="") {
			$str_turno= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
			$tit_turno= " (". pega_turno($_POST["id_turno"]) .")";
			
			$id_turno_aqui= $_POST["id_turno"];
		}
		else $id_turno_aqui= 0;
		
		$result_depto= mysql_query("select * from rh_departamentos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									". $str_depto ."
									and   bate_ponto = '1'
									order by departamento asc
									");
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(230,230,230);
		
		$geral_total_faltas= 0;
		$geral_n_func= 0;
		$geral_hht= 0;
		
		//$total_horas_set= 792000;
		
		$j=0;
		while ($rs_depto = mysql_fetch_object($result_depto)) {
			
			if (($j%2)!=0) $fill=1;
			else $fill= 0;
			
			$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], $rs_depto->id_departamento, $id_turno_aqui, 0, $data1, $data2);
			$novo= explode("@", $retorno);
			
			$horas_normais= $novo[1]+$novo[2];
			
			$periodo3= explode("/", $_POST["periodo"]);
			$proximo_mes= $periodo3[1] ."-". $periodo3[0] ."-01";
			
			$result_ativos= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras
											where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_funcionarios.status_funcionario <> '2'
											and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
											and   rh_funcionarios.id_funcionario NOT IN
											(
											select id_funcionario from rh_carreiras
											where id_acao_carreira = '2'
											and   data < '". $data_mes_atual ."'
											)
											
											$str_turno
											and   rh_funcionarios.id_funcionario IN
											(
											select id_funcionario from rh_carreiras
											where id_acao_carreira = '1'
											and   data <= '". $data_mes_atual ."'
											)
											") or die(mysql_error());
			$linhas_ativos= mysql_num_rows($result_ativos);
			
			$result_dias_trabalhados= mysql_query("select distinct(rh_escala.data_escala) as data_escala from rh_escala, rh_carreiras, rh_funcionarios
												  	where rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
													$str_turno
													and   rh_carreiras.id_funcionario = rh_escala.id_funcionario
													and   rh_carreiras.atual = '1'
													and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
													and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
													and   DATE_FORMAT(rh_escala.data_escala, '%m/%Y') = '". $_POST["periodo"] ."'
													");
			$linhas_dias_trabalhados= mysql_num_rows($result_dias_trabalhados);
			
			$horas_media_semana= pega_media_diaria_trabalho($rs_depto->id_departamento, 0);
			$hht= $linhas_ativos*$horas_media_semana*$linhas_dias_trabalhados;
			
			//$hht= (transforma_hora_em_decimal($horas_normais)*$linhas_ativos*$linhas_dias_trabalhados);
			
			$faltas_aqui= $novo[8]+$novo[9]+$novo[10];
			
			if ($hht>0)
				$absenteismo= ($faltas_aqui*$horas_media_semana/$hht)*100;
			
			//echo (transforma_hora_em_decimal($novo[3])) ." | ". transforma_hora_em_decimal($total_horas_set) ." | ". $hht;
			
			$pdf->Cell(5, 0.6, $rs_depto->departamento . $tit_turno, 1, 0, "L", $fill);
			$pdf->Cell(3, 0.6, $faltas_aqui, 1, 0, "C", $fill);
			$pdf->Cell(3, 0.6, fnum($horas_media_semana), 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, $linhas_ativos, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, $linhas_dias_trabalhados, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, fnum($absenteismo) ."%", 1, 1, "C", $fill);
			
			$geral_total_faltas+= $faltas_aqui;
			$geral_horas_media_semana+= $horas_media_semana;
			$geral_n_func+= $linhas_ativos;
			$geral_hht= 0;
			
			//@mysql_free_result($result_ativos);
			//@mysql_free_result($result_dias_trabalhados);
			
			$j++;
		} //fim while
		
		//@mysql_free_result($result_depto);
		
		//se estiver gerando geral
		if ($_POST["id_departamento"]=="") {
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->SetFillColor(200,200,200);
			
			$result_dias_trabalhados_geral= mysql_query("select distinct(rh_escala.data_escala) as data_escala from rh_escala, rh_carreiras, rh_funcionarios
														where rh_carreiras.id_funcionario = rh_escala.id_funcionario
														and   rh_carreiras.atual = '1'
														and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
														and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
														and   DATE_FORMAT(rh_escala.data_escala, '%m/%Y') = '". $_POST["periodo"] ."'
														");
			$linhas_dias_trabalhados_geral= mysql_num_rows($result_dias_trabalhados_geral);
			
			$horas_media_semana_geral= ($geral_horas_media_semana/$j);
			$geral_hht= $geral_n_func*$horas_media_semana*$linhas_dias_trabalhados_geral;
				
			if ($geral_hht>0)
				$absenteismo_geral= ($geral_total_faltas*$horas_media_semana_geral/$geral_hht)*100;
			//$absenteismo= ($novo[8]*$horas_media_semana/$hht)*100;
			
			$pdf->Cell(5, 0.6, "", 0, 0, "L");
			$pdf->Cell(3, 0.6, $geral_total_faltas, 1, 0, "C", 1);
			$pdf->Cell(3, 0.6, fnum($horas_media_semana_geral), 1, 0, "C", 1);
			$pdf->Cell(2, 0.6, $geral_n_func, 1, 0, "C", 1);
			$pdf->Cell(2, 0.6, $linhas_dias_trabalhados_geral, 1, 0, "C", 1);
			$pdf->Cell(2, 0.6, fnum($absenteismo_geral) ."%", 1, 1, "C", 1);
			
			//@mysql_free_result($result_dias_trabalhados_geral);
		}
		
	}
	
	//faltas justificadas/n~ao justificadas
	if ($_POST["tipo_relatorio"]=="j") {
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "FALTAS", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$periodo3= explode("/", $_POST["periodo"]);
		//$proximo_mes= $periodo2[1] ."-". $periodo2[0] ."-01";
		
		$data_mes_anterior= date("Y-m-d" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		
		$m_anterior= date("m" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		$a_anterior= date("Y" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(5, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
		$pdf->Cell(2, 0.6, "ATIVOS", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "FALTAS", 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, "SUSPENSÕES", 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, "JUSTIFICADAS", 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, "NÃO JUSTIFICADAS", 1, 1, "C", 1);
		
		if ($_POST["id_departamento"]!="") $str_depto= " and   id_departamento = '". $_POST["id_departamento"] ."' ";
		if ($_POST["id_turno"]!="") {
			$str_turno= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
			$tit_turno= " (". pega_turno($_POST["id_turno"]) .")";
			
			$id_turno_aqui= $_POST["id_turno"];
		} else $id_turno_aqui= 0;
		
		$result_depto= mysql_query("select * from rh_departamentos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									". $str_depto ."
									and   bate_ponto = '1'
									order by departamento asc
									");
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		$geral_total_faltas= 0;
		$geral_total_suspensoes= 0;
		$geral_total_nj= 0;
		$geral_total_j= 0;
		
		$j=0;
		while ($rs_depto = mysql_fetch_object($result_depto)) {
			
			if (($j%2)!=0) $fill=1;
			else $fill= 0;
			
			$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], $rs_depto->id_departamento, $id_turno_aqui, 0, $data1, $data2);
			$novo= explode("@", $retorno);
			
			$periodo3= explode("/", $_POST["periodo"]);
			$proximo_mes= $periodo3[1] ."-". $periodo3[0] ."-01";
			
			$result_ativos= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras
											where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_funcionarios.status_funcionario <> '2'
											and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
											$str_turno
											and   rh_funcionarios.id_funcionario NOT IN
											(
											select id_funcionario from rh_carreiras
											where id_acao_carreira = '2'
											and   data < '". $proximo_mes ."'
											)
											
											and   rh_funcionarios.id_funcionario IN
											(
											select id_funcionario from rh_carreiras
											where id_acao_carreira = '1'
											and   data <= '". $proximo_mes ."'
											)
											") or die(mysql_error());
			$linhas_ativos= mysql_num_rows($result_ativos);
			
			$total_faltas_depto= $novo[8]+$novo[9];
			
			$pdf->Cell(5, 0.6, $rs_depto->departamento . $tit_turno, 1, 0, "L", $fill);
			$pdf->Cell(2, 0.6, $linhas_ativos, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, $total_faltas_depto, 1, 0, "C", $fill);
			$pdf->Cell(2.5, 0.6, $novo[10], 1, 0, "C", $fill);
			$pdf->Cell(2.5, 0.6, $novo[9], 1, 0, "C", $fill);
			$pdf->Cell(3, 0.6, $novo[8], 1, 1, "C", $fill);
			
			$geral_total_faltas+= $total_faltas_depto;
			$geral_ativos+= $linhas_ativos;
			$geral_total_suspensoes+= $novo[10];
			$geral_total_j+= $novo[9];
			$geral_total_nj+= $novo[8];
			
			//@mysql_free_result($result_ativos);
			
			$j++;
		} //fim while
		
		//@mysql_free_result($result_depto);
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(200,200,200);
		
		$total_geral_faltas= $geral_total_j+$geral_total_nj;
		
		if ($total_geral_faltas>0) $geral_percent_j= ($geral_total_j*100)/$total_geral_faltas;
		else $geral_percent_j= 0;
		
		if ($total_geral_faltas>0) $geral_percent_nj= ($geral_total_nj*100)/$total_geral_faltas;
		else $geral_percent_nj= 0;
		
		$pdf->Cell(5, 0.6, "", 0, 0, "L");
		$pdf->Cell(2, 0.6, $geral_ativos, 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, $geral_total_faltas, 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, $geral_total_suspensoes, 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, $geral_total_j ." (". fnum($geral_percent_j) ."%)", 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, $geral_total_nj ." (". fnum($geral_percent_nj) ."%)", 1, 1, "C", 1);
		
	}
	
	//sem suspensao/adv/atestado
	if ($_POST["tipo_relatorio"]=="s") {
		
		$str_filtra= "";
		
		if ($_POST["modo"]==1) {
			$modo_str="NOT IN";
			$tit_str="SEM ";
		}
		else {
			$modo_str="IN";
			$tit_str="COM ";
		}
		
		
		if ($_POST["advertencia"]==1) {
			$tit_str.="ADV./";
			$str_filtra .= " or tipo_afastamento = 'd' ";
		}
		if ($_POST["suspensao"]==1) {
			$tit_str.="SUS./";
			$str_filtra .= " or tipo_afastamento = 's' ";
		}
		if ($_POST["atestado"]==1) {
			$tit_str.="ATES./";
			$str_filtra .= " or tipo_afastamento = 'a' ";
		}
		if ($_POST["pericia"]==1) {
			$tit_str.="PER./";
			$str_filtra .= " or tipo_afastamento = 'p' ";
		}
		if ($_POST["ferias"]==1) {
			$tit_str.="FÉR./";
			$str_filtra .= " or tipo_afastamento = 'f' ";
		}
		if ($_POST["faltas"]==1) {
			$tit_str.="FALTAS";
			//$str_filtra .= " or tipo_afastamento = 'a' ";
		}
		
		$str_filtra= substr($str_filtra, 4, 9999);
		
		if (($_POST["periodicidade_relatorio"]=="0") || ($_POST["periodicidade_relatorio"]=="1")) {
			
			
			if (($_POST["data1"]!="") && ($_POST["data2"]!="")) {
				$data1d= explode("/", $_POST["data1"]);
				$data2d= explode("/", $_POST["data2"]);
				
				$ponto_inicio= date("Y-m-d", mktime(14, 0, 0, $data1d[1], $data1d[0], $data1d[2]));
				$ponto_fim= date("Y-m-d", mktime(14, 0, 0, $data2d[1], $data2d[0], $data2d[2]));
				
				$linha2= "ENTRE ". $_POST["data1"] ." E ". $_POST["data2"];
			}
			else {	
				$periodo2= explode('/', $_POST["periodo"]);
				$mes_atual= $periodo2[1] ."-". $periodo2[0] ."-01";
				
				//$proximo_mes= date("Y-m-d", mktime(14, 0, 0, $periodo2[0]+1, 1, $periodo2[1]));
				
				$ponto_inicio= date("Y-m-d", mktime(14, 0, 0, $periodo2[0]-1, 26, $periodo2[1]));
				$ponto_fim= date("Y-m-d", mktime(14, 0, 0, $periodo2[0], 25, $periodo2[1]));
				
				$linha2= traduz_mes($periodo2[0]) ."/". $periodo2[1];
			}
			//for ($i=1; $i<13; $i++) {
			
			$pdf->SetXY(16.9, 1.75);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 13);
			$pdf->Cell(0, 0.6, "FUNCIONÁRIOS ". $tit_str, 0, 1, "R");
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, $linha2, 0 , 1, "R");
			
			$pdf->Ln();$pdf->Ln();
			
				if ($_POST["id_departamento"]!="") $str_depto= " and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."' ";
				if ($_POST["id_turno"]!="") $str_depto.= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
				
				//if ($_SESSION["id_usuario"]=="13") {
				//	$str_depto .="and   rh_funcionarios.id_funcionario = '14' ";
				//}
				
				$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_departamentos
											where pessoas.id_pessoa = rh_funcionarios.id_pessoa
											and   pessoas.tipo = 'f'
											and   rh_funcionarios.status_funcionario <> '2'
											and   rh_funcionarios.status_funcionario <> '0'
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
											$str_depto
											and   rh_funcionarios.id_funcionario ". $modo_str ."
											(
											 select distinct(id_funcionario) as id_funcionario from rh_afastamentos_dias
											 where  rh_afastamentos_dias.data >= '". $ponto_inicio ."'
											 and    rh_afastamentos_dias.data <= '". $ponto_fim ."'
											 and  (
													$str_filtra
												  )
											 )
											and   rh_funcionarios.id_funcionario ". $modo_str ."
											(
											 select distinct(id_funcionario) as id_funcionario from rh_afastamentos
											 where  rh_afastamentos.data_inicial >= '". $ponto_inicio ."'
											 and    rh_afastamentos.data_inicial <= '". $ponto_fim ."'
											 and  (
													$str_filtra
												)
											 )
											
											and   rh_funcionarios.id_funcionario NOT IN
											(
											 select id_funcionario from rh_carreiras
											 where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											 and   rh_carreiras.id_acao_carreira = '1'
											 and   rh_carreiras.data > '". $ponto_inicio ."'
											)
											order by rh_departamentos.departamento asc, pessoas.nome_rz asc
											") or die(mysql_error());
				
				/*if ($_SESSION["id_usuario"]==13) {
					echo "select * from  pessoas, rh_funcionarios, rh_carreiras, rh_departamentos
											where pessoas.id_pessoa = rh_funcionarios.id_pessoa
											and   pessoas.tipo = 'f'
											and   rh_funcionarios.status_funcionario <> '2'
											and   rh_funcionarios.status_funcionario <> '0'
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
											$str_depto
											and   rh_funcionarios.id_funcionario ". $modo_str ."
											(
											 select distinct(id_funcionario) as id_funcionario from rh_afastamentos_dias
											 where  rh_afastamentos_dias.data >= '". $ponto_inicio ."'
											 and    rh_afastamentos_dias.data <= '". $ponto_fim ."'
											 and  (
													$str_filtra
												  )
											 )
											and   rh_funcionarios.id_funcionario NOT IN
											(
											 select id_funcionario from rh_carreiras
											 where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											 and   rh_carreiras.id_acao_carreira = '1'
											 and   rh_carreiras.data > '". $ponto_inicio ."'
											)
											order by rh_departamentos.departamento asc, pessoas.nome_rz asc
											";
											
											die();
				}*/
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->SetFillColor(210,210,210);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				
				//$pdf->Cell(0, 0.8, traduz_mes($i) ."/". $_POST["periodo"] ." (". mysql_num_rows($result) ." funcionários)", 'B', 1, 'L', 0);
				//$pdf->Ln();
				
				// ------------- tabela
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->Cell(7, 0.6, "NOME", 1, 0, "L", 1);
				$pdf->Cell(6, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
				$pdf->Cell(4, 0.6, "TURNO", 1, 1, "L", 1);
				
				$pdf->SetFillColor(235,235,235);
				$pdf->SetFont('ARIALNARROW', '', 9);
				
				$j=0;
				while ($rs = mysql_fetch_object($result)) {
					
					$faltas_horas= 0;
					
					if (($_POST["modo"]==1) && ($_POST["faltas"]==1)) {
						$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], 0, 0, $rs->id_funcionario, desformata_data($ponto_inicio), desformata_data($ponto_fim));
						$novo= explode("@", $retorno);
						
						$faltas_horas= $novo[8];
						
					}
					
					if (($_POST["modo"]==2) || (($_POST["modo"]==1) && ($faltas_horas==0)) ) {
						if (($j%2)!=0) $fill=1;
						else $fill= 0;
						
						$pdf->Cell(7, 0.6, $rs->nome_rz, 1, 0, "L", $fill);
						$pdf->Cell(6, 0.6, pega_departamento($rs->id_departamento), 1, 0, "L", $fill);
						$pdf->Cell(4, 0.6, pega_turno($rs->id_turno), 1, 1, "L", $fill);
						
						$j++;
					}
				} //fim while
				
				//@mysql_free_result($result);
				
				$pdf->Ln();$pdf->Ln();
			//}
		}//fim periodicidade
		
		
		
		
		if (($_POST["periodicidade_relatorio"]=="0") || ($_POST["periodicidade_relatorio"]=="2")) {
		
			if ($_POST["periodicidade_relatorio"]=="0") $pdf->AddPage();
			
			
			// ------------- tabela
			
			if ($_POST["id_departamento"]!="") $str_depto= " and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."' ";
			if ($_POST["id_turno"]!="") $str_depto.= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
			
			switch ($_POST["periodicidade"]) {
				case 1: $per_dia1= 26;
						$per_mes1= 12;
						$per_ano1= $_POST["periodo"]-1;
						
						$per_dia2= 25;
						$per_mes2= 3;
						$per_ano2= $_POST["periodo"];
						
						break;
				case 2: $per_dia1= 26;
						$per_mes1= 3;
						$per_ano1= $_POST["periodo"];
						
						$per_dia2= 25;
						$per_mes2= 6;
						$per_ano2= $_POST["periodo"];
						break;
				case 3: $per_dia1= 26;
						$per_mes1= 6;
						$per_ano1= $_POST["periodo"];
						
						$per_dia2= 25;
						$per_mes2= 9;
						$per_ano2= $_POST["periodo"];
						break;
				case 4: $per_dia1= 26;
						$per_mes1= 9;
						$per_ano1= $_POST["periodo"];
						
						$per_dia2= 25;
						$per_mes2= 12;
						$per_ano2= $_POST["periodo"];
						break;
				default:
						$per_dia1= 26;
						$per_mes1= 12;
						$per_ano1= $_POST["periodo"]-1;
						
						$per_dia2= 25;
						$per_mes2= 12;
						$per_ano2= $_POST["periodo"];
							break;
			}
			
			$ponto_inicio= date("Y-m-d", mktime(14, 0, 0, $per_mes1, $per_dia1, $per_ano1));
			$ponto_fim= date("Y-m-d", mktime(14, 0, 0, $per_mes2, $per_dia2, $per_ano2));
		
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_departamentos
										where pessoas.id_pessoa = rh_funcionarios.id_pessoa
										and   pessoas.tipo = 'f'
										and   rh_funcionarios.status_funcionario <> '2'
										and   rh_funcionarios.status_funcionario <> '0'
										and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
										and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
										$str_depto
										and   rh_funcionarios.id_funcionario ". $modo_str ."
										(
										 select distinct(id_funcionario) as id_funcionario from rh_afastamentos_dias
										 where  rh_afastamentos_dias.data >= '". $ponto_inicio ."'
										 and    rh_afastamentos_dias.data <= '". $ponto_fim ."'
										 and  (
												$str_filtra
											)
										 )
										and   rh_funcionarios.id_funcionario ". $modo_str ."
										(
										 select distinct(id_funcionario) as id_funcionario from rh_afastamentos
										 where  rh_afastamentos.data_inicial >= '". $ponto_inicio ."'
										 and    rh_afastamentos.data_inicial <= '". $ponto_fim ."'
										 and  (
												$str_filtra
											)
										 )
										
										and   rh_funcionarios.id_funcionario NOT IN
										(
										 select id_funcionario from rh_carreiras
										 where id_empresa = '". $_SESSION["id_empresa"] ."'
										 and   id_acao_carreira = '1'
										 and   data > '". $ponto_inicio ."'
										)
										order by rh_departamentos.departamento asc, pessoas.nome_rz asc
										") or die(mysql_error());
			
			$pdf->SetXY(16.9, 1.75);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "FUNCIONÁRIOS ". $tit_str ." - ANUAL", 0, 1, "R");
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, pega_periodicidade_anual($_POST["periodicidade"]) . " ". $_POST["periodo"] ."", 0 , 1, "R");
			
			$pdf->Ln();$pdf->Ln();
			
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell(7, 0.6, "NOME", 1, 0, "L", 1);
			$pdf->Cell(6, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
			$pdf->Cell(4, 0.6, "TURNO", 1, 1, "L", 1);
			
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(230,230,230);
			
			$j=0;
			while ($rs = mysql_fetch_object($result)) {
				
				$faltas_horas=0;
				
				if (($_POST["modo"]==1) && ($_POST["faltas"]==1)) {
					$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], 0, 0, $rs->id_funcionario, desformata_data($ponto_inicio), desformata_data($ponto_fim));
					$novo= explode("@", $retorno);
						
					$faltas_horas= $novo[8];
				}
				
				if (($_POST["modo"]==2) || (($_POST["modo"]==1) && ($faltas_horas==0)) ) {
				
					if (($j%2)!=0) $fill=1;
					else $fill= 0;
					
					$pdf->Cell(7, 0.6, $rs->nome_rz, 1, 0, "L", $fill);
					$pdf->Cell(6, 0.6, pega_departamento($rs->id_departamento), 1, 0, "L", $fill);
					$pdf->Cell(4, 0.6, pega_turno($rs->id_turno), 1, 1, "L", $fill);
					
					$j++;
				}
			} //fim while
			
			//@mysql_free_result($result);
		}
	}//fim periodicidade
	
	//faltas por dia
	if ($_POST["tipo_relatorio"]=="fd") {
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "FALTANTES NO DIA", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["data"], 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(7, 0.6, "FUNCIONÁRIO", 1, 0, "L", 1);
		$pdf->Cell(6, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "TURNO", 1, 1, "L", 1);
		
		if ($_POST["id_departamento"]!="") $str= " and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."' ";
		if ($_POST["id_turno"]!="") $str.= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
		
		$result_fun= mysql_query("select * from rh_carreiras, rh_funcionarios, pessoas
									where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
									$str
									and   rh_carreiras.atual = '1'
									and   rh_funcionarios.id_funcionario IN
									(
									 select id_funcionario from rh_escala
									 where data_escala = '". formata_data($_POST["data"]) ."'
									 and   trabalha = '1'
									 )
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_ponto
									 where vale_dia = '". formata_data($_POST["data"]) ."'
									 )
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_ponto_banco
									 where data_he = '". formata_data($_POST["data"]) ."'
									 )
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_afastamentos_dias
									 where data = '". formata_data($_POST["data"]) ."'
									 and   tipo_afastamento <> 's'
									 )
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_carreiras
									 where id_acao_carreira = '1'
									 and   data > '". formata_data($_POST["data"]) ."'
									 )
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_carreiras
									 where id_acao_carreira = '2'
									 and   data < '". formata_data($_POST["data"]) ."'
									 )
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		$j=0;
		while ($rs_fun= mysql_fetch_object($result_fun)) {
			
			if (($j%2)!=0) $fill=1;
			else $fill= 0;
			
			$pdf->Cell(7, 0.6, $rs_fun->nome_rz, 1, 0, "L", $fill);
			$pdf->Cell(6, 0.6, pega_departamento($rs_fun->id_departamento), 1, 0, "L", $fill);
			$pdf->Cell(4, 0.6, pega_turno($rs_fun->id_turno), 1, 1, "L", $fill);
						
			$j++;
		} //fim while
		
		//@mysql_free_result($result_fun);
		
	}
	
	//faltas por dia
	if ($_POST["tipo_relatorio"]=="fdis") {
		
		if (($_POST["data1"]!="") && ($_POST["data2"]!="")) {
			$data1d= explode("/", $_POST["data1"]);
			$data2d= explode("/", $_POST["data2"]);
			
			$ponto_inicio= date("Y-m-d", mktime(14, 0, 0, $data1d[1], $data1d[0], $data1d[2]));
			$ponto_fim= date("Y-m-d", mktime(14, 0, 0, $data2d[1], $data2d[0], $data2d[2]));
			
			$data1_mk= faz_mk_data($ponto_inicio);
			$data2_mk= faz_mk_data($ponto_fim);
			
			$linha2= "ENTRE ". $_POST["data1"] ." E ". $_POST["data2"];
		}
		else {	
			$periodo2= explode('/', $_POST["periodo"]);
			$mes_atual= $periodo2[1] ."-". $periodo2[0] ."-01";
			
			//$proximo_mes= date("Y-m-d", mktime(14, 0, 0, $periodo2[0]+1, 1, $periodo2[1]));
			
			$ponto_inicio= date("Y-m-d", mktime(14, 0, 0, $periodo2[0]-1, 26, $periodo2[1]));
			$ponto_fim= date("Y-m-d", mktime(14, 0, 0, $periodo2[0], 25, $periodo2[1]));
			
			$data1_mk= faz_mk_data($ponto_inicio);
			$data2_mk= faz_mk_data($ponto_fim);
			
			$linha2= traduz_mes($periodo2[0]) ."/". $periodo2[1];
		}
		
		$diferenca= $data2_mk-$data1_mk;
		
		$dias= round(($diferenca/60/60/24));
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "FALTAS DISCRIMINADAS", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $linha2, 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(5, 0.6, "FUNCIONÁRIO", 1, 0, "L", 1);
		$pdf->Cell(12, 0.6, "FALTAS", 1, 1, "L", 1);
		
		if ($_POST["id_departamento"]!="") $str= " and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."' ";
		if ($_POST["id_turno"]!="") $str.= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
		
		$result_fun= mysql_query("select * from rh_carreiras, rh_funcionarios, pessoas
									where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
									$str
									and   rh_carreiras.atual = '1'
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->SetFillColor(230,230,230);
		
		$j=0;
		while ($rs_fun= mysql_fetch_object($result_fun)) {
			
			if (($j%2)!=0) $fill=1;
			else $fill= 0;
			
			$faltas= "";
			$mostrar= 0;
			
			//repetir todos os dias do intervalo
			for ($d= 0; $d<=$dias; $d++) {
				
				$e= $d+1;
				$calculo_data= $data1_mk+(86400*$d);
				
				$dia= date("d/m", $calculo_data);
				$data= date("d/m/Y", $calculo_data);
				$id_dia= date("w", $calculo_data);
				$vale_dia= date("Y-m-d", $calculo_data);
				
				$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], 0, 0, $rs_fun->id_funcionario, desformata_data($vale_dia), desformata_data($vale_dia));
				$novo= explode("@", $retorno);
				
				//echo $retorno ."<br />";
				
				if (($novo[8]>0) || ($novo[9]>0)) {
					$faltas.= $dia;
					$mostrar= 1;
					
					if ($novo[9]>0) $faltas.="* ";
					else $faltas.=" ";
				}
				
			}
			
			if ($mostrar) {
				$pdf->Cell(5, 0.6, $rs_fun->nome_rz, 1, 0, "L", $fill);
				$pdf->Cell(12, 0.6, " ". $faltas, 1, 1, "L", $fill);
				
				$j++;
			}
			
		} //fim while
		
		//@mysql_free_result($result_fun);
		
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(0, 1, "* Falta justificada.", 0, 0, "L", 0);
		
	}
	
	//atrasos por dia
	if ($_POST["tipo_relatorio"]=="ad") {
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ATRASOS NO DIA", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["data"], 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(7, 0.6, "FUNCIONÁRIO", 1, 0, "L", 1);
		$pdf->Cell(6, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "TURNO", 1, 1, "L", 1);
		
		if ($_POST["id_departamento"]!="") $str= " and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."' ";
		if ($_POST["id_turno"]!="") $str.= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
		
		$result_fun= mysql_query("select * from rh_carreiras, rh_funcionarios, pessoas
									where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
									$str
									and   rh_carreiras.atual = '1'
									and   rh_funcionarios.id_funcionario IN
									(
									 select id_funcionario from rh_escala
									 where data_escala = '". formata_data($_POST["data"]) ."'
									 and   trabalha = '1'
									 )
									and   rh_funcionarios.id_funcionario IN
									(
									 select id_funcionario from rh_ponto
									 where vale_dia = '". formata_data($_POST["data"]) ."'
									 )
									/*
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_ponto_banco
									 where data_he = '". formata_data($_POST["data"]) ."'
									 )
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_afastamentos_dias
									 where data = '". formata_data($_POST["data"]) ."'
									 )
									*/
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_carreiras
									 where id_acao_carreira = '1'
									 and   data > '". formata_data($_POST["data"]) ."'
									 )
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_carreiras
									 where id_acao_carreira = '2'
									 and   data < '". formata_data($_POST["data"]) ."'
									 )
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		$j=0;
		while ($rs_fun= mysql_fetch_object($result_fun)) {
			
			if (($j%2)!=0) $fill=1;
			else $fill= 0;
			
			$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], 0, 0, $rs_fun->id_funcionario, $_POST["data"], $_POST["data"]);
			$novo= explode("@", $retorno);
			
			if ($novo[3]>0) {			
				$pdf->Cell(7, 0.6, $rs_fun->nome_rz, 1, 0, "L", $fill);
				$pdf->Cell(6, 0.6, pega_departamento($rs_fun->id_departamento), 1, 0, "L", $fill);
				$pdf->Cell(4, 0.6, pega_turno($rs_fun->id_turno), 1, 1, "L", $fill);
							
				$j++;
			}
		} //fim while
		
		//@mysql_free_result($result_fun);
	}
	
	//horas extras por dia
	if ($_POST["tipo_relatorio"]=="hd") {
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "HORAS EXTRAS POR DIA", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["data"], 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(5.5, 0.6, "FUNCIONÁRIO", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
		$pdf->Cell(3.5, 0.6, "TURNO", 1, 0, "L", 1);
		$pdf->Cell(2, 0.6, "HE DIURNAS", 1, 0, "L", 1);
		$pdf->Cell(2, 0.6, "HE NOTURNAS", 1, 1, "L", 1);
		
		
		if ($_POST["id_departamento"]!="") $str= " and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."' ";
		if ($_POST["id_turno"]!="") $str.= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
		
		$result_fun= mysql_query("select * from rh_carreiras, rh_funcionarios, pessoas
									where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
									$str
									and   rh_carreiras.atual = '1'
									and   rh_funcionarios.id_funcionario IN
									(
									 select id_funcionario from rh_ponto
									 where vale_dia = '". formata_data($_POST["data"]) ."'
									 )
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_carreiras
									 where id_acao_carreira = '1'
									 and   data > '". formata_data($_POST["data"]) ."'
									 )
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_carreiras
									 where id_acao_carreira = '2'
									 and   data < '". formata_data($_POST["data"]) ."'
									 )
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		$geral_hed_aqui= 0;
		$geral_hen_aqui= 0;
		
		$j=0;
		while ($rs_fun= mysql_fetch_object($result_fun)) {
			
			if (($j%2)!=0) $fill=1;
			else $fill= 0;
			
			$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], 0, 0, $rs_fun->id_funcionario, $_POST["data"], $_POST["data"]);
			$novo= explode("@", $retorno);
			
			if (($novo[4]>0) || ($novo[5]>0) || ($novo[6]>0) || ($novo[7]>0)) {			
				
				$hed_aqui= $novo[4]+$novo[5];
				$hen_aqui= $novo[6]+$novo[7];
				
				$geral_hed_aqui+= $hed_aqui;
				$geral_hen_aqui+= $hen_aqui;
				
				$pdf->Cell(5.5, 0.6, $rs_fun->nome_rz, 1, 0, "L", $fill);
				$pdf->Cell(4, 0.6, pega_departamento($rs_fun->id_departamento), 1, 0, "L", $fill);
				$pdf->Cell(3.5, 0.6, pega_turno($rs_fun->id_turno), 1, 0, "L", $fill);
				$pdf->Cell(2, 0.6, calcula_total_horas($hed_aqui), 1, 0, "L", $fill);
				$pdf->Cell(2, 0.6, calcula_total_horas($hen_aqui), 1, 1, "L", $fill);
							
				$j++;
			}
			
		} //fim while
		
		//@mysql_free_result($result_fun);
		
		$pdf->SetFillColor(210,210,210);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(13, 0.6, "", 0, 0);
		$pdf->Cell(2, 0.6, calcula_total_horas($geral_hed_aqui), 1, 0, "L", 1);
		$pdf->Cell(2, 0.6, calcula_total_horas($geral_hen_aqui), 1, 1, "L", 1);
		
	}
	
	//horas extras por mês ou período
	if ($_POST["tipo_relatorio"]=="hm") {
		
		if (($_POST["data1"]!="") && ($_POST["data2"]!="")) {
			$data1d= explode("/", $_POST["data1"]);
			$data2d= explode("/", $_POST["data2"]);
			
			$ponto_inicio= date("Y-m-d", mktime(14, 0, 0, $data1d[1], $data1d[0], $data1d[2]));
			$ponto_fim= date("Y-m-d", mktime(14, 0, 0, $data2d[1], $data2d[0], $data2d[2]));
			
			$linha2= "ENTRE ". $_POST["data1"] ." E ". $_POST["data2"];
		}
		else {	
			$periodo2= explode('/', $_POST["periodo"]);
			$mes_atual= $periodo2[1] ."-". $periodo2[0] ."-01";
			
			//$proximo_mes= date("Y-m-d", mktime(14, 0, 0, $periodo2[0]+1, 1, $periodo2[1]));
			
			$ponto_inicio= date("Y-m-d", mktime(14, 0, 0, $periodo2[0]-1, 26, $periodo2[1]));
			$ponto_fim= date("Y-m-d", mktime(14, 0, 0, $periodo2[0], 25, $periodo2[1]));
			
			$linha2= traduz_mes($periodo2[0]) ."/". $periodo2[1];
		}
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "HORAS EXTRAS POR PERÍODO", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $linha2, 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(5.5, 0.6, "FUNCIONÁRIO", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
		$pdf->Cell(3.5, 0.6, "TURNO", 1, 0, "L", 1);
		$pdf->Cell(2, 0.6, "HE DIURNAS", 1, 0, "L", 1);
		$pdf->Cell(2, 0.6, "HE NOTURNAS", 1, 1, "L", 1);
		
		
		if ($_POST["id_departamento"]!="") $str= " and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."' ";
		if ($_POST["id_turno"]!="") $str.= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
		
		$result_fun= mysql_query("select * from rh_carreiras, rh_funcionarios, pessoas
									where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
									$str
									and   rh_carreiras.atual = '1'
									and   rh_funcionarios.id_funcionario IN
									(
									 select id_funcionario from rh_ponto
									 where vale_dia >= '". $ponto_inicio ."'
									 and   vale_dia <= '". $ponto_fim ."'
									 )
									
									/* and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_carreiras
									 where id_acao_carreira = '1'
									 and   data > '". $ponto_inicio ."'
									 )*/
									 
									and   rh_funcionarios.id_funcionario NOT IN
									(
									 select id_funcionario from rh_carreiras
									 where id_acao_carreira = '2'
									 and   data < '". $ponto_inicio ."'
									 )
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		$geral_hed_aqui= 0;
		$geral_hen_aqui= 0;
		
		$j=0;
		while ($rs_fun= mysql_fetch_object($result_fun)) {
			
			if (($j%2)!=0) $fill=1;
			else $fill= 0;
			
			$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], 0, 0, $rs_fun->id_funcionario, desformata_data($ponto_inicio), desformata_data($ponto_fim));
			$novo= explode("@", $retorno);
			
			if (($novo[4]>0) || ($novo[5]>0) || ($novo[6]>0) || ($novo[7]>0)) {			
				
				$hed_aqui= $novo[4]+$novo[5];
				$hen_aqui= $novo[6]+$novo[7];
				
				$geral_hed_aqui+= $hed_aqui;
				$geral_hen_aqui+= $hen_aqui;
				
				$pdf->Cell(5.5, 0.6, $rs_fun->nome_rz, 1, 0, "L", $fill);
				$pdf->Cell(4, 0.6, pega_departamento($rs_fun->id_departamento), 1, 0, "L", $fill);
				$pdf->Cell(3.5, 0.6, pega_turno($rs_fun->id_turno), 1, 0, "L", $fill);
				$pdf->Cell(2, 0.6, calcula_total_horas($hed_aqui), 1, 0, "L", $fill);
				$pdf->Cell(2, 0.6, calcula_total_horas($hen_aqui), 1, 1, "L", $fill);
							
				$j++;
			}
			
		} //fim while
		
		//@mysql_free_result($result_fun);
		
		$pdf->SetFillColor(210,210,210);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(13, 0.6, "", 0, 0);
		$pdf->Cell(2, 0.6, calcula_total_horas($geral_hed_aqui), 1, 0, "L", 1);
		$pdf->Cell(2, 0.6, calcula_total_horas($geral_hen_aqui), 1, 1, "L", 1);
		
	}
	
	//turnover
	if ($_POST["tipo_relatorio"]=="mmt") {
		
		if ($_POST["id_departamento"]!="") {
			$str_depto= " and   id_departamento = '". $_POST["id_departamento"] ."' ";
			$tit_depto= " - ". pega_departamento($_POST["id_departamento"]) ."";
		}
		if ($_POST["id_turno"]!="") {
			$str_turno= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
			$tit_turno= " (". pega_turno($_POST["id_turno"]) .")";
		}
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "TURNOVER". $tit_depto . $tit_turno, 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["ano"], 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$data_mes_anterior= date("Y-m-d" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		
		$m_anterior= date("m" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		$a_anterior= date("Y" , mktime(14, 0, 0, $periodo3[0]-1, 1, $periodo3[1]));
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(5, 0.6, "MÊS", 1, 0, "L", 1);
		$pdf->Cell(3, 0.6, "ADMISSÕES", 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, "DESLIGAMENTOS", 1, 0, "C", 1);
		$pdf->Cell(4, 0.6, "ATIVOS ANTERIOR", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "TURNOVER", 1, 1, "C", 1);
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		$geral_total_admissoes= 0;
		$geral_total_desligamentos= 0;
		$geral_total_ativos_anterior= 0;
		
		for ($i=1; $i<13; $i++) {
			
			$data_ano_anterior= date("Y-m-d" , mktime(14, 0, 0, 1, 1, $_POST["ano"]-1));
			$data_ano_atual= date("Y-m-d" , mktime(14, 0, 0, 1, 1, $_POST["ano"]));
			
			$data_mes_anterior= date("Y-m-d" , mktime(14, 0, 0, $i-1, 1, $_POST["ano"]));
			$data_mes_atual= date("Y-m-d" , mktime(14, 0, 0, $i, 1, $_POST["ano"]));
			
			$m_anterior= date("m" , mktime(14, 0, 0, $i-1, 1, $_POST["ano"]));
			$a_anterior= date("Y" , mktime(14, 0, 0, $i-1-1, 1, $_POST["ano"]));
			
			if (($i%2)==0) $fill=1;
			else $fill= 0;
			
			$result_admissao= mysql_query("select count(rh_carreiras.id_carreira) as total_admissoes from rh_carreiras
											where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											$str_depto
											$str_turno
											and   rh_carreiras.id_acao_carreira = '1'
											and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["ano"] ."'
											") or die(mysql_error());
			$rs_admissao= mysql_fetch_object($result_admissao);
			
			$result_desligamento= mysql_query("select count(rh_carreiras.id_carreira) as total_desligamentos from rh_carreiras
												where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
												$str_depto
												$str_turno
												and   rh_carreiras.id_acao_carreira = '2'
												and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["ano"] ."'
											") or die(mysql_error());
			$rs_desligamento= mysql_fetch_object($result_desligamento);
			
			$result_ativos_anterior= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras
													where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
													and   rh_carreiras.atual = '1'
													and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
													and   rh_funcionarios.status_funcionario <> '2'
													$str_depto
													$str_turno
													and   rh_funcionarios.id_funcionario NOT IN
													(
													select id_funcionario from rh_carreiras
													where id_acao_carreira = '2'
													and   data < '". $data_mes_atual ."'
													)
													
													and   rh_funcionarios.id_funcionario IN
													(
													select id_funcionario from rh_carreiras
													where id_acao_carreira = '1'
													and   data <= '". $data_mes_atual ."'
													)
													
													") or die(mysql_error());
			
			$linhas_ativos_anterior= mysql_num_rows($result_ativos_anterior);
			
			if ($linhas_ativos_anterior>0) $turnover= ((($rs_admissao->total_admissoes+$rs_desligamento->total_desligamentos)/2)/$linhas_ativos_anterior)*100;
			else $turnover= "0";
			
			$grafico_dado[$i]= $turnover;
			
			$pdf->Cell(5, 0.6, traduz_mes($i), 1, 0, "L", $fill);
			$pdf->Cell(3, 0.6, $rs_admissao->total_admissoes, 1, 0, "C", $fill);
			$pdf->Cell(3, 0.6, $rs_desligamento->total_desligamentos, 1, 0, "C", $fill);
			$pdf->Cell(4, 0.6, $linhas_ativos_anterior, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, fnum($turnover) ."%", 1, 1, "C", $fill);
			
			$geral_total_admissoes+= $rs_admissao->total_admissoes;
			$geral_total_desligamentos+= $rs_desligamento->total_desligamentos;
			$geral_ativos_anterior+= $linhas_ativos_anterior;
			
			//@mysql_free_result($result_admissao);
			//@mysql_free_result($result_desligamento);
			//@mysql_free_result($result_ativos_anterior);
			
			$j++;
		} //fim while
		
		$result_ativos_ano_anterior= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras
												where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
												and   rh_carreiras.atual = '1'
												and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
												and   rh_funcionarios.status_funcionario <> '2'
												$str_depto
												$str_turno
												and   rh_funcionarios.id_funcionario NOT IN
												(
												select id_funcionario from rh_carreiras
												where id_acao_carreira = '2'
												and   data < '". $data_ano_atual ."'
												)
												
												and   rh_funcionarios.id_funcionario IN
												(
												select id_funcionario from rh_carreiras
												where id_acao_carreira = '1'
												and   data <= '". $data_ano_atual ."'
												)
												
												") or die(mysql_error());
		
		$linhas_ativos_ano_anterior= mysql_num_rows($result_ativos_ano_anterior);
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(210,210,210);
		
		if ($linhas_ativos_ano_anterior>0) $geral_turnover= ((($geral_total_admissoes+$geral_total_desligamentos)/2)/$linhas_ativos_ano_anterior)*100;
		else $geral_turnover= "0";
		
		$pdf->Cell(5, 0.6, "", 0, 0, "L");
		$pdf->Cell(3, 0.6, $geral_total_admissoes, 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, $geral_total_desligamentos, 1, 0, "C", 1);
		$pdf->Cell(4, 0.6, $linhas_ativos_ano_anterior, 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, fnum($geral_turnover) ."%", 1, 1, "C", 1);
		
		//@mysql_free_result($result_ativos_ano_anterior);
		
		//------------ gráfico
		
		$GRAFICO_PATH= "includes/pchart/";
		
		// Standard inclusions   
		include($GRAFICO_PATH ."pChart/pData.class");
		include($GRAFICO_PATH ."pChart/pChart.class");
		
		// Dataset definition 
		$DataSet = new pData;
		$DataSet->AddPoint($grafico_dado,"Serie1");
		$DataSet->AddPoint(array("Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"),"Serie2"); 
		$DataSet->AddSerie("Serie1");
		$DataSet->SetAbsciseLabelSerie("Serie2"); 
		$DataSet->SetSerieName("Taxa de turnover","Serie1");
		$DataSet->SetYAxisUnit("%");
		
		// Initialise the graph
		$Test= new pChart(800,450);
		$Test->setColorPalette(0,35,35,142);
		$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrow.ttf",11);
		$Test->setGraphArea(75,50,740,300);
		
		$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
		$Test->drawGrid(4,TRUE,230,230,230,50);
		
		// Draw the line graph
		$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
		$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
		
		// Finish the graph
		$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrowBold.ttf",12);
		$Test->drawTitle(100,30,"TURNOVER". $tit_depto . $tit_turno ." ". $_POST["ano"],50,50,50,585);
		$ar= gera_auth();
		$Test->Render("uploads/grafico_". $ar .".png");
		
		if (file_exists("uploads/grafico_". $ar .".png")) {
			$pdf->Image("uploads/grafico_". $ar .".png", $pdf->GetX()+0.5, $pdf->GetY()+1, 16, 9);
			unlink("uploads/grafico_". $ar .".png");
		}
		
	}//fim turnover
	
	//absenteismo
	if ($_POST["tipo_relatorio"]=="mma") {
		
		if ($_POST["id_departamento"]!="") {
			$str_depto= " and   id_departamento = '". $_POST["id_departamento"] ."' ";
			$tit_depto= " - ". pega_departamento($_POST["id_departamento"]) ."";
			$id_departamento_busca= $_POST["id_departamento"];
		} else $id_departamento_busca= 0;
		if ($_POST["id_turno"]!="") {
			$str_turno= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
			$tit_turno= " (". pega_turno($_POST["id_turno"]) .")";
			$id_turno_busca= $_POST["id_turno"];
		} else $id_turno_busca= 0;
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ABSENTEÍSMO". $tit_depto . $tit_turno, 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["ano"], 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(5, 0.6, "PERÍODO", 1, 0, "L", 1);
		$pdf->Cell(3, 0.6, "FALTAS (D)", 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, "HORAS/DIA", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "Nº FUNC.", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "DIAS TRAB.", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "ABSENTEÍSMO", 1, 1, "C", 1);
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(230,230,230);
		
		$geral_total_faltas= 0;
		$geral_n_func= 0;
		$geral_hht= 0;
		
		//$total_horas_set= 792000;
		
		for ($i=1; $i<13; $i++) {
			
			$j=$i-1;
			
			$data_ano_anterior= date("Y-m-d" , mktime(14, 0, 0, 1, 1, $_POST["ano"]-1));
			$data_ano_atual= date("Y-m-d" , mktime(14, 0, 0, 1, 1, $_POST["ano"]));
			
			$data_mes_anterior= date("Y-m-d" , mktime(14, 0, 0, $i-1, 1, $_POST["ano"]));
			$data_mes_atual= date("Y-m-d" , mktime(14, 0, 0, $i, 1, $_POST["ano"]));
			
			$data1= date("d/m/Y" , mktime(22, 0, 0, $i, 1, $_POST["ano"]));
			$dias_mes= date("t" , mktime(22, 0, 0, $i, 1, $_POST["ano"]));
			
			$data2= date("d/m/Y" , mktime(14, 0, 0, $i, $dias_mes, $_POST["ano"]));
			
			$m_anterior= date("m" , mktime(14, 0, 0, $i-1, 1, $_POST["ano"]));
			$a_anterior= date("Y" , mktime(14, 0, 0, $i-1-1, 1, $_POST["ano"]));
			
			if (($j%2)!=0) $fill=1;
			else $fill= 0;
			
			$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], $id_departamento_busca, $id_turno_busca, 0, $data1, $data2);
			$novo= explode("@", $retorno);
			
			$horas_normais= $novo[1]+$novo[2];
			
			$result_ativos= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras
											where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_funcionarios.status_funcionario <> '2'
											$str_depto
											$str_turno
											and   rh_funcionarios.id_funcionario NOT IN
											(
											select id_funcionario from rh_carreiras
											where id_acao_carreira = '2'
											and   data < '". $data_mes_atual ."'
											)
											and   rh_funcionarios.id_funcionario IN
											(
											select id_funcionario from rh_carreiras
											where id_acao_carreira = '1'
											and   data <= '". $data_mes_atual ."'
											)
											") or die(mysql_error());
			$linhas_ativos= mysql_num_rows($result_ativos);
			
			$result_dias_trabalhados= mysql_query("select distinct(rh_escala.data_escala) as data_escala from rh_escala, rh_carreiras, rh_funcionarios
												  	where rh_carreiras.id_funcionario = rh_escala.id_funcionario
													$str_depto
													$str_turno
													and   rh_carreiras.atual = '1'
													and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
													and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
													and   DATE_FORMAT(rh_escala.data_escala, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["ano"] ."'
													");
			$linhas_dias_trabalhados= mysql_num_rows($result_dias_trabalhados);
			
			$horas_media_semana= pega_media_diaria_trabalho($id_departamento_busca, $id_turno_busca);
			$hht= $linhas_ativos*$horas_media_semana*$linhas_dias_trabalhados;
			
			//$hht= (transforma_hora_em_decimal($horas_normais)*$linhas_ativos*$linhas_dias_trabalhados);
			
			$faltas_aqui= $novo[8]+$novo[9];
			
			if ($hht>0)
				$absenteismo= ($faltas_aqui*$horas_media_semana/$hht)*100;
			
			//echo (transforma_hora_em_decimal($novo[3])) ." | ". transforma_hora_em_decimal($total_horas_set) ." | ". $hht;
			
			$grafico_dado[$i]= $absenteismo;
			
			$pdf->Cell(5, 0.6, traduz_mes($i), 1, 0, "L", $fill);
			$pdf->Cell(3, 0.6, $faltas_aqui, 1, 0, "C", $fill);
			$pdf->Cell(3, 0.6, $horas_media_semana, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, $linhas_ativos, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, $linhas_dias_trabalhados, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, fnum($absenteismo) ."%", 1, 1, "C", $fill);
			
			$geral_total_faltas+= $faltas_aqui;
			$geral_horas_media_semana+= $horas_media_semana;
			$geral_n_func+= $linhas_ativos;
			$geral_hht+= $hht;
			
			//@mysql_free_result($result_ativos);
			//@mysql_free_result($result_dias_trabalhados);
			
			$j++;
		} //fim while
	
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(200,200,200);
		
		$result_dias_trabalhados_geral= mysql_query("select distinct(rh_escala.data_escala) as data_escala from rh_escala, rh_carreiras, rh_funcionarios
													where rh_carreiras.id_funcionario = rh_escala.id_funcionario
													and   rh_carreiras.atual = '1'
													$str_depto
													$str_turno
													and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
													and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
													and   DATE_FORMAT(rh_escala.data_escala, '%Y') = '". $_POST["ano"] ."'
													");
		$linhas_dias_trabalhados_geral= mysql_num_rows($result_dias_trabalhados_geral);
		
		$horas_media_semana_geral= ($geral_horas_media_semana/$j);
		$geral_hht= $geral_n_func*$horas_media_semana*($linhas_dias_trabalhados_geral/$j);
			
		if ($geral_hht>0)
			$absenteismo_geral= ($geral_total_faltas*$horas_media_semana_geral/$geral_hht)*100;
		//$absenteismo= ($novo[8]*$horas_media_semana/$hht)*100;
		
		$pdf->Cell(5, 0.6, "", 0, 0, "L");
		$pdf->Cell(3, 0.6, $geral_total_faltas, 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, $horas_media_semana_geral, 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, $geral_n_func, 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, fnum($linhas_dias_trabalhados_geral/$j), 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, fnum($absenteismo_geral) ."%", 1, 1, "C", 1);
		
		//@mysql_free_result($result_dias_trabalhados_geral);
		
		//------------ gráfico
		
		$GRAFICO_PATH= "includes/pchart/";
		
		// Standard inclusions   
		include($GRAFICO_PATH ."pChart/pData.class");
		include($GRAFICO_PATH ."pChart/pChart.class");
		
		// Dataset definition 
		$DataSet = new pData;
		$DataSet->AddPoint($grafico_dado,"Serie1");
		$DataSet->AddPoint(array("Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"),"Serie2"); 
		$DataSet->AddSerie("Serie1");
		$DataSet->SetAbsciseLabelSerie("Serie2"); 
		$DataSet->SetSerieName("Taxa de absenteísmo","Serie1");
		$DataSet->SetYAxisUnit("%");
		
		// Initialise the graph
		$Test= new pChart(800,450);
		$Test->setColorPalette(0,35,35,142);
		$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrow.ttf",11);
		$Test->setGraphArea(75,50,740,300);
		
		$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
		$Test->drawGrid(4,TRUE,230,230,230,50);
		
		// Draw the line graph
		$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
		$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
		
		// Finish the graph
		$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrowBold.ttf",12);
		$Test->drawTitle(100,30,"ABSENTEÍSMO ". $tit_depto . $tit_turno ." ". $_POST["ano"],50,50,50,585);
		$ar= gera_auth();
		$Test->Render("uploads/grafico_". $ar .".png");
		
		if (file_exists("uploads/grafico_". $ar .".png")) {
			$pdf->Image("uploads/grafico_". $ar .".png", $pdf->GetX()+0.5, $pdf->GetY()+1, 16, 9);
			unlink("uploads/grafico_". $ar .".png");
		}
		
	}
	
	//faltas justificadas/n~ao justificadas
	if ($_POST["tipo_relatorio"]=="mmj") {
		
		if ($_POST["id_departamento"]!="") {
			$str_depto= " and   id_departamento = '". $_POST["id_departamento"] ."' ";
			$tit_depto= " - ". pega_departamento($_POST["id_departamento"]) ."";
			$id_departamento_busca= $_POST["id_departamento"];
		} else $id_departamento_busca= 0;
		if ($_POST["id_turno"]!="") {
			$str_turno= " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
			$tit_turno= " (". pega_turno($_POST["id_turno"]) .")";
			$id_turno_busca= $_POST["id_turno"];
		} else $id_turno_busca= 0;
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "FALTAS". $tit_depto . $tit_turno, 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["ano"], 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(5, 0.6, "PERÍODO", 1, 0, "L", 1);
		$pdf->Cell(2, 0.6, "ATIVOS", 1, 0, "C", 1);
		$pdf->Cell(2, 0.6, "FALTAS", 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, "SUSPENSÕES", 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, "JUSTIFICADAS", 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, "NÃO JUSTIFICADAS", 1, 1, "C", 1);
		
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		$geral_total_faltas= 0;
		$geral_total_suspensoes= 0;
		$geral_total_nj= 0;
		$geral_total_j= 0;
		
		for ($i=1; $i<13; $i++) {
			
			$j=$i-1;
			
			$data_ano_anterior= date("Y-m-d" , mktime(14, 0, 0, 1, 1, $_POST["ano"]-1));
			$data_ano_atual= date("Y-m-d" , mktime(14, 0, 0, 1, 1, $_POST["ano"]));
			
			$data_mes_anterior= date("Y-m-d" , mktime(14, 0, 0, $i-1, 1, $_POST["ano"]));
			$data_mes_atual= date("Y-m-d" , mktime(14, 0, 0, $i, 1, $_POST["ano"]));
			
			$data1= date("d/m/Y" , mktime(22, 0, 0, $i, 1, $_POST["ano"]));
			$dias_mes= date("t" , mktime(22, 0, 0, $i, 1, $_POST["ano"]));
			
			$data2= date("d/m/Y" , mktime(14, 0, 0, $i, $dias_mes, $_POST["ano"]));
			
			$m_anterior= date("m" , mktime(14, 0, 0, $i-1, 1, $_POST["ano"]));
			$a_anterior= date("Y" , mktime(14, 0, 0, $i-1-1, 1, $_POST["ano"]));
			
			if (($j%2)!=0) $fill=1;
			else $fill= 0;
			
			$retorno= pega_dados_rh_consolidados($_SESSION["id_empresa"], $id_departamento_busca, $id_turno_busca, 0, $data1, $data2);
			$novo= explode("@", $retorno);
			
			$result_ativos= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras
											where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_funcionarios.status_funcionario <> '2'
											$str_depto
											$str_turno
											and   rh_funcionarios.id_funcionario NOT IN
											(
											select id_funcionario from rh_carreiras
											where id_acao_carreira = '2'
											and   data < '". $data_mes_atual ."'
											)
											
											and   rh_funcionarios.id_funcionario IN
											(
											select id_funcionario from rh_carreiras
											where id_acao_carreira = '1'
											and   data <= '". $data_mes_atual ."'
											)
											") or die(mysql_error());
			$linhas_ativos= mysql_num_rows($result_ativos);
			
			$total_faltas_aqui= $novo[8]+$novo[9];
			
			$pdf->Cell(5, 0.6, traduz_mes($i), 1, 0, "L", $fill);
			$pdf->Cell(2, 0.6, $linhas_ativos, 1, 0, "C", $fill);
			$pdf->Cell(2, 0.6, $total_faltas_aqui, 1, 0, "C", $fill);
			$pdf->Cell(2.5, 0.6, $novo[10], 1, 0, "C", $fill);
			$pdf->Cell(2.5, 0.6, $novo[9], 1, 0, "C", $fill);
			$pdf->Cell(3, 0.6, $novo[8], 1, 1, "C", $fill);
			
			$grafico_dado1[$i]= $novo[9];
			$grafico_dado2[$i]= $novo[8];
			
			$geral_total_faltas+= $total_faltas_aqui;
			$geral_ativos+= $linhas_ativos;
			$geral_total_suspensoes+= $novo[10];
			$geral_total_j+= $novo[9];
			$geral_total_nj+= $novo[8];
			
			//@mysql_free_result($result_ativos);
			
			$j++;
		} //fim while
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(200,200,200);
		
		$total_geral_faltas= $geral_total_j+$geral_total_nj;
		
		if ($total_geral_faltas>0) $geral_percent_j= ($geral_total_j*100)/$total_geral_faltas;
		else $geral_percent_j= 0;
		
		if ($total_geral_faltas>0) $geral_percent_nj= ($geral_total_nj*100)/$total_geral_faltas;
		else $geral_percent_nj= 0;
		
		$pdf->Cell(5, 0.6, "", 0, 0, "L");
		$pdf->Cell(2, 0.6, "", 0, 0);
		$pdf->Cell(2, 0.6, $geral_total_faltas, 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, $geral_total_suspensoes, 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.6, $geral_total_j ." (". fnum($geral_percent_j) ."%)", 1, 0, "C", 1);
		$pdf->Cell(3, 0.6, $geral_total_nj ." (". fnum($geral_percent_nj) ."%)", 1, 1, "C", 1);
		
		//------------ gráfico
		
		$GRAFICO_PATH= "includes/pchart/";
		
		// Standard inclusions   
		include($GRAFICO_PATH ."pChart/pData.class");
		include($GRAFICO_PATH ."pChart/pChart.class");
		
		// Dataset definition 
		$DataSet = new pData;
		$DataSet->AddPoint($grafico_dado1,"Serie1");
		$DataSet->AddPoint($grafico_dado2,"Serie2");
		$DataSet->AddPoint(array("Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"),"Serie3");
		$DataSet->AddSerie("Serie1");
		$DataSet->AddSerie("Serie2");
		$DataSet->SetAbsciseLabelSerie("Serie3"); 
		$DataSet->SetSerieName("Justificadas","Serie1");
		$DataSet->SetSerieName("Não justificadas","Serie2");
		
		// Initialise the graph
		$Test= new pChart(800,450);
		$Test->setColorPalette(0,35,35,142);
		$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrow.ttf",11);
		$Test->setGraphArea(75,50,740,300);
		
		$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
		$Test->drawGrid(4,TRUE,230,230,230,50);
		
		// Draw the line graph
		$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
		$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
		
		// Finish the graph
		$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrow.ttf",10);
		$Test->drawLegend(620,5,$DataSet->GetDataDescription(),255,255,255);
		$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrowBold.ttf",12);
		$Test->drawTitle(100,30,"FALTAS ". $tit_depto . $tit_turno ." ". $_POST["ano"],50,50,50,585);
		$ar= gera_auth();
		$Test->Render("uploads/grafico_". $ar .".png");
		
		if (file_exists("uploads/grafico_". $ar .".png")) {
			$pdf->Image("uploads/grafico_". $ar .".png", $pdf->GetX()+0.5, $pdf->GetY()+1, 16, 9);
			unlink("uploads/grafico_". $ar .".png");
		}
		
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>
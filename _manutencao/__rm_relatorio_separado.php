<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode_algum("ps", $_SESSION["permissao"])) {

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
	
	if ($_POST["data"]!="") $data= formata_data_hifen($_POST["data"]);
	else $data= date("Y-m-d");
	
	$i=0;
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	if ($_POST["tipo_relatorio"]=="d") {
		
	}
	//mensal
	elseif ($_POST["tipo_relatorio"]=="m") {
		
		$periodo2= explode('/', $_POST["periodo"]);
			
		$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "REQUISIÇÕES DE MANUTENÇÃO", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ." de ". $periodo2[1], 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$periodo_mk= mktime(0, 0, 0, $periodo2[0], 1, $periodo2[1]);
		$total_dias_mes= date("t", $periodo_mk);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(9, 0.6, "SOLICITADA POR", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
		$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
		
		$result_deptos= mysql_query("select distinct(rh_carreiras.id_departamento) as id_departamento from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
										where man_rms.id_usuario = usuarios.id_usuario
										and   man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '1'
										and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
										order by rh_departamentos.departamento asc
										") or die(mysql_error());
		
		$result_total= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
										where man_rms.id_usuario = usuarios.id_usuario
										and   man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '1'
										and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		
		$linhas_total= mysql_num_rows($result_total);
		
		$total_rm= 0;
		
		$i=0;
		
		while ($rs_deptos= mysql_fetch_object($result_deptos)) {
		
			$result_total_depto= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
											where man_rms.id_usuario = usuarios.id_usuario
											and   man_rms.id_rm = man_rms_andamento.id_rm
											and   man_rms_andamento.id_situacao = '1'
											and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
											and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
											and   rh_carreiras.id_departamento = '". $rs_deptos->id_departamento ."'
											") or die(mysql_error());
			
			$linhas_total_depto= mysql_num_rows($result_total_depto);
			
			if (($i%2)==0) $fill=1;
			else $fill= 0;
			
			$pdf->SetFillColor(235,235,235);
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			$percent= ($linhas_total_depto*100)/$linhas_total;
			
			$pdf->Cell(9, 0.5, pega_departamento($rs_deptos->id_departamento), 1, 0, "L", $fill);
			$pdf->Cell(4, 0.5, $linhas_total_depto, 1, 0, "C", $fill);
			$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
			
			$total_rm += $linhas_total_depto;
			
			$i++;
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
		$pdf->Cell(4, 0.5, $total_rm, 1, 0, "C", !$fill);
		$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
	}	
	elseif ($_POST["tipo_relatorio"]=="f") {
		
		$periodo2= explode('/', $_POST["periodo"]);
			
		$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "EXECUÇÕES POR FUNCIONÁRIO", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ." de ". $periodo2[1], 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$periodo_mk= mktime(0, 0, 0, $periodo2[0], 1, $periodo2[1]);
		$total_dias_mes= date("t", $periodo_mk);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(9, 0.6, "FUNCIONÁRIO", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
		$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
		
		$result_fun= mysql_query("select distinct(man_rms_andamento.id_usuario) as id_usuario, pessoas.nome_rz
										from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, pessoas
										where man_rms_andamento.id_usuario = usuarios.id_usuario
										and   man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '5'
										and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   pessoas.id_pessoa = rh_funcionarios.id_pessoa
										and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
										order by pessoas.nome_rz asc
										") or die(mysql_error());
		
		$result_total= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
										where man_rms.id_usuario = usuarios.id_usuario
										and   man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '5'
										and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		
		$linhas_total= mysql_num_rows($result_total);
		
		$total_rm= 0;
		
		$i=0;
		
		while ($rs_fun= mysql_fetch_object($result_fun)) {
		
			$result_total_execucoes= mysql_query("select distinct(man_rms.id_rm)
													from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
													where man_rms_andamento.id_usuario = usuarios.id_usuario
													and   man_rms.id_rm = man_rms_andamento.id_rm
													and   man_rms_andamento.id_situacao = '5'
													and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
													and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
													and   rh_carreiras.atual = '1'
													and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
													and   man_rms_andamento.id_usuario = '". $rs_fun->id_usuario ."'
													") or die(mysql_error());
			
			$linhas_total_execucoes= mysql_num_rows($result_total_execucoes);
			
			if (($i%2)==0) $fill=1;
			else $fill= 0;
			
			$pdf->SetFillColor(235,235,235);
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			$percent= ($linhas_total_execucoes*100)/$linhas_total;
			
			$pdf->Cell(9, 0.5, $rs_fun->nome_rz, 1, 0, "L", $fill);
			$pdf->Cell(4, 0.5, $linhas_total_execucoes, 1, 0, "C", $fill);
			$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
			
			$total_rm += $linhas_total_execucoes;
			
			$i++;
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
		$pdf->Cell(4, 0.5, $total_rm, 1, 0, "C", !$fill);
		$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
		
		
	}
	//abertas x fechadas
	elseif ($_POST["tipo_relatorio"]=="a") {
		
		
		$periodo2= explode('/', $_POST["periodo"]);
			
		$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "REQUISIÇÕES ABERTAS X FINALIZADAS", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ." de ". $periodo2[1], 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$periodo_mk= mktime(0, 0, 0, $periodo2[0], 1, $periodo2[1]);
		$total_dias_mes= date("t", $periodo_mk);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		//$pdf->Cell(9, 0.6, "", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "ABERTAS", 1, 0, "C", 1);
		$pdf->Cell(4, 0.6, "FINALIZADAS", 1, 1, "C", 1);
		
		$result_total_abertas= mysql_query("select distinct(man_rms.id_rm)
												from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
												where man_rms.id_usuario = usuarios.id_usuario
												and   man_rms.id_rm = man_rms_andamento.id_rm
												and   man_rms_andamento.id_situacao = '1'
												and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
												and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
												and   rh_carreiras.atual = '1'
												and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
												") or die(mysql_error());
		$linhas_total_abertas= mysql_num_rows($result_total_abertas);
		
		$result_total_finalizadas= mysql_query("select distinct(man_rms.id_rm)
												from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
												where man_rms.id_usuario = usuarios.id_usuario
												and   man_rms.id_rm = man_rms_andamento.id_rm
												and   man_rms_andamento.id_situacao = '5'
												and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
												and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
												and   rh_carreiras.atual = '1'
												and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
												") or die(mysql_error());
		$linhas_total_finalizadas= mysql_num_rows($result_total_finalizadas);
		
		$fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		//$pdf->Cell(9, 0.5, "", 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_abertas, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_finalizadas, 1, 1, "C", $fill);
		
		
	}
	//por equipamento
	elseif ($_POST["tipo_relatorio"]=="e") {
		
		$periodo2= explode('/', $_POST["periodo"]);
			
		$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "REQUISIÇÕES POR EQUIPAMENTO", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ." de ". $periodo2[1], 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$periodo_mk= mktime(0, 0, 0, $periodo2[0], 1, $periodo2[1]);
		$total_dias_mes= date("t", $periodo_mk);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(9, 0.6, "EQUIPAMENTO", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
		$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
		
		$result_eq= mysql_query("select distinct(man_rms.id_equipamento) as id_equipamento, op_equipamentos.equipamento
										from man_rms, man_rms_andamento, op_equipamentos
										where man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '1'
										and   man_rms.id_equipamento = op_equipamentos.id_equipamento
										and   man_rms.tipo_rm = 'e'
										and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
										and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
										order by op_equipamentos.equipamento asc
										") or die(mysql_error());
		
		$result_total= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
										where man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '1'
										and   man_rms.tipo_rm = 'e'
										and   man_rms.id_equipamento <> '0'
										and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		
		$linhas_total= mysql_num_rows($result_total);
		
		$total_rm= 0;
		
		$i=0;
		
		while ($rs_eq= mysql_fetch_object($result_eq)) {
		
			$result_total_execucoes= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
													where man_rms.id_rm = man_rms_andamento.id_rm
													and   man_rms_andamento.id_situacao = '1'
													and   man_rms.tipo_rm = 'e'
													and   man_rms.id_equipamento <> '0'
													and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
													and   man_rms.id_equipamento = '". $rs_eq->id_equipamento ."'
													") or die(mysql_error()); 
			
			$linhas_total_execucoes= mysql_num_rows($result_total_execucoes);
			
			if (($i%2)==0) $fill=1;
			else $fill= 0;
			
			$pdf->SetFillColor(235,235,235);
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			$percent= ($linhas_total_execucoes*100)/$linhas_total;
			
			$pdf->Cell(9, 0.5, $rs_eq->equipamento, 1, 0, "L", $fill);
			$pdf->Cell(4, 0.5, $linhas_total_execucoes, 1, 0, "C", $fill);
			$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
			
			$total_rm += $linhas_total_execucoes;
			
			$i++;
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
		$pdf->Cell(4, 0.5, $total_rm, 1, 0, "C", !$fill);
		$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
		
		
	}
	//por equipamento
	elseif ($_POST["tipo_relatorio"]=="p") {
		
		$periodo2= explode('/', $_POST["periodo"]);
			
		$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "REQUISIÇÕES POR EQUIPAMENTO", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ." de ". $periodo2[1], 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$periodo_mk= mktime(0, 0, 0, $periodo2[0], 1, $periodo2[1]);
		$total_dias_mes= date("t", $periodo_mk);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		//$pdf->Cell(9, 0.6, "", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "PREDIAL", 1, 0, "C", 1);
		$pdf->Cell(4, 0.6, "EQUIPAMENTO", 1, 1, "C", 1);
		
		$result_total_eq= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
										where man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '1'
										and   man_rms.tipo_rm = 'e'
										and   man_rms.id_equipamento <> '0'
										and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		
		$linhas_total_eq= mysql_num_rows($result_total_eq);
		
		$result_total_pre= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
										where man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '1'
										and   man_rms.tipo_rm = 'p'
										and   man_rms.id_equipamento = '0'
										and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		
		$linhas_total_pre= mysql_num_rows($result_total_pre);
		
		$fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		//$pdf->Cell(9, 0.5, "", 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_pre, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_eq, 1, 1, "C", $fill);
		
		
	}
	
	
	$pdf->AliasNbPages();
	$pdf->Output("rm_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}

?>
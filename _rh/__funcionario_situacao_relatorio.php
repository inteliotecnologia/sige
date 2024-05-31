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
	
	if ($_POST["opcao_relatorio"]=="d") {
		
		if ($_POST["periodo"]!="") {
			$periodo2= explode('/', $_POST["periodo"]);
			
			$data1_mk= mktime(0, 0, 0, $periodo2[0]-1, 26, $periodo2[1]);
			$data2_mk= mktime(0, 0, 0, $periodo2[0], 25, $periodo2[1]);
			
			$data1= date("d/m/Y", $data1_mk);
			$data2= date("d/m/Y", $data2_mk);
		}
		
		$result= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y%m%d') as data_demissao from rh_funcionarios, pessoas, rh_carreiras, rh_departamentos, rh_turnos
											where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.id_acao_carreira = '2'
											and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   rh_carreiras.id_turno = rh_turnos.id_turno
											and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". $_POST["periodo"] ."'
											order by rh_carreiras.data asc, rh_funcionarios.id_funcionario asc
											") or die(mysql_error());
		$linhas= mysql_num_rows($result);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.75, "FUNCIONÁRIOS POR DESLIGAMENTO (". $linhas .")", 0, 1, 'R');
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0, 1, 'R');
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(1, 0.6, "MAT.", 1, 0, 'C', 1);
		$pdf->Cell(7.2, 0.6, "NOME", 1, 0, 'L', 1);
		$pdf->Cell(1.8, 0.6, "ADMISSÃO", 1, 0, 'C', 1);
		$pdf->Cell(1.8, 0.6, "FIM EXP.", 1, 0, 'C', 1);
		$pdf->Cell(1.8, 0.6, "DEMISSÃO", 1, 0, 'C', 1);
		$pdf->Cell(3.2, 0.6, "MOTIVO", 1, 1, 'C', 1);
			
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(240,240,240);
		
		$i=0;
		while ($rs= mysql_fetch_object($result)) {
			$j= $i+1;
			
			$data_admissao= formata_data_hifen(pega_data_admissao($rs->id_funcionario));
			$data_admissao_seca= formata_data(pega_data_admissao($rs->id_funcionario));
			
			//data30= soma_data($rs->data, 29, 0, 0);
			$data90= soma_data($data_admissao_seca, 89, 0, 0);
			
			//echo $rs->data_demissao ." <= ". $data90 ."<br />";
			
			if ($rs->data_demissao<=$data90) $aster=" *";
			else $aster="";
			
			/*$result_atualiza= mysql_query("update rh_funcionarios
											set num_func= '$j'
											where id_empresa = '". $id_empresa ."'
											and   id_funcionario = '". $rs->id_funcionario."'
											");*/
			
			if (($i%2)==0) $fill= 0;
			else $fill= 1;
			
			$pdf->Cell(1, 0.6, $rs->num_func, 1, 0, 'C', $fill);
			$pdf->Cell(7.2, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
			$pdf->Cell(1.8, 0.6, desformata_data($data_admissao), 1, 0, 'C', $fill);
			$pdf->Cell(1.8, 0.6, desformata_data($data90), 1, 0, 'C', $fill);
			$pdf->Cell(1.8, 0.6, desformata_data($rs->data) . $aster, 1, 0, 'C', $fill);
			$pdf->Cell(3.2, 0.6, pega_detalhe_carreira_desligamento($rs->id_detalhe_carreira), 1, 1, 'C', $fill);
						
			$i++;
		}
		
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(0, 1.6, "* Desligado dentro ou no fim do período de experiência.", 0, 0, 'L', 0);
		
	}
	elseif ($_POST["opcao_relatorio"]=="a") {
		if ($_GET["admissao"]=="1") {
			$result= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras, rh_departamentos, rh_turnos
												where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
												and   rh_carreiras.id_acao_carreira = '1'
												and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
												and   rh_funcionarios.status_funcionario <> '2'
												and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
												and   rh_carreiras.id_turno = rh_turnos.id_turno
												". $str ."
												order by rh_carreiras.data asc, rh_funcionarios.id_funcionario asc
												") or die(mysql_error());
			$linhas= mysql_num_rows($result);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.75, "RELATÓRIO GERAL DE FUNCIONÁRIOS POR ADMISSÃO", 0, 1, 'R');
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.6, $linhas ." FUNCIONÁRIOS", 0, 1, 'R');
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(1, 0.6, "MAT.", 1, 0, 'C', 1);
			$pdf->Cell(10, 0.6, "NOME", 1, 0, 'L', 1);
			$pdf->Cell(3, 0.6, "ADMISSÃO", 1, 0, 'C', 1);
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
				
				$pdf->Cell(1, 0.6, $rs->num_func, 1, 0, 'C', $fill);
				$pdf->Cell(10, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
				$pdf->Cell(3, 0.6, pega_data_admissao($rs->id_funcionario), 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.6, strip_tags(ativo_inativo($rs->status_funcionario)), 1, 1, 'C', $fill);
							
				$i++;
			}
		}
		else {
			if ($_POST["status_funcionario"]=="0") {
				
				$result = mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras, rh_departamentos, rh_turnos
													where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
													and   rh_carreiras.atual = '1'
													and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
													and   rh_funcionarios.status_funcionario <> '2'
													and   rh_funcionarios.status_funcionario = '0'
													and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
													and   rh_carreiras.id_turno = rh_turnos.id_turno
													". $str ."
													order by rh_departamentos.departamento asc, rh_turnos.turno asc, pessoas.nome_rz asc
													") or die(mysql_error());
				$linhas= mysql_num_rows($result);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, "RELATÓRIO DE FUNCIONÁRIOS INATIVOS", 0, 1, 'R');
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(0, 0.6, $linhas ." FUNCIONÁRIOS", 0, 1, 'R');
				$pdf->Ln();$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->Cell(1, 0.6, "MAT.", 1, 0, 'C', 1);
				$pdf->Cell(6.75, 0.6, "NOME", 1, 0, 'L', 1);
				$pdf->Cell(4.25, 0.6, "DEPARTAMENTO", 1, 0, 'L', 1);
				$pdf->Cell(2.5, 0.6, "ADMISSÃO", 1, 0, 'C', 1);
				$pdf->Cell(2.5, 0.6, "DEMISSÃO", 1, 1, 'C', 1);
					
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->SetFillColor(240,240,240);
				
				$total_funcionarios_depto=0;
				$total_funcionarios_turno=0;
				$i=0;
				
				while ($rs= mysql_fetch_object($result)) {
					$j= $i+1;
					
					if (($i%2)==0) $fill= 0;
					else $fill= 1;
					
					$id_departamento_aqui[$i]= $rs->id_departamento; 
					$id_turno_aqui[$i]= $rs->id_turno; 
					
					if (($i>0) && ($id_departamento_aqui[$i]!=$id_departamento_aqui[$i-1])) {
						
						if (($i>0) && ($id_turno_aqui[$i]!=$id_turno_aqui[$i-1])) {
							$pdf->Cell(12, 0.6, "", 0, 0, 'L');
							$pdf->Cell(0, 0.6, $total_funcionarios_turno. " funcionário(s)", 0, 1, 'L');
							
							$total_funcionarios_turno=0;
							$pdf->LittleLn();
						}
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
						
						$pdf->Cell(7.75, 0.6, "", 0, 0, 'L');
						$pdf->Cell(0, 0.6, $total_funcionarios_depto. " funcionário(s)", 0, 1, 'L');
						
						$pdf->LittleLn();
						
						$total_funcionarios_depto=0;
					}
					
					$pdf->Cell(1, 0.6, $rs->num_func, 1, 0, 'C', $fill);
					$pdf->Cell(6.75, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
					$pdf->Cell(4.25, 0.6, pega_departamento($rs->id_departamento), 1, 0, 'L', $fill);
					$pdf->Cell(2.5, 0.6, pega_data_admissao($rs->id_funcionario), 1, 0, 'C', $fill);
					$pdf->Cell(2.5, 0.6, pega_data_demissao($rs->id_funcionario), 1, 1, 'C', $fill);
								
					$i++;
					$total_funcionarios_depto++;
					$total_funcionarios_turno++;
				}
				
			}//fim inativos
			elseif ($_POST["status_funcionario"]=="1") {
				$result = mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras, rh_departamentos, rh_turnos
													where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
													and   rh_carreiras.atual = '1'
													and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
													and   rh_funcionarios.status_funcionario <> '2'
													and   ( rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1' )
													and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
													and   rh_carreiras.id_turno = rh_turnos.id_turno
													". $str ."
													order by rh_departamentos.departamento asc, rh_turnos.turno asc, pessoas.nome_rz asc
													") or die(mysql_error());
				$linhas= mysql_num_rows($result);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.75, "RELATÓRIO DE FUNCIONÁRIOS ATIVOS", 0, 1, 'R');
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(0, 0.6, $linhas ." FUNCIONÁRIOS", 0, 1, 'R');
				$pdf->Ln();$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->Cell(1, 0.6, "MAT.", 1, 0, 'C', 1);
				$pdf->Cell(6.75, 0.6, "NOME", 1, 0, 'L', 1);
				$pdf->Cell(4.25, 0.6, "DEPARTAMENTO", 1, 0, 'L', 1);
				$pdf->Cell(2.5, 0.6, "TURNO", 1, 0, 'C', 1);
				$pdf->Cell(2.5, 0.6, "ADMISSÃO", 1, 1, 'C', 1);
					
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->SetFillColor(240,240,240);
				
				$total_funcionarios_depto=0;
				$total_funcionarios_turno=0;
				
				$i=0;
				while ($rs= mysql_fetch_object($result)) {
					$j= $i+1;
					
					if (($i%2)==0) $fill= 0;
					else $fill= 1;
					
					$id_departamento_aqui[$i]= $rs->id_departamento; 
					$id_turno_aqui[$i]= $rs->id_turno; 
					
					if (($i>0) && ($id_departamento_aqui[$i-1]==1) && ($id_turno_aqui[$i]!=$id_turno_aqui[$i-1])) {
						$pdf->Cell(12, 0.6, "", 0, 0, 'L');
						$pdf->Cell(0, 0.6, $total_funcionarios_turno. " funcionário(s)", 0, 1, 'L');
						
						$total_funcionarios_turno=0;
						$pdf->LittleLn();
					}
					
					if (($i>0) && ($id_departamento_aqui[$i]!=$id_departamento_aqui[$i-1])) {
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
						
						$pdf->Cell(7.75, 0.6, "", 0, 0, 'L');
						$pdf->Cell(0, 0.6, $total_funcionarios_depto. " funcionário(s)", 0, 1, 'L');
						
						$pdf->LittleLn();
						
						$total_funcionarios_depto=0;
						
					}
					
					/*$result_afastado= mysql_query("select * from rh_afastamentos, rh_afastamentos_dias
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
					$linhas_afastado= mysql_num_rows($result_afastado);
					*/
					
					if ($rs->afastado==1) {
						$pdf->SetTextColor(255,255,255);
						$pdf->SetFillColor(0,0,0);
						$fill=1;
					}
					else {
						$pdf->SetTextColor(0,0,0);
						$pdf->SetFillColor(235,235,235);
					}
					
					$pdf->SetFont('ARIALNARROW', '', 8);
					
					$pdf->Cell(1, 0.6, $rs->num_func, 1, 0, 'C', $fill);
					$pdf->Cell(6.75, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
					$pdf->Cell(4.25, 0.6, pega_departamento($rs->id_departamento), 1, 0, 'L', $fill);
					$pdf->Cell(2.5, 0.6, pega_turno($rs->id_turno), 1, 0, 'C', $fill);
					$pdf->Cell(2.5, 0.6, pega_data_admissao($rs->id_funcionario), 1, 1, 'C', $fill);
								
					$i++;
					$total_funcionarios_turno++;
					$total_funcionarios_depto++;
				}
			}
			elseif ($_POST["status_funcionario"]=="2") {
				$result = mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras, rh_departamentos, rh_turnos
													where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
													and   rh_carreiras.atual = '1'
													and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
													and   rh_funcionarios.status_funcionario <> '2'
													and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
													and   rh_carreiras.id_turno = rh_turnos.id_turno
													". $str ."
													order by rh_departamentos.departamento asc, rh_turnos.turno asc, pessoas.nome_rz asc
													") or die(mysql_error());
				$linhas= mysql_num_rows($result);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.75, "RELATÓRIO GERAL DE FUNCIONÁRIOS", 0, 1, 'R');
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(0, 0.6, $linhas ." FUNCIONÁRIOS", 0, 1, 'R');
				$pdf->Ln();$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				
				$pdf->Cell(1, 0.6, "MAT.", 1, 0, 'C', 1);
				$pdf->Cell(10, 0.6, "NOME", 1, 0, 'L', 1);
				$pdf->Cell(3, 0.6, "ADMISSÃO", 1, 0, 'C', 1);
				$pdf->Cell(3, 0.6, "SITUAÇÃO", 1, 1, 'C', 1);
					
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->SetFillColor(240,240,240);
				
				$i=0;
				while ($rs= mysql_fetch_object($result)) {
					$j= $i+1;
					
					if (($i%2)==0) $fill= 0;
					else $fill= 1;
					
					$pdf->Cell(1, 0.6, $rs->num_func, 1, 0, 'C', $fill);
					$pdf->Cell(10, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
					$pdf->Cell(3, 0.6, pega_data_admissao($rs->id_funcionario), 1, 0, 'C', $fill);
					$pdf->Cell(3, 0.6, strip_tags(ativo_inativo($rs->status_funcionario)), 1, 1, 'C', $fill);
								
					$i++;
				}
			}
			//procurando ativos dentro de um mês
			elseif ($_POST["status_funcionario"]=="3") {
				
				$periodo2= explode("/", $_POST["periodo"]);
				$proximo_mes= $periodo2[1] ."-". $periodo2[0] ."-01";
				
				$result = mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras
													where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
													and   rh_carreiras.atual = '1'
													and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
													and   rh_funcionarios.status_funcionario <> '2'
													
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
													". $str ."
													order by ". $_POST["ordenacao"] ." asc
													") or die(mysql_error());
																
				$linhas= mysql_num_rows($result);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.75, "RELATÓRIO DE FUNCIONÁRIOS ATIVOS EM ". strtoupper(traduz_mes($periodo2[0])) . "/". $periodo2[1], 0, 1, 'R');
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(0, 0.6, $linhas ." FUNCIONÁRIOS", 0, 1, 'R');
				$pdf->Ln();$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				
				$pdf->Cell(1, 0.6, "MAT.", 1, 0, 'C', 1);
				$pdf->Cell(6.75, 0.6, "NOME", 1, 0, 'L', 1);
				$pdf->Cell(4.25, 0.6, "DEPARTAMENTO", 1, 0, 'L', 1);
				$pdf->Cell(2.5, 0.6, "TURNO", 1, 0, 'C', 1);
				$pdf->Cell(2.5, 0.6, "ADMISSÃO", 1, 1, 'C', 1);
					
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->SetFillColor(240,240,240);
				
				$i=0;
				while ($rs= mysql_fetch_object($result)) {
					
					$j= $i+1;
					
					if (($i%2)==0) $fill= 0;
					else $fill= 1;
					
					$pdf->Cell(1, 0.6, $rs->num_func, 1, 0, 'C', $fill);
					$pdf->Cell(6.75, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
					$pdf->Cell(4.25, 0.6, pega_departamento($rs->id_departamento), 1, 0, 'L', $fill);
					$pdf->Cell(2.5, 0.6, pega_turno($rs->id_turno), 1, 0, 'C', $fill);
					$pdf->Cell(2.5, 0.6, pega_data_admissao($rs->id_funcionario), 1, 1, 'C', $fill);
								
					$i++;
				}
			}
		}
	}
	elseif ($_POST["opcao_relatorio"]=="m") {
		
		if ($_POST["situacao_atual"]=="2") {
			$str_add= " and   rh_funcionarios.status_funcionario = '1' ";
			$tit_add= " - ATIVOS ATUALMENTE";
		}
		else $str_add= "";
		
		$result= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y-%m-%d') as data_admissao from rh_funcionarios, pessoas, rh_carreiras, rh_departamentos, rh_turnos
											where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.id_acao_carreira = '1'
											and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   rh_carreiras.id_turno = rh_turnos.id_turno
											and   DATE_FORMAT(rh_carreiras.data, '%Y') = '". $_POST["periodo"] ."'
											$str_add
											order by data_admissao asc, rh_funcionarios.id_funcionario asc
											") or die(mysql_error());
		$linhas= mysql_num_rows($result);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.75, "FUNCIONÁRIOS ADMITIDOS". $tit_add ." (". $linhas .")", 0, 1, 'R');
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, $_POST["periodo"], 0, 1, 'R');
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(1, 0.6, "MAT.", 1, 0, 'C', 1);
		$pdf->Cell(5.8, 0.6, "NOME", 1, 0, 'L', 1);
		$pdf->Cell(3.5, 0.6, "DEPTO", 1, 0, 'L', 1);
		$pdf->Cell(3, 0.6, "TURNO", 1, 0, 'L', 1);
		$pdf->Cell(1.8, 0.6, "ADMISSÃO", 1, 0, 'C', 1);
		$pdf->Cell(1.8, 0.6, "DEMISSÃO", 1, 1, 'C', 1);
			
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(240,240,240);
		
		$i=0;
		while ($rs= mysql_fetch_object($result)) {
			$j= $i+1;
			
			$data_demissao= pega_data_demissao($rs->id_funcionario);
			
			if (($i%2)==0) $fill= 0;
			else $fill= 1;
			
			$pdf->Cell(1, 0.6, $rs->num_func, 1, 0, 'C', $fill);
			$pdf->Cell(5.8, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
			$pdf->Cell(3.5, 0.6, $rs->departamento, 1, 0, 'L', $fill);
			$pdf->Cell(3, 0.6, $rs->turno, 1, 0, 'L', $fill);
			$pdf->Cell(1.8, 0.6, desformata_data($rs->data_admissao), 1, 0, 'C', $fill);
			$pdf->Cell(1.8, 0.6, ($data_demissao), 1, 1, 'C', $fill);
						
			$i++;
		}
	}
	elseif ($_POST["opcao_relatorio"]=="o") {
		
		/*$result= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y-%m-%d') as data_admissao from rh_funcionarios, pessoas, rh_carreiras, rh_departamentos, rh_turnos
											where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.id_acao_carreira = '1'
											and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   rh_carreiras.id_turno = rh_turnos.id_turno
											and   DATE_FORMAT(rh_carreiras.data, '%Y') = '". $_POST["periodo"] ."'
											$str_add
											order by data_admissao asc, rh_funcionarios.id_funcionario asc
											") or die(mysql_error());
		$linhas= mysql_num_rows($result);
		*/
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.75, "FUNCIONÁRIOS DEMITIDOS", 0, 1, 'R');
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, $_POST["periodo"], 0, 1, 'R');
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(4, 0.6, "MOTIVO", 1, 0, 'L', 1);
		
		for ($i=1; $i<13; $i++)
			$pdf->Cell(1, 0.6, substr(traduz_mes($i), 0, 3), 1, 0, 'C', 1);
		
		$pdf->Cell(1, 0.6, "Total", 1, 1, 'C', 1);
			
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(240,240,240);
		
		for ($i=1; $i<5; $i++) {
			
			if (($i%2)!=0) $fill= 0;
			else $fill= 1;
			
			switch($i) {
				case 0:
					$motivo= "Não especificado";
					break;
				case 1:
					$motivo= "Demitidos";
					break;
				case 2:
					$motivo= "Pedidos de demissão";
					break;
				case 3:
					$motivo= "Demitidos (experiência)";
					break;
				case 4:
					$motivo= "Pedidos de demissão (experiência)";
					break;
				case 5:
					$motivo= "Total";
					break;
			}
			
			$pdf->Cell(4, 0.6, $motivo, 1, 0, 'L', $fill);
			
			for ($j=1; $j<13; $j++) {
				
				switch($i) {
					case 0:
						$result_demitidos= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y-%m-%d') as data_admissao from rh_funcionarios, pessoas, rh_carreiras
															where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
															and   rh_carreiras.id_acao_carreira = '2'
															and   (rh_carreiras.id_detalhe_carreira = '0' or rh_carreiras.id_detalhe_carreira is NULL)
															and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
															and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															") or die(mysql_error());
						$linhas_demitidos= mysql_num_rows($result_demitidos);
						
						$valor= $linhas_demitidos;
						
						$total[$i]+= $valor;
						$total_mes[$j]+= $valor;
						
						break;
					case 1:
						$result_demitidos= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y-%m-%d') as data_admissao from rh_funcionarios, pessoas, rh_carreiras
															where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
															and   rh_carreiras.id_acao_carreira = '2'
															and   rh_carreiras.id_detalhe_carreira = '1'
															and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
															and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															") or die(mysql_error());
						$linhas_demitidos= mysql_num_rows($result_demitidos);
						
						$valor= $linhas_demitidos;
						
						$total[$i]+= $valor;
						$total_mes[$j]+= $valor;
						
						break;
					case 2:
						$result_demitidos= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y-%m-%d') as data_admissao from rh_funcionarios, pessoas, rh_carreiras
															where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
															and   rh_carreiras.id_acao_carreira = '2'
															and   rh_carreiras.id_detalhe_carreira = '2'
															and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
															and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															") or die(mysql_error());
						$linhas_demitidos= mysql_num_rows($result_demitidos);
						
						$valor= $linhas_demitidos;
						$total[$i]+= $valor;
						
						break;
					case 3:
						
						$result_demitidos= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y-%m-%d') as data_demissao from rh_funcionarios, pessoas, rh_carreiras
															where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
															and   rh_carreiras.id_acao_carreira = '2'
															and   rh_carreiras.id_detalhe_carreira = '1'
															and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
															and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															") or die(mysql_error());
						
						$total_funcionarios=0;
						
						while ($rs_demitidos= mysql_fetch_object($result_demitidos)) {
							$data_admissao= faz_mk_data(formata_data_hifen(pega_data_admissao($rs_demitidos->id_funcionario)));
							
							$data_limite_admissao= faz_mk_data(soma_data($rs_demitidos->data_demissao, -90, 0, 0));
							
							if ($data_admissao>=$data_limite_admissao) $total_funcionarios++;
						}
						
						$valor= $total_funcionarios;
						
						$total[$i]+= $valor;
						
						break;
					case 4:
						
						$result_demitidos= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%Y-%m-%d') as data_demissao from rh_funcionarios, pessoas, rh_carreiras
															where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
															and   rh_carreiras.id_acao_carreira = '2'
															and   rh_carreiras.id_detalhe_carreira = '2'
															and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
															and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															") or die(mysql_error());
						
						$total_funcionarios=0;
						
						while ($rs_demitidos= mysql_fetch_object($result_demitidos)) {
							$data_admissao= faz_mk_data(formata_data_hifen(pega_data_admissao($rs_demitidos->id_funcionario)));
							
							$data_limite_admissao= faz_mk_data(soma_data($rs_demitidos->data_demissao, -90, 0, 0));
							
							if ($data_admissao>=$data_limite_admissao) $total_funcionarios++;
						}
						
						$valor= $total_funcionarios;
						
						$total[$i]+= $valor;
						
						break;
					case 5:
						
						$valor= $total_mes[$j];
						
						break;
				}
				
				
				
				$pdf->Cell(1, 0.6, $valor, 1, 0, 'C', $fill);
			}
			
			$pdf->Cell(1, 0.6, $total[$i], 1, 1, 'C', $fill);
		}
	}

	
	$pdf->Ln();
	
	$pdf->AliasNbPages(); 
	$pdf->Output("funcionario_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>
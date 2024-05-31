<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode_algum("rhw", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem_rh.php");
	
	$periodo= explode('/', $_POST["periodo"]);
		
	$data1_mk= mktime(22, 0, 0, $periodo[0], 1, $periodo[1]);
	
	$dias_mes= date("t", $data1_mk);
	$data2_mk= mktime(14, 0, 0, $periodo[0], $dias_mes, $periodo[1]);
	
	$data1= date("Y-m-d", $data1_mk);
	$data2= date("Y-m-d", $data2_mk);
	
	$data1f= desformata_data($data1);
	$data2f= desformata_data($data2);
	
	$dataq= explode("/", $data1f);
	$mes_extenso= traduz_mes($dataq[1]);
	
	$calculo_data_aqui= (soma_data($data1, 0, 1, 0));
	$calculo_data_aqui_mk= faz_mk_data($calculo_data_aqui);
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1.5, 1.5, 1.5);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(200,200,200);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	if ($_POST["id_turno"]=="") {
		$result_tur= mysql_query("select * from  rh_departamentos
								where 1=1
								and   rh_departamentos.id_departamento =  '". $_POST["id_departamento"] ."'
								order by rh_departamentos.departamento asc
								") or die(mysql_error());
	}
	else {
		$result_tur= mysql_query("select * from  rh_departamentos, rh_turnos
								where 1=1
								and   rh_departamentos.id_departamento = rh_turnos.id_departamento
								and   rh_turnos.id_turno = '". $_POST["id_turno"] ."'
								order by rh_departamentos.departamento asc
								") or die(mysql_error());
		$turno= " - ". pega_turno($_POST["id_turno"]);
	}
	
	$i=0;
	while ($rs_tur= mysql_fetch_object($result_tur)) {		
		
		if (($_POST["id_departamento"]==5) || ($_POST["id_departamento"]==6) || ($_POST["id_departamento"]==23)) {
			$str = " and   (rh_carreiras.id_departamento =  '5'
							or rh_carreiras.id_departamento =  '6'
							or rh_carreiras.id_departamento =  '23') ";
			$titulo_pg= "TRANSPORTE";
		}
		else {
			if ($_POST["id_turno"]=="") $str_depois= " and   rh_carreiras.id_departamento = '". $rs_tur->id_departamento ."' ";
			else $str_depois= " and   rh_carreiras.id_turno = '". $rs_tur->id_turno ."' ";
		
			//$str = " and   rh_carreiras.id_departamento = '". $rs_tur->id_departamento ."' ";
			$titulo_pg= $rs_tur->departamento . $turno;
		}
		
		$pdf->AddPage();
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		
		if ($rs_tur->turno!="") $str_turno= " - ". $rs_tur->turno;
		else $str_turno= "";
		
		$pdf->Cell(0, 0.6, "ESCALA DE FUNCIONÁRIOS (". $titulo_pg .")", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, strtoupper($mes_extenso ."/". $dataq[2]), 0 , 1, "R");
		
		$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(5.5, 1.4, "FUNCIONÁRIOS", 1 , 0, "L", 1);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$diferenca_tit= date("d", $data2_mk-$data1_mk);
		$total_dias_mes= date("t", $data1_mk);
		
		$largura= 21/$total_dias_mes;
		
		//repetir todos os dias do intervalo
		for ($t=0; $t<=$diferenca_tit; $t++) {
			$calculo_data_tit= $data1_mk+(86400*$t);
			
			$dia_tit= date("d", $calculo_data_tit);
			$dia_semana_tit= date("w", $calculo_data_tit);
			$vale_dia_tit= date("Y-m-d", $calculo_data_tit);
			
			$pdf->Cell($largura, 0.7, $dia_tit, 1 , 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->SetXY(7, 4.25);
		
		//repetir todos os dias do intervalo
		for ($t=0; $t<=$diferenca_tit; $t++) {
			$calculo_data_tit= $data1_mk+(86400*$t);
			
			$dia_tit= date("d", $calculo_data_tit);
			$dia_semana_tit= date("w", $calculo_data_tit);
			$vale_dia_tit= date("Y-m-d", $calculo_data_tit);
			
			$pdf->Cell($largura, 0.7, traduz_dia_resumido($dia_semana_tit), 1 , 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->SetXY(1.5, 4.9);
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		if ($_POST["id_turno"]!="") $str .= " and   rh_carreiras.id_turno =  '". $_POST["id_turno"] ."'";
		else $str .= "";
		
		$periodo2= explode("/", $_POST["periodo"]);
		$mes_atual= $periodo2[1] ."-". $periodo2[0] ."-01";
		
		$proximo_mes= date("Y-m-d", mktime(14, 0, 0, $periodo2[0]+1, 1, $periodo2[1]));
		
		//$mes_atual= $proximo_mes;
		
		$j=0;
		
		if ($_POST["modo"]==2) {
		
			$result_fun = mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras
										where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										/* and   rh_carreiras.atual = '1' */
										and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
										and   rh_funcionarios.status_funcionario <> '2'
										
										and   rh_funcionarios.id_funcionario NOT IN
										(
										select id_funcionario from rh_carreiras
										where id_acao_carreira = '2'
										and   data < '". $mes_atual ."'
										)
										
										and   rh_funcionarios.id_funcionario IN
										(
										select id_funcionario from rh_carreiras
										where id_acao_carreira = '1'
										and   data <= '". $proximo_mes ."'
										)
										
										$str_depois
										
										$str
										
										and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
										order by pessoas.nome_rz asc
										
										") or die(mysql_error());
										
		}
		else {
			$result_fun= mysql_query("select *
										from  pessoas, rh_funcionarios, rh_carreiras
										where pessoas.id_pessoa = rh_funcionarios.id_pessoa
										and   pessoas.tipo = 'f'
										and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_carreiras.atual = '1'
										
										$str_depois
										
										$str
										
										and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_funcionarios.status_funcionario <> '0'
										and   rh_funcionarios.status_funcionario <> '2'
										order by pessoas.nome_rz asc
										") or die(mysql_error());
		}
		
		while ($rs_fun= mysql_fetch_object($result_fun)) {
			
			if ($_POST["modo"]==2) {
							
				$result_teste_pre= mysql_query("select * from rh_carreiras
												where id_funcionario = '". $rs_fun->id_funcionario ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												order by data desc
												");
				$linhas_teste_pre= mysql_num_rows($result_teste_pre);
				
				$k=1;
				while ($rs_teste_pre= mysql_fetch_object($result_teste_pre)) {
					if (($rs_teste_pre->data<=$mes_atual) || ($linhas_teste_pre==$k)) {
						$id_carreira_vale= $rs_teste_pre->id_carreira;
						
						break;
					}
					$k++;
					//else
				}
				
				/*$result_teste= mysql_query("select * from rh_carreiras
											where id_funcionario = '". $rs_fun->id_funcionario ."'
											and   id_carreira = '". $rs_fun->id_carreira ."'
											
											");
				
				$rs_teste= mysql_fetch_object($result_teste);
				*/
				
				
			}
			
			if (($_POST["modo"]==1) || (($_POST["modo"]==2) && ($rs_fun->id_carreira==$id_carreira_vale)) ) {
			
				$data_admissao= formata_data_hifen(pega_data_admissao($rs_fun->id_funcionario));
				$data_admissao_mk= faz_mk_data($data_admissao);
				
				//01-02-2009
				
				/*
				if ($calculo_data_aqui_mk>=$data_admissao_mk) {
					echo "<strong>". $rs_fun->nome_rz .") ". $calculo_data_aqui ." - ";
					echo $data_admissao ." </strong><br /><br />";
				}
				else {
					echo $rs_fun->nome_rz .") ". $calculo_data_aqui ." - ";
					echo $data_admissao ." <br /><br />";
				} */
				
				//se a data atual for maior ou igual a data de admissao === ELE JÁ É FUNCIONÁRIO DA EMPRESA
				if ($calculo_data_aqui_mk>=$data_admissao_mk) {
				//if (1==1) {
					if (($j%2)==0) {
						$fill=1;
						$pdf->SetFillColor(230,230,230);
					}
					else $fill= 0;
					
					$linhas_afastamento= array();
					
					//repetir todos os dias do intervalo
					for ($d=0; $d<=$diferenca; $d++) {
						$calculo_data= $data1_mk+(86400*$d);
						$dia_tit= date("d", $calculo_data);
						$dia_semana= date("w", $calculo_data);
						$vale_dia= date("Y-m-d", $calculo_data);
						$mes= date("m", $calculo_data);
						
						$result_afa= mysql_query("select * from rh_afastamentos_dias
													where id_funcionario = '". $rs_fun->id_funcionario ."'
													and   data = '". $vale_dia ."'
													") or die(mysql_error());
						$linhas_afastamento[$d]= mysql_num_rows($result_afa);
						
					}
					
					$result_afa_periodo= mysql_query("select * from rh_afastamentos_dias
													where id_funcionario = '". $rs_fun->id_funcionario ."'
													and   DATE_FORMAT(data, '%m/%Y') = '". $_POST["periodo"] ."'
													") or die(mysql_error());
					$linhas_afastamento_periodo= mysql_num_rows($result_afa_periodo);
					
					if ($linhas_afastamento_periodo>0) {
						$mostrar_esse= false;
						
						//repetir todos os dias do intervalo
						for ($d=0; $d<=$diferenca; $d++) {
							if ($linhas_afastamento[$d]==0) {
								$mostrar_esse= true;
								break;
							}
						}
					}
					else {
						$mostrar_esse= true;
					}
					
					//if (1==1) {
						$pdf->Cell(5.5, 0.5, $rs_fun->nome_rz, 1 , 0, "L", $fill);
						
						$diferenca= date("d", $data2_mk-$data1_mk);
									
						//repetir todos os dias do intervalo
						for ($d=0; $d<=$diferenca; $d++) {
							$calculo_data= $data1_mk+(86400*$d);
							$dia_tit= date("d", $calculo_data);
							$dia_semana= date("w", $calculo_data);
							$vale_dia= date("Y-m-d", $calculo_data);
							$mes= date("m", $calculo_data);
							
							$result_esc= mysql_query("select * from rh_escala
														where id_funcionario = '". $rs_fun->id_funcionario ."'
														and   data_escala = '". $vale_dia ."'
														") or die(mysql_error());
							$rs_esc= mysql_fetch_object($result_esc);
							
							$result_afa= mysql_query("select * from rh_afastamentos_dias
														where id_funcionario = '". $rs_fun->id_funcionario ."'
														and   data = '". $vale_dia ."'
														") or die(mysql_error());
							$linhas_afastamento= mysql_num_rows($result_afa);
							
							if ($linhas_afastamento>0) {
								$rs_afa= mysql_fetch_object($result_afa);
								
								$result_af_dia= mysql_query("select *, DATE_FORMAT(data_emissao, '%d/%m/%Y') as data_emissao2 from rh_afastamentos
															where id_afastamento = '". $rs_afa->id_afastamento ."'
															") or die(mysql_error());
								$rs_af_dia= mysql_fetch_object($result_af_dia);
								
								$afastamento= "<strong>". pega_tipo_afastamento($rs_af_dia->tipo_afastamento) ."</strong><br />";
								$afastamento .= "<strong>Data de emissão:</strong> ". $rs_af_dia->data_emissao2 ."<br />";
								$afastamento .= "<strong>Dias:</strong> ". $rs_af_dia->qtde_dias ."<br />";
								
								$trabalha= "-";
							}
							else {
								if ($rs_esc->trabalha==1) $trabalha= "T";
								else $trabalha= "F";
							}
							
							if ($dia_tit==$total_dias_mes) $flutua=1;
							else $flutua= 0;
							
							$pdf->Cell($largura, 0.5, $trabalha, 1 , $flutua, "C", $fill);
							
							$i++;
						}
						$j++;
					//}//fim mostrar_esse
				}//fim admissao
			}//fim passa
		}
		
	}//fim while turnos
	
	$pdf->AliasNbPages(); 
	$pdf->Output("escala_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>
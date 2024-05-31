<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pl", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem.php");
	
	if ($_POST["id_cliente"]!="") $str .= " and   pessoas.id_empresa =  '". $_POST["id_cliente"] ."'";
	
	/*$periodo= explode("/", $_POST["periodo"]);
	$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
	$total_dias_mes= date("t", $periodo_mk);
	*/
	
	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= $_POST["data1"];
		$data2= $_POST["data2"];
	}
	else {
		if ( ($_GET["data1"]!="") && ($_GET["data2"]!="") ) {
			$data1= $_GET["data1"];
			$data2= $_GET["data2"];
		}
	}
	
	if ( ($data1!="") && ($data2!="") ) {
		$data1f= $data1; $data1= formata_data_hifen($data1);
		$data2f= $data2; $data2= formata_data_hifen($data2);
		
		$data1= soma_data($data1, -1, 0, 0);
		$data2= soma_data($data2, -1, 0, 0);
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
		
		//$primeiro_dia_mes_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		$primeiro_dia_mes_mk= $data1_mk;
		
		$id_dia_primeiro_dia= date("w", $primeiro_dia_mes_mk);
		
		$i_inicio= 1;
		
		// 10/10/2010
		
		$primeiro_dia_periodo_mk= $primeiro_dia_mes_mk;
		
		//echo substr($data1f, 6, 4);//date("d/m/Y", $primeiro_dia_periodo_mk); die();
		
		$ultimo_dia_periodo_mk= $data2_mk+(86400*2);
	}
	else {
		$periodo= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(20, 0, 0, $periodo[0], 1, $periodo[1]);
		
		$total_dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo[0], $total_dias_mes, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		//$primeiro_dia_mes_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		$primeiro_dia_mes_mk= $data1_mk;
		
		$id_dia_primeiro_dia= date("w", $primeiro_dia_mes_mk);
		
		$i_inicio= 1;
		
		//echo $id_dia_primeiro_dia; die();
		
		switch ($id_dia_primeiro_dia) {
			case 1: $i_inicio=0; break;
			case 2: $i_inicio=-1; break;
			case 3: $i_inicio=-2; break;
			case 4: $i_inicio=-3; break;
			case 5: $i_inicio=-4; break;
			case 6: $i_inicio=-5; break;
			case 0: $i_inicio=-6; break;
		}
		
		// 10/10/2010
		
		$primeiro_dia_periodo_mk= mktime(0, 0, 0, substr($data1f, 3, 2), $i_inicio, substr($data1f, 6, 4));
		
		//echo substr($data1f, 6, 4);//date("d/m/Y", $primeiro_dia_periodo_mk); die();
		
		$ultimo_dia_periodo_mk= mktime(0, 0, 0, substr($data2f, 3, 2), substr($data2f, 0, 2), substr($data2f, 6, 4));
	}
	
	$total_dias_considerados=0;
	$total_dias_considerados_semana=0;
	$total_dias_considerados_plantao=0;
	
	$total_dias_considerados_soh_semana=0;
	$total_dias_considerados_soh_plantao=0;
	
	$vale_dia_inicio= date("Y-m-d", $primeiro_dia_periodo_mk);
	$vale_dia_fim= date("Y-m-d", $ultimo_dia_periodo_mk);
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1, 1.5, 1);
	$pdf->SetAutoPageBreak(true, 2.5);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.25);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "PRODUÇÃO DA ÁREA SUJA", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, $data1f ." à ". $data2f, 0 , 1, 'R');
	
	$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
	$pdf->Cell(2, 0.5, "", 0, 0, "C");
	
	$total_remessas_dia=4;
	
	$largura=22.5/$total_remessas_dia;
	
	$largura1= $largura*0.45;
	$largura2= $largura*0.25;
	$largura3= $largura*0.30;
	
	for ($h=1; $h<=$total_remessas_dia; $h++) {
		$pdf->Cell($largura, 0.5, "REMESSA ". $h, 1, 0, "C", 1);
	}
	
	$pdf->Cell(3, 0.5, "TOTAL", 1, 1, "C", 1);
	
	//------
	
	$pdf->Cell(2, 0.5, "DATA", 1, 0, "C", 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 6);
	
	for ($r=1; $r<=$total_remessas_dia; $r++) {
		
		$pdf->Cell($largura1/2, 0.5, "chegada", 1, 0, "C", 1);
		$pdf->Cell($largura1/2, 0.5, "duração", 1, 0, "C", 1);
		$pdf->Cell($largura2, 0.5, "peso (kg)", 1, 0, "C", 1);
		$pdf->Cell($largura3, 0.5, "média ind", 1, 0, "C", 1);
	}
	
	$pdf->Cell(1.5, 0.5, "total (kg)", 1, 0, "C", 1);
	$pdf->Cell(1.5, 0.5, "média ind.", 1, 1, "C", 1);
	
	$pdf->SetFillColor(240, 240, 240);
	
	// ---------------------------------------------------------------------------------------------------------------
	
	/*
	$result_apaga= mysql_query("DROP TABLE IF EXISTS rh_ponto_virtual ") or die(mysql_error());
	
	$result_tt1= mysql_query("CREATE TABLE rh_ponto_virtual
							 ( id_horario int, id_funcionario int, vale_dia date, tipo int, tipo_aux int, data_hora_batida timestamp NULL )
							 TYPE=MyISAM;
							 ") or die(mysql_error());
	*/
	
	if ($_POST["denovo"]=="1") {
		$result_copia_del= mysql_query("delete from rh_ponto_virtual
										where rh_ponto_virtual.vale_dia >= '". $vale_dia_inicio ."'
										and   rh_ponto_virtual.vale_dia <= '". $vale_dia_fim ."'
										and   rh_ponto_virtual.id_departamento= '2'
										") or die(mysql_error());
	}
	
	$result_copia= mysql_query("select rh_ponto.id_horario, rh_ponto.id_funcionario, rh_ponto.vale_dia, rh_ponto.tipo, rh_ponto.data_hora_batida from rh_ponto, rh_carreiras
								where rh_ponto.vale_dia >= '". $vale_dia_inicio ."'
								and   rh_ponto.vale_dia <= '". $vale_dia_fim ."'
								and   rh_ponto.id_funcionario = rh_carreiras.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_carreiras.id_departamento = '2'
								") or die(mysql_error());
	$linhas_copia= mysql_num_rows($result_copia);
	
	while ($rs_copia= mysql_fetch_object($result_copia)) {
		
		$result_pre= mysql_query("select count(*) as contagem from rh_ponto_virtual
									where id_horario = '". $rs_copia->id_horario ."'
									");
		$rs_pre= mysql_fetch_object($result_pre);
		
		if ($rs_pre->contagem==0) {
		
			$tipo_aux= "";
			
			if ($rs_copia->tipo==0) {
				$result_teste_insano= mysql_query("select count(id_horario) as contagem from rh_ponto
													where vale_dia = '". $rs_copia->vale_dia ."'
													and   data_hora_batida > '". $rs_copia->data_hora_batida ."'
													and   tipo = '0'
													and   id_funcionario = '". $rs_copia->id_funcionario ."'
													");
				$rs_teste_insano= mysql_fetch_object($result_teste_insano);
				
				if ($rs_teste_insano->contagem==0) $tipo_aux= 0;
				else $tipo_aux= 2;
			}
			
			$result_insere= mysql_query("insert into rh_ponto_virtual
										(id_horario, id_funcionario, id_departamento, vale_dia, tipo, tipo_aux, data_hora_batida)
										values
										('". $rs_copia->id_horario ."', '". $rs_copia->id_funcionario ."', '2', '". $rs_copia->vale_dia ."',
										'". $rs_copia->tipo ."', '". $tipo_aux ."', '". $rs_copia->data_hora_batida ."')
										") or die(mysql_error());
		}
	}
	
	// ---------------------------------------------------------------------------------------------------------------
	
	$k=0;
	
	$diferenca_dias = ceil(($ultimo_dia_periodo_mk-$primeiro_dia_periodo_mk)/86400);
	
	for ($i=$i_inicio; $i<$diferenca_dias; $i++) {
		
		if ($i>0) $k++;
			
		$calculo_data= $data1_mk+(86400*$i);
		
		$amanha_mk= mktime(0, 0, 0, $periodo[0], $i+1, $periodo[1]);
		
		$id_dia= date("w", $calculo_data);
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$data_formatada= date("d/m/Y", $calculo_data);
		
		$data= date("Y-m-d", $calculo_data);
		$amanha= soma_data($data, 1, 0, 0);
		
		//if (($data=="2010-09-20") || ($data=="2010-09-21")) {
		
		$data_mesmo= date("Y-m-d", $calculo_data);
		
		$result_total_remessa= mysql_query("select * from op_suja_remessas
											where data_remessa = '". $data ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
		$linhas_total_remessa= mysql_num_rows($result_total_remessa);
		
		if ($linhas_total_remessa>0) {
			
			$total_dia=0;
			//$media_dia=0;
			$media_turno_aqui=0;
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(1.3, 0.5, $data_formatada, 'TLB', 0, "C", $fill);
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(0.7, 0.5, traduz_dia_resumido($id_dia), 'TRB', 0, "L", $fill);
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			
			/*if (($id_dia==0) || ($id_dia==6)) {
				
				//sábado
				if ($id_dia==6) {
					$id_turno_find= -1;
				}
				//domingo
				else {
					$id_turno_find= -2;
				}
				
				$result_pre= mysql_query("select * from rh_ponto_producao
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   vale_dia = '". $data ."'
											and   id_departamento = '2'
											and   hora = '". $h ."'
											");
				$linhas_pre= mysql_num_rows($result_pre);
				
				if (($_POST["denovo"]=="1") || ($linhas_pre==0)) {
					$funcionarios_neste_plantao= pega_funcionarios_trabalhando_retroativo($_SESSION["id_empresa"], 2, 0, $data_mesmo, $data ." 06:00:00", $amanha ." 06:00:00", $_POST["identificar"], 24);
				}
				
				for ($h=0; $h<24; $h++) {
					if ($h==23) $quebra=1;
					else $quebra=0;
				
					$result_soma= mysql_query("select sum(peso) as soma from op_suja_pesagem
												where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   op_suja_pesagem.data_pesagem = '". $data ."'
												and   op_suja_pesagem.hora_pesagem like '". formata_saida($h, 2) .":%'
												") or die(mysql_error());
					$rs_soma= mysql_fetch_object($result_soma);
					
					$soma= $rs_soma->soma+$rs_soma_costura->soma_costura;
					$total_dia= $soma;
					
					//$funcionarios_neste_plantao= pega_funcionarios_trabalhando_retroativo_plantao(1, $data_mesmo);
					
					
					$sql_pre="";
					
					if ($linhas_pre>0) {
						
						$rs_pre= mysql_fetch_object($result_pre);
						
						if ($_POST["denovo"]=="1") {
							
							$sql_pre="update rh_ponto_producao
										set media = '". $funcionarios_neste_plantao ."'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   vale_dia = '". $data ."'
										and   id_departamento = '2'
										";
						}
						else {
							$funcionarios_neste_plantao= $rs_pre->media;
						}
						
					}
					else {				
						$sql_pre="insert into rh_ponto_producao
								(id_empresa, id_departamento, vale_dia, id_turno_index, media, id_usuario)
								values
								('". $_SESSION["id_empresa"] ."', '2', '". $data ."', '0', 
								'". $funcionarios_neste_plantao ."', '". $_SESSION["id_usuario"] ."')
								";
					}
					
					if ($sql_pre!="") {
						$result_fixa= mysql_query($sql_pre) or die(mysql_error());
					}
					
					
					
					
					if ($funcionarios_neste_plantao>0) $media_plantao= ($soma/$funcionarios_neste_plantao);
					else $media_plantao= 0;
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
					$pdf->Cell($largura, 0.5, "x", 'TLB', $quebra, "C", 0);
					
					$media_mes+= (($media_plantao/20)*6);
					
					$total_semana+= $soma;
					$media_semana+= (($media_plantao/20)*6);
					
					$total_dias_considerados_plantao++;
					$total_dias_considerados_soh_plantao++;
				}//fim for horas
			}
			else { */
				
			// 2010-10-10
			
			$ontem= soma_data($data, -1, 0, 0);
			$amanha= soma_data($data, 1, 0, 0);
			
			/*for ($hi=6; $hi<30; $hi++) {
		
				if ($hi<24) $h=$hi;
				else $h= $hi-24;
				
				if ($h==5) $quebra=1;
				else $quebra=0;
				
				$soma= 0;
				
				$str_add="";
				
				if ($h<7) $data_que_vale= $ontem;
				else $data_que_vale= $hoje;
				
				$result_soma= mysql_query("select sum(peso) as soma from op_suja_pesagem
											where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   op_suja_pesagem.data_pesagem = '". $data ."'
											and   op_suja_pesagem.hora_pesagem like '". formata_saida($h, 2) .":%'
											") or die(mysql_error());
				$rs_soma= mysql_fetch_object($result_soma);
				
				$sql_pre="";
				
				$result_pre= mysql_query("select * from rh_ponto_producao
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   vale_dia = '". $data ."'
											and   id_turno_index = '". $h ."'
											and   id_departamento = '2'
											");
				$linhas_pre= mysql_num_rows($result_pre);
				
				if ($h<6) $data_parametro= $amanha;
				else $data_parametro= $data;
				
				$data1_producao_mk= mktime($h, 0, 0, substr($data_parametro, 5, 2), substr($data_parametro, 8, 2), substr($data_parametro, 0, 4));
				$data2_producao_mk= mktime($h+1, 0, 0, substr($data_parametro, 5, 2), substr($data_parametro, 8, 2), substr($data_parametro, 0, 4));
				
				if (($_POST["denovo"]=="1") || ($linhas_pre==0)) {
					$funcionarios_neste_turno_neste_dia[$h]= pega_funcionarios_trabalhando_retroativo($_SESSION["id_empresa"], 2, $h, $data_parametro, $data_parametro ." ". date("H:i:s", $data1_producao_mk), $data_parametro ." ". date("H:i:s", $data2_producao_mk), $_POST["identificar"], 1);
				}
				
				if ($linhas_pre>0) {
					
					$rs_pre= mysql_fetch_object($result_pre);
					
					if ($_POST["denovo"]=="1") {
						
						$sql_pre="update rh_ponto_producao
									set media = '". $funcionarios_neste_turno_neste_dia[$h] ."'
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   vale_dia = '". $data ."'
									and   id_turno_index = '". $h ."'
									and   id_departamento = '2'
									";
					}
					else {
						$funcionarios_neste_turno_neste_dia[$h]= $rs_pre->media;
					}
					
				}
				else {				
					$sql_pre="insert into rh_ponto_producao
							(id_empresa, id_departamento, vale_dia, id_turno_index, media, id_usuario)
							values
							('". $_SESSION["id_empresa"] ."', '2', '". $data ."', '". $h ."', 
							'". $funcionarios_neste_turno_neste_dia[$h] ."', '". $_SESSION["id_usuario"] ."')
							";
				}
				
				if ($sql_pre!="") {
					$result_fixa= mysql_query($sql_pre) or die(mysql_error());
				}
				
				
				
				$soma= $rs_soma->soma;
				$soma_vetor[$h]= $soma;
				
				$total_dia+= $soma;
				$total_turno[$h]+= $soma;
				
				$total_semana+= $soma;
				
				$total_turno_semana[$h]+= $soma;
				
				$pdf->Cell($largura, 0.5, fnum($rs_soma->soma) ."kg", 1, $quebra, "C", $fill);
			}//fim for horas
			
			$pdf->Cell(1.65, 0.5, "", 0, 0, "C", 0);
			
			for ($hi=6; $hi<30; $hi++) {
		
				if ($hi<24) $h=$hi;
				else $h= $hi-24;
				
				if ($h==5) $quebra=1;
				else $quebra=0;
				
				$pdf->Cell($largura, 0.5, fnumi($funcionarios_neste_turno_neste_dia[$h]), 1, $quebra, "C", 0);
				
			}
			*/
			
			for ($r=1; $r<=$total_remessas_dia; $r++) {
		
				$result_remessa= mysql_query("select *, DATE_FORMAT(hora_chegada, '%H:%i') as hora_chegada,
												DATE_FORMAT(hora_inicio_separacao, '%H:%i') as hora_inicio_separacao,
												
												DATE_FORMAT(hora_inicio_separacao, '%H') as hora_inicio_separacao2,
												DATE_FORMAT(hora_inicio_separacao, '%i') as minuto_inicio_separacao2,
												
												DATE_FORMAT(hora_fim_separacao, '%H:%i') as hora_fim_separacao,
												DATE_FORMAT(hora_fim_separacao, '%H') as hora_fim_separacao2,
												DATE_FORMAT(hora_fim_separacao, '%i') as minuto_fim_separacao2
												
												from op_suja_remessas
												
												where data_remessa = '". $data ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												and   num_remessa = '". $r ."'
												");
				$linhas_remessa= mysql_num_rows($result_remessa);
				
				if ($linhas_remessa>0) {
					$rs_remessa= mysql_fetch_object($result_remessa);
					
					$soma=0;
					
					$str_add="";
					
					//if ($h<7) $data_que_vale= $ontem;
					//else $data_que_vale= $hoje;
					
					$result_soma= mysql_query("select sum(peso) as soma from op_suja_pesagem
												where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   ( op_suja_pesagem.data_pesagem = '". $data ."' or op_suja_pesagem.data_pesagem = '". $amanha ."' )
												and   op_suja_pesagem.id_remessa = '". $rs_remessa->id_remessa ."'
												") or die(mysql_error());
					$rs_soma= mysql_fetch_object($result_soma);
					
					$sql_pre="";
					
					$tempo_separacao=0;
			
					$result_separacoes= mysql_query("select *, DATE_FORMAT(data_separacao, '%d') as dia_separacao2,
														DATE_FORMAT(data_separacao, '%m') as mes_separacao2,
														DATE_FORMAT(data_separacao, '%Y') as ano_separacao2,
														DATE_FORMAT(hora_separacao, '%H') as hora_separacao2,
														DATE_FORMAT(hora_separacao, '%i') as minuto_separacao2,
														DATE_FORMAT(hora_separacao, '%s') as segundo_separacao2
														
														from op_suja_remessas_separacoes
														where id_remessa = '". $rs_remessa->id_remessa ."'
														and   tipo_separacao = '1'
														order by data_separacao asc, hora_separacao asc
														") or die(mysql_error());
					
					$linhas_separacoes= mysql_num_rows($result_separacoes);
					
					if ($linhas_separacoes==0) {
						die("Separação não realizada na remessa nº <strong>". $rs_remessa->num_remessa ."</strong> no dia <strong>". desformata_data($data) ."</strong>. <br />");
					}
					else {
						
						$e=0;
						
						while ($rs_separacoes= mysql_fetch_object($result_separacoes)) {
                    
							$result_separacao_encerra= mysql_query("select *,
																	DATE_FORMAT(data_separacao, '%d') as dia_separacao2,
																	DATE_FORMAT(data_separacao, '%m') as mes_separacao2,
																	DATE_FORMAT(data_separacao, '%Y') as ano_separacao2,
																	DATE_FORMAT(hora_separacao, '%H') as hora_separacao2,
																	DATE_FORMAT(hora_separacao, '%i') as minuto_separacao2,
																	DATE_FORMAT(hora_separacao, '%s') as segundo_separacao2
																	
																	from op_suja_remessas_separacoes
																	where id_remessa = '". $rs_remessa->id_remessa ."'
																	and   tipo_separacao = '0'
																	
																	/*and   data_separacao >= '". $rs_separacoes->data_separacao ."'
																	and   hora_separacao >= '". $rs_separacoes->hora_separacao ."' */
																	
																	and   id_separacao_fecha = '". $rs_separacoes->id_separacao ."'
																	order by data_separacao asc, hora_separacao asc limit 1
																	") or die(mysql_error());
							$linhas_separacao_encerra= mysql_num_rows($result_separacao_encerra);
							
							if ($linhas_separacao_encerra>0) {
								$rs_separacao_encerra= mysql_fetch_object($result_separacao_encerra);
								
								$data1_producao_mk[$e]= mktime($rs_separacoes->hora_separacao2, $rs_separacoes->minuto_separacao2, $rs_separacoes->segundo_separacao2, $rs_separacoes->mes_separacao2, $rs_separacoes->dia_separacao2, $rs_separacoes->ano_separacao2);
								$data2_producao_mk[$e]= mktime($rs_separacao_encerra->hora_separacao2, $rs_separacao_encerra->minuto_separacao2, $rs_separacao_encerra->segundo_separacao2, $rs_separacao_encerra->mes_separacao2, $rs_separacao_encerra->dia_separacao2, $rs_separacao_encerra->ano_separacao2);
								
								$data1_producao_redonda_mk[$e]= mktime($rs_separacoes->hora_separacao2, 0, 0, $rs_separacoes->mes_separacao2, $rs_separacoes->dia_separacao2, $rs_separacoes->ano_separacao2);
								$data2_producao_redonda_mk[$e]= mktime($rs_separacao_encerra->hora_separacao2, 0, 0, $rs_separacao_encerra->mes_separacao2, $rs_separacao_encerra->dia_separacao2, $rs_separacao_encerra->ano_separacao2);
								
								$diferenca[$e]= $data2_producao_mk[$e]-$data1_producao_mk[$e];
								
								if (($diferenca[$e]<=0) || ($diferenca[$e]>36000)) $erro=1;
								else $erro=0;
								
								$diferenca_separacoes[$e]= floor($diferenca[$e]/3600);
								$tempo_separacao+=$diferenca[$e];
								
								if ($erro) die("Irregularidade na remessa nº <strong>". $rs_remessa->num_remessa ."</strong> no dia <strong>". desformata_data($data) ."</strong>. <br />");
							}
							else {
								die("Separação não encerrada na remessa nº <strong>". $rs_remessa->num_remessa ."</strong> no dia <strong>". desformata_data($data) ."</strong>. <br />");
							}
							
							$e++;
							
						}
						
					}
					
					//if ($h<6) $data_parametro= $amanha;
					//else
					/*$data_parametro= $data;
					
					$data1_producao_mk= mktime($rs_remessa->hora_inicio_separacao2, $rs_remessa->minuto_inicio_separacao2, 0, substr($rs_remessa->data_inicio_separacao, 5, 2), substr($rs_remessa->data_inicio_separacao, 8, 2), substr($rs_remessa->data_inicio_separacao, 0, 4));
					$data2_producao_mk= mktime($rs_remessa->hora_fim_separacao2, $rs_remessa->minuto_fim_separacao2, 0, substr($rs_remessa->data_fim_separacao, 5, 2), substr($rs_remessa->data_fim_separacao, 8, 2), substr($rs_remessa->data_fim_separacao, 0, 4));
					
					$data1_producao_redonda_mk= mktime($rs_remessa->hora_inicio_separacao2, 0, 0, substr($rs_remessa->data_inicio_separacao, 5, 2), substr($rs_remessa->data_inicio_separacao, 8, 2), substr($rs_remessa->data_inicio_separacao, 0, 4));
					$data2_producao_redonda_mk= mktime($rs_remessa->hora_fim_separacao2, 0, 0, substr($rs_remessa->data_fim_separacao, 5, 2), substr($rs_remessa->data_fim_separacao, 8, 2), substr($rs_remessa->data_fim_separacao, 0, 4));
					
					$diferenca= $data2_producao_mk-$data1_producao_mk;
		
					if (($diferenca<=0) || ($diferenca>36000)) $erro=1;
					else $erro=0;
					
					if ($erro) die("Problema com os horários do dia <strong>". desformata_data($data) ."</strong>, remessa <strong>". $r ."</strong>. Veja na lista de separação.");
					*/
					
					//if ($r==4) {
					//	echo date("d/m/Y H:i:s", $data2_producao_mk);
					//	die();
					//}
					
					$diferenca_separacoes_float= ($tempo_separacao/3600);
					
					
					$soma_funcionarios_remessa= 0;
					
					$passadas=0;
					
					//echo $linhas_separacoes ." ";
					
					for ($hsp=0; $hsp<$linhas_separacoes; $hsp++) {
						
						//echo $hsp ." ". $diferenca_separacoes[$hsp] ."<br /> ";
						
						for ($hs= 0; $hs<=$diferenca_separacoes[$hsp]; $hs++) {
							
							$calculo_hora2= $data1_producao_redonda_mk[$hsp]+(3600*$hs);
							$calculo_hora_proxima2= $calculo_hora2+3600;
							
							//echo date("d/m/Y H:i:s", $calculo_hora2) ." ";
							
							$hora= date("H", $calculo_hora2);
							//echo date("d/m/Y H:i:s", $calculo_hora) ." ";
							
							//buscando por hora e não mais por turno
							$result_pre= mysql_query("select * from rh_ponto_producao
														where id_empresa = '". $_SESSION["id_empresa"] ."'
														and   vale_dia = '". $data ."'
														and   hora = '". $hora ."'
														and   id_departamento = '2'
														");
							$linhas_pre= mysql_num_rows($result_pre);
							
							$data_parametro= $data;
							
							if (($_POST["denovo"]=="1") || ($linhas_pre==0)) {
								$funcionarios_neste_turno_neste_dia[$hsp][$hs]= pega_funcionarios_trabalhando_retroativo($_SESSION["id_empresa"], 2, (int)$hora, $data_parametro, date("d/m/Y H:i:s", $calculo_hora2), date("d/m/Y H:i:s", $calculo_hora_proxima2), $_POST["identificar"], 1);
								
								//echo " (". $funcionarios_neste_turno_neste_dia[$hsp][$hs] .") ";
								
								//if ($r==3) {
								//	echo date("d/m/Y H:i:s", $calculo_hora) ." ". date("d/m/Y H:i:s", $calculo_hora_proxima) ." - ". $funcionarios_neste_turno_neste_dia[$hs] ."<br />";
								//}
								
								//echo $hora ." ";
								
								/*
								//hora inicial
								if ($hs==0) {
									
								}
								
								//hora final
								if ($hs==$diferenca_separacoes) {
									
								}*/
								
								//echo date("d/m/Y H:i:s", $calculo_hora) ." ". date("d/m/Y H:i:s", $calculo_hora_proxima) ."<br />";
							}
							
							if ($linhas_pre>0) {
								
								$rs_pre= mysql_fetch_object($result_pre);
								
								if ($_POST["denovo"]=="1") {
									
									$sql_pre="update rh_ponto_producao
												set media = '". $funcionarios_neste_turno_neste_dia[$hsp][$hs] ."'
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   vale_dia = '". $data ."'
												and   hora = '". $hora ."'
												and   id_departamento = '2'
												";
								}
								else {
									$funcionarios_neste_turno_neste_dia[$hsp][$hs]= $rs_pre->media;
								}
								
							}
							else {				
								$sql_pre="insert into rh_ponto_producao
										(id_empresa, id_departamento, vale_dia, hora, media, id_usuario)
										values
										('". $_SESSION["id_empresa"] ."', '2', '". $data ."', '". $hora ."', 
										'". $funcionarios_neste_turno_neste_dia[$hsp][$hs] ."', '". $_SESSION["id_usuario"] ."')
										";
							}
							
							if ($sql_pre!="") {
								$result_fixa= mysql_query($sql_pre) or die(mysql_error());
							}
							
							$soma_funcionarios_remessa+= $funcionarios_neste_turno_neste_dia[$hsp][$hs];
							
							if ($r==4) {
								//echo $funcionarios_neste_turno_neste_dia[$hs] ." "; echo date("d/m/Y H:i:s", $calculo_hora) ." ";
							}
							
							$passadas++;
						}
						
						//echo "<br /><br />";
						
					}//fim for separações
					
					//echo "<br /><br />". $passadas ."<br /><br />";
					
					//if ($r==4) die();
					
					//echo "<br /><br />";
					
					$soma= $rs_soma->soma;
					$total_remessa_semana[$r]+=$rs_soma->soma;
					$total_remessa_mes[$r]+=$rs_soma->soma;
					
					$total_semana+=$rs_soma->soma;
					//$soma_vetor[$r]= $soma;
					
					if ($r==4) {
						//echo $diferenca_separacoes; die();
						//echo $soma ." ". $diferenca_separacoes_float ." ". $media_funcionarios_remessa ."<br />";
					}
					
					$diferenca_separacoes_considerar= $passadas;
					
					if ($diferenca_separacoes>0) $media_funcionarios_remessa= $soma_funcionarios_remessa/$diferenca_separacoes_considerar;
					else $media_funcionarios_remessa= 0;
					
					if ($diferenca_separacoes_float>0) {
						if ($media_funcionarios_remessa>0) $media_por_funcionario= (($soma/$diferenca_separacoes_float)/$media_funcionarios_remessa);
						else $media_por_funcionario= 0;
					}
					else $media_por_funcionario= 0;
					
					
					$media_por_funcionario_dia[$i]+=$media_por_funcionario;
					
					$total_dia+= $soma;
					//$total_turno[$h]+= $soma;
					
					//$total_semana+= $soma;
					
					//$total_turno_semana[$h]+= $soma;
					
					$largura1= $largura*0.45;
					$largura2= $largura*0.25;
					$largura3= $largura*0.30;
					
					$pdf->Cell($largura1/2, 0.5, $rs_remessa->hora_chegada, 1, 0, "C", $fill);
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
					$pdf->Cell($largura1/2, 0.5, calcula_total_horas($tempo_separacao), 1, 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 7);
					$pdf->Cell($largura2, 0.5, fnum($soma) ." kg", 1, 0, "C", $fill);
					$pdf->Cell($largura3, 0.5, fnum($media_por_funcionario) ." (". fnum($media_funcionarios_remessa) .")", 1, 0, "C", $fill);
					
					$media_semana[$r]+= $media_por_funcionario;
					$media_mes[$r]+= $media_por_funcionario;
					
					$media_func_semana[$r]+= $media_funcionarios_remessa;
					$media_func_mes[$r]+= $media_funcionarios_remessa;
				}
			}//fim for remessas
			
			if ($total_remessas_dia>0) $media_dia[$i]= ($media_por_funcionario_dia[$i]/$total_remessas_dia);
			
			$pdf->Cell(1.5, 0.5, fnum($total_dia), 1, 0, "C", $fill);
			$pdf->Cell(1.5, 0.5, fnum($media_dia[$i]), 1, 1, "C", $fill);
			
			
			
			//$media_mes+= $media_dia/$total_turnos;
			//$media_semana+= $media_dia/$total_turnos;
			
			$total_dias_considerados_semana++;
			//}//fim else
			
			$total_mes+= $total_dia;
			
			$total_dias_considerados_mes++;
			
			
			//é domingo, vamos fechar a produção da semana
			if ($id_dia==0) {
				
				$pdf->SetFillColor(210,210,210);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				$pdf->Cell(2, 0.5, "TOTAL SEMANA", 1, 0, "L", 1);
				
				$pdf->SetFont('ARIALNARROW', '', 7);
				
				for ($r=1; $r<=$total_remessas_dia; $r++) {
					
					if ($total_dias_considerados_semana>0) {
						$resultado2= $media_semana[$r]/$total_dias_considerados_semana;
						$resultado3= $media_func_semana[$r]/$total_dias_considerados_semana;
					}
					else $resultado2=0;
			
					$total_media_semana+= $resultado2;
					
					$pdf->Cell($largura1, 0.5, "-", 1, 0, "C", 1);
					$pdf->Cell($largura2, 0.5, fnum($total_remessa_semana[$r]) ." kg", 1, 0, "C", 1);
					$pdf->Cell($largura3, 0.5, fnum($resultado2) ." (". fnum($resultado3) .")", 1, 0, "C", 1);
					
					$total_remessa_semana[$r]=0;
				}
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				$pdf->Cell(1.5, 0.5, fnum($total_semana) ." kg", 1, 0, "C", 1);
				
				$pdf->SetFont('ARIALNARROW', '', 7);
				$pdf->Cell(1.5, 0.5, fnum($total_media_semana/$total_remessas_dia), 1, 1, "C", 1);
				
				$pdf->LittleLn();
				
				$pdf->SetFillColor(240, 240, 240);
				
				$total_media_semana=0;
				$total_semana=0;
				//$media_semana=0;
				
				$total_dias_considerados_semana=0;
				
				$saldo_dias= $total_dias_mes-$k;
				
				if ($saldo_dias<7) break;
			}
		
		}//fim tem remessa neste dia
	}
	
	$pdf->LittleLn();
	$pdf->SetFillColor(210,210,210);
				
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
	$pdf->Cell(2, 0.5, "TOTAL PERÍODO", 1, 0, "L", 1);
	
	$pdf->SetFont('ARIALNARROW', '', 7);
	
	for ($r=1; $r<=$total_remessas_dia; $r++) {
		
		if ($total_dias_considerados_mes>0) {
			$resultado2= $media_mes[$r]/$total_dias_considerados_mes;
			$resultado3= $media_func_mes[$r]/$total_dias_considerados_mes;
		}
		else $resultado2=0;

		$total_media_mes+= $resultado2;
		
		$pdf->Cell($largura1, 0.5, "-", 1, 0, "C", 1);
		$pdf->Cell($largura2, 0.5, fnum($total_remessa_mes[$r]) ." kg", 1, 0, "C", 1);
		$pdf->Cell($largura3, 0.5, fnum($resultado2) ." (". fnum($resultado3) .")", 1, 0, "C", 1);
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
	$pdf->Cell(1.5, 0.5, fnum($total_mes) ." kg", 1, 0, "C", 1);
	
	$pdf->SetFont('ARIALNARROW', '', 7);
	$pdf->Cell(1.5, 0.5, fnum($total_media_mes/$total_remessas_dia), 1, 1, "C", 1);
	
	$pdf->LittleLn();
	
	$pdf->SetFillColor(240, 240, 240);
	
	//$pdf->SetFont('ARIALNARROW', '', 7);
	//$pdf->Cell(1.5, 0.5, fnum($resultado3), 1, 1, "C", 1);
	
	$pdf->AliasNbPages(); 
	$pdf->Output("producao_limpa_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>
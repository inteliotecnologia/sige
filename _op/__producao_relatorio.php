<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pl", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
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
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 2);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "PRODUÇÃO DA ÁREA LIMPA", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, $data1f ." à ". $data2f, 0 , 1, 'R');
	
	$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	$pdf->Cell(2, 0.5, "DATA", 1, 0, "L", 1);
	
	$vetor_turno= pega_turno_padrao("l");
	
	$total_turnos= sizeof($vetor_turno);
	
	$largura=12/$total_turnos;
	
	$i=1;
    while ($vetor_turno[$i]) {
		$pdf->Cell($largura, 0.5, strtoupper($vetor_turno[$i]), 1, 0, "C", 1);
		$i++;
	}
	
	$pdf->Cell(3, 0.5, "EQUIPE", 1, 1, "C", 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 6);
	
	$pdf->Cell(2, 0.5, "", 1, 0, "L", 0);
	
	for ($k=-1; $k<4; $k++) {
		if ($k==3) {
			$quebra= 1;
			
			$tit1= "peso dia (kg)";
		}
		else {
			$tit1= "peso turno (kg)";
			$quebra= 0;
		}
		
		$pdf->Cell(1.5, 0.5, $tit1, 1, 0, "C", 0);
		$pdf->Cell(1.5, 0.5, "média individual", 1, $quebra, "C", 0);
	}
	
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
										") or die(mysql_error());
	}
	
	$result_copia= mysql_query("select rh_ponto.id_horario, rh_ponto.id_funcionario, rh_ponto.vale_dia, rh_ponto.tipo, rh_ponto.data_hora_batida from rh_ponto, rh_carreiras
								where rh_ponto.vale_dia >= '". $vale_dia_inicio ."'
								and   rh_ponto.vale_dia <= '". $vale_dia_fim ."'
								and   rh_ponto.id_funcionario = rh_carreiras.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_carreiras.id_departamento = '1'
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
										(id_horario, id_funcionario, vale_dia, tipo, tipo_aux, data_hora_batida)
										values
										('". $rs_copia->id_horario ."', '". $rs_copia->id_funcionario ."', '". $rs_copia->vale_dia ."',
										'". $rs_copia->tipo ."', '". $tipo_aux ."', '". $rs_copia->data_hora_batida ."')
										") or die(mysql_error());
		}
	}
	
	// ---------------------------------------------------------------------------------------------------------------
	
	$k=0;
	
	$diferenca = ceil(($ultimo_dia_periodo_mk-$primeiro_dia_periodo_mk)/86400);
	
	//echo $i_inicio; die();
	
	for ($i=$i_inicio; $i<$diferenca; $i++) {
		
		//echo $i ."-". $diferenca ." ";
		
		if ($i>0) $k++;
		
		$calculo_data= $data1_mk+(86400*$i);
		
		$amanha_mk= mktime(0, 0, 0, $periodo[0], $i+1, $periodo[1]);
		
		$id_dia= date("w", $calculo_data);
		
		if (($i%2)==0) $fill=0;
		else $fill= 1;
		
		$data_formatada= date("d/m/Y", $calculo_data);
		$data= date("Y-m-d", $calculo_data);
		$amanha= soma_data($data, 1, 0, 0);
		
		//if (($data=="2010-09-20") || ($data=="2010-09-21")) {
		
		$data_mesmo= date("Y-m-d", $calculo_data);
	
		$total_dia=0;
		$media_dia=0;
		$media_turno_aqui=0;
		
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(1.4, 0.5, $data_formatada, 1, 0, "C", $fill);
		
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(0.6, 0.5, traduz_dia_resumido($id_dia), 1, 0, "C", $fill);
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		/*if ($_SESSION[id_usuario]==13) { echo "select sum(peso) as soma_costura from op_limpa_pesagem
											where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   op_limpa_pesagem.id_turno = '-3'
											and   ( op_limpa_pesagem.data_hora_pesagem >= '". $data ." 06:00:00' and  op_limpa_pesagem.data_hora_pesagem <= '". $amanha ." 05:59:00' )
											"; die(); }*/
		
		$result_soma_costura= mysql_query("select sum(peso) as soma_costura from op_limpa_pesagem
											where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											/* and   op_limpa_pesagem.extra = '0' */
											and   op_limpa_pesagem.id_turno = '-3'
											and   ( op_limpa_pesagem.data_hora_pesagem >= '". $data ." 06:00:00' and  op_limpa_pesagem.data_hora_pesagem <= '". $amanha ." 05:59:00' )
											") or die(mysql_error());
		$rs_soma_costura= mysql_fetch_object($result_soma_costura);
		
		//domingo ou sábado
		if (($id_dia==0) || ($id_dia==6)) {
			
			$result_des= mysql_query("select count(*) as linhas from rh_ponto_producao_desconsiderados
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_departamento = '1'
										and   id_turno_index = '0'
										and   vale_dia = '". $data_mesmo ."'
										and   desconsiderado = '1'
										");
			$rs_des= mysql_fetch_object($result_des);
			
			//desconsiderando este turno...
			if ($rs_des->linhas>0) {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				$pdf->Cell(7.5, 0.5, "-", 1, 0, "C", 0);
				
				$pdf->SetFont('ARIALNARROW', '', 7);
				$pdf->Cell(7.5, 0.5, "-", 1, 1, "C", 0);
			}
			//plantão desconsiderado
			else {
				
				//sábado
				if ($id_dia==6) {
					$id_turno_find= -1;
				}
				//domingo
				else {
					$id_turno_find= -2;
				}
				
				$result_soma= mysql_query("select sum(peso) as soma from op_limpa_pesagem
											where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											/* and   op_limpa_pesagem.extra = '0' */
											and   op_limpa_pesagem.id_turno = '". $id_turno_find ."'
											and   ( op_limpa_pesagem.data_pesagem = '". $data ."' or  op_limpa_pesagem.data_pesagem = '". $amanha ."' )
											") or die(mysql_error());
				$rs_soma= mysql_fetch_object($result_soma);
				
				$soma= $rs_soma->soma+$rs_soma_costura->soma_costura;
				$total_dia= $soma;
				
				//$funcionarios_neste_plantao= pega_funcionarios_trabalhando_retroativo_plantao(1, $data_mesmo);
				
				
				$sql_pre="";
								
				$result_pre= mysql_query("select * from rh_ponto_producao
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   vale_dia = '". $data ."'
											and   id_departamento = '1'
											");
				$linhas_pre= mysql_num_rows($result_pre);
				
				//if (($_POST["denovo"]=="1") || ($linhas_pre==0)) {
					$funcionarios_neste_plantao= pega_funcionarios_trabalhando_manual($_SESSION["id_empresa"], 1, 0, $data_mesmo);
				//}
				
				/*
				if ($linhas_pre>0) {
					
					$rs_pre= mysql_fetch_object($result_pre);
					
					if ($_POST["denovo"]=="1") {
						
						$sql_pre="update rh_ponto_producao
									set media = '". $funcionarios_neste_plantao ."'
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   vale_dia = '". $data ."'
									and   id_departamento = '1'
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
							('". $_SESSION["id_empresa"] ."', '1', '". $data ."', '0', 
							'". $funcionarios_neste_plantao ."', '". $_SESSION["id_usuario"] ."')
							";
				}
				
				if ($sql_pre!="") {
					$result_fixa= mysql_query($sql_pre) or die(mysql_error());
				}
				
				*/
				
				
				
				
				
				
				
				if ($funcionarios_neste_plantao>0) $media_plantao= ($soma/$funcionarios_neste_plantao);
				else $media_plantao= 0;
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				$pdf->Cell(3.75, 0.5, "Peso do plantão:", 'TLB', 0, "R", 0);
				
				$pdf->SetFont('ARIALNARROW', '', 7);
				$pdf->Cell(3.75, 0.5, fnum($soma) ." kg", 'TB', 0, "L", 0);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				$pdf->Cell(3.75, 0.5, "Média individual:", 'TLB', 0, "R", 0);
				
				$pdf->SetFont('ARIALNARROW', '', 7);
				$pdf->Cell(3.75, 0.5, fnum(($media_plantao/23)*6) ." kg      (". fnum($funcionarios_neste_plantao) .")", 'TBR', 1, "L", 0);
				
				$media_mes+= (($media_plantao/23)*6);
				
				$total_semana+= $soma;
				$media_semana+= (($media_plantao/23)*6);
				
				//somar proporcional aos turnos normal, mesmo durante o fim de semana...
				for ($q=1; $q<5; $q++) {
					$total_turno_semana[$q]+= ($soma/4);
					$total_turno[$q]+= ($soma/4);
					
					$total_semana_turno_considerado[$q]++;
					$total_mes_turno_considerado[$q]++;
					
					$media_turno[$q]+= (($media_plantao/23)*6);
					$media_turno_semana[$q]+= (($media_plantao/23)*6);
				}
				
				$total_dias_considerados_plantao++;
				$total_dias_considerados_soh_plantao++;
			}//fim plantão desconsiderado
			
		}
		else {
			
			$h=1;
			$vetor_turno= pega_turno_padrao("l");
			$total_turnos= sizeof($vetor_turno);
			$largura=6/$total_turnos;
			
			while ($vetor_turno[$h]) {
				
				$result_des= mysql_query("select count(*) as linhas from rh_ponto_producao_desconsiderados
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_departamento = '1'
											and   id_turno_index = '". $h ."'
											and   vale_dia = '". $data_mesmo ."'
											and   desconsiderado = '1'
											");
				$rs_des= mysql_fetch_object($result_des);
				
				//desconsiderando este turno...
				if ($rs_des->linhas>0) {
				
				}
				else {
					$soma= 0;
					
					$ontem= soma_data($data, -1, 0, 0);
					$amanha= soma_data($data, 1, 0, 0);
					
					//$str= " and   op_limpa_pesagem.id_turno = '". $h ."' ";
					
					/*if ($data=="2010-02-11") echo "select sum(peso) as soma from op_limpa_pesagem, rh_turnos
												where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   op_limpa_pesagem.id_turno = rh_turnos.id_turno
												and   rh_turnos.id_turno_index = '". $h ."'
												and   op_limpa_pesagem.extra = '0'
												and   op_limpa_pesagem.data_pesagem = '". $data ."'
												<br /><br />
												";
						*/
					
					$str_add="";
					
					//noite
					if ($h==3) {
						$str_add= " and  ( op_limpa_pesagem.data_pesagem = '". $data ."'
											or op_limpa_pesagem.data_pesagem = '". $amanha ."' )
									and   op_limpa_pesagem.data_hora_pesagem < '". $amanha ." 04:00:00'
									and   op_limpa_pesagem.data_hora_pesagem > '". $data ." 14:00:00'
									";
					}
					//madrugada
					elseif ($h==4) {
						//$str_add= " and   op_limpa_pesagem.data_pesagem = '". $amanha ."' ";
						
						$str_add= " and  ( op_limpa_pesagem.data_pesagem = '". $data ."'
											or op_limpa_pesagem.data_pesagem = '". $amanha ."' )
									and   op_limpa_pesagem.data_hora_pesagem > '". $data ." 20:00:00'
									and   op_limpa_pesagem.data_hora_pesagem < '". $amanha ." 10:00:00'
									";
						
					}
					//manhã e tarde
					else {
						$str_add= " and   op_limpa_pesagem.data_pesagem = '". $data ."' ";
					}
					
					$result_soma= mysql_query("select sum(peso) as soma from op_limpa_pesagem, rh_turnos
												where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   op_limpa_pesagem.id_turno = rh_turnos.id_turno
												and   rh_turnos.id_turno_index = '". $h ."'
												/* and   op_limpa_pesagem.extra = '0' */
												
												$str_add
												") or die(mysql_error());
					$rs_soma= mysql_fetch_object($result_soma);
					
					/*
					$result_soma= mysql_query("select avg(qtde_funcionarios) as media from op_limpa_pesagem
												where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   data_pesagem = '". $data ."'
												". $str ."
												order by data_pesagem desc, hora_pesagem desc
												");
					$rs_soma= mysql_fetch_object($result_soma);
					*/
					
					$result_dia= mysql_query("select rh_turnos_horarios.* from rh_turnos, rh_turnos_horarios
												where rh_turnos.id_turno = rh_turnos_horarios.id_turno
												and   rh_turnos.id_turno_index = '". $h ."'
												and   rh_turnos_horarios.id_dia = '$id_dia'
												");
					$rs_dia= mysql_fetch_object($result_dia);
					
					//$funcionarios_neste_turno_neste_dia= 2;
					
					$sql_pre="";
								
					/*$result_pre= mysql_query("select * from rh_ponto_producao
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   vale_dia = '". $data ."'
												and   id_turno_index = '". $h ."'
												and   id_departamento = '1'
												");
					$linhas_pre= mysql_num_rows($result_pre);
					*/
					
					//echo "pega_funcionarios_trabalhando_retroativo($_SESSION[id_empresa], 1, $h, $data_mesmo, $data .' '. $rs_dia->entrada, $data .' '. $rs_dia->saida, $_POST[identificar], 6);<br />";
					
					//if (($_POST["denovo"]=="1") || ($linhas_pre==0)) {
						$funcionarios_neste_turno_neste_dia= pega_funcionarios_trabalhando_manual($_SESSION["id_empresa"], 1, $h, $data_mesmo);
					//}
					
					/*
					if ($linhas_pre>0) {
						
						$rs_pre= mysql_fetch_object($result_pre);
						
						if ($_POST["denovo"]=="1") {
							
							$sql_pre="update rh_ponto_producao
										set media = '". $funcionarios_neste_turno_neste_dia ."'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   vale_dia = '". $data ."'
										and   id_turno_index = '". $h ."'
										and   id_departamento = '1'
										";
						}
						else {
							$funcionarios_neste_turno_neste_dia= $rs_pre->media;
						}
						
					}
					else {				
						$sql_pre="insert into rh_ponto_producao
								(id_empresa, id_departamento, vale_dia, id_turno_index, media, id_usuario)
								values
								('". $_SESSION["id_empresa"] ."', '1', '". $data ."', '". $h ."', 
								'". $funcionarios_neste_turno_neste_dia ."', '". $_SESSION["id_usuario"] ."')
								";
					}
					
					if ($sql_pre!="") {
						$result_fixa= mysql_query($sql_pre) or die(mysql_error());
					}
					*/
					
					
					$soma= $rs_soma->soma;
					$soma_vetor[$h]= $soma;
					
					$total_dia+= $soma;
					$total_turno[$h]+= $soma;
					
					$total_semana+= $soma;
					
					$total_turno_semana[$h]+= $soma;
					
					/*if ($funcionarios_neste_turno_neste_dia!=0) {
						$media_turno_aqui= ($soma/$funcionarios_neste_turno_neste_dia);
						$media_turno_aqui_vetor[$h]= $media_turno_aqui;
						
						//$media_dia+= $media_turno_aqui;
						//$media_turno[$h]+= $media_turno_aqui;
						
						$media_turno_semana[$h]+= $media_turno_aqui;
					}*/
					//echo $funcionarios_neste_turno_neste_dia ." ";
					$funcionarios_neste_turno_neste_dia_vetor[$h]= $funcionarios_neste_turno_neste_dia;
					
					//---
					
					/*
					$pdf->Cell($largura, 0.5, fnum($soma), 1, 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 7);
					
					$pdf->Cell($largura-0.7, 0.5, fnum($media_turno_aqui), "TLB", 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 6);
					$pdf->Cell(0.7, 0.5, "(". fnum($funcionarios_neste_turno_neste_dia) .")", "RTB", 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 7);
					*/
					
					$total_semana_turno_considerado[$h]++;
					$total_mes_turno_considerado[$h]++;
					
				}//fim dia considerado
				
				$h++;
			}//fim while
			
			//fazendo denovo pra adicionar o peso da costura
			
			$total_dia_original= $total_dia;
			
			$h=1;
			
			while ($vetor_turno[$h]) {
				
				$result_des= mysql_query("select count(*) as linhas from rh_ponto_producao_desconsiderados
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_departamento = '1'
											and   id_turno_index = '". $h ."'
											and   vale_dia = '". $data_mesmo ."'
											and   desconsiderado = '1'
											");
				$rs_des= mysql_fetch_object($result_des);
				
				//desconsiderando este turno...
				if ($rs_des->linhas>0) {
					$pdf->Cell($largura, 0.5, "-", 1, 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 7);
					
					$pdf->Cell($largura-0.7, 0.5, "-", "TLB", 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 6);
					$pdf->Cell(0.7, 0.5, "(-)", "RTB", 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 7);
				}
				else {
					if ($total_dia_original>0) $turno_percent[$h]= ($soma_vetor[$h]*100)/$total_dia_original;
					else $turno_percent[$h]=0;
					
					$soma_costura_adicional[$h]= ($rs_soma_costura->soma_costura*$turno_percent[$h])/100;
					
					//echo $soma_vetor[$h] .'* 100 /'. $total_dia_original .' = '. $turno_percent[$h] ." <br />";
					
					$total_dia+= $soma_costura_adicional[$h];
					$total_turno[$h]+= $soma_costura_adicional[$h];
					
					$total_semana+= $soma_costura_adicional[$h];
					
					$total_turno_semana[$h]+= $soma_costura_adicional[$h];
					
					if ($funcionarios_neste_turno_neste_dia_vetor[$h]>0) {
						
						$media_turno_aqui= (($soma_vetor[$h]+$soma_costura_adicional[$h])/$funcionarios_neste_turno_neste_dia_vetor[$h]);
						//$media_turno_aqui_vetor[$h]= $media_turno_aqui;
						
						$media_dia+= $media_turno_aqui;
						$media_turno[$h]+= $media_turno_aqui;
						
						$media_turno_semana[$h]+= $media_turno_aqui;
					}
					
					$pdf->SetFont('ARIALNARROW', '', 7);
					
					$pdf->Cell($largura, 0.5, fnum($soma_vetor[$h]+$soma_costura_adicional[$h]), 1, 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 7);
					
					$pdf->Cell($largura-0.7, 0.5, fnum($media_turno_aqui), "TLB", 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 6);
					$pdf->Cell(0.7, 0.5, "(". fnum($funcionarios_neste_turno_neste_dia_vetor[$h]) .")", "RTB", 0, "C", $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 7);
				}
				
				$h++;
			}//fim while
			
			$media_mes+= $media_dia/$total_turnos;
			$media_semana+= $media_dia/$total_turnos;
			
			$pdf->Cell(1.5, 0.5, fnum($total_dia), 1, 0, "C", $fill);
			$pdf->Cell(1.5, 0.5, fnum($media_dia/$total_turnos), 1, 1, "C", $fill);
			
			$total_dias_considerados_semana++;
			$total_dias_considerados_soh_semana++;
		}//fim else
		
		$total_mes+= $total_dia;
		
		$total_dias_considerados++;
		
		//é domingo, vamos fechar a produção da semana
		if ($id_dia==0) {
			
			$pdf->SetFillColor(210,210,210);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			$pdf->Cell(2, 0.5, "TOTAL SEMANA", 1, 0, "L", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			
			$result_turnos= mysql_query("select * from rh_turnos, rh_departamentos
											where rh_turnos.id_departamento = rh_departamentos.id_departamento
											and   rh_departamentos.id_empresa= '". $_SESSION["id_empresa"] ."'
											and   rh_turnos.id_departamento = '1'
											and   rh_turnos.fixo = '1'
											order by rh_turnos.ordem asc
											") or die(mysql_error());
			$total_turnos= mysql_num_rows($result_turnos);
			
			$largura=(12/$total_turnos)/2;
			
			$j=1;
			$t=1;
			while ($rs_turnos= mysql_fetch_object($result_turnos)) {
				
				//echo $total_semana_turno_considerado[$t] ." ";
				
				if ($total_semana_turno_considerado[$t]>0) $resultado2= $media_turno_semana[$j]/$total_semana_turno_considerado[$t];
				else $resultado2=0;
		
				$pdf->Cell($largura, 0.5, fnum($total_turno_semana[$j]) ." kg", 1, 0, "C", 1);
				$pdf->Cell($largura, 0.5, fnum($resultado2), 1, 0, "C", 1);
				
				$media_turno_semana[$j]=0;
				
				$total_semana_turno_considerado[$t]=0;
				
				$t++;
				$j++;
			}
			
			$total_dias_considerados_semana_e_plantao= $total_dias_considerados_semana+$total_dias_considerados_plantao;
			
			if ($total_dias_considerados_semana_e_plantao>0) $resultado3= $media_semana/$total_dias_considerados_semana_e_plantao;
			else $resultado3=0;
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			$pdf->Cell(1.5, 0.5, fnum($total_semana) ." kg", 1, 0, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(1.5, 0.5, fnum($resultado3), 1, 1, "C", 1);
			
			$pdf->LittleLn();
			
			$pdf->SetFillColor(240, 240, 240);
			
			unset($total_turno_semana);
			$total_semana=0;
			$media_semana=0;
			
			$total_dias_considerados_semana=0;
			$total_dias_considerados_plantao=0;
			
			$saldo_dias= $total_dias_mes-$k;
			
			//if ($saldo_dias<7) break;
		}
		
		//}
	}
	
	$pdf->SetFillColor(210,210,210);
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
	
	$pdf->LittleLn();
	$pdf->Cell(2, 0.5, "TOTAL", 1, 0, "L", 1);
	
	$pdf->SetFont('ARIALNARROW', '', 7);
	
	$j=1;
	$result_turnos= mysql_query("select * from rh_turnos, rh_departamentos
									where rh_turnos.id_departamento = rh_departamentos.id_departamento
									and   rh_departamentos.id_empresa= '". $_SESSION["id_empresa"] ."'
									and   rh_turnos.id_departamento = '1'
									and   rh_turnos.fixo = '1'
									order by rh_turnos.ordem asc
									") or die(mysql_error());
	$total_turnos= mysql_num_rows($result_turnos);
	
	$largura=(12/$total_turnos)/2;
	
	$t=1;
	while ($rs_turnos= mysql_fetch_object($result_turnos)) {
		
		if ($total_mes_turno_considerado[$t]>0) $resultado2= $media_turno[$j]/$total_mes_turno_considerado[$t];
		else $resultado2=0;
		
		$pdf->Cell($largura, 0.5, fnum($total_turno[$j]) ." kg", 1, 0, "C", 1);
		$pdf->Cell($largura, 0.5, fnum($resultado2), 1, 0, "C", 1);
		
		$t++;
		$j++;
	}
	
	if ($total_dias_considerados>0) $resultado3= $media_mes/$total_dias_considerados;
	else $resultado3=0;
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
	$pdf->Cell(1.5, 0.5, fnum($total_mes) ." kg", 1, 0, "C", 1);
	
	$pdf->SetFont('ARIALNARROW', '', 7);
	$pdf->Cell(1.5, 0.5, fnum($resultado3), 1, 1, "C", 1);
	
	$pdf->AliasNbPages(); 
	$pdf->Output("producao_limpa_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>
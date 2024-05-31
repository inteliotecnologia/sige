<?
if (!$conexao)
	require_once("conexao_ponto.php");

require_once("funcoes.php");

header("Content-type: text/html; charset=iso-8859-1", true);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$arruma= mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
//$arruma= mktime(4, 12, 44, 11, 03, 2010);

if ($_SERVER["REMOTE_ADDR"]!="192.168.1.95") $rastrea=1;
else $rastrea=0;

$rastrea_str="";

if (isset($_GET["carregaPagina"])) {
	require_once("index2.php");
}

if (isset($_GET["buscaTempo"])) {
	echo date("d/m/Y", $arruma) ."<br />". date("H:i", $arruma);
}

if (isset($_GET["submetePonto"])) {
	echo "<script language=\"javascript\">clearTimeout(temporizador);</script>";
	
	$var=0;
	inicia_transacao();
	
	$result_pre1 = mysql_query("select * from rh_cartoes where numero_cartao = '". $_GET["cartao"] ."' ");
	if (!$result_pre1) $var++;
	
	//cartão não encontrado
	if (mysql_num_rows($result_pre1)==0) {
		$rastrea_str.="1.";
		
		$erro=1;
		include("_ponto/__msg.php");
		die();
	}
	//cartão encontrado
	else {
		$rastrea_str.="2.";
		
		$rs_pre1= mysql_fetch_object($result_pre1);
		$id_cartao= $rs_pre1->id_cartao;
		
		$result_pre2 = mysql_query("select * from rh_cartoes, rh_funcionarios, pessoas
										where rh_cartoes.numero_cartao = ". $_GET["cartao"] ."
										and   rh_cartoes.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_funcionarios.status_funcionario <> '0'
										and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
										");
		if (!$result_pre2) $var++;
		$linhas_pre2= mysql_num_rows($result_pre2);
		
		//funcionário inativo
		if ($linhas_pre2==0) {
			$rastrea_str.="3.";
			
			$erro= 2;
			include("_ponto/__msg.php");
			die();
		}
		//funcionário ativo
		else {
			$rastrea_str.="4.";
			
			$rs_pre2= mysql_fetch_object($result_pre2);
			$id_funcionario= $rs_pre2->id_funcionario;
			
			//se cartão de supervisor
			if ($rs_pre1->tipo_cartao==2) {
				$rastrea_str.="5.";
				
				//se passar outro cartÃo de supervisor
				if ($_GET["id_supervisor"]!="0") {
					$rastrea_str.="6.";
					
					$erro=3;
					include("_ponto/__msg.php");
					die();
				}
				//se passar um cartão de supervisor pela primeira vez
				else {
					$rastrea_str.="7.";
					
					$nome_supervisor= explode(" ", $rs_pre2->nome_rz);
					require_once("_ponto/__supervisor.php");
					die();
				}
			}
			//cartão de funcionário passou, agora é necessário verificar o horário
			else {
				$rastrea_str.="8.";
				
				$dia_atual= date("d", $arruma); $mes_atual= date("m", $arruma); $ano_atual= date("Y", $arruma);
				$hora_atual= date("H", $arruma); $minuto_atual= date("i", $arruma); $segundo_atual= date("s", $arruma);
				$dia_semana_atual= date("w", $arruma);
				$dia_semana_anterior= date("w", mktime(0, 0, 0, $mes_atual, $dia_atual-1, $ano_atual));
				
				$data_atual_mk= $arruma;
				$data_atual= date("Y-m-d", $arruma);
				$horario_atual= date("H:i:s", $arruma);
				
				$data_anterior= date("Y-m-d", mktime(0, 0, 0, $mes_atual, $dia_atual-1, $ano_atual));
				$data_posterior= date("Y-m-d", mktime(0, 0, 0, $mes_atual, $dia_atual+1, $ano_atual));
				$vale_dia= $data_atual;
				$data_intervalo= $data_atual;
				
				$hl=0;
				
				$result_vinculado= mysql_query("select * from rh_carreiras where id_funcionario = '$id_funcionario'");
				if (mysql_num_rows($result_vinculado)==0) {
					$rastrea_str.="9.";
					
					$erro=11;
					include("_ponto/__msg.php");
					die();
				}
				
				$sql_horario= "select * from rh_funcionarios, rh_carreiras, rh_turnos, rh_turnos_horarios
												where rh_funcionarios.id_funcionario = '$id_funcionario'
												and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
												and   rh_carreiras.atual = '1'
												and   rh_carreiras.id_turno = rh_turnos.id_turno
												and   rh_turnos.id_turno = rh_turnos_horarios.id_turno
												and   rh_turnos_horarios.id_dia = ";
				
				$result_horario= mysql_query($sql_horario . $dia_semana_atual);
				
				if ( (mysql_num_rows($result_horario)==0) && ($_GET["id_supervisor"]=="") ) {
					$rastrea_str.="10.";
					
					$erro=10;
					include("_ponto/__msg.php");
					die();
				}
				
				$rs_horario= mysql_fetch_object($result_horario);
				$turnante= $rs_horario->turnante;
				$id_departamento= $rs_horario->id_departamento;
				
				if ($turnante==1) {
					$rastrea_str.="11.";
					
					//pegar alguma possível ação do funcionário no dia anterior
					$result_anterior= mysql_query("select * from rh_ponto
													where id_funcionario = '$id_funcionario'
													and   vale_dia = '". $data_anterior ."'
													order by data_batida desc, hora desc");
					$total_entradas_saidas= mysql_num_rows($result_anterior);
					
					//se ontem ele entrou ou saiu pro "intervalo"
					if (($total_entradas_saidas%2)==1) {
						$rastrea_str.="12.";
						
						$rs_anterior= mysql_fetch_object($result_anterior);
						
						//echo $rs_anterior->data_batida .' '. $rs_anterior->hora ." | ". $data_atual .' '. $horario_atual;
						
						if (retorna_intervalo($rs_anterior->data_batida .' '. $rs_anterior->hora, $data_atual .' '. $horario_atual)<46800) {
							
							$rastrea_str.="12a.";
							
							$vale_dia= $data_anterior;
							$dia_semana_atual= $dia_semana_anterior;
							$data_entrada= $data_anterior;
							
							$ultima_data_batida= $rs_anterior->data_batida;
							$ultima_hora= $rs_anterior->hora;
						}
						//bateu ilhado no dia anterior, e está voltando no dia atual
						else {
							$rastrea_str.="12b.";
							
							//@mysql_free_result($result_anterior);
							
							//pegar alguma possível ação do funcionário no dia anterior
							$result_anterior= mysql_query("select * from rh_ponto
															where id_funcionario = '$id_funcionario'
															and   vale_dia = '". $vale_dia ."'
															order by data_batida desc, hora desc");
							$total_entradas_saidas= mysql_num_rows($result_anterior);
							
							//@mysql_free_result($result_anterior);
						}
					}
					//se o total de entradas e/ou saidas do dia anterior for par
					//nao entrou, ou entrou e saiu, começa a valer o dia de hoje
					else {
						$rastrea_str.="13.";
						// ----------------------------------- TESTE ------------------------------------
						if ($hora_atual==0) {
							$rastrea_str.="14.";
							$data_entrada= $data_anterior;
						}
						else {
							$rastrea_str.="15.";
							//pegar alguma possível ação do funcionário no dia atual
							$result_atual= mysql_query("select * from rh_ponto
															where id_funcionario = '$id_funcionario'
															and   vale_dia = '". $vale_dia ."'
															order by data_batida desc, hora desc");
							$total_entradas_saidas= mysql_num_rows($result_atual);
							
							if ($total_entradas_saidas>0) {
								$rastrea_str.="16.";
								
								$rs_atual= mysql_fetch_object($result_atual);
								
								$ultima_data_batida= $rs_atual->data_batida;
								$ultima_hora=  $rs_atual->hora;
							}
							
							//@mysql_free_result($result_atual);
							
							$data_entrada= $data_atual;
						}
					}
					
					//@mysql_free_result($result_anterior);
				}
				//se não for turnante
				else {
					$rastrea_str.="17.";
					
					if ($hora_atual==0) {
						$rastrea_str.="18.";
						
						//se está batendo após a meia noite e tem HL no dia anterior
						if (tem_hl($id_funcionario, $dia_semana_anterior)) {
							$rastrea_str.="19.";
							
							//pegar alguma possível ação do funcionário no dia anterior
							$result_anterior= mysql_query("select * from rh_ponto
															where id_funcionario = '$id_funcionario'
															and   vale_dia = '". $data_anterior ."'
															order by data_batida desc, hora desc");
							$total_entradas_saidas= mysql_num_rows($result_anterior);
							$rs_anterior= mysql_fetch_object($result_anterior);
							
							$ultima_data_batida= $rs_anterior->data_batida;
							$ultima_hora= $rs_anterior->hora;
							
							//se ainda não entrou, ou se já entrou e saiu
							if (($total_entradas_saidas%2)==0) {
								$rastrea_str.="20.";
								
								//echo "está entrando pro turno do dia anterior";
								$vale_dia= $data_anterior;
								$dia_semana_atual= $dia_semana_anterior;
								$hl= 1;
								$data_entrada= $data_atual;
							
								$result_horario= mysql_query($sql_horario . $dia_semana_atual);
								$rs_horario= mysql_fetch_object($result_horario);
							}
							//se já entrou ou entrou/saiu
							else {
								$rastrea_str.="21.";
							}
							
							//@mysql_free_result($result_anterior);
						}
						else {
							$rastrea_str.="22.";
							
							//pegar alguma possível ação do funcionário no dia anterior
							$result_anterior= mysql_query("select * from rh_ponto
															where id_funcionario = '$id_funcionario'
															and   vale_dia = '". $data_anterior ."'
															order by data_batida desc, hora desc");
							$total_entradas_saidas= mysql_num_rows($result_anterior);
							$rs_anterior= mysql_fetch_object($result_anterior);
							
							$ultima_data_batida= $rs_anterior->data_batida;
							$ultima_hora= $rs_anterior->hora;
							
							$data_entrada= $data_anterior;
							
							//@mysql_free_result($result_anterior);
						}
						
						//se falta sair
						if ( (($total_entradas_saidas%2)==1) || ( ($total_entradas_saidas==2) && (retorna_intervalo($ultima_data_batida .' '. $ultima_hora, $data_atual .' '. $horario_atual)<5000) ) ) {
							$rastrea_str.="23.";
							
							$vale_dia= $data_anterior;
							$dia_semana_atual= $dia_semana_anterior;
							//$hl= 1;
							
							$result_horario= mysql_query($sql_horario . $dia_semana_atual);
							$rs_horario= mysql_fetch_object($result_horario);
						}
						//se tiver entrado e saído, vai voltar para o trabalho (fim de semana)

					}
					//nao é meia noite
					else {
						$rastrea_str.="24.";
						
						//pegar alguma possível ação do funcionário no dia anterior
						$result_anterior= mysql_query("select * from rh_ponto
														where id_funcionario = '$id_funcionario'
														and   vale_dia = '". $data_anterior ."'
														order by data_batida desc, hora desc");
						$total_entradas_saidas= mysql_num_rows($result_anterior);
						$rs_anterior= mysql_fetch_object($result_anterior);
						
						$ultima_data_batida= $rs_anterior->data_batida;
						$ultima_hora= $rs_anterior->hora;
						
						//se falta sair
						//se ele esquecer de sair no dia anterior, a entrada do dia seguinte vai pegar como sendo...
						//dae vai calcular o tamanho da jornada e ver se é muito longa
						//muito longa é quando é maior que 11h
						if (($total_entradas_saidas%2)==1) {
							$rastrea_str.="25.";
							
							if ( ($_GET["id_supervisor"]=="0") && (retorna_intervalo($rs_anterior->data_batida .' '. $rs_anterior->hora, $data_atual .' '. $horario_atual)>46800) ) {
								$rastrea_str.="26.";
								
								//echo retorna_intervalo($rs_anterior->data_batida .' '. $rs_anterior->hora, $data_atual .' '. $horario_atual);
								
								//echo "anterior: ". $rs_anterior->data_batida .' '. $rs_anterior->hora ."<br>"; 
								//echo "atual: ". $data_atual .' '. $horario_atual;
								
								$erro=8;
								include("_ponto/__msg.php");
								die();
							}
							else {
								$rastrea_str.="27.";
								
								//echo "está saindo do turno do dia anterior";
								$vale_dia= $data_anterior;
								$dia_semana_atual= $dia_semana_anterior;
								$hl= 1;
							
								$data_entrada= $data_atual;
							
								$result_horario= mysql_query($sql_horario . $dia_semana_atual);
								$rs_horario= mysql_fetch_object($result_horario);
							}
						}
						//se ainda não entrou, entrou e foi embora OU se entrou e foi pro intervalo
						else {
							$rastrea_str.="28.";
							
							$diferenca_ultima_saida= @retorna_intervalo($ultima_data_batida .' '. $ultima_hora, $data_atual .' '. $horario_atual);
							
							//echo $data_atual .' '. $horario_atual;
							
							//voltando do intervalo dpois da meia noite
							
							//aqui podia dar erro qdo a pessoa está num turno regular, de dia e no dia anterior só veio de manhã
							//assim faz o teste se entrou e saiu no dia anterior
							//se tem intervalo no dia anterior
							//e se, desde a última saída, passaram-se menos de 11h (se forem mais de 11h está o dia atual)
							
							if ( ($total_entradas_saidas==2) && (tem_intervalo_no_dia($rs_horario->id_intervalo, $dia_semana_anterior)) && ($diferenca_ultima_saida<39600) ) {
								$rastrea_str.="29.";
								
								$vale_dia= $data_anterior;
								$dia_semana_atual= $dia_semana_anterior;
								$data_entrada= $data_anterior;
								
								$data_intervalo= $data_anterior;
							
								$result_horario= mysql_query($sql_horario . $dia_semana_atual);
								$rs_horario= mysql_fetch_object($result_horario);
							}
							else {
								$rastrea_str.="30.";
								
								//caso o indivíduo se atrase no turno da madrugada e o supervisor autorize sua entrada
								//se for da 1h em diante
								/*if ( ($total_entradas_saidas==0) && ($diferenca_ultima_saida>44400) ) {
									$vale_dia= $data_anterior;
									$dia_semana_atual= $dia_semana_anterior;
									$data_entrada= $data_anterior;
								
									$result_horario= mysql_query($sql_horario . $dia_semana_atual);
									$rs_horario= mysql_fetch_object($result_horario);
								}
								else {*/
									//pegar alguma possível ação do funcionário no dia atual
									$result_atual= mysql_query("select * from rh_ponto
																	where id_funcionario = '$id_funcionario'
																	and   vale_dia = '". $vale_dia ."'
																	order by data_batida desc, hora desc");
									$total_entradas_saidas= mysql_num_rows($result_atual);
									
									if ($total_entradas_saidas>0) {
										$rastrea_str.="31.";
										
										$rs_atual= mysql_fetch_object($result_atual);
										
										$ultima_data_batida= $rs_atual->data_batida;
										$ultima_hora= $rs_atual->hora;
									}
									
									if (tem_hl($id_funcionario, $dia_semana_atual)) {
										$rastrea_str.="32.";
										
										$data_entrada= $data_posterior;
									}
									else {
										//pegar alguma possível ação do funcionário no dia atual
										$result_anterior= mysql_query("select * from rh_ponto
																		where id_funcionario = '$id_funcionario'
																		and   vale_dia = '". $data_anterior ."'
																		order by data_batida desc, hora desc");
										$total_entradas_saidas_anterior= mysql_num_rows($result_anterior);
										
										//entrou e saiu pro intervalo e tá voltando no outro dia
										if ( ($total_entradas_saidas_anterior==2) && ($diferenca_ultima_saida<5000) ) {
											$rastrea_str.="33a.";
											
											$vale_dia= $data_anterior;
											$dia_semana_atual= $dia_semana_anterior;
											$data_entrada= $data_anterior;
										}
										else {
											$rastrea_str.="33b.";
										
											$data_entrada= $data_atual;
										}
										
										//@mysql_free_result($result_anterior);
									}
									
									//@mysql_free_result($result_atual);
								//}
							}
						}
						
						//@mysql_free_result($result_anterior);
					}
				}//fim else não turnante
				
				//se estiver passando o cartão noraml... sem estar sendo autorizado por um supervisor
				if ($_GET["id_supervisor"]=="0") {
					$rastrea_str.="33c.";
					//se a escala do mes corrente está feita
					if (tem_escala_mes($vale_dia, $id_departamento)) {
						$rastrea_str.="33d.";
						//procurando o dia de trabalho anterior ao dia atual
						$result_escala= mysql_query("select * from rh_escala
														where id_funcionario = '". $id_funcionario ."'
														and   trabalha = '1'
														and   data_escala = '". $vale_dia ."'
														");
						$rs_escala= mysql_fetch_object($result_escala);
						
						if (mysql_num_rows($result_escala)==0) {
							$rastrea_str.="33e.";
							
							$erro= 13;
							require_once("_ponto/__msg.php");
							die();
						}
						
						//@mysql_free_result($result_escala);
					}
				}
				
				
				/*
				//se estiver passando o cartÃo noraml... sem estar sendo autorizado por um supervisor
				if ($_GET["id_supervisor"]=="0") {
					//se a escala do mes corrente está feita
					if (tem_escala_mes($vale_dia, $id_departamento)) {
						//procurando o dia de trabalho anterior ao dia atual
						$result_escala= mysql_query("select * from rh_escala
														where id_funcionario = '". $id_funcionario ."'
														and   trabalha = '1'
														and   data_escala < '". $vale_dia ."'
														order by data_escala desc limit 1
														");
						$rs_escala= mysql_fetch_object($result_escala);
						
						//echo $rs_escala->data_escala;
						
						//procurando alguma batida do dia anterior de trabalho
						//(para ver se ele trabalhou mesmo no dia anterior
						$result_ponto_escala= mysql_query("select * from rh_ponto
															where id_funcionario = '". $id_funcionario ."'
															and   data_batida = '". $rs_escala->data_escala ."'
															");
						
						//se nao encontrou nenhum registro no dia anterior, que ele deveria ter trabalhado
						//...vai trancar a entrada, só entrando com um supervisor liberando
						if (mysql_num_rows($result_ponto_escala)==0) {
							$erro= 12;
							require_once("_ponto/__msg.php");
							die();
						}
					}
				}
				*/
				//echo $rs_horario->id_intervalo; die();
				//echo "\$total_entradas_saidas: ". $total_entradas_saidas ." | ";
				//echo $data_entrada ." ". $rs_horario->entrada ."<br><br>";
				
				//pegar alguma possível ação do funcionário no dia atual
				$result_ultima_batida= mysql_query("select * from rh_ponto
													where id_funcionario = '$id_funcionario'
													order by data_batida desc, hora desc
													limit 1
													");
				$rs_ultima_batida= mysql_fetch_object($result_ultima_batida);
				
				switch ($total_entradas_saidas) {
					
					//está entrando pela primeira vez agora
					case 0:
							//se saiu e voltou em uma diferença de 1h +-, quer dizer que está no mesmo dia...
							$diferenca_intervalo_provavel= retorna_intervalo($data_atual ." ". $horario_atual, $rs_ultima_batida->data_batida ." ". $rs_ultima_batida->hora);
							
							if (($diferenca_intervalo_provavel>=-4400) && ($diferenca_intervalo_provavel<=4400)) $vale_dia= $data_anterior;
							
							$rastrea_str.="35.";
							//se estiver no intervalo de +/- 10 minutos da primeira entrada ou da saída do intervalo ou se for turnante, bate a entrada
							if ( (pode_bater($data_entrada ." ". $rs_horario->entrada)) || ($turnante==1) ) {
								$rastrea_str.="36.";
								
								$bateu= bate_ponto($id_funcionario, $data_atual, $horario_atual, 1, NULL, $hl, $vale_dia, $turnante);
								
								@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, strip_tags(entrada_saida(1)), 1, $_SERVER["REMOTE_ADDR"], 1, $bateu);
								
								if (!$bateu) $var++;
							}
							else {
								$rastrea_str.="37.";
								
								if ($_GET["id_supervisor"]!="0") {
									$rastrea_str.="38.";
									
									$bateu= bate_ponto($id_funcionario, $data_atual, $horario_atual, 1, $_GET["id_supervisor"], $hl, $vale_dia, $turnante);
									
									@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, strip_tags(entrada_saida(1)), 1, $_SERVER["REMOTE_ADDR"], 2, $bateu);
									
									if (!$bateu) $var++;
								}
								else {
									$rastrea_str.="39.";
									
									$erro= 5;
									require_once("_ponto/__msg.php");
									die();
								}
							}
					break;
					//está voltando do intervalo (se tiver)
					case 2:
						$rastrea_str.="40.";
						
						//teste 60 segs
						if (retorna_intervalo($ultima_data_batida .' '. $ultima_hora, $data_atual .' '. $horario_atual)<60) {
							$rastrea_str.="41.";
							
							$erro= 4;
							include("_ponto/__msg.php");
							die();
						}
						else {
							$rastrea_str.="42.";
							
							//se for turnante
							if ($turnante==1) {
								$rastrea_str.="43.";
								
								//se tem intervalo no dia...
								if (($_GET["id_supervisor"]!="0") || (tem_intervalo_no_dia($rs_horario->id_intervalo, $dia_semana_atual))) {
									$rastrea_str.="44.";
									
									$bateu= bate_ponto($id_funcionario, $data_atual, $horario_atual, 1, 0, $hl, $vale_dia, $turnante);
									
									@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, strip_tags(entrada_saida(1)), 1, $_SERVER["REMOTE_ADDR"], 3, $bateu);
									
									if (!$bateu) $var++;
								}
								else {
									$rastrea_str.="45.";
									
									$erro=7;
									include("_ponto/__msg.php");
									die();
								}
									
							}
							//se não for turnante
							else {
								$rastrea_str.="46.";
								
								//se está em um turno que tem intervalo...
								if (tem_intervalo_no_dia($rs_horario->id_intervalo, $dia_semana_atual)) {
									$rastrea_str.="47.";
									
									$hora_saiu= explode(':', $ultima_hora);
									$dia_saiu= explode('-', $ultima_data_batida);
									
									$intervalo_inicio_total= explode(' ', calcula_horario_intervalo('i', $rs_horario->id_intervalo, $dia_semana_atual, $data_intervalo));
									$intervalo_fim_total= explode(' ', calcula_horario_intervalo('f', $rs_horario->id_intervalo, $dia_semana_atual, $data_intervalo));
									
									$intervalo_inicio_data= explode('-', $intervalo_inicio_total[0]);
									$intervalo_inicio_horario= explode(':', $intervalo_inicio_total[1]);
									
									$intervalo_fim_data= explode('-', $intervalo_fim_total[0]);
									$intervalo_fim_horario= explode(':', $intervalo_fim_total[1]);
									
									$hora_saiu_mk= mktime($hora_saiu[0], $hora_saiu[1], $hora_saiu[2], $dia_saiu[1], $dia_saiu[2], $dia_saiu[0]);
									
									$intervalo_inicio_mk= mktime($intervalo_inicio_horario[0], $intervalo_inicio_horario[1], $intervalo_inicio_horario[2], $intervalo_inicio_data[1], $intervalo_inicio_data[2], $intervalo_inicio_data[0]);
									$intervalo_fim_mk= mktime($intervalo_fim_horario[0], $intervalo_fim_horario[1], $intervalo_fim_horario[2], $intervalo_fim_data[1], $intervalo_fim_data[2], $intervalo_fim_data[0]);
									
									/*echo "Hora saiu:". date("d/m/Y H:i:s", $hora_saiu_mk) ."<br>";
									echo "Início:". date("d/m/Y H:i:s", $intervalo_inicio_mk) ."<br>";
									echo "Fim:". date("d/m/Y H:i:s", $intervalo_fim_mk) ."<br>";*/
									
									//se saiu antes do horário do intervalo...
									if ($hora_saiu_mk<($intervalo_inicio_mk-600)) {
										$rastrea_str.="48.";
										
										//tem que retornar na hora do fim do intervalo (+- 10)
										$hora_retorno_mk= $intervalo_fim_mk;
									//se saiu depois do horário do intervalo
									}
									else {
										$rastrea_str.="49.";
										//tem que retornar 1 hora após a saída
										$hora_retorno_mk= $hora_saiu_mk+5400;
									}
									
									$hora_retorno_formatada= date("Y-m-d H:i:s", $hora_retorno_mk);
									$intervalo_fim_formatado= date("Y-m-d H:i:s", $intervalo_fim_mk);
									
									//echo $hora_retorno_formatada;
									//echo $intervalo_fim_formatado;
									
									//se estiver no intervalo de +/- 10 minutos da primeira entrada ou da saída do intervalo ou se for turnante, bate a entrada
									if ( (pode_bater_ou_menor($hora_retorno_formatada)) || (pode_bater_ou_menor($intervalo_fim_formatado)) || ($turnante==1) ) {
										$rastrea_str.="50.";
										
										$bateu= bate_ponto($id_funcionario, $data_atual, $horario_atual, 1, NULL, $hl, $vale_dia, $turnante);
										
										@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, strip_tags(entrada_saida(1)), 1, $_SERVER["REMOTE_ADDR"], 4, $bateu);
										
										if (!$bateu) $var++;
									}
									else {
										$rastrea_str.="51.";
										
										if ($_GET["id_supervisor"]!="0") {
											$rastrea_str.="52.";
											
											$bateu= bate_ponto($id_funcionario, $data_atual, $horario_atual, 1, $_GET["id_supervisor"], $hl, $vale_dia, $turnante);
											@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, strip_tags(entrada_saida(1)), 1, $_SERVER["REMOTE_ADDR"], 6, $bateu);
											
											if (!$bateu) $var++;
										}
										else {
											$rastrea_str.="53.";
											
											$erro= 9;
											require_once("_ponto/__msg.php");
											die();
										}
									}
							
									/*
									if ($data_atual_mk>$hora_retorno+600)
										echo "vc está atrasado";
									else
										echo "voltando do intervalo";						
									*/
									//echo "Retorno às:". date("d/m/Y H:i:s", $hora_retorno) ."<br>";
								}
								//está tentando entrar, após ter saído no meio do expediente
								else {
									$rastrea_str.="54.";
									
									//provavelmente hora extra
									if ($_GET["id_supervisor"]!="0") {
										$rastrea_str.="55.";
										
										$bateu= bate_ponto($id_funcionario, $data_atual, $horario_atual, 1, $_GET["id_supervisor"], $hl, $vale_dia, $turnante);
										@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, strip_tags(entrada_saida(1)), 1, $_SERVER["REMOTE_ADDR"], 7, $bateu);
										if (!$bateu) $var++;
									}
									else {
										$rastrea_str.="56.";
										
										$erro=7;
										require_once("_ponto/__msg.php");
										die();
									}
								}
							}//fim else não turnante
						}//fim else sem problemas 90 segs
					break;
					//OPERAÇÕES DE SAÍDA
					case 4:
					case 6:
					case 8:
					case 10:
					case 12:
					case 14:
							//provavelmente hora extra
							if ($_GET["id_supervisor"]!="0") {
								$rastrea_str.="56b.";
								
								$bateu= bate_ponto($id_funcionario, $data_atual, $horario_atual, 1, $_GET["id_supervisor"], $hl, $vale_dia, $turnante);
								@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, strip_tags(entrada_saida(1)), 1, $_SERVER["REMOTE_ADDR"], 7, $bateu);
								if (!$bateu) $var++;
							}
							else {
								$rastrea_str.="57.";
								
								$erro=7;
								require_once("_ponto/__msg.php");
								die();
							}
					break;
					case 1:
					case 3:
					case 5:
					case 7:
					case 9:
					case 11:
							$rastrea_str.="58.";
							
							/*if ($_GET["id_supervisor"]!="0") {
								$erro= 6;
								include("_ponto/__msg.php");
								die();
							}
							else {*/
								//se o ultimo ponto batido foi há menos que 90 segundos, dá um erro
								
								if (retorna_intervalo($ultima_data_batida .' '. $ultima_hora, $data_atual .' '. $horario_atual)<90) {
									$rastrea_str.="59.";
									
									$erro= 4;
									include("_ponto/__msg.php");
									die();
								}
								else {
									$rastrea_str.="60.";
									
									$bateu= bate_ponto($id_funcionario, $data_atual, $horario_atual, 0, NULL, $hl, $vale_dia, $turnante);
									
									@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, strip_tags(entrada_saida(0)), 0, $_SERVER["REMOTE_ADDR"], 8, $bateu);
									
									if (!$bateu) $var++;
								}
							//}
					break;
				}
				
				//@mysql_free_result($result_ultima_batida);
				
				/*	//tem mais de um registro no dia e o último é saída ou seja, está voltando do intervalo de fim de semana
					if ((mysql_num_rows($result_anterior)==2) && ($rs_horario->id_intervalo!="0")) {
						
						
					}
					else {
						
					}
				}
				*/
				//$result_insere= mysql_query("insert into ponto ()");
				
				//$erro= 9999;
				//include("_ponto/__msg.php");
			}
			
		}
		
		if ($rastrea) {
			?>
            <script language="javascript">
				var logo= document.getElementById("logo");
				logo.innerHTML='<?=$rastrea_str?>';
			</script>
            <?
		}
		
		//@mysql_free_result($result_pre2);
	}
	
	//echo $rastrea_str;
	
	finaliza_transacao($var);
	
	//@mysql_free_result($result_horario);
}

?>
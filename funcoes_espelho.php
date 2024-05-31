<?

function pega_dados_rh_consolidados($id_empresa, $id_departamento, $id_turno, $id_funcionario, $data_inicial, $data_final) {
	
	
	$data_atual_real= date("Y-m-d");
	
	if ( ($data_inicial!="") && ($data_final!="") ) {
		$data1= formata_data_hifen($data_inicial); $data1f= $data_inicial;
		$data2= formata_data_hifen($data_final); $data2f= $data_final;
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	
	//echo $data_inicial ." -> ". $data_final ."<br />";
	
	if ($id_funcionario!=0) $str .= " and   rh_funcionarios.id_funcionario =  '". $id_funcionario ."'";
	if ($id_departamento!=0) $str .= " and   rh_carreiras.id_departamento =  '". $id_departamento ."'";
	if ($id_turno!=0) $str .= " and   rh_carreiras.id_turno =  '". $id_turno ."'";
	
	$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_funcionarios.id_empresa = '". $id_empresa ."'
								". $str ."
								and   rh_funcionarios.status_funcionario = '1'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								order by rh_funcionarios.id_funcionario asc
								") or die(mysql_error());
	
	$geral_total_horas_diurnas= 0;
	$geral_total_horas_noturnas= 0;
	$geral_total_faltas= 0;
	$geral_total_extras_diurnas=0;
	$geral_total_extras_noturnas=0;
	
	/*$geral_total_faixa60[0]= 0;
	$geral_total_faixa100[0]= 0;
	$geral_total_faixa60[1]= 0;
	$geral_total_faixa100[1]= 0;*/
	
	$geral_total_faltas_dias=0;
	$geral_total_faltas_dias_justificadas=0;
	$geral_total_suspensoes=0;
	
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		
		$diferenca_data_mk= $data2_mk-$data1_mk;
		$diferenca= round(($diferenca_data_mk/60/60/24));
		
		//echo "|". $diferenca ."|<br />";
		
		$total_horas_diurnas=0;
		$total_horas_noturnas=0;
		$total_faltas=0;
		$total_faixa60[0]=0;
		$total_faixa100[0]=0;
		$total_faixa60[1]=0;
		$total_faixa100[1]=0;
		
		$total_faltas_dias= 0;
		$total_faltas_dias_justificadas=0;
		$total_suspensoes=0;
		
		//repetir todos os dias do intervalo
		for ($d= 0; $d<=$diferenca; $d++) {
			
			$e= $d+1;
			$calculo_data= $data1_mk+(86400*$d);
			
			$dia= date("d/m", $calculo_data);
			$data= date("d/m/Y", $calculo_data);
			$id_dia= date("w", $calculo_data);
			$vale_dia= date("Y-m-d", $calculo_data);
			
			//echo pega_funcionario($id_funcionario) . " ". desformata_data($vale_dia) ."<br />";
			
			//se a data aqui for ontem ou datas anteriores... (pra não contar dias futuros)
			if ($vale_dia<$data_atual_real) {
			
				$result_pre= mysql_query("select * from rh_consolidado
											where id_funcionario = '". $rs->id_funcionario ."'
											and   data = '". $vale_dia ."'
											");
				//tem dados consolidados
				if (mysql_num_rows($result_pre)>0) {
					$rs_pre= mysql_fetch_object($result_pre);
					
					$total_horas_diurnas+= $rs_pre->ht_diurnas;
					$total_horas_noturnas+= $rs_pre->ht_noturnas;
					$total_faltas+= $rs_pre->faltas;
					$total_faixa60[0]+= $rs_pre->he_diurnas_faixa1;
					$total_faixa100[0]+= $rs_pre->he_diurnas_faixa2;
					$total_faixa60[1]+= $rs_pre->he_noturnas_faixa1;
					$total_faixa100[1]+= $rs_pre->he_noturnas_faixa2;
					
					$total_faltas_dias+= $rs_pre->falta_nao_justificada;
					$total_faltas_dias_justificadas+= $rs_pre->falta_justificada;
					$total_suspensoes+=$rs_pre->suspensao;
					/*
					if ($rs_pre->falta_nao_justificada>0) {
						echo $rs->nome_rz ." faltou sem justificar em ". desformata_data($vale_dia) ."<br />";
					}
					if ($rs_pre->falta_justificada>0) {
						echo $rs->nome_rz ." faltou e justificou em ". desformata_data($vale_dia) ."<br />";
					}*/
				}
				else {
					
					$retorno_nao_consolidado= pega_dados_rh($id_empresa, 0, 0, $rs->id_funcionario, desformata_data($vale_dia), desformata_data($vale_dia));
					$novo= explode("@", $retorno_nao_consolidado);
					
					$total_horas_diurnas+= $novo[1];
					$total_horas_noturnas+= $novo[2];
					$total_faltas+= $novo[3];
					
					$total_faixa60[0]+= $novo[4];
					$total_faixa100[0]+= $novo[5];
					$total_faixa60[1]+= $novo[6];
					$total_faixa100[1]+= $novo[7];
					
					$total_faltas_dias+= $novo[8];
					$total_faltas_dias_justificadas+= $novo[9];
					$total_suspensoes+= $novo[10];
					
				}
			}
		}
		
		$geral_total_horas_diurnas+= $total_horas_diurnas;
		$geral_total_horas_noturnas+= $total_horas_noturnas;
		$geral_total_faltas+= $total_faltas;
		
		$geral_total_faixa60[0]+= $total_faixa60[0];
		$geral_total_faixa100[0]+= $total_faixa100[0];
		$geral_total_faixa60[1]+= $total_faixa60[1];
		$geral_total_faixa100[1]+= $total_faixa100[1];
		
		$geral_total_faltas_dias+= $total_faltas_dias;
		$geral_total_faltas_dias_justificadas+= $total_faltas_dias_justificadas;
		
		$geral_total_suspensoes+= $total_suspensoes;
		
		$i++;
	}
	
	
	//0- motivo (qdo for só um dia)
	//1- horas trabalhadas dirunas
	//2- horas trabalhadas noturnas
	//3- faltas em horas
	//4- horas extras diurnas 60%
	//5- horas extras diurnas 100%
	//6- horas extras noturnas 60%
	//7- horas extras noturnas 100%
	
	//8- faltas injustificadas em dias
	//9- faltas justificadas em dias
	//10- suspensões
	
	if ($geral_total_faixa60[0]<0) $geral_total_faixa60[0]=0;
	if ($geral_total_faixa100[0]<0) $geral_total_faixa100[0]=0;
	
	if ($geral_total_faixa60[1]<0) $geral_total_faixa60[1]=0;
	if ($geral_total_faixa100[1]<0) $geral_total_faixa100[1]=0;
	
	$retorno= $motivo . $motivo_bh .'@'; // 0
	$retorno .= $geral_total_horas_diurnas .'@'; // 1
	$retorno .= $geral_total_horas_noturnas .'@'; // 2
	$retorno .= $geral_total_faltas .'@'; // 3
	$retorno .= $geral_total_faixa60[0] .'@'; // 4
	$retorno .= $geral_total_faixa100[0] .'@'; // 5
	$retorno .= $geral_total_faixa60[1] .'@'; // 6
	$retorno .= $geral_total_faixa100[1] .'@'; // 7
	$retorno .= $geral_total_faltas_dias .'@'; // 8
	$retorno .= $geral_total_faltas_dias_justificadas .'@'; // 9
	$retorno .= $geral_total_suspensoes .'@'; // 10
	
	return($retorno);
	
}

function pega_dados_rh($id_empresa, $id_departamento, $id_turno, $id_funcionario, $data_inicial, $data_final) {
	
	$data_atual_real= date("Y-m-d");
	
	if ( ($data_inicial!="") && ($data_final!="") ) {
		$data1= formata_data_hifen($data_inicial); $data1f= $data_inicial;
		$data2= formata_data_hifen($data_final); $data2f= $data_final;
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	
	//echo $data_inicial ." -> ". $data_final ."<br />";
	
	if ($id_funcionario!=0) $str .= " and   rh_funcionarios.id_funcionario =  '". $id_funcionario ."'";
	if ($id_departamento!=0) $str .= " and   rh_carreiras.id_departamento =  '". $id_departamento ."'";
	if ($id_turno!=0) $str .= " and   rh_carreiras.id_turno =  '". $id_turno ."'";
	
	$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_funcionarios.id_empresa = '". $id_empresa ."'
								". $str ."
								and   rh_funcionarios.status_funcionario = '1'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								
								order by rh_funcionarios.id_funcionario asc
								") or die(mysql_error());
	
	$geral_total_horas_diurnas= 0;
	$geral_total_horas_noturnas= 0;
	$geral_total_faltas= 0;
	$geral_total_faixa60[0]= 0;
	$geral_total_faixa100[0]= 0;
	$geral_total_faixa60[1]= 0;
	$geral_total_faixa100[1]= 0;
	$geral_total_faltas_dias=0;
	$geral_total_faltas_dias_justificadas=0;
	$geral_total_suspensoes=0;
	
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		
		//setando variáveis que são de cada funcionário
		$total_he_normais_60[0]= 0; $total_he_normais_60[1]= 0;
		$total_he_normais_100[0]= 0; $total_he_normais_100[1]= 0;
		$total_he_dsr[0]= 0; $total_he_dsr[1]= 0;
		$total_he_folga[0]= 0; $total_he_folga[1]= 0;
		$total_he_feriado[0]= 0; $total_he_feriado[1]= 0;
		$total_faixa[0]= 0; $total_faixa[1]= 0;
	
		//echo "<br />". $rs->nome_rz ."<br />";
		
		$id_funcionario= $rs->id_funcionario;
		$id_turno= $rs->id_turno;
		
		for ($i=0; $i<=6; $i++) {
			$result_dia= mysql_query("select * from rh_turnos_horarios
										where id_turno = '". $id_turno ."'
										and   id_dia = '$i'
										");
			$rs_dia= mysql_fetch_object($result_dia);
			
			$jornada_entrada_hora= explode(':', $rs_dia->entrada);
			$jornada_saida_hora= explode(':', $rs_dia->saida);
			
			$d_jornada_entrada= 10;
			$d_jornada_saida= 10;
			
			if ($jornada_entrada_hora[0]>$jornada_saida_hora[0]) $d_jornada_saida++;
			
			$jornada_entrada[$i]= @mktime($jornada_entrada_hora[0], $jornada_entrada_hora[1], $jornada_entrada_hora[2], $m_jornada, $d_jornada_entrada, $a_jornada);
			$jornada_saida[$i]= @mktime($jornada_saida_hora[0], $jornada_saida_hora[1], $jornada_saida_hora[2], $m_jornada, $d_jornada_saida, $a_jornada);
			
			$jornada_entrada_hora2[$i]= @mktime($jornada_entrada_hora[0], $jornada_entrada_hora[1], $jornada_entrada_hora[2], 0, 0, 0);
			$jornada_saida_hora2[$i]= @mktime($jornada_saida_hora[0], $jornada_saida_hora[1], $jornada_saida_hora[2], 0, 0, 0);
			
			//@mysql_free_result($result_dia);
		}
		
		$num_operacoes= 4;
		
		$diferenca_data_mk= $data2_mk-$data1_mk;
		
		//if (($diferenca_data_mk)==0)
		//	$diferenca= 0;
		//else
		//	$diferenca= date("d", $diferenca_data_mk);
		
		$diferenca= round(($diferenca_data_mk/60/60/24));
		
		//if ($_SESSION["id_usuario"]==13) { echo "'". $data2_mk ." ~ ". $data1_mk ." = ". $diferenca_data_mk ." = ". $diferenca ." dias' <br />"; die(); }
		
		$hoje= date("Y-m-d");
		$hoje_mk= faz_mk_data2($hoje);
		
		$erro=0;
		
		$total_horas_diurnas=0;
		$total_horas_noturnas=0;
		$total_faltas=0;
		$total_extras_diurnas=0;
		$total_extras_noturnas=0;
		
		$total_faltas_dias= 0;
		$total_faltas_dias_justificadas=0;
		$total_suspensoes=0;
		
		$saldo_extras_diurnas=0;
		$saldo_extras_noturnas=0;
		
		$result_abandono= mysql_query("select distinct(rh_afastamentos.id_afastamento) as id_afastamento, rh_afastamentos.data_inicial
												from rh_afastamentos, rh_afastamentos_dias
												where rh_afastamentos.id_afastamento = rh_afastamentos_dias.id_afastamento
												and   rh_afastamentos.id_funcionario = '". $id_funcionario ."'
												and   rh_afastamentos.tipo_afastamento = 'b'
												") or die(mysql_error());
		$linhas_abandono= mysql_num_rows($result_abandono);
		if ($linhas_abandono>0)
			$rs_abandono= mysql_fetch_object($result_abandono);
		
		//repetir todos os dias do intervalo
		for ($d= 0; $d<=$diferenca; $d++) {
			
			$extras_diurnas=0;
			$extras_noturnas=0;
			$falta_justificada=0;
			$falta_nao_justificada=0;
			$suspensao=0;
			
			$total_he_normais_60_dia[0]= 0; $total_he_normais_60_dia[1]= 0;
			$total_he_normais_100_dia[0]= 0; $total_he_normais_100_dia[1]= 0;
			
			$total_he_dsr_dia[0]=0; $total_he_dsr_dia[1]=0;
			$total_he_folga_dia[0]=0; $total_he_folga_dia[1]=0;
			$total_he_feriado_dia[0]=0; $total_he_feriado_dia[1]=0;
			
			$total_saidas[$d]= 0;
			
			$e= $d+1;
			$calculo_data= $data1_mk+(86400*$d);
			
			$dia= date("d/m", $calculo_data);
			$data= date("d/m/Y", $calculo_data);
			$id_dia= date("w", $calculo_data);
			$vale_dia= date("Y-m-d", $calculo_data);
			
			//echo pega_funcionario($id_funcionario) . " ". desformata_data($vale_dia) ."<br />";
			
			//se a data aqui for ontem ou datas anteriores... (pra não contar dias futuros)
			//if ($vale_dia<$data_atual_real) {
			if ($hoje_mk>= $calculo_data) {
			
				$result_adm= mysql_query("select * from rh_carreiras
											where id_funcionario = '". $id_funcionario ."'
											and   id_acao_carreira = '1'
											") or die(mysql_error());
				$linhas_admissao= mysql_num_rows($result_adm);
				
				if ($linhas_admissao>0) {
					$rs_adm= mysql_fetch_object($result_adm);
					//echo $rs_adm->data; die();
					$data_admissao_mk= faz_mk_data($rs_adm->data);
				}
				
				//@mysql_free_result($linhas_admissao);
				
				$result_dem= mysql_query("select * from rh_carreiras
											where id_funcionario = '". $id_funcionario ."'
											and   id_acao_carreira = '2'
											") or die(mysql_error());
				$linhas_demissao= mysql_num_rows($result_dem);
				
				if ($linhas_demissao>0) {
					$rs_dem= mysql_fetch_object($result_dem);
					//echo $rs_adm->data; die();
					$data_demissao_mk= faz_mk_data($rs_dem->data);
					$demitido=1;
				} else $demitido=0;
				
				//@mysql_free_result($result_dem);
				
				//if ($_SESSION["tipo_usuario"]=="a")
				//	echo date("d/m/Y", $calculo_data) ." | ". date("d/m/Y H:i:s", $data_admissao_mk) ."<br /><br />";
				
				$data_atual_aqui= date("d/m/Y", $calculo_data);
				$data_admissao_aqui= date("d/m/Y", $data_admissao_mk);
				
				//vai mostrar o dia...
				//se a data atual for maior ou igual a data de admissao === ELE JÁ É FUNCIONÁRIO DA EMPRESA
				//ou
				//se o funcionário não foi demitido ou se foi demitido e a data atual é menor a data da demissão
				if ((($calculo_data>=$data_admissao_mk) || ($data_atual_aqui==$data_admissao_aqui)) && ((!$demitido) || (($demitido) && ($calculo_data<=$data_demissao_mk)) ) ) {
				
					$result_hor= mysql_query("select *, DATE_FORMAT(data_batida, '%d/%m/%Y') as data_batida2
												from rh_ponto
												where vale_dia= '$vale_dia'
												and   id_funcionario = '". $id_funcionario ."'
												order by data_batida, hora
												");
					$total_operacoes= mysql_num_rows($result_hor);
					
					$h=0;
					$z=0;
					$m= 0;
					unset($entrada); unset($saida);
					
					while ($rs_hor= mysql_fetch_object($result_hor)) {
						$horario[$z]= faz_mk_data_completa($rs_hor->data_batida2 .' '. $rs_hor->hora);
											
						if (($rs_hor->tipo==0) && (($rs->id_regime==2) || ($rs->id_regime==3))) {
							$total_saidas[$d]++;
							//echo $total_saidas[$d];
							//echo $intervalo_automatico[0] ." - ";
							//echo $intervalo_automatico[1];
							//echo $atual;
							
							if ( (($total_operacoes/$total_saidas[$d])==2) ) {
								//passando
								$intervalo_automatico= retorna_intervalo_automatico($horario[$z-1], $horario[$z]);
								
								for ($p=0; $p<2; $p++) {
									//se tem, pelo menos 5:40 trabalhadas... faz o intervalo
									if ($horario[$z]-$horario[$z-1]>=4500) {
										$h++;
									}
								}
							}//fim 
						}
						
						//*--------------------------------------------------------
						
						$data_hora_batida= $rs_hor->data_batida ." ". $rs_hor->hora;
						
						if ($rs_hor->tipo==1){ 
							$entrada[$m]= $data_hora_batida;
							
							$entrada_mk= faz_mk_hora($rs_hor->hora);
							$diferenca_entrada= ($jornada_entrada_hora2[$id_dia]-$entrada_mk);
							
							if (($diferenca_entrada>0) && ($diferenca_entrada<=600) )
								$entrada[$m]= $rs_hor->data_batida ." ". date("H:i:s", $jornada_entrada_hora2[$id_dia]);
						}
						else {
							$saida[$m]= $data_hora_batida;
							$m++;
						}
		
						$z++;
						$h++;
					}//fim while horarios
					
					if (($total_operacoes%2)==1) {
						$erro=1;
						$h++;
					}
					
					//@mysql_free_result($result_hor);
					
					//--- consultando escala
					
					$result_esc= mysql_query("select * from rh_escala
												where id_funcionario = '". $id_funcionario ."'
												and   data_escala = '". $vale_dia ."'
												") or die(mysql_error());
					$linhas_escala= mysql_num_rows($result_esc);
					
					//@mysql_free_result($result_esc);
					
					$result_feriado= mysql_query("select * from rh_feriados
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   data_feriado = '". $vale_dia ."'
												") or die(mysql_error());
					$linhas_feriado= mysql_num_rows($result_feriado);
					
					//@mysql_free_result($result_feriado);
					
					if (($linhas_abandono==0) || (($linhas_abandono>0) && ($vale_dia<$rs_abandono->data_inicial))) {
					
						//se nao bateu o ponto nesse dia
						if ($total_operacoes==0) {
							$result_afa= mysql_query("select * from rh_afastamentos_dias
														where id_funcionario = '". $id_funcionario ."'
														and   data = '". $vale_dia ."'
														") or die(mysql_error());
							$linhas_afastamento= mysql_num_rows($result_afa);
							$rs_afa= mysql_fetch_object($result_afa);
							
							//if ($id_funcionario==47) echo $linhas_afastamento;
							
							//echo "Q";
							
							if (($linhas_afastamento>0) && ($rs_afa->tipo_afastamento!='s')) {
								$tipo_afastamento= pega_tipo_afastamento_pelo_id_afastamento($rs_afa->id_afastamento);
								$motivo= strtoupper(pega_tipo_afastamento($tipo_afastamento));
								
								if (($rs_afa->tipo_afastamento!='f') && ($rs_afa->tipo_afastamento!='o') && ($rs_afa->tipo_afastamento!='p')) {
									
									//se for atestado, considerar primeiros 15 dias
									if ($rs_afa->tipo_afastamento=='a') {
										
										$result_afastamento= mysql_query("select distinct(rh_afastamentos.id_afastamento) as id_afastamento, rh_afastamentos.data_inicial
																			from rh_afastamentos, rh_afastamentos_dias
																			where rh_afastamentos.id_afastamento = rh_afastamentos_dias.id_afastamento
																			and   rh_afastamentos.id_funcionario = '". $id_funcionario ."'
																			and   rh_afastamentos_dias.data = '". $vale_dia ."'
																			") or die(mysql_error());
										$rs_afastamento= mysql_fetch_object($result_afastamento);
										
										$total_horas_diferenca_mk= abs(retorna_intervalo($rs_afastamento->data_inicial ." 14:00:00", $vale_dia ." 14:00:00"));
										$total_horas_diferenca= calcula_total_horas_ss($total_horas_diferenca_mk);
										$dias_diferenca= calcula_dias_pelas_horas($total_horas_diferenca);
									}
									
									
									//echo $vale_dia ." 14:00:00 ". $rs_afastamento->data_inicial ." 14:00:00 <br />" . retorna_intervalo($vale_dia ." 14:00:00", $rs_afastamento->data_inicial ." 14:00:00") ."<br /><br />";
										
									if (($rs_afa->tipo_afastamento!='a') || (($rs_afa->tipo_afastamento=='a') && ($dias_diferenca<15)) ) {
										
										//echo $vale_dia ." ". $rs_abandono->data_inicial ."<br />";
									
										$total_faltas_dias_justificadas++;
										
										$falta_justificada= 1;
										//echo pega_funcionario($id_funcionario) ." faltou justificadamente em ". desformata_data($vale_dia) ." (". $motivo .") <br />";
									}
								}
							}
							else {
								if ($linhas_afastamento>0) {
									$motivo= "SUSPENSÃO";
									$suspensao= 1;
									$total_suspensoes++;
								}
								else {
									//falta
									if ($linhas_escala==1) {
										$motivo= "FALTA";
										
										if ($vale_dia<$data_atual_real) {
											$total_faltas_dias++;
											
											$falta_nao_justificada=1;
											//echo pega_funcionario($id_funcionario) ." faltou em ". desformata_data($vale_dia) ." <br />";
										}
									}
									else {
										if (($id_dia==0) || ($id_dia==6))
											$motivo= "DSR";
										else
											$motivo= "FOLGA";
									}
								}
							}
							
							//@mysql_free_result($result_afa);
							
							$horas_diurnas= 0;
							$horas_noturnas= 0;
							$extras_diurnas= 0;
							$extras_noturnas= 0;
						}
						//se tem batidas neste dia
						else {
							$motivo_apriori= "";
							$motivo= "";
							
							$horas_trabalhadas= calcula_diurno_noturno($entrada, $saida);
							
							$horas_diurnas= $horas_trabalhadas[0];
							$horas_noturnas= $horas_trabalhadas[1];
							
							//se não deveria trabalhar este dia, é hora extra
							if (($linhas_escala==0) || ($linhas_feriado==1)) {
								$extras_diurnas= $horas_diurnas;
								$extras_noturnas= $horas_noturnas;
								
								//se for feriado, computa para horas extras em dsr
								if ($linhas_feriado==1) {
									$motivo= "HE (FERIADO)";
									$total_he_feriado[0]+= $extras_diurnas;
									$total_he_feriado[1]+= $extras_noturnas;
									
									$total_he_feriado_dia[0]+= $extras_diurnas;
									$total_he_feriado_dia[1]+= $extras_noturnas;
								}
								else {
									//se for fim de semana, computa para horas extras em dsr
									if (($id_dia==0) || ($id_dia==6)) {
										$motivo= "HE (DSR)";
										$total_he_dsr[0]+= $extras_diurnas;
										$total_he_dsr[1]+= $extras_noturnas;
										
										$total_he_dsr_dia[0]+= $extras_diurnas;
										$total_he_dsr_dia[1]+= $extras_noturnas;
									}
									//dia de semana, horas extras em folgas
									else {
										$motivo= "HE (FOLGA)";
										$total_he_folga[0]+= $extras_diurnas;
										$total_he_folga[1]+= $extras_noturnas;
										
										$total_he_folga_dia[0]+= $extras_diurnas;
										$total_he_folga_dia[1]+= $extras_noturnas;
									}
								}
							}
							//se trabalha este dia, são horas normais
							else {
								$extras_diurnas= 0;
								$extras_noturnas= 0;
							}
						}
					
						$total_horas_diurnas += $horas_diurnas;
						$total_horas_noturnas += $horas_noturnas;
						
						$jornada_diaria= pega_jornada_diaria($id_turno, $id_dia);
						
						$intervalo_dia= pega_duracao_intervalo_dia($id_turno, $id_dia);
						if ($intervalo_dia!=0) {
							$jornada_diaria= $jornada_diaria-$intervalo_dia; //*************************
							
							//echo "<strong>$vale_dia</strong>: ". calcula_total_horas($intervalo_dia) ."<br />";
							
							//if ($horas_diurnas>$horas_noturnas) $horas_diurnas+=$intervalo_dia;
							//else $horas_noturnas+=$intervalo_dia;
						}
						
						$total_horas= $horas_diurnas+$horas_noturnas;
						
						//se a pessoa trabalha neste dia, calcula a falta
						
						//se a pessoa trabalha neste dia, calcula a falta
						//ou se está suspensa
						if ((($linhas_escala==1) ) || ($rs_afa->tipo_afastamento=='s'))
							$calculo_faltas= ($jornada_diaria-$total_horas);//+$intervalo_dia;
						else
							$calculo_faltas= 0;
						
						
						//echo "<br>". $vale_dia ." - ". $calculo_faltas;
						
						//se trabalhou mais que a carga horária
						if ($calculo_faltas<=0) {
							$falta_string= "00:00:00";
							$falta_dia= 0;
						}
						else {
							//se veio trabalhar
							if ($total_operacoes>0) {
							//if ($motivo=="") {
								//20 minutos de atraso (entrada e saída)
								$desconto= 1200;
								//se o dia tem um intervalo de batida (adiciona mais 20minutos)
								if ($intervalo_dia!=0) {
									//$jornada_diaria= $jornada_diaria+$intervalo_dia;
									$desconto+=1200;
								}
								
								//se faltou até o limite (20 minutos ou 40, dependendo da batida)
								if ($calculo_faltas<=$desconto) {
									$falta_string= "00:00:00";
									$falta_dia= 0;
								}
								else {
									$falta_string= calcula_total_horas($calculo_faltas);
									$falta_dia= $calculo_faltas;
								}
							}
							//se não veio trabalhar, mas a falta foi justificada
							else {
								if (($motivo=="FALTA") || ($motivo=="SUSPENSÃO")) {
									/*if ($intervalo_dia!=0)
										$jornada_diaria= $jornada_diaria;//+$intervalo_dia; */
									//echo " motivo: ". $motivo ." | jornada diaria: ". $jornada_diaria ."<br><br>";
									$falta_string= calcula_total_horas($jornada_diaria);
									$falta_dia= $jornada_diaria;
									
									//echo $falta_dia ."<br>";
								}
								else {
									$falta_string= "00:00:00";
									$falta_dia= 0;
								}
							}
						}
						
						
						
						// --------------------------------------------------------------------------- calculo de horas extras
						//echo date("H:i:s", mktime(0, 0, $adicional, 0, 0, 0)); die();
						//echo date("d/m/Y H:i:s", $jornada_entrada[$id_dia]); die();
						$entrada_aqui[0]= date("Y-m-d H:i:s", $jornada_entrada[$id_dia]);
						$saida_aqui[0]= date("Y-m-d H:i:s", $jornada_saida[$id_dia]);
						
						$horas_jornada_periodo= calcula_diurno_noturno($entrada_aqui, $saida_aqui);
						
						//echo $horas_diurnas; die();
						//echo $horas_jornada_periodo[0] ." | ";
						//echo $horas_jornada_periodo[1] . " <br> ";
						
						//echo $jornada_diaria ."<br>";
						//echo "hd: ". $horas_diurnas ." hn: ". $horas_noturnas ."<br>";
						
						//se tem intervalo no dia...
						//a variavel recebe a jornada diaria total + o intervalo
						//if ($intervalo_dia!=0) $jornada_diaria_para_extra= $jornada_diaria;//+$intervalo_dia;
						//se nao tem intervalo no dia...
						//a variavel recebe a jornada diaria
						$jornada_diaria_para_extra= $jornada_diaria;//+$intervalo_dia;
						
						//total de horas trabalhadas no dia, no total...
						$total_horas_trabalhadas_dia= ($horas_diurnas+$horas_noturnas);
						
						$diferenca_horas_trabalhadas_jornada= $total_horas_trabalhadas_dia-$jornada_diaria_para_extra;
						
						//echo $total_horas_trabalhadas_dia; die();
						
						//echo $vale_dia ." -> horas trabalhadas: ". calcula_total_horas($total_horas_trabalhadas_dia) ." horas da jornada: ". calcula_total_horas($jornada_diaria_para_extra) ."<br>";
						
						//se o total de horas que foi trabalhada for maior que a jornada diária, a sobra é hora extra
						//e se não tem nenhuma extra noturna e diurna vindo de dsr, feriado ou folga
						if (($diferenca_horas_trabalhadas_jornada>=1200) && ($extras_diurnas==0) && ($extras_noturnas==0)) {
							//trabalha no periodo diurno
							//if ($horas_jornada_periodo[0]>0)
								
								//echo $vale_dia ." -> horas diurnas: ". calcula_total_horas($horas_diurnas) ." jornada diurna: ". calcula_total_horas($horas_jornada_periodo[0]) ."<br>";
								
								//$extras_diurnas= $diferenca_horas_trabalhadas_jornada;
								$extras_diurnas= ($horas_diurnas-$horas_jornada_periodo[0]);
								if (($extras_diurnas<=1200) || ($extras_diurnas>46800)) $extras_diurnas= 0;
								
								$extras_noturnas= ($horas_noturnas-$horas_jornada_periodo[1]);
								if (($extras_noturnas<=1200) || ($extras_noturnas>46800)) $extras_noturnas= 0;
								
								if (($extras_diurnas>0) && ($extras_diurnas>$diferenca_horas_trabalhadas_jornada)) $extras_diurnas= $diferenca_horas_trabalhadas_jornada;
								if (($extras_noturnas>0) && ($extras_noturnas>$diferenca_horas_trabalhadas_jornada)) $extras_noturnas= $diferenca_horas_trabalhadas_jornada;
								
								//echo $extras_diurnas;
								
								if ($intervalo_dia!=0) {
									$calculo_he_final= ($horas_diurnas+$horas_noturnas)-$jornada_diaria_para_extra;
									
									//se a diferença trabalhada for maior que 20 minutos...
									if ($calculo_he_final>1200) {
										//if ($extras_diurnas>=$extras_noturnas) $extras_noturnas+= $calculo_he_final;
										//else $extras_diurnas+= $calculo_he_final;
									}
								}
								
								//echo "horas diurnas: ". ($horas_diurnas) ." jornada diurna: ". ($horas_jornada_periodo[0]) ."<br><br />";
								//echo "horas noturnas: ". ($horas_noturnas) ." jornada noturna: ". ($horas_jornada_periodo[1]) ."<br>";
								
								/*
								if (($horas_noturnas<$horas_jornada_periodo[1]) && ($horas_diurnas>$horas_jornada_periodo[0]) && ($extras_diurnas>1200)) {
									//echo " !deu! ";
									
									$diferenca_noturna_aqui= $horas_jornada_periodo[1]-$horas_noturnas;
									$extras_diurnas -= $diferenca_noturna_aqui;
								}
								*/
						}
						else {
							if (($extras_diurnas==0) && ($extras_noturnas==0)) {
								$extras_diurnas=0;
								$extras_noturnas=0;
							}
						}
						
						//se for regime integral, pode ter hora extra quando tem intervalo
						if (($rs->id_regime==1) && ($motivo=="")) {
							//manipular extras como sendo diurnas
							if ($horas_diurnas>=$horas_noturnas) {
								$extras_diurnas= ($total_horas_trabalhadas_dia-$jornada_diaria);
								$extras_noturnas= 0;
							}
							//manipular extras como sendo noturnas
							else {
								$extras_diurnas= 0;
								$extras_noturnas= ($total_horas_trabalhadas_dia-$jornada_diaria);
							}
							
							if (($extras_diurnas<=1200) || ($extras_diurnas>46800)) $extras_diurnas= 0;
							if (($extras_noturnas<=1200) || ($extras_noturnas>46800)) $extras_noturnas= 0;
						}
						
						//-----------gambiarra
						if ($extras_diurnas>86400) $extras_diurnas -= 86400;
						if ($extras_noturnas>86400) $extras_noturnas -= 86400;
						
						
						//echo "<br>";
						
						//echo $vale_dia ." -> extras diurnas: ". calcula_total_horas($extras_diurnas) ." extras noturnas: ". calcula_total_horas($extras_noturnas) ."<br><br>";
						
						//echo $horas_trabalhadas_aqui[0] ."|"; //total de horas diurnas da jornada
						//echo $horas_trabalhadas_aqui[1]; //total de horas noturnas da jornada
						
						//die();
						
						//date("H:i:s", mktime(0, 0, $jornada_diaria, 0, 0, 0))
						
						//if ($vale_dia=="2008-09-08") echo calcula_total_horas($falta_dia);
						
						//procurar no banco de horas algo referente a este dia
						$result_bh= mysql_query("select sum(he) as he, operacao from rh_ponto_banco
													where id_funcionario = '". $id_funcionario ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													and   data_he = '". $vale_dia ."'
													group by operacao
													") or die(mysql_error());
						
						$total_bh_dia=0;
						$sinal_bh= "";
						
						while ($rs_bh= mysql_fetch_object($result_bh)) {
							
							//if ($rs_bh->operacao==0) $classe2="vermelho";
							//else $classe2="azul";
												
							/*if ($rs_bh->tipo_he==0) {
								$hed= $rs_bh->he; $hen= 0;
								
								if ($rs_bh->operacao==0) $bh_d-=$hed;
								else $bh_d+=$hed;
							}
							else {
								$hed= 0; $hen= $rs_bh->he;
								
								if ($rs_bh->operacao==0) $bh_n-=$hen;
								else $bh_n+=$hen;
							}
							*/
							
							$total_bh_dia= $rs_bh->he;
							
							if ($rs_bh->operacao==0) $sinal_bh= "-";
							else $sinal_bh= "+";
							
						}
						
						//@mysql_free_result($result_bh);
						
						$motivo_apriori= $motivo;
						
						if ($total_bh_dia!=0) {
							$motivo_bh= "  [". $sinal_bh . calcula_total_horas($total_bh_dia) ." BH]";
						}
						
						///// motivo /////$pdf->Cell(3.2, 0.4, $motivo . $motivo_bh, 1 , 0, "C", $fill);
						
						$motivo_bh= "";
						
						if ($sinal_bh=="-") //&& ($total_bh_dia>=$falta_dia))
							$falta_dia -= $total_bh_dia;
						
						if ($falta_dia<0) $falta_dia= 0;
						
						$total_faltas += $falta_dia;
						
						//echo $vale_dia ." : ". $motivo ."<br />";
						
						if (($motivo_apriori=="FALTA") && ($total_faltas==0)) {
							//$total_faltas_dias--;
							$falta_nao_justificada= 0;
						}
						
						
						//if ($falta_dia>0) echo pega_funcionario($id_funcionario) ." faltou ". calcula_total_horas($falta_dia) ." em ". desformata_data($vale_dia) ."<br />";
						
						//$pdf->Cell(1.25, 0.4, calcula_total_horas($horas_diurnas), 1 , 0, "C", $fill);
						//$pdf->Cell(1.25, 0.4, calcula_total_horas($horas_noturnas), 1 , 0, "C", $fill);
						//$pdf->Cell(1, 0.4, calcula_total_horas($falta_dia), 1 , 0, "C", $fill);
						
						$saldo_extras_diurnas= $extras_diurnas;
						$saldo_extras_noturnas= $extras_noturnas;
						$total_saldo_extras= $saldo_extras_diurnas+$saldo_extras_noturnas;
						
						//if ( (($extras_diurnas>0) || ($extras_noturnas>0)) && ($saldo_extras_diurnas>$saldo_extras_noturnas)) $saldo_extras_diurnas+= $intervalo_dia;
						//elseif (($extras_diurnas>0) || ($extras_noturnas>0)) $saldo_extras_noturnas+= $intervalo_dia;
						
						$total_horas_dia_bh= 0;
						
						if (($extras_diurnas>0) || ($extras_noturnas>0) ) {
							//procurar no banco de horas algo referente a este dia
							$result_bhd= mysql_query("select sum(he) as total_horas_dia from rh_ponto_banco
														where id_funcionario = '". $id_funcionario ."'
														and   id_empresa = '". $_SESSION["id_empresa"] ."'
														and   data_he = '". $vale_dia ."'
														") or die(mysql_error());
							$rs_bhd= mysql_fetch_object($result_bhd);
							
							$total_horas_dia_bh= $rs_bhd->total_horas_dia;
							
							if ($rs_bhd->total_horas_dia>0) {
								//$fill=1;
								//$pdf->SetFillColor(100,100,100);
								//$pdf->SetTextColor(255,255,255);
								
								//if ($saldo_extras_diurnas>$saldo_extras_noturnas) $saldo_extras_diurnas-= $rs_bhd->total_horas_dia;
								//else $saldo_extras_noturnas-= $rs_bhd->total_horas_dia;
								
								$total_saldo_extras-= $rs_bhd->total_horas_dia;
												
								//if ($saldo_extras_diurnas>$saldo_extras_noturnas) $saldo_extras_diurnas-= $rs_bhd->total_horas_dia;
								//else $saldo_extras_noturnas-= $rs_bhd->total_horas_dia;
								
								if ($saldo_extras_diurnas<$rs_bhd->total_horas_dia) {
									$sobra_noturna= $rs_bhd->total_horas_dia-$saldo_extras_diurnas;
									$saldo_extras_diurnas= 0;											
								}
								
								if ($saldo_extras_noturnas>=$sobra_noturna) {
									$saldo_extras_noturnas-= $sobra_noturna;
								}
								
								if ($saldo_extras_diurnas>=$rs_bhd->total_horas_dia) {
									$saldo_extras_diurnas -= $rs_bhd->total_horas_dia;
								}
		
								//fazer o teste pra ver qual é o maior valor entre horas noturnas e diurnas, o maior valor é decrescido as horas do banco, não influindo se é noturna ou diurna... pois pro banco de horas vai só o total de horas e não o tipo...
								if ($total_he_dsr[0]>$total_he_dsr[1]) {
									$total_he_dsr_controle[0]=1;
									$total_he_dsr_controle[1]=0;
								}
								else {
									$total_he_dsr_controle[0]=0;
									$total_he_dsr_controle[1]=1;
								}
								
								if ($total_he_dsr_dia[0]>$total_he_dsr_dia[1]) {
									$total_he_dsr_dia_controle[0]=1;
									$total_he_dsr_dia_controle[1]=0;
								}
								else {
									$total_he_dsr_dia_controle[0]=0;
									$total_he_dsr_dia_controle[1]=1;
								}
								
								if ($total_he_folga[0]>$total_he_folga[1]) {
									$total_he_folga_controle[0]=1;
									$total_he_folga_controle[1]=0;
								}
								else {
									$total_he_folga_controle[0]=0;
									$total_he_folga_controle[1]=1;
								}
								
								if ($total_he_folga_dia[0]>$total_he_folga_dia[1]) {
									$total_he_folga_dia_controle[0]=1;
									$total_he_folga_dia_controle[1]=0;
								}
								else {
									$total_he_folga_dia_controle[0]=0;
									$total_he_folga_dia_controle[1]=1;
								}
								
								
								if (($total_he_dsr_controle[0]) && ($total_he_dsr[0]>0) && (($motivo_apriori=="DSR") || ($motivo_apriori=="HE (DSR)")) ) $total_he_dsr[0]-= $rs_bhd->total_horas_dia;
								if (($total_he_dsr_dia_controle[0]) && ($total_he_dsr_dia[0]>0) && (($motivo_apriori=="DSR") || ($motivo_apriori=="HE (DSR)")) ) $total_he_dsr_dia[0]-= $rs_bhd->total_horas_dia;
								
								if (($total_he_folga_controle[0]) && ($total_he_folga[0]>0) && (($motivo_apriori=="FOLGA") || ($motivo_apriori=="HE (FOLGA)")) ) $total_he_folga[0]-= $rs_bhd->total_horas_dia;
								if (($total_he_folga_dia_controle[0]) && ($total_he_folga_dia[0]>0) && (($motivo_apriori=="FOLGA") || ($motivo_apriori=="HE (FOLGA)")) ) $total_he_folga_dia[0]-= $rs_bhd->total_horas_dia;
								
								if (($total_he_dsr_controle[1]) && ($total_he_dsr[1]>0) && (($motivo_apriori=="DSR") || ($motivo_apriori=="HE (DSR)")) ) $total_he_dsr[1]-= $rs_bhd->total_horas_dia;
								if (($total_he_dsr_dia_controle[1]) && ($total_he_dsr_dia[1]>0) && (($motivo_apriori=="DSR") || ($motivo_apriori=="HE (DSR)")) ) $total_he_dsr_dia[1]-= $rs_bhd->total_horas_dia;
								
								if (($total_he_folga_controle[1]) && ($total_he_folga[1]>0) && (($motivo_apriori=="FOLGA") || ($motivo_apriori=="HE (FOLGA)")) ) $total_he_folga[1]-= $rs_bhd->total_horas_dia;
								if (($total_he_folga_dia_controle[1]) && ($total_he_folga_dia[1]>0) && (($motivo_apriori=="FOLGA") || ($motivo_apriori=="HE (FOLGA)")) ) $total_he_folga_dia[1]-= $rs_bhd->total_horas_dia;
								
								if ($total_he_feriado[0]>$total_he_feriado[1]) {
									//echo "<strong>0. ".$total_he_feriado[0] ." - $rs_bhd->total_horas_dia</strong><br />";
									if ($total_he_feriado[0]>0) {
										//echo "q";
										$total_he_feriado[0]-= $rs_bhd->total_horas_dia;
										
										if ($total_he_feriado[0]<0) {
											$total_he_feriado[1]+= $total_he_feriado[0];
											$total_he_feriado[0]=0;
										}
									}
								}
								else {
									//echo "<strong>1. ".$total_he_feriado[1] ." - $rs_bhd->total_horas_dia </strong><br />";
									if ($total_he_feriado[1]>0) {
										//echo "w";
										$total_he_feriado[1]-= $rs_bhd->total_horas_dia;
										
										if ($total_he_feriado[1]<0) {
											$total_he_feriado[0]+= $total_he_feriado[1];
											$total_he_feriado[1]=0;
										}
									}
								}
								
								if ($total_he_feriado_dia[0]>$total_he_feriado_dia[1]) {
									//echo "<strong>0. ".$total_he_feriado[0] ." - $rs_bhd->total_horas_dia</strong><br />";
									if ($total_he_feriado_dia[0]>0) {
										//echo "q";
										$total_he_feriado_dia[0]-= $rs_bhd->total_horas_dia;
										
										if ($total_he_feriado_dia[0]<0) {
											$total_he_feriado_dia[1]+= $total_he_feriado_dia[0];
											$total_he_feriado_dia[0]=0;
										}
									}
								}
								else {
									//echo "<strong>1. ".$total_he_feriado[1] ." - $rs_bhd->total_horas_dia </strong><br />";
									if ($total_he_feriado_dia[1]>0) {
										//echo "w";
										$total_he_feriado_dia[1]-= $rs_bhd->total_horas_dia;
										
										if ($total_he_feriado_dia[1]<0) {
											$total_he_feriado_dia[0]+= $total_he_feriado_dia[1];
											$total_he_feriado_dia[1]=0;
										}
									}
								}
								
								if ($total_he_dsr[0]<0) $total_he_dsr[0]=0;
								if ($total_he_dsr[1]<0) $total_he_dsr[1]=0;
								
								if ($total_he_dsr_dia[0]<0) $total_he_dsr_dia[0]=0;
								if ($total_he_dsr_dia[1]<0) $total_he_dsr_dia[1]=0;
								
								
								if ($total_he_folga[0]<0) $total_he_folga[0]=0;
								if ($total_he_folga[1]<0) $total_he_folga[1]=0;
								
								if ($total_he_folga_dia[0]<0) $total_he_folga_dia[0]=0;
								if ($total_he_folga_dia[1]<0) $total_he_folga_dia[1]=0;
								
							}
							
							//@mysql_free_result($result_bhd);
							
							//else
							//	$saldo_extras_diurnas= $extras_diurnas;
							
							//echo $rs_bhd->total_horas_dia ."<br>";
						}
						
						
						
						
						
						
						
						
						$total_faltas_diurnas_aqui=0;
						$total_faltas_noturnas_aqui=0;
						
						if ($falta_dia>0) {
							//se tem batidas do ponto
							if ($total_operacoes>0) {
								$total_operacoes_calculo= $m-1;
								
								unset($resolvido);
								
								for ($o=0; $o<=$total_operacoes_calculo; $o++) {
									
									$a_jornada= 2008;
									$m_jornada= 10;
									
									$d_jornada_entrada= 10;
									$d_jornada_saida= 10;
									
									$anterior_entrada= explode(" ", $entrada[$o]);
									$hora1_entrada= explode(":", $anterior_entrada[1]);
									$data1_entrada= explode("-", $anterior_entrada[0]);
									$completa_entrada[$o]= @mktime($hora1_entrada[0], $hora1_entrada[1], $hora1_entrada[2], $m_jornada, $d_jornada_entrada, $a_jornada);
									
									$anterior_saida= explode(" ", $saida[$o]);
									$hora1_saida= explode(":", $anterior_saida[1]);
									$data1_saida= explode("-", $anterior_saida[0]);
									if ($hora1_entrada[0]>$hora1_saida[$total_operacoes_calculo]) $d_jornada_saida++;
									$completa_saida[$o]= @mktime($hora1_saida[0], $hora1_saida[1], $hora1_saida[2], $m_jornada, $d_jornada_saida, $a_jornada);
									
									//if (eh_diurno($hora1_entrada[0])) $total_faltas_diurnas= $calculo_faltas;
									//else $total_faltas_noturnas= $calculo_faltas;
									
									//$completa_entrada_jornada= substr($jornada_entrada[$id_dia], 11, 8);
									
									//echo $o ." ";
									//echo $vale_dia ." = <br />";
									//echo date("Y-m-d H:i:s", $jornada_entrada[$id_dia]) . " -> ". date("Y-m-d H:i:s", $jornada_saida[$id_dia]) ."<br />";
									//echo date("Y-m-d H:i:s", $completa_entrada[$o]) . " -> ". date("Y-m-d H:i:s", $completa_saida[$o]) ."<br />";
									
									if ($resolvido[$d]!=1) {
									
										$diferenca_entrada_falta[$o]= $completa_entrada[$o]-$jornada_entrada[$id_dia];
										$diferenca_saida_falta[$o]= $jornada_saida[$id_dia]-$completa_saida[$o];
										
										$total_faltas_diurnas_aqui=0;
										$total_faltas_noturnas_aqui=0;
										
										$mostrar_isso=0;
										$mostrar_isso2=0;
										
										if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo "> ". calcula_total_horas($diferenca_entrada_falta[$o]) ." - ". calcula_total_horas($diferenca_saida_falta[$o]) ." < <br />";
										
										if ($diferenca_entrada_falta[$o]>$falta_dia) $diferenca_entrada_falta[$o]=$falta_dia;
										if ($diferenca_saida_falta[$o]<$falta_dia) $diferenca_entrada_falta[$o]=$falta_dia;
										
										if (abs($diferenca_entrada_falta[$o])>14400) {
											if (eh_diurno($hora1_saida[$total_operacoes_calculo])) $total_faltas_diurnas_aqui= $falta_dia;
											else $total_faltas_noturnas_aqui+= $falta_dia;
											
											if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo 1;
										}
										else {
											
											//if ($diferenca_entrada_falta[$o]>600) {
											
											//se entrou depois dos 10 min de tolerância, gera falta aqui...
											if ($diferenca_entrada_falta[$o]>600) {
												//$hora1_entrada[0]=$hora_entrada[0]+0;
												
												if (eh_diurno($hora1_entrada[$o])) $total_faltas_diurnas_aqui= abs($diferenca_entrada_falta[$o]);
												else $total_faltas_noturnas_aqui= abs($diferenca_entrada_falta[$o]);
												
												if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo 2;
											}
											
											//se entrou depois dos 10 min de tolerância, gera falta aqui...
											elseif ($diferenca_saida_falta[$o]>600) {
												//$hora1_saida[0]=$hora1_saida[0]+0;
												
												if (eh_diurno($hora1_saida[$o])) $total_faltas_diurnas_aqui= abs($diferenca_saida_falta[$o]);
												else $total_faltas_noturnas_aqui= abs($diferenca_saida_falta[$o]);
												
												if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo 3;
											}
											else {
												if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo 4;
											}
										}
										
										if (($total_faltas_diurnas_aqui>$total_faltas_noturnas_aqui) && ($total_faltas_diurnas_aqui>$falta_dia)) $total_faltas_diurnas_aqui= $falta_dia;
										if (($total_faltas_noturnas_aqui>$total_faltas_diurnas_aqui) && ($total_faltas_noturnas_aqui>$falta_dia)) $total_faltas_noturnas_aqui= $falta_dia;
										
										if ($mostrar_isso2) if ($_SESSION["id_usuario"]==13) {
											if (($total_faltas_diurnas_aqui>0) || ($total_faltas_noturnas_aqui>0)) {
												echo " <strong>> Dia (". calcula_total_horas($falta_dia) ."): ". $vale_dia .": ". calcula_total_horas($total_faltas_diurnas_aqui) ." - ". calcula_total_horas($total_faltas_noturnas_aqui) ." < <br /><br /></strong>";
											}
										}
										
										$total_faltas_diurnas+= $total_faltas_diurnas_aqui;
										$total_faltas_noturnas+= $total_faltas_noturnas_aqui;
										
										if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo $hora1_entrada[$o] ." * ". calcula_total_horas($total_faltas_diurnas) ." | ". calcula_total_horas($total_faltas_noturnas);
										
										$resolvido[$d]=1;
									}
								}
							}
							//nao tem batidas
							else {
								
								
								
								//$jornada_entrada_calculo_faltas[0]= date("Y-m-d H:i:s", $jornada_entrada[$id_dia]);
								//$jornada_saida_calculo_faltas[0]= date("Y-m-d H:i:s", $jornada_saida[$id_dia]);
								
								//$horas_jornada_periodo_calculo_faltas= calcula_diurno_noturno($jornada_entrada_calculo_faltas, $jornada_saida_calculo_faltas);
								
								$total_faltas_diurnas_aqui= $horas_jornada_periodo[0];
								$total_faltas_noturnas_aqui= $horas_jornada_periodo[1];
								
								
								$total_faltas_diurnas+= $horas_jornada_periodo[0]; //$horas_jornada_periodo_calculo_faltas[0];
								$total_faltas_noturnas+= $horas_jornada_periodo[1]; //$horas_jornada_periodo_calculo_faltas[1];
								
								if (($intervalo_dia!=0) && ($horas_jornada_periodo[0]>$intervalo_dia) && ($horas_jornada_periodo[0]>$horas_jornada_periodo[1])) {
									$total_faltas_diurnas= $total_faltas_diurnas-$intervalo_dia;
									$total_faltas_diurnas_aqui= $total_faltas_diurnas_aqui-$intervalo_dia;
								}
								
								if (($intervalo_dia!=0) && ($horas_jornada_periodo[1]>$intervalo_dia) && ($horas_jornada_periodo[1]>$horas_jornada_periodo[0])) {
									$total_faltas_noturnas= $total_faltas_noturnas-$intervalo_dia;
									$total_faltas_noturnas_aqui= $total_faltas_noturnas_aqui-$intervalo_dia;
								}
								
								//echo $vale_dia .") ";
								//echo calcula_total_horas($total_faltas_diurnas) ." | ". calcula_total_horas($total_faltas_noturnas) ."<br />";
								
								//if (($intervalo_dia!=0) && ($horas_jornada_periodo[0]>$intervalo_dia)) $total_faltas_diurnas-= $intervalo_dia;
								//if (($intervalo_dia!=0) && ($horas_jornada_periodo[1]>$intervalo_dia)) $total_faltas_noturnas-= $intervalo_dia;
								
							}
						}
						
						
						
						
						
						
						
						
						
						
						
						///// extras diurnas /////$pdf->Cell(1.25, 0.4, calcula_total_horas($saldo_extras_diurnas), 1 , 0, "C", $fill);
						
						/*if ($extras_noturnas>0) {
							//procurar no banco de horas algo referente a este dia
							$result_bhn= mysql_query("select sum(he) as total_horas_dia from rh_ponto_banco
														where id_funcionario = '". $id_funcionario ."'
														and   id_empresa = '". $_SESSION["id_empresa"] ."'
														and   data_he = '". $vale_dia ."'
														and   tipo_he = '1'
														") or die(mysql_error());
							$rs_bhn= mysql_fetch_object($result_bhn);
							
							if ($rs_bhn->total_horas_dia>0) {
								
								$saldo_extras_noturnas= $extras_noturnas-$rs_bhn->total_horas_dia;
								
								//aqui ele desconta do geral o banco de horas noturnas............
								//$total_he_normais_60[1]-= $rs_bhn->total_horas_dia;
								//$total_he_normais_100[1]-= $rs_bhn->total_horas_dia;
								if ($total_he_dsr[1]>0) $total_he_dsr[1]-= $rs_bhn->total_horas_dia;
								if ($total_he_folga[1]>0) $total_he_folga[1]-= $rs_bhn->total_horas_dia;
								if ($total_he_feriado[1]>0) $total_he_feriado[1]-= $rs_bhn->total_horas_dia;
							}
							else
								$saldo_extras_noturnas= $extras_noturnas;
						}
						else $saldo_extras_noturnas= $extras_noturnas;
						
						*/
						///// extras noturnas /////$pdf->Cell(1.25, 0.4, calcula_total_horas($saldo_extras_noturnas), 1 , 1, "C", $fill); 
						
						//FIM DA TABELA DE HORARIOS BATIDOS
						
						//echo $saldo_extras_diurnas ." <br> ";
						
						$total_extras_diurnas += $saldo_extras_diurnas;
						$total_extras_noturnas += $saldo_extras_noturnas;
						
						$primeira_faixa= 7200;
						
						//se for um dia comum (nao feriado, nao dsr, nao folga)
						if (($linhas_feriado==0) && ($linhas_escala==1)) {
							if ($saldo_extras_diurnas>$primeira_faixa) {
								$total_he_normais_60[0]+= $primeira_faixa;
								$total_he_normais_100[0]+= $saldo_extras_diurnas-$primeira_faixa;
								
								$total_he_normais_60_dia[0]= $primeira_faixa;
								$total_he_normais_100_dia[0]= $saldo_extras_diurnas-$primeira_faixa;
							}
							else {
								$total_he_normais_60[0]+= $saldo_extras_diurnas;
								$total_he_normais_100[0]+= 0;
								
								$total_he_normais_60_dia[0]= $saldo_extras_diurnas;
								$total_he_normais_100_dia[0]= 0;
							}
							
							if ($saldo_extras_noturnas>$primeira_faixa) {
								$total_he_normais_60[1]+= $primeira_faixa;
								$total_he_normais_100[1]+= $saldo_extras_noturnas-$primeira_faixa;
								
								$total_he_normais_60_dia[1]= $primeira_faixa;
								$total_he_normais_100_dia[1]= $saldo_extras_noturnas-$primeira_faixa;
							}
							else {
								$total_he_normais_60[1]+= $saldo_extras_noturnas;
								$total_he_normais_100[1]+= 0;
								
								$total_he_normais_60_dia[1]= $saldo_extras_noturnas;
								$total_he_normais_100_dia[1]= 0;
							}
						}
					
						$i++;
					}//fim abandono
					//se abandonou, considerar aqui sendo falta para não aparecer no relatório
					else {
						$total_faltas+=1;
					}
					
					
					
					//considerando além das he trabalhadas, dsr, folga e feriado.
					$total_he_normais_100_dia[0]+= $total_he_dsr_dia[0]+$total_he_folga_dia[0]+$total_he_feriado_dia[0];
					$total_he_normais_100_dia[1]+= $total_he_dsr_dia[1]+$total_he_folga_dia[1]+$total_he_feriado_dia[1];
					
					
					
					
					$ht_funcao= $horas_diurnas+$horas_noturnas;
					$he_funcao= $saldo_extras_diurnas+$saldo_extras_noturnas;
					
					ajusta_dados_rh($_SESSION["id_empresa"], $id_funcionario, $vale_dia, $ht_funcao, $horas_diurnas, $horas_noturnas, $falta_dia, $total_faltas_diurnas_aqui, $total_faltas_noturnas_aqui,
												$he_funcao, $saldo_extras_diurnas, $saldo_extras_noturnas, $total_he_normais_60_dia[0], $total_he_normais_100_dia[0], $total_he_normais_60_dia[1], $total_he_normais_100_dia[1],
												$falta_justificada, $falta_nao_justificada, $suspensao, $_SESSION["id_usuario"]);
					
					
					
					
					
				}//FIM DO IF SE JÁ TRABALHA NA EMPRESA
			}//fim checagem dias futuros
			
			//if ($_SESSION["id_usuario"]=="13") {
			//	if ($total_faltas_dias>0) echo $vale_dia;
			//}
		}//fim dos dias
		
		//@mysql_free_result($result_abandono);
		
		/*$pdf->Cell(1.25, 0.4, calcula_total_horas($total_horas_diurnas), 1 , 0, "C");
		$pdf->Cell(1.25, 0.4, calcula_total_horas($total_horas_noturnas), 1 , 0, "C");
		$pdf->Cell(1, 0.4, calcula_total_horas($total_faltas), 1 , 0, "C");
		$pdf->Cell(1.25, 0.4, calcula_total_horas($total_extras_diurnas), 1 , 0, "C");
		$pdf->Cell(1.25, 0.4, calcula_total_horas($total_extras_noturnas), 1 , 0, "C");
		*/
	
	//if ($_SESSION["tipo_usuario"]=="a") {
		//echo calcula_total_horas($saldo_extras_diurnas) ."|". calcula_total_horas($saldo_extras_noturnas);
	//}
	
	$total_faixa60[0]= $total_he_normais_60[0];
	$total_faixa60[1]= $total_he_normais_60[1];
	
	$total_faixa100[0]= $total_he_normais_100[0]+$total_he_dsr[0]+$total_he_folga[0]+$total_he_feriado[0];
	$total_faixa100[1]= $total_he_normais_100[1]+$total_he_dsr[1]+$total_he_folga[1]+$total_he_feriado[1];
	
	/*
	for ($i=0; $i<2; $i++) {
		if ($i==0) {
			$x= 12.1; $y= 17.5;
			$faixa= 60;
		}
		else {
			$x= 15.3; $y= 17.5;
			$faixa= 100;
		}
		
		$pdf->SetXY($x, $y);
		$pdf->Cell(2, 0.3, "NORMAIS 60%", 1 , 0, "L");
		$pdf->Cell(1.2, 0.3, calcula_total_horas($total_he_normais_60[$i]), 1 , 0, "C");
		
		$pdf->SetXY($x, $y+0.3);
		$pdf->Cell(2, 0.3, "NORMAIS 100%", 1 , 0, "L");
		$pdf->Cell(1.2, 0.3, calcula_total_horas($total_he_normais_100[$i]), 1 , 1, "C");
	
		$pdf->SetXY($x, $y+0.6);
		$pdf->Cell(2, 0.3, "DSR 100%", 1 , 0, "L");
		$pdf->Cell(1.2, 0.3, calcula_total_horas($total_he_dsr[$i]), 1 , 1, "C");
		
		$pdf->SetXY($x, $y+0.9);
		$pdf->Cell(2, 0.3, "FOLGA 100%", 1 , 0, "L");
		$pdf->Cell(1.2, 0.3, calcula_total_horas($total_he_folga[$i]), 1 , 1, "C");
		
		$pdf->SetXY($x, $y+1.2);
		$pdf->Cell(2, 0.3, "FERIADO 100%", 1 , 0, "L");
		$pdf->Cell(1.2, 0.3, calcula_total_horas($total_he_feriado[$i]), 1 , 1, "C");
		
		$pdf->SetFillColor(235,235,235);
		$fill= 1;
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->SetXY($x, $y+1.5);
		$pdf->Cell(2, 0.3, "TOTAL 60%", 1 , 0, "L", $fill);
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(1.2, 0.3, calcula_total_horas($total_faixa60[$i]), 1 , 1, "C", $fill);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->SetXY($x, $y+1.8);
		$pdf->Cell(2, 0.3, "TOTAL 100%", 1 , 0, "L", $fill);
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(1.2, 0.3, calcula_total_horas($total_faixa100[$i]), 1 , 1, "C", $fill);
	}
	*/
		
		
		
		if ($total_faixa60[0]<0) $total_faixa60[0]=0;
		if ($total_faixa100[0]<0) $total_faixa100[0]=0;
		
		if ($total_faixa60[1]<0) $total_faixa60[1]=0;
		if ($total_faixa100[1]<0) $total_faixa100[1]=0;
		
		$geral_total_horas_diurnas+= $total_horas_diurnas;
		$geral_total_horas_noturnas+= $total_horas_noturnas;
		$geral_total_faltas+= $total_faltas;
		
		//echo calcula_total_horas_ss($total_faltas) ."<br />";
		
		$geral_total_faixa60[0]+= $total_faixa60[0];
		$geral_total_faixa100[0]+= $total_faixa100[0];
		$geral_total_faixa60[1]+= $total_faixa60[1];
		$geral_total_faixa100[1]+= $total_faixa100[1];
		
		$geral_total_faltas_dias+= $total_faltas_dias;
		$geral_total_faltas_dias_justificadas+= $total_faltas_dias_justificadas;
		
		$geral_total_suspensoes+= $total_suspensoes;
		
		
	}//fim while funcionarios
	
	//@mysql_free_result($result);
	
	//0- motivo (qdo for só um dia)
	//1- horas trabalhadas dirunas
	//2- horas trabalhadas noturnas
	//3- faltas em horas
	//4- horas extras diurnas 60%
	//5- horas extras diurnas 100%
	//6- horas extras noturnas 60%
	//7- horas extras noturnas 100%
	
	//8- faltas injustificadas em dias
	//9- faltas justificadas em dias
	//10- suspensões
	
	if ($geral_total_faixa60[0]<0) $geral_total_faixa60[0]=0;
	if ($geral_total_faixa100[0]<0) $geral_total_faixa100[0]=0;
	
	if ($geral_total_faixa60[1]<0) $geral_total_faixa60[1]=0;
	if ($geral_total_faixa100[1]<0) $geral_total_faixa100[1]=0;
	
	$retorno= $motivo . $motivo_bh .'@'; // 0
	$retorno .= $geral_total_horas_diurnas .'@'; // 1
	$retorno .= $geral_total_horas_noturnas .'@'; // 2
	$retorno .= $geral_total_faltas .'@'; // 3
	$retorno .= $geral_total_faixa60[0] .'@'; // 4
	$retorno .= $geral_total_faixa100[0] .'@'; // 5
	$retorno .= $geral_total_faixa60[1] .'@'; // 6
	$retorno .= $geral_total_faixa100[1] .'@'; // 7
	$retorno .= $geral_total_faltas_dias .'@'; // 8
	$retorno .= $geral_total_faltas_dias_justificadas .'@'; // 9
	$retorno .= $geral_total_suspensoes .'@'; // 10
	
	return($retorno);
}

?>
<?
require_once("conexao.php");
if (pode_algum("rvh", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
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
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	else {
		$periodo= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(0, 0, 0, $periodo[0]-1, 26, $periodo[1]);
		$data2_mk= mktime(0, 0, 0, $periodo[0], 25, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	$result_pre= mysql_query("select * from rh_carreiras, rh_turnos
								where rh_carreiras.id_funcionario = '$id_funcionario'
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_carreiras.atual = '1'
								order by rh_carreiras.id_carreira asc limit 1
								");
	$rs_pre= mysql_fetch_object($result_pre);
	
	$id_departamento= $rs_pre->id_departamento;
	$id_turno= $rs_pre->id_turno;
	$id_intervalo= $rs_pre->id_intervalo;
	$id_regime= $rs_pre->id_regime;
?>

<div id="tela_banco_horas" class="telinha1 screen">
	teste
</div>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<? if ($id_funcionario=="") { ?>
	<ul class="recuo1">
    	<li><a target="_blank" href="index2.php?pagina=rh/espelho_relatorio&amp;data1=<?= $data1f; ?>&amp;data2=<?= $data2f; ?>&amp;id_departamento=<?= $_POST["id_departamento"]; ?>&amp;id_turno=<?= $_POST["id_turno"]; ?>">gerar cartão ponto geral entre <strong><?= $data1f; ?></strong> e <strong><?= $data2f; ?></strong></a></li>
    </ul>
<? } else { ?>
    <fieldset>
        <legend>Alteração manual de espelho</legend>
            
            <div class="parte50">
                <label>Funcionário:</label>
                <? //pega_funcionario($id_funcionario); ?>
                <select name="id_funcionario" id="id_funcionario" title="Funcionário" onchange="alteraEspelhoFuncionario(this.value, '<?= $data1f; ?>', '<?= $data2f; ?>');">
                    <?
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
												and   rh_funcionarios.status_funcionario = '1'
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label>Departamento:</label>
				<?= pega_departamento($id_departamento); ?>
                <br />
                
                <label>Turno:</label>
                <a onmouseover="Tip('<?
					for ($i=0; $i<=6; $i++) {
						$result_dia= mysql_query("select *, DATE_FORMAT(entrada, '%H:%i') as entrada,
													DATE_FORMAT(saida, '%H:%i') as saida
													from rh_turnos_horarios
													where id_turno = '". $id_turno ."'
													and   id_dia = '$i'
													");
						$rs_dia= mysql_fetch_object($result_dia);
						
						if (mysql_num_rows($result_dia)>0)
							echo "<strong>". traduz_dia($i) .":</strong> ". $rs_dia->entrada ." (". pega_detalhes_intervalo($id_intervalo, $i, 0) .") ". $rs_dia->saida ." <br />";
						else
							echo "<strong>". traduz_dia($i) .":</strong> sem expediente <br />";
					}
					?>');" href="javascript:void(0);"><?= pega_turno($id_turno); ?></a>
                <br />
            
            </div>
            <div class="parte50">
                <a target="_blank" href="index2.php?pagina=rh/espelho_relatorio&amp;id_funcionario=<?= $id_funcionario; ?>&amp;data1=<?= $data1f; ?>&amp;data2=<?= $data2f; ?>">gerar relatório</a>
            </div>
            
            <br />
            
            <div class="parte50" id="horario_edita">
                <? require_once("_rh/__espelho_form.php"); ?>
            </div>
            
            <div class="parte50">
                <div id="horario_opcao" class="nao_mostra">
                    <fieldset>
                        <legend>O que deseja fazer com este horário?</legend>
                        
                        <div id="horario_oque_deseja_fazer"></div>
                        
                        <ul class="recuo1">
                            <li><a id="link_edita_horario" href="javascript:void(0);" onclick="alert('Nada setado!');">Editar</a></li>
                            <li><a id="link_exclui_horario" href="javascript:void(0);" onclick="alert('Nada setado!');">Excluir</a></li>
                            
                            <li><a href="javascript:void(0);" onclick="fechaDiv('horario_opcao');">Cancelar</a></li>
                        </ul>
                    </fieldset>
                </div>
                <div id="horario_exclui" class="nao_mostra">
                    <fieldset>
                        <legend>Tem certeza que deseja excluir este horário?</legend>
                        
                        <form action="<?= AJAX_FORM; ?>formHorarioExcluir" method="post" name="formHorarioExcluir" id="formHorarioExcluir" onsubmit="return ajaxForm('conteudo_interno', 'formHorarioExcluir', 'validacoes_exclusao', true);">
                            
                            <input type="hidden" class="escondido" name="validacoes_exclusao" value="id_horario_exclusao@vazio|id_funcionario_exclusao@vazio|data1_exclusao@vazio|data2_exclusao@vazio" />
                            
                            <input type="hidden" class="escondido" id="id_horario_exclusao" name="id_horario" value="" />
                            <input type="hidden" class="escondido" id="id_funcionario_exclusao" name="id_funcionario" value="" />
                            <input type="hidden" class="escondido" id="data1_exclusao" name="data1" value="" />
                            <input type="hidden" class="escondido" id="data2_exclusao" name="data2" value="" />
                            
                            <label>Horário:</label>
                            <div id="horario_exclui_horario"></div>
                            <br />
                            
                            <label for="id_motivo">Motivo:</label>
                            <select name="id_motivo" id="id_motivo" title="Motivo">	  		
                                <?
                                $i=0;
                                $result_mot= mysql_query("select * from rh_motivos where tipo_motivo = 'p' order by motivo asc ");
                                while ($rs_mot= mysql_fetch_object($result_mot)) {
                                ?>
                                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_mot->id_motivo; ?>"><?= $rs_mot->motivo; ?></option>
                                <? $i++; } ?>
                            </select>
                            
                            <br /><br />
                            <center>
                                <button type="submit" id="enviar">Excluir &raquo;</button>
                            </center>
                        
                        </form>
                        
                    </fieldset>
                </div>
            </div>
            
            <br />
            
            <div>
                <fieldset>
                    <legend>Registros entre <?= $data1f; ?> e <?= $data2f; ?></legend>
                    
                    <div class="menor">
						<?
                        for ($i=0; $i<=6; $i++) {
                            $result_dia= mysql_query("select * from rh_turnos_horarios
                                                        where id_turno = '". $id_turno ."'
                                                        and   id_dia = '$i'
                                                        ");
                            $rs_dia= mysql_fetch_object($result_dia);
                            
                            $jornada_entrada_hora= explode(':', $rs_dia->entrada);
                            $jornada_saida_hora= explode(':', $rs_dia->saida);
                            
                            $m_jornada= 10; $a_jornada= 2008;
                            $d_jornada= 10;
                            
                            $jornada_entrada[$i]= @mktime($jornada_entrada_hora[0], $jornada_entrada_hora[1], $jornada_entrada_hora[2], $m_jornada, $d_jornada, $a_jornada);
                            $jornada_saida[$i]= @mktime($jornada_saida_hora[0], $jornada_saida_hora[1], $jornada_saida_hora[2], $m_jornada, $d_jornada, $a_jornada);
							
							$jornada_entrada_hora2[$i]= @mktime($jornada_entrada_hora[0], $jornada_entrada_hora[1], $jornada_entrada_hora[2], 0, 0, 0);
                            $jornada_saida_hora2[$i]= @mktime($jornada_saida_hora[0], $jornada_saida_hora[1], $jornada_saida_hora[2], 0, 0, 0);
                        }
                        ?>
    
                        <table width="100%" cellspacing="0">
                            <tr>
                                <th align="left" width="10%">Dia da semana</th>
                                <?
                                $j=1;
                                
                                $num_operacoes= 4;
                                
                                for ($i=1; $i<($num_operacoes*2)+1; $i++) {
                                    if (($i%2)==0) $operacao= "Saída";
                                    else $operacao= "Entrada";
                                ?>
                                <th>
                                    <?= $operacao .' '. $j; ?>
                                </th>
                                <?
                                    if (($i%2)==0) $j++;
                                }
                                ?>
                                <th>Motivo</th>
                                <th>Faltas</th>
                                <th>HED</th>
                                <th>HEN</th>
                            </tr>
                            <?
                            //$diferenca= date("d", $data2_mk-$data1_mk);
                            //$diferenca_meses= date("m", $data2_mk-$data1_mk);
                            $erro=0;
                            
                            $total_horas_diurnas=0;
                            $total_horas_noturnas=0;
                            $total_faltas=0;
                            $total_extras_diurnas=0;
                            $total_extras_noturnas=0;
                            
                            $diferenca = ceil(($data2_mk-$data1_mk)/86400);
                            
                            //repetir todos os dias do intervalo
                            for ($d= 0; $d<=$diferenca; $d++) {
                                if (($d%2)==0) $classe= "cor_nao";
								else $classe= "cor_sim";
								
								$extras_diurnas=0;
                                $extras_noturnas=0;
                                
                                $total_saidas[$d]= 0;
                                
                                $e= $d+1;
                                $calculo_data= $data1_mk+(86400*$d);
                                
                                $dia= date("d/m", $calculo_data);
                                $data= date("d/m/Y", $calculo_data);
                                $id_dia= date("w", $calculo_data);
                                $vale_dia= date("Y-m-d", $calculo_data);
                            ?>
                            <tr class="<?= $classe; ?>">
                                <th align="left" class="td_dia_semana">
                                    <?= traduz_dia($id_dia); ?> <br />
                                    <?= $data; ?>
                                </th>
                                <?
                                //repetir as entradas e saidas
                                $z=0;
                                $h=0;
								$m=0;
								
                                $result_hor= mysql_query("select *, DATE_FORMAT(data_batida, '%d/%m/%Y') as data_batida2
															from rh_ponto
															where vale_dia= '$vale_dia'
															and   id_funcionario = '$id_funcionario'
															order by data_batida, hora
                                                        ");
                                                        
                                $total_operacoes= mysql_num_rows($result_hor);
								
								unset($entrada); unset($saida);
								
                                if ($total_operacoes==0) {
								?>
                                <td colspan="<?=($num_operacoes*2);?>" class="recuo_texto">
                                	Não foram encontrados horários neste dia.
                                    
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    
                                    <a href="javascript:void(0);" onclick="batidaAutomatica('<?=$id_dia;?>', '<?=$data;?>', '<?=$id_funcionario;?>', '<?= $data1f; ?>', '<?= $data2f; ?>');">&raquo; batida automática</a>
                                </td>
                                <?
                                    $h= ($num_operacoes*2);
                                }
                                else {
                                    while ($rs_hor= mysql_fetch_object($result_hor)) {
                                        $horario[$z]= faz_mk_data_completa($rs_hor->data_batida2 .' '. $rs_hor->hora);
                                        
                                        if (($rs_hor->tipo==0) && ($id_regime==2)) {
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
													if ($horario[$z]-$horario[$z-1]>=7200) {
														$h++;
                                                ?>
                                                <td align="center">
                                                    <?= date("d/m/Y", $intervalo_automatico[$p]) ."<br />". date("H:i:s", $intervalo_automatico[$p]); ?>
                                                </td>
                                                <?
													}
                                                }
                                            }//fim 
                                        }
                                        ?>
                                        <td align="center">
                                            <a href="javascript:void(0);" onclick="setaIdHorario('<?= $rs_hor->id_horario; ?>', '<?= $rs_hor->tipo; ?>', '<?= $rs_hor->data_batida2 .' às '. $rs_hor->hora; ?>', '<?= $id_funcionario; ?>', '<?= $data1f; ?>', '<?= $data2f; ?>');">
                                                <?= $rs_hor->data_batida2 .'<br />'. $rs_hor->hora; ?>
                                                <? //= $horario[$z]; ?>
                                            </a>
                                        </td>
                                        <?
                                        
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
                                    }//fim while horários
                                }//fim else tem horários
                                
                                if (($total_operacoes%2)==1) {
                                    $erro=1;
                                    $h++;
                                ?>
                                <td class="vermelho" align="center">
                                    erro
                                </td>
                                <? } ?>
                                
                                <?
                                //preencher o que falta
                                for ($i=$h; $i<($num_operacoes*2); $i++) {
                                ?>
                                <td align="center">
                                    -
                                </td>
                                <?
                                }//fim for preenche o que falta
                                
                                // -------------------------------------------------------------------------------------------------------------------------
                                
                                //--- consultando escala
                                $result_esc= mysql_query("select * from rh_escala
                                                            where id_funcionario = '". $id_funcionario ."'
                                                            and   data_escala = '". $vale_dia ."'
                                                            ") or die(mysql_error());
                                $linhas_escala= mysql_num_rows($result_esc);
                                
                                $result_feriado= mysql_query("select * from rh_feriados
                                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                            and   data_feriado = '". $vale_dia ."'
                                                            ") or die(mysql_error());
                                $linhas_feriado= mysql_num_rows($result_feriado);
                                
                                //se nao bateu o ponto nesse dia
                                if ($total_operacoes==0) {
                                    $result_afa= mysql_query("select * from rh_afastamentos_dias
                                                                where id_funcionario = '". $id_funcionario ."'
                                                                and   data = '". $vale_dia ."'
                                                                ") or die(mysql_error());
                                    $linhas_afastamento= mysql_num_rows($result_afa);
                                    $rs_afa= mysql_fetch_object($result_afa);
									
                                    if (($linhas_afastamento>0) && ($rs_afa->tipo_afastamento!='s')) {
                                        $tipo_afastamento= pega_tipo_afastamento_pelo_id_afastamento($rs_afa->id_afastamento);
                                        $motivo= strtoupper(pega_tipo_afastamento($tipo_afastamento));
                                    }
                                    else {
                                        if ($linhas_afastamento>0)
                                            $motivo= "SUSPENSÃO";
                                        else {
											if ($linhas_escala==1)
												$motivo= "FALTA";
											else {
												if (($id_dia==0) || ($id_dia==6))
													$motivo= "DSR";
												else
													$motivo= "FOLGA";
											}
                                        }
                                    }
                                    
                                    $horas_diurnas= 0;
                                    $horas_noturnas= 0;
                                    $extras_diurnas= 0;
                                    $extras_noturnas= 0;
                                }
                                //se tem batidas neste dia
                                else {
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
                                        }
                                        else {
                                            //se for fim de semana, computa para horas extras em dsr
                                            if (($id_dia==0) || ($id_dia==6)) {
                                                $motivo= "HE (DSR)";
                                                $total_he_dsr[0]+= $extras_diurnas;
                                                $total_he_dsr[1]+= $extras_noturnas;
                                            }
                                            //dia de semana, horas extras em folgas
                                            else {
                                                $motivo= "HE (FOLGA)";
                                                $total_he_folga[0]+= $extras_diurnas;
                                                $total_he_folga[1]+= $extras_noturnas;
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
                                $total_horas= $horas_diurnas+$horas_noturnas;
                                
                                $intervalo_dia= pega_duracao_intervalo_dia($id_turno, $id_dia);
                                if ($intervalo_dia!=0) $jornada_diaria= $jornada_diaria-$intervalo_dia;
                                
                                //se a pessoa trabalha neste dia, calcula a falta
								//ou se está suspensa
								if ((($linhas_escala==1) && ($linhas_feriado==0)) || ($rs_afa->tipo_afastamento=='s'))
									$calculo_faltas= $jornada_diaria-$total_horas;
								else
									$calculo_faltas= 0;
                                
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
                                            $falta_string= date("H:i:s", mktime(0, 0, $calculo_faltas, 0, 0, 0));
                                            $falta_dia= $calculo_faltas;
                                        }
                                    }
                                    //se não veio trabalhar, mas a falta foi justificada
                                    else {
                                        if (($motivo=="FALTA") || ($motivo=="SUSPENSÃO")) {
                                            /*if ($intervalo_dia!=0)
                                                $jornada_diaria= $jornada_diaria+$intervalo_dia;*/
                                            
                                            $falta_dia= $jornada_diaria;
                                        }
                                        else {
                                            $falta_dia= 0;
                                        }
                                    }
                                }
                                $total_faltas += $falta_dia;
                                
                                // --------------------------------------------------------------------------- calculo de horas extras
                                //echo date("H:i:s", mktime(0, 0, $adicional, 0, 0, 0)); die();
                                //echo date("d/m/Y H:i:s", $jornada_entrada[$id_dia]); die();
                                $entrada_aqui[0]= date("Y-m-d H:i:s", $jornada_entrada[$id_dia]);
                                $saida_aqui[0]= date("Y-m-d H:i:s", $jornada_saida[$id_dia]);
                                
                                $horas_jornada_periodo= calcula_diurno_noturno($entrada_aqui, $saida_aqui);
                                
                                //echo $horas_diurnas; die();
                                //echo $horas_jornada_periodo[0] ." | ";
                                //echo $horas_jornada_periodo[1] . " <br> "; die();
                                
                                if ($intervalo_dia!=0) $jornada_diaria_para_extra= $jornada_diaria;//+$intervalo_dia;
                                else $jornada_diaria_para_extra= $jornada_diaria;
                                
                                $total_horas_trabalhadas_dia= $horas_diurnas+$horas_noturnas;
                                $diferenca_horas_trabalhadas_jornada= $total_horas_trabalhadas_dia-$jornada_diaria_para_extra;
                                
								//echo $vale_dia ." -> horas trabalhadas: ". calcula_total_horas($total_horas_trabalhadas_dia) ." horas da jornada: ". calcula_total_horas($jornada_diaria_para_extra) ."<br>";
                                //echo $total_horas_trabalhadas_dia; die();
                                
                                //se o total de horas que foi trabalhada for maior que a jornada diária, a sobra é hora extra
                                if (($diferenca_horas_trabalhadas_jornada>=1200) && ($extras_diurnas==0) && ($extras_noturnas==0)) {
                                    //trabalha no periodo diurno
                                    //if ($horas_jornada_periodo[0]>0)
                                        
										//echo $vale_dia ." -> horas diurnas: ". calcula_total_horas($horas_diurnas) ." jornada diurna: ". calcula_total_horas($horas_jornada_periodo[0]) ."<br>";
										
                                        //$extras_diurnas= $diferenca_horas_trabalhadas_jornada;
                                        $extras_diurnas= abs($horas_diurnas-$horas_jornada_periodo[0]);
                                        if ($extras_diurnas<=1200) $extras_diurnas= 0;
                                        
                                        $extras_noturnas= abs($horas_noturnas-$horas_jornada_periodo[1]);
                                        if ($extras_noturnas<=1200) $extras_noturnas= 0;
                                }
                                else {
                                    if (($extras_diurnas==0) && ($extras_noturnas==0)) {
                                        $extras_diurnas=0;
                                        $extras_noturnas=0;
                                    }
                                }
                                
								//-----------gambiarra
								if ($extras_diurnas>86400) $extras_diurnas -= 86400;
								if ($extras_noturnas>86400) $extras_noturnas -= 86400;
                                
								//echo $vale_dia ." -> extras diurnas: ". calcula_total_horas($extras_diurnas) ." extras noturnas: ". calcula_total_horas($extras_noturnas) ."<br><br>";
								
                                //echo $horas_trabalhadas_aqui[0] ."|"; //total de horas diurnas da jornada
                                //echo $horas_trabalhadas_aqui[1]; //total de horas noturnas da jornada
                                
                                //die();
                                
                                $total_extras_diurnas += $extras_diurnas;
                                $total_extras_noturnas += $extras_noturnas;
                                
                                // -------------------------------------------------------------------------------------------------------------------------
                                /*
                                $pdf->Cell(3.2, 0.4, $motivo, 1 , 0, "C", $fill);
                                $pdf->Cell(1.25, 0.4, date("H:i:s", mktime(0, 0, $horas_diurnas, 0, 0, 0)), 1 , 0, "C", $fill);
                                $pdf->Cell(1.25, 0.4, date("H:i:s", mktime(0, 0, $horas_noturnas, 0, 0, 0)), 1 , 0, "C", $fill);
                                $pdf->Cell(1, 0.4, $falta_string, 1 , 0, "C", $fill);
                                $pdf->Cell(1.25, 0.4, date("H:i:s", mktime(0, 0, $extras_diurnas, 0, 0, 0)), 1 , 0, "C", $fill);
                                $pdf->Cell(1.25, 0.4, date("H:i:s", mktime(0, 0, $extras_noturnas, 0, 0, 0)), 1 , 1, "C", $fill); 
                                */
                                ?>
                                <td align="center" width="6%">
                                    <?= $motivo; ?>
                                </td>
                                <td align="center" width="6%">
                                    <?= calcula_total_horas($falta_dia); ?>
                                    <? /*if ($falta_dia>0) { ?>
                                    <a onmouseover="Tip('<?=$tip;?>');" href="javascript:void(0);" onclick="ajaxLink('tela_banco_horas', 'carregaPagina&amp;pagina=rh/banco_form&amp;id_funcionario=<?=$id_funcionario;?>&amp;data=<?=$vale_dia;?>&amp;tipo_he=0&amp;he=<?=$saldo_extras_diurnas;?>&amp;operacao=1&amp;data1f=<?=$data1f;?>&amp;data2f=<?=$data2f;?>'); abreDiv('tela_banco_horas');">
										<?= calcula_total_horas($saldo_extras_diurnas); ?>
                                    </a>
                                    <? } else echo calcula_total_horas($saldo_extras_diurnas); */ ?>
                                </td>
                                <?
								//procurar no banco de horas algo referente a este dia
								$result_bhd= mysql_query("select sum(he) as total_horas_dia from rh_ponto_banco
															where id_funcionario = '". $id_funcionario ."'
															and   id_empresa = '". $_SESSION["id_empresa"] ."'
															and   data_he = '". $vale_dia ."'
															and   tipo_he = '0'
															") or die(mysql_error());
								$rs_bhd= mysql_fetch_object($result_bhd);
								
								if ($rs_bhd->total_horas_dia==0)
									$tip= "Adicionar esta hora extra diurna ao banco de horas!";
								else
									$tip= "Existe <strong>". calcula_total_horas($rs_bhd->total_horas_dia) ."</strong> deste dia no banco de horas!";
								
								$saldo_extras_diurnas= $extras_diurnas-$rs_bhd->total_horas_dia;
								?>
                                <td align="center" width="6%">
                                	<? if ($extras_diurnas>0) { ?>
                                    <a onmouseover="Tip('<?=$tip;?>');" href="javascript:void(0);" onclick="ajaxLink('tela_banco_horas', 'carregaPagina&amp;pagina=rh/banco_form&amp;id_funcionario=<?=$id_funcionario;?>&amp;data=<?=$vale_dia;?>&amp;tipo_he=0&amp;he=<?=$saldo_extras_diurnas;?>&amp;operacao=1&amp;data1f=<?=$data1f;?>&amp;data2f=<?=$data2f;?>'); abreDiv('tela_banco_horas');">
										<?= calcula_total_horas($saldo_extras_diurnas); ?>
                                    </a>
                                    <? } else echo calcula_total_horas($saldo_extras_diurnas); ?>
                                </td>
                                <?
								//procurar no banco de horas algo referente a este dia
								$result_bhn= mysql_query("select sum(he) as total_horas_dia from rh_ponto_banco
															where id_funcionario = '". $id_funcionario ."'
															and   id_empresa = '". $_SESSION["id_empresa"] ."'
															and   data_he = '". $vale_dia ."'
															and   tipo_he = '1'
															") or die(mysql_error());
								$rs_bhn= mysql_fetch_object($result_bhn);
								
								if ($rs_bhn->total_horas_dia==0)
									$tip= "Adicionar esta hora extra diurna ao banco de horas!";
								else
									$tip= "Existe <strong>". calcula_total_horas($rs_bhn->total_horas_dia) ."</strong> deste dia no banco de horas!";
								
								$saldo_extras_noturnas= $extras_noturnas-$rs_bhn->total_horas_dia;
								?>
                                <td align="center" width="6%">
                                	<? if ($extras_noturnas>0) { ?>
                                	<a onmouseover="Tip('<?=$tip;?>');" href="javascript:void(0);" onclick="ajaxLink('tela_banco_horas', 'carregaPagina&amp;pagina=rh/banco_form&amp;id_funcionario=<?=$id_funcionario;?>&amp;data=<?=$vale_dia;?>&amp;tipo_he=1&amp;he=<?=$saldo_extras_noturnas;?>&amp;operacao=1&amp;data1f=<?=$data1f;?>&amp;data2f=<?=$data2f;?>'); abreDiv('tela_banco_horas');">
	                                    <?= calcula_total_horas($saldo_extras_noturnas); ?>
									</a>
                                    <? } else echo calcula_total_horas($saldo_extras_noturnas); ?>
                                </td>
                            </tr>
                            <? } ?>
                        </table>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Erros encontrados</legend>
                    
                    <? if ($erro==1) { ?>
                    <p class="vermelho">Foram encontrados erros neste cartão ponto, visualize acima!</p>
                    <? } else { ?>
                    <p class="verde">Tudo certo, nenhum erro encontrado!</p>
                    <? } ?>
                    
                </fieldset>
            </div>
            
    </fieldset>
	<? } ?>
<? } ?>
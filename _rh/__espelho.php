<?
require_once("conexao.php");

if (pode("rvh4", $_SESSION["permissao"])) {
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
		
		$data1_mk= mktime(20, 0, 0, $periodo[0]-1, 26, $periodo[1]);
		$data2_mk= mktime(14, 0, 0, $periodo[0], 25, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	$result_pre= mysql_query("select * from rh_funcionarios, rh_carreiras, rh_turnos
								where rh_carreiras.id_funcionario = '$id_funcionario'
								and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
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
    	<li><a target="_blank" href="index2.php?pagina=rh/espelho_relatorio&amp;data1=<?= $data1f; ?>&amp;data2=<?= $data2f; ?>&amp;id_departamento=<?= $_POST["id_departamento"]; ?>&amp;id_turno=<?= $_POST["id_turno"]; ?>&amp;geral=<?= $_POST["geral"]; ?>">gerar cartão ponto geral entre <strong><?= $data1f; ?></strong> e <strong><?= $data2f; ?></strong></a></li>
    </ul>
<? } else { ?>
    <fieldset>
        <legend>Alteração manual de espelho</legend>
            
            <div class="parte50">
                <label for="lista">Lista:</label>
                <select name="lista" id="lista" onchange="alteraFuncionariosAtivosInativos(this.value);">
                	<option value="1">Ativos</option>
                    <option value="0" class="cor_sim">Inativos</option>
                    <option value="-1">Em espera</option>
                </select>
                <br />
                
                <label for="id_funcionario">Funcionário:</label>
                <? //pega_funcionario($id_funcionario); ?>
                <div id="id_funcionario_atualiza">
                    <select name="id_funcionario" id="id_funcionario" title="Funcionário" onchange="alteraEspelhoFuncionario(this.value, '<?= $data1f; ?>', '<?= $data2f; ?>');">
                        <?
                        $result_fun= mysql_query("select *
                                                    from  pessoas, rh_funcionarios, rh_carreiras
                                                    where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                    and   pessoas.tipo = 'f'
                                                    and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                    and   rh_carreiras.atual = '1'
                                                    and   rh_funcionarios.status_funcionario = '". $rs_pre->status_funcionario ."'
                                                    and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    order by pessoas.nome_rz asc
                                                    ") or die(mysql_error());
                        $i=0;
                        while ($rs_fun= mysql_fetch_object($result_fun)) {
                            //if ($rs_fun->status_funcionario==1) $classe= "cor_sim";
                            //else $classe= "cor_nao";
							
							if (($i%2)==0) $classe= "cor_sim";
							else $classe= "cor_nao";
                        ?>
                        <option class="<?= $classe; ?>" value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
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
                <ul class="recuo1">
                	<li><a target="_blank" href="index2.php?pagina=rh/espelho_relatorio&amp;id_funcionario=<?= $id_funcionario; ?>&amp;data1=<?= $data1f; ?>&amp;data2=<?= $data2f; ?>">gerar relatório</a></li>
                    <li><a href="javascript:void(0);" onclick="alteraEspelhoFuncionario(<?= $id_funcionario; ?>, '<?= $data1f; ?>', '<?= $data2f; ?>');">recarregar esta página</a></li>
                </ul>
            </div>
            
            <br />
            
            <? if (pode("r", $_SESSION["permissao"])) { ?>
            
            <div class="parte50" id="horario_edita">
                <? require_once("_rh/__espelho_form.php"); ?>
            </div>
            
            <div class="parte50">
                <div id="horario_opcao" class="nao_mostra">
                    <? if (pode("rv", $_SESSION["permissao"])) { ?>
                    <fieldset>
                        <legend>O que deseja fazer com este horário?</legend>
                        
                        <div id="horario_oque_deseja_fazer"></div>
                        
                        <ul class="recuo1">
                            <li><a id="link_edita_horario" href="javascript:void(0);" onclick="alert('Nada setado!');">Editar</a></li>
                            <li><a id="link_exclui_horario" href="javascript:void(0);" onclick="alert('Nada setado!');">Excluir</a></li>
                            
                            <li><a href="javascript:void(0);" onclick="fechaDiv('horario_opcao');">Cancelar</a></li>
                        </ul>
                    </fieldset>
                    <? } ?>
                </div>
                <div id="horario_exclui" class="nao_mostra">
                    <? if (pode("rv", $_SESSION["permissao"])) { ?>
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
                    <? } ?>
                </div>
            </div>
            <? } ?>
            
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
                            
							$a_jornada= 2008;
                            $m_jornada= 10;
                            
							$d_jornada_entrada= 10;
							$d_jornada_saida= 10;
                            
							if ($jornada_entrada_hora[0]>$jornada_saida_hora[0]) $d_jornada_saida++;
							
                            $jornada_entrada[$i]= @mktime($jornada_entrada_hora[0], $jornada_entrada_hora[1], $jornada_entrada_hora[2], $m_jornada, $d_jornada_entrada, $a_jornada);
                            $jornada_saida[$i]= @mktime($jornada_saida_hora[0], $jornada_saida_hora[1], $jornada_saida_hora[2], $m_jornada, $d_jornada_saida, $a_jornada);
							
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
                                <th>HTD</th>
                                <th>HTN</th>
                                <th>Faltas</th>
                                <th>HE</th>
                                <th>HED</th>
                                <th>HEN</th>
                            </tr>
                            <?
							
							$hoje= date("Y-m-d");
							$hoje_mk= faz_mk_data2($hoje);
							
                            //$diferenca= date("d", $data2_mk-$data1_mk);
                            //$diferenca_meses= date("m", $data2_mk-$data1_mk);
                            $erro=0;
                            
                            $total_horas_diurnas=0;
                            $total_horas_noturnas=0;
                            $total_faltas=0;
                            $total_extras_diurnas=0;
                            $total_extras_noturnas=0;
                            
                            $diferenca = ceil(($data2_mk-$data1_mk)/86400);
                            
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
                                if (($d%2)==0) $classe= "cor_nao";
								else $classe= "cor_sim";
								
								/*
								
								if ($hoje_mk>=$data_mk) {
								*/
								
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
								
								//if ($_SESSION["tipo_usuario"]=="a")
								//	echo date("d/m/Y", $calculo_data) ." | ". date("d/m/Y H:i:s", $data_admissao_mk) ."<br /><br />";
								
								$data_atual_aqui= date("d/m/Y", $calculo_data);
								$data_admissao_aqui= date("d/m/Y", $data_admissao_mk);
								
								//vai mostrar o dia...
								//se a data atual for maior ou igual a data de admissao === ELE JÁ É FUNCIONÁRIO DA EMPRESA
								//ou
								//se o funcionário não foi demitido ou se foi demitido e a data atual é menor a data da demissão
								if ((($calculo_data>=$data_admissao_mk) || ($data_atual_aqui==$data_admissao_aqui)) && ((!$demitido) || (($demitido) && ($calculo_data<=$data_demissao_mk)) ) ) {
								
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
                                    
                                    <? /*&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    
                                    <a href="javascript:void(0);" onclick="batidaAutomatica('<?=$id_dia;?>', '<?=$data;?>', '<?=$id_funcionario;?>', '<?= $data1f; ?>', '<?= $data2f; ?>');">&raquo; batida automática</a>
									*/ ?>
                                </td>
                                <?
                                    $h= ($num_operacoes*2);
                                }
                                else {
                                    while ($rs_hor= mysql_fetch_object($result_hor)) {
                                        $horario[$z]= faz_mk_data_completa($rs_hor->data_batida2 .' '. $rs_hor->hora);
                                        
										if ($rs_hor->id_usuario!="") {
											$quem_foi= "(M)";
											$quem_foi_usuario= "<strong>". pega_nome_pelo_id_usuario($rs_hor->id_usuario) ."</strong>";
											$quem_foi_classe= "vermelho";
										}
										else {
											$quem_foi= "(P)";
											$quem_foi_usuario="";
											$quem_foi_classe= "verde";
										}
										
                                        if (($rs_hor->tipo==0) && (($id_regime==2) || ($id_regime==3)) ) {
                                            $total_saidas[$d]++;
                                            //echo $total_saidas[$d];
                                            //echo $intervalo_automatico[0] ." - ";
                                            //echo $intervalo_automatico[1];
                                            //echo $atual;
                                            
                                            if ( (($total_operacoes/$total_saidas[$d])==2) ) {
                                                //passando
                                                $intervalo_automatico= retorna_intervalo_automatico($horario[$z-1], $horario[$z]);
                                                
                                                for ($p=0; $p<2; $p++) {
													
													//echo date("d/m/Y H:i:s", $horario[$z]) ." - ". date("d/m/Y H:i:s", $horario[$z-1]);
													
													//echo $horario[$z] ." - ". $horario[$z-1];
													
													//se tem, pelo menos 5:40 trabalhadas... faz o intervalo
													if ($horario[$z]-$horario[$z-1]>=4500) {
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
                                        	
                                            <? if (pode("r", $_SESSION["permissao"])) { ?>
                                            <a <? if ($quem_foi_usuario!="") { ?> onmouseover="Tip('<?= $quem_foi_usuario; ?>');" <? } ?> href="javascript:void(0);" onclick="setaIdHorario('<?= $rs_hor->id_horario; ?>', '<?= $rs_hor->tipo; ?>', '<?= $rs_hor->data_batida2 .' às '. $rs_hor->hora; ?>', '<?= $id_funcionario; ?>', '<?= $data1f; ?>', '<?= $data2f; ?>');">
                                                <?= $rs_hor->data_batida2 .'<br />'. $rs_hor->hora; ?>
                                                <? //= $horario[$z]; ?>
                                            </a>
                                             <span class="<?=$quem_foi_classe;?>"><strong><?= $quem_foi; ?></strong></span>
                                            <? } else { ?>
                                            <span <? if ($quem_foi_usuario!="") { ?> onmouseover="Tip('<?= $quem_foi_usuario; ?>');" <? } ?>>
												<?= $rs_hor->data_batida2 .'<br />'. $rs_hor->hora; ?> <span class="<?=$quem_foi_classe;?>"><strong><?= $quem_foi; ?></strong></span>
                                            </span>
                                            <? } ?>
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
                                
								if (($id_regime==2) && (($id_dia==0) || ($id_dia==6)) && ($total_operacoes==2)) $desconto_link=1;
								else $desconto_link=0;
								
								
								
                                if (($total_operacoes%2)==1) {
                                    $erro=1;
                                    $h++;
                                ?>
                                <td class="vermelho" align="center">
                                    erro
                                </td>
                                <? } ?>
                                
                                <?
                                // ---------------------------------------------------------------
                                
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
									
                                    if (($linhas_afastamento>0) && ($rs_afa->tipo_afastamento!='s') && ($rs_afa->tipo_afastamento!='b')) {
                                        $tipo_afastamento= pega_tipo_afastamento_pelo_id_afastamento($rs_afa->id_afastamento);
										$id_motivo_afastamento= pega_id_motivo_pelo_id_afastamento($rs_afa->id_afastamento);
										
										if ($tipo_afastamento=='o') $motivo= pega_motivo($id_motivo_afastamento);
										else $motivo= pega_tipo_afastamento($tipo_afastamento);
										
										$motivo= strtoupper($motivo);
										
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
											
										if (($rs_afa->tipo_afastamento=='o') || (($rs_afa->tipo_afastamento=='a') && ($dias_diferenca<15)) ) {
											$falta_justificada=1;
										}
                                    }
                                    else {
                                        if ($linhas_afastamento>0) {
                                            if ($rs_afa->tipo_afastamento=='s') {
												$motivo= "SUSPENSÃO";
												$suspensao= 1;
											}
											else $motivo= "INÍCIO DO ABANDONO";
										}
                                        else {
											if ($linhas_escala==1) {
												$motivo= "FALTA";
												$falta_nao_justificada=1;
											}
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
                                
								//retorna o numero de horas que precisam ser trabalhadas em tal dia (sem intervalos)
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
								//ou se está suspensa
								if ((($linhas_escala==1) ) || ($rs_afa->tipo_afastamento=='s') || ($rs_afa->tipo_afastamento=='b')) {
									$calculo_faltas= ($jornada_diaria-$total_horas);//+$intervalo_dia;
								}
								else
									$calculo_faltas= 0;
                                
                                //se trabalhou mais que a carga horária
                                if ($calculo_faltas<=0) {
                                    //echo 0;
									$falta_string= "00:00:00";
                                    $falta_dia= 0;
                                }
                                else {
                                    //se veio trabalhar
                                    if ($total_operacoes>0) {
										//echo 1;
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
										//echo 2;
                                        if (($motivo=="FALTA") || ($motivo=="SUSPENSÃO") || ($motivo=="INÍCIO DO ABANDONO")) {
                                            /*if ($intervalo_dia!=0)
                                                $jornada_diaria= $jornada_diaria+$intervalo_dia;*/
                                            
                                            $falta_dia= $jornada_diaria;
                                        }
                                        else {
                                            $falta_dia= 0;
                                        }
                                    }
                                }

								//echo $falta_dia;

                                // --------------------------------------------------------------------------- calculo de horas extras
                                //echo date("H:i:s", mktime(0, 0, $adicional, 0, 0, 0)); die();
                                //echo date("d/m/Y H:i:s", $jornada_entrada[$id_dia]); die();
                                $entrada_aqui[0]= date("Y-m-d H:i:s", $jornada_entrada[$id_dia]);
                                $saida_aqui[0]= date("Y-m-d H:i:s", $jornada_saida[$id_dia]);
                                
                                $horas_jornada_periodo= calcula_diurno_noturno($entrada_aqui, $saida_aqui);
                                
                                //echo $horas_diurnas; die();
                                //echo $horas_jornada_periodo[0] ." | ";
                                //echo $horas_jornada_periodo[1] . " <br> "; die();
								
								//echo date("d/m/Y H:i:s", $jornada_entrada[$id_dia]) ." | ";
                                //echo date("d/m/Y H:i:s", $jornada_saida[$id_dia]) . " <br> "; //die();
                                
								//echo $intervalo_dia;
								
                                $jornada_diaria_para_extra= $jornada_diaria;//+$intervalo_dia; //6h
                                
                                $total_horas_trabalhadas_dia= ($horas_diurnas+$horas_noturnas);//6:20
                                $diferenca_horas_trabalhadas_jornada= $total_horas_trabalhadas_dia-$jornada_diaria_para_extra;
                                
								//if ($_SESSION[id_usuario]==13) echo $vale_dia ." -> horas trabalhadas: ". calcula_total_horas($total_horas_trabalhadas_dia) ." horas da jornada: ". calcula_total_horas($jornada_diaria_para_extra) ." diferença: ". calcula_total_horas($diferenca_horas_trabalhadas_jornada) ." | ". $diferenca_horas_trabalhadas_jornada ." | ". $extras_diurnas ." | ". $extras_noturnas ."<br>";
                                
                                //se o total de horas que foi trabalhada for maior que a jornada diária, a sobra é hora extra
                                if (($diferenca_horas_trabalhadas_jornada>=1200) && ($extras_diurnas==0) && ($extras_noturnas==0)) {
                                    //trabalha no periodo diurno
                                    //if ($horas_jornada_periodo[0]>0)
                                        
										//echo "horas diurnas: ". calcula_total_horas($horas_diurnas) ." jornada diurna: ". calcula_total_horas($horas_jornada_periodo[0]) ."<br>";
										//echo "horas noturnas: ". calcula_total_horas($horas_noturnas) ." jornada noturna: ". calcula_total_horas($horas_jornada_periodo[1]) ."<br><br />";
										
                                        //$extras_diurnas= $diferenca_horas_trabalhadas_jornada;
                                        $extras_diurnas= ($horas_diurnas-$horas_jornada_periodo[0]);
                                        if (($extras_diurnas<=1200) || ($extras_diurnas>46800)) $extras_diurnas= 0;
                                        
                                        $extras_noturnas= ($horas_noturnas-$horas_jornada_periodo[1]);
                                        if (($extras_noturnas<=1200) || ($extras_noturnas>46800)) $extras_noturnas= 0;
										
										if (($extras_diurnas>0) && ($diferenca_horas_trabalhadas_jornada>0) && ($extras_diurnas>$diferenca_horas_trabalhadas_jornada)) $extras_diurnas= $diferenca_horas_trabalhadas_jornada;
										if (($extras_noturnas>0) && ($diferenca_horas_trabalhadas_jornada>0) && ($extras_noturnas>$diferenca_horas_trabalhadas_jornada)) $extras_noturnas= $diferenca_horas_trabalhadas_jornada;
										
										
										// ---------- ultima alteração ------------
										
										if ($intervalo_dia!=0) {
											$calculo_he_final= ($horas_diurnas+$horas_noturnas)-$jornada_diaria_para_extra;
											
											//se a diferença trabalhada for maior que 20 minutos...
											if ($calculo_he_final>1200) {
												//if ($extras_diurnas>=$extras_noturnas) $extras_noturnas+= $calculo_he_final;
												//else $extras_diurnas+= $calculo_he_final;
											}
										}
										
										//echo "\$calculo_he_final: ". $calculo_he_final ." | hd: ". calcula_total_horas($horas_diurnas) ." hed: ". calcula_total_horas($extras_diurnas) ." jornada diurna: ". calcula_total_horas($horas_jornada_periodo[0]) ."<br />";
										//echo "hn: ". calcula_total_horas($horas_noturnas) ." hen: ". calcula_total_horas($extras_noturnas) ." jornada noturna: ". calcula_total_horas($horas_jornada_periodo[1]) ."<br /><br />";
										
										// ---------- ultima alteração ------------
										
										/*
										if (($horas_noturnas<$horas_jornada_periodo[1]) && ($horas_diurnas>$horas_jornada_periodo[0]) && ($extras_diurnas>1200)) {
											//echo " !deu! ";
											
											$diferenca_noturna_aqui= $horas_jornada_periodo[1]-$horas_noturnas;
											
											$extras_diurnas -= $diferenca_noturna_aqui;
										}
										*/
										//echo $extras_noturnas;
                                }
                                else {
                                    if (($extras_diurnas==0) && ($extras_noturnas==0)) {
                                        $extras_diurnas=0;
                                        $extras_noturnas=0;
                                    }
                                }
								
								//echo "hd: ". calcula_total_horas($extras_diurnas) ."| hn: ". calcula_total_horas($extras_noturnas);
								
								//se for regime integral, pode ter hora extra quando tem intervalo
								if (($id_regime==1) && ($motivo=="")) {
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
                                
                                <?
								if ($desconto_link) {
								?>
                                <td align="center">
                                    <? if (pode("r", $_SESSION["permissao"])) { ?>
                                    <a href="javascript:void(0);" onclick="batidaIntervaloFind('<?=$id_dia;?>', '<?=$data;?>', '<?=$id_funcionario;?>', '<?= $data1f; ?>', '<?= $data2f; ?>');">&raquo; bate intervalo</a>
                                    <? } ?> &nbsp;
                                </td>
                                <? } ?>
                                
                                <?
								if ((($id_regime==2) || ($id_regime==3)) && ($id_dia!=6) && ($id_dia!=0) && ($total_horas>=25000) && ($total_operacoes==2)) $trabalhou_demais=1;
								else $trabalhou_demais= 0;
								
								if ($trabalhou_demais) {
								?>
                                <td align="center">
                                	<? if (pode("r", $_SESSION["permissao"])) { ?>
                                    <a href="javascript:void(0);" onclick="batidaIntervaloNormal('<?=$id_dia;?>', '<?=$data;?>', '<?=$id_funcionario;?>', '<?= $data1f; ?>', '<?= $data2f; ?>');">&raquo; bate intervalo</a>
                                	<? } ?>
                                    &nbsp;
                                </td>
                                <? } ?>
                                
                                <?
                                //preencher o que falta
                                for ($i=$h; $i<(($num_operacoes*2)-$desconto_link-$trabalhou_demais); $i++) {
                                ?>
                                <td align="center">
                                    -
                                </td>
                                <? } ?>
                                
                                <td align="center" width="6%" <? if ($total_horas_trabalhadas_dia>43200) echo "style=\"background: #900; color: #fff;\""; ?>>
                                	<?
									$motivo_apriori= $motivo;
									
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
										
										if ($rs_bh->operacao==0) $classe2="vermelho";
										else $classe2="azul";
															
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
									
									if ($total_bh_dia!=0) $motivo= "  [". $sinal_bh . calcula_total_horas($total_bh_dia) ." BH]";
									
									//if ($total_bh_dia!=0) {
									//	$motivo_bh= "  [". $sinal_bh . calcula_total_horas($total_bh_dia) ." BH]";
									//}
									
									echo $motivo;
									
									$motivo_bh= "";
									?>
                                </td>
                                
                                <td align="center"><?= calcula_total_horas($horas_diurnas); ?></td>
                                <td align="center"><?= calcula_total_horas($horas_noturnas); ?></td>
                                <td align="center" width="6%">
                                    <?
                                    if ($sinal_bh=="-") //&& ($total_bh_dia>=$falta_dia))
										$falta_dia -= $total_bh_dia;
									
									if ($falta_dia<0) $falta_dia= 0;
									
									$total_faltas += $falta_dia;
									
									if (($motivo_apriori=="FALTA") && ($falta_dia==0)) {
										//$total_faltas_dias--;
										$falta_nao_justificada= 0;
									}
									
									//echo calcula_total_horas($falta_dia);
									?>
                                    
									<? if (pode("r", $_SESSION["permissao"])) { ?>
										<? if ($falta_dia>0) { ?>
                                        <a onmouseover="Tip('Clique para debitar essas horas de falta do banco de horas.');" href="javascript:void(0);" onclick="ajaxLink('tela_banco_horas', 'carregaPagina&amp;pagina=rh/banco_form&amp;id_funcionario=<?=$id_funcionario;?>&amp;data=<?=$vale_dia;?>&amp;tipo_he=0&amp;he=<?=$falta_dia;?>&amp;operacao=0&amp;data1f=<?=$data1f;?>&amp;data2f=<?=$data2f;?>'); abreDiv('tela_banco_horas');">
                                            <?= calcula_total_horas($falta_dia); ?>
                                        </a>
                                        <? } else echo calcula_total_horas($falta_dia); ?>
                                    <? } else echo calcula_total_horas($falta_dia); ?>
                                </td>
                                <?
								
								$saldo_extras_diurnas= $extras_diurnas;//-$rs_bhd->total_horas_dia;
								$saldo_extras_noturnas= $extras_noturnas;//-$rs_bhn->total_horas_dia;
								
								$total_saldo_extras= $saldo_extras_diurnas+$saldo_extras_noturnas;
								
								//echo $saldo_extras_diurnas ."|". $saldo_extras_noturnas;
								
								//if ( (($extras_diurnas>0) || ($extras_noturnas>0)) && ($saldo_extras_diurnas>$saldo_extras_noturnas)) $saldo_extras_diurnas+= $intervalo_dia;
								//elseif (($extras_diurnas>0) || ($extras_noturnas>0)) $saldo_extras_noturnas+= $intervalo_dia;
								
								//echo "xxx".$saldo_extras_diurnas."xxx";
								
								//procurar no banco de horas algo referente a este dia
								/*$result_bhd= mysql_query("select sum(he) as total_horas_dia from rh_ponto_banco
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
								*/
								
								if (($extras_diurnas>0) || ($extras_noturnas>0) ) {
									//procurar no banco de horas algo referente a este dia
									$result_bhd= mysql_query("select sum(he) as total_horas_dia from rh_ponto_banco
																where id_funcionario = '". $id_funcionario ."'
																and   id_empresa = '". $_SESSION["id_empresa"] ."'
																and   data_he = '". $vale_dia ."'
																") or die(mysql_error());
									$rs_bhd= mysql_fetch_object($result_bhd);
									
									//echo $rs_bhd->total_horas_dia;
									
									if ($rs_bhd->total_horas_dia>0) {
										//$fill=1;
										//$pdf->SetFillColor(100,100,100);
										//$pdf->SetTextColor(255,255,255);
										
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
									//else
									//	$saldo_extras_diurnas= $extras_diurnas;
									
									//echo $rs_bhd->total_horas_dia ."<br>";
								}
								
								//echo $saldo_extras_diurnas;
								
								?>
                                <td align="center" width="6%">
                                	<? if (pode("r", $_SESSION["permissao"])) { ?>
										<? if ($total_saldo_extras>0) { ?>
                                        <a onmouseover="Tip('Clique para adicionar horas ao banco de horas.');" href="javascript:void(0);" onclick="ajaxLink('tela_banco_horas', 'carregaPagina&amp;pagina=rh/banco_form&amp;id_funcionario=<?=$id_funcionario;?>&amp;data=<?=$vale_dia;?>&amp;tipo_he=1&amp;he=<?=$total_saldo_extras;?>&amp;operacao=1&amp;data1f=<?=$data1f;?>&amp;data2f=<?=$data2f;?>'); abreDiv('tela_banco_horas');">
                                            <?= calcula_total_horas($total_saldo_extras); ?>
                                        </a>
                                        <? } else echo calcula_total_horas($total_saldo_extras); ?>
                                    <? } else echo calcula_total_horas($total_saldo_extras); ?>
                                </td>
                                
                                <td align="center" width="6%">
                                	<? /*if ($saldo_extras_diurnas>0) { ?>
                                    <a onmouseover="Tip('Clique para adicionar horas ao banco de horas.');" href="javascript:void(0);" onclick="ajaxLink('tela_banco_horas', 'carregaPagina&amp;pagina=rh/banco_form&amp;id_funcionario=<?=$id_funcionario;?>&amp;data=<?=$vale_dia;?>&amp;tipo_he=0&amp;he=<?=$saldo_extras_diurnas;?>&amp;operacao=1&amp;data1f=<?=$data1f;?>&amp;data2f=<?=$data2f;?>'); abreDiv('tela_banco_horas');">
										<?= calcula_total_horas($saldo_extras_diurnas); ?>
                                    </a>
                                    <? } else */ echo calcula_total_horas($saldo_extras_diurnas); ?>
                                </td>
                                <?
								//procurar no banco de horas algo referente a este dia
								/*$result_bhn= mysql_query("select sum(he) as total_horas_dia from rh_ponto_banco
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
								*/
								//echo $saldo_extras_noturnas;
								?>
                                <td align="center" width="6%">
                                	<? /*if ($saldo_extras_noturnas>0) { ?>
                                	<a onmouseover="Tip('Clique para adicionar horas ao banco de horas.');" href="javascript:void(0);" onclick="ajaxLink('tela_banco_horas', 'carregaPagina&amp;pagina=rh/banco_form&amp;id_funcionario=<?=$id_funcionario;?>&amp;data=<?=$vale_dia;?>&amp;tipo_he=1&amp;he=<?=$saldo_extras_noturnas;?>&amp;operacao=1&amp;data1f=<?=$data1f;?>&amp;data2f=<?=$data2f;?>'); abreDiv('tela_banco_horas');">
	                                    <?= calcula_total_horas($saldo_extras_noturnas); ?>
									</a>
                                    <? } else*/ echo calcula_total_horas($saldo_extras_noturnas); ?>
                                </td>
                                
                                
                            </tr>
                            <?
								$total_faltas_diurnas=0;
								$total_faltas_noturnas=0;
								
								
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
										$total_faltas_diurnas_aqui= $horas_jornada_periodo[1];
										
										$total_faltas_diurnas+= $horas_jornada_periodo[0]; //$horas_jornada_periodo_calculo_faltas[0];
										$total_faltas_noturnas+= $horas_jornada_periodo[1]; //$horas_jornada_periodo_calculo_faltas[1];
										
										if (($intervalo_dia!=0) && ($horas_jornada_periodo[0]>$intervalo_dia) && ($horas_jornada_periodo[0]>$horas_jornada_periodo[1])) {
											$total_faltas_diurnas= $total_faltas_diurnas-$intervalo_dia;
											
											$total_faltas_diurnas_aqui= $total_faltas_diurnas_aqui-$intervalo_dia;
										}
										if (($intervalo_dia!=0) && ($horas_jornada_periodo[1]>$intervalo_dia) && ($horas_jornada_periodo[1]>$horas_jornada_periodo[0])) {
											$total_faltas_noturnas= $total_faltas_noturnas-$intervalo_dia;
											
											$total_faltas_noturnas_aqui= $total_faltas_noturnas-$intervalo_dia;
										}
										
										//echo $vale_dia .") ";
										//echo calcula_total_horas($total_faltas_diurnas) ." | ". calcula_total_horas($total_faltas_noturnas) ."<br />";
										
										//if (($intervalo_dia!=0) && ($horas_jornada_periodo[0]>$intervalo_dia)) $total_faltas_diurnas-= $intervalo_dia;
										//if (($intervalo_dia!=0) && ($horas_jornada_periodo[1]>$intervalo_dia)) $total_faltas_noturnas-= $intervalo_dia;
										
									}
								}
								
								
								
								//echo calcula_total_horas($total_faltas_diurnas) ." | ". calcula_total_horas($total_faltas_noturnas);
								
								
								//echo $saldo_extras_diurnas ." <br> ";
									
								
								$primeira_faixa= 7200;
								
								//se for um dia comum (nao feriado, nao dsr, nao folga)
								if (($linhas_feriado==0) && ($linhas_escala==1)) {
									if ($saldo_extras_diurnas>$primeira_faixa) {
										$total_he_normais_60_dia[0]= $primeira_faixa;
										$total_he_normais_100_dia[0]= $saldo_extras_diurnas-$primeira_faixa;
									}
									else {
										$total_he_normais_60_dia[0]= $saldo_extras_diurnas;
										$total_he_normais_100_dia[0]= 0;
									}
									
									if ($saldo_extras_noturnas>$primeira_faixa) {
										$total_he_normais_60_dia[1]= $primeira_faixa;
										$total_he_normais_100_dia[1]= $saldo_extras_noturnas-$primeira_faixa;
									}
									else {
										$total_he_normais_60_dia[1]= $saldo_extras_noturnas;
										$total_he_normais_100_dia[1]= 0;
									}
								}
								
								$total_he_normais_60[0]+=$total_he_normais_60_dia[0];
								$total_he_normais_100[0]+=$total_he_normais_100_dia[0];
								$total_he_normais_60[1]+=$total_he_normais_60_dia[1];
								$total_he_normais_100[1]+=$total_he_normais_100_dia[1];
								
								$total_he_normais_100_dia[0]+= $total_he_dsr_dia[0]+$total_he_folga_dia[0]+$total_he_feriado_dia[0];
								$total_he_normais_100_dia[1]+= $total_he_dsr_dia[1]+$total_he_folga_dia[1]+$total_he_feriado_dia[1];
								
								//echo "qwe:". $total_he_normais_100_dia[1] ."<br />";
								
								$ht_funcao= $horas_diurnas+$horas_noturnas;
								$he_funcao= $saldo_extras_diurnas+$saldo_extras_noturnas;
								
								ajusta_dados_rh($_SESSION["id_empresa"], $id_funcionario, $vale_dia, $ht_funcao, $horas_diurnas, $horas_noturnas, $falta_dia, $total_faltas_diurnas_aqui, $total_faltas_noturnas_aqui,
												$he_funcao, $saldo_extras_diurnas, $saldo_extras_noturnas, $total_he_normais_60_dia[0], $total_he_normais_100_dia[0], $total_he_normais_60_dia[1], $total_he_normais_100_dia[1],
												$falta_justificada, $falta_nao_justificada, $suspensao, $_SESSION["id_usuario"]);
							
                            }// fim já trabalha na empresa
							}//fim hoje mk
						}//fim dias
						?>
                        </table>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Erros encontrados</legend>
                    
                    <? if ($erro==1) { ?>
                    <p class="vermelho">Foram encontrados erros neste cartão ponto, visualize acima!</p>
                    <? } else { ?>
                    <p class="verde">Nenhum erro encontrado!</p>
                    <? } ?>
                    
                </fieldset>
            </div>
            
    </fieldset>
	<? } ?>
<? } ?>
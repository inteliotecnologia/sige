<?
require_once("conexao.php");
if (pode_algum("rw", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= formata_data_hifen($_POST["data1"]); $data1f= $_POST["data1"];
		$data2= formata_data_hifen($_POST["data2"]); $data2f= $_POST["data2"];
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	else {
		$periodo= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(14, 0, 0, $periodo[0], 1, $periodo[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo[0], $dias_mes, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	if ($_POST["id_turno"]!="") $str_turno= " and   id_turno = '". $_POST["id_turno"] ."' ";
	
	if ($_POST["id_departamento"]!="") $str_departamento= " and   id_departamento = '". $_POST["id_departamento"] ."' ";
	
	
	
	//echo "id_turno: ". $_POST["id_turno"] ." id_departamento: ". $_POST["id_departamento"];
?>

    <div id="tela_mensagens2">
        <? include("__tratamento_msgs.php"); ?>
    </div>
    
    <? if ($_POST["impressao"]=="1") { ?>
    	<ul class="recuo1">
            <li><a target="_blank" href="index2.php?pagina=rh/escala_relatorio&amp;data1=<?= $data1f; ?>&amp;data2=<?= $data2f; ?>&amp;id_departamento=<?= $_POST["id_departamento"]; ?>&amp;id_turno=<?= $_POST["id_turno"]; ?>">gerar escala para impressão (<strong><?= $data1f; ?></strong> até <strong><?= $data2f; ?></strong>).</a></li>
        </ul>
    <? } else { ?>
    
    <p>Registros entre <strong><?= $data1f; ?></strong> e <strong><?= $data2f; ?></strong>.</p>
    
    <ul class="recuo1">
        <li><a href="javascript:void(0);" onclick="checarTudo('tudo');">checar/deschecar tudo</a></li>
    </ul>
    
    <form action="<?= AJAX_FORM; ?>formEscala" method="post" name="formEscala" id="formEscala" onsubmit="return ajaxForm('conteudo_interno', 'formEscala', 'validacoes', true);">
    	
        <input class="escondido" type="hidden" id="validacoes" />
        
        <input class="escondido" type="hidden" id="id_departamento" name="id_departamento" value="<?= $_POST["id_departamento"]; ?>" />
        
        <input class="escondido" type="hidden" id="data1" name="data1" value="<?= $data1f; ?>" />
        <input class="escondido" type="hidden" id="data2" name="data2" value="<?= $data2f; ?>" />
        
        <input class="escondido" type="hidden" id="modo" name="modo" value="<?= $_POST["modo"]; ?>" />
        
		<?
		$i=0;
		
        $result_dep= mysql_query("select * from rh_departamentos
                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
									". $str_departamento ."
                                    order by departamento asc
                                    ");
        while ($rs_dep = mysql_fetch_object($result_dep)) {
        ?>
        <fieldset class="fescala">
            <legend><?= $rs_dep->departamento; ?></legend>
                
            <?
			
            $result_tur= mysql_query("select * from rh_turnos
                                        where id_departamento = '". $rs_dep->id_departamento ."'
										". $str_turno ."
                                        order by turno asc
                                        ");
            while ($rs_tur = mysql_fetch_object($result_tur)) {
            ?>
            
            <fieldset>
                <legend><?= $rs_tur->turno; ?></legend>
                
                <table width="100%" cellspacing="0">
                    <tr>
                        <th width="18%" align="left">Funcionário</th>
                        <?
                        $diferenca_tit= date("d", $data2_mk-$data1_mk);
                        
                        //repetir todos os dias do intervalo
                        for ($t=0; $t<=$diferenca_tit; $t++) {
                          $calculo_data_tit= $data1_mk+(86400*$t);
                          
                          $dia_tit= date("d", $calculo_data_tit);
                          $dia_semana_tit= date("w", $calculo_data_tit);
                          $vale_dia_tit= date("Y-m-d", $calculo_data_tit);
						  
                        ?>
                        <th align="left">
                            <?= $dia_tit; ?> <br />
                            <?= traduz_dia_resumido($dia_semana_tit); ?>
                        </th>
                        <? } ?>
                    </tr>
                    <?
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
													
													and   rh_carreiras.id_turno = '". $rs_tur->id_turno ."'
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
													and   rh_carreiras.id_turno = '". $rs_tur->id_turno ."'
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
							
							
						} else $mostra_funcionario_escala= 1;
						
						if (($_POST["modo"]==1) || (($_POST["modo"]==2) && ($rs_fun->id_carreira==$id_carreira_vale)) ) {
						//if (1==1) {
						
                    ?>
                    <tr id="funcionario_<?= $rs_fun->id_funcionario; ?>">
                        <th align="left" class="td_dia_semana">
                            <a href="javascript:void(0);" onclick="checarTudo('funcionario_<?= $rs_fun->id_funcionario; ?>');"><?= $rs_fun->nome_rz; ?></a>
                        </th>
                        <?
                        $diferenca= date("d", $data2_mk-$data1_mk);
                        
                        //repetir todos os dias do intervalo
                        for ($d=0; $d<=$diferenca; $d++) {
                            $calculo_data= $data1_mk+(86400*$d);
                            $dia_semana= date("w", $calculo_data);
                            $vale_dia= date("Y-m-d", $calculo_data);
							$vale_dia_mk= faz_mk_data($vale_dia);
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
							}
							
							$result_adm= mysql_query("select * from rh_carreiras
														where id_funcionario = '". $rs_fun->id_funcionario ."'
														and   id_acao_carreira = '1'
														") or die(mysql_error());
							$rs_adm= mysql_fetch_object($result_adm);
							$data_admissao_mk= faz_mk_data($rs_adm->data);
							
                        ?>
                        <td align="center" <? if ($linhas_afastamento>0) { ?> class="cor_cont" onmouseover="Tip('<?= $afastamento; ?>');" <? } ?>>
                            <input class="escondido" type="hidden" name="id_funcionario[<?=$i;?>]" value="<?= $rs_fun->id_funcionario; ?>" />
                            <input class="escondido" type="hidden" name="data_escala[<?=$i;?>]" value="<?= $vale_dia; ?>" />
                            <input <? if ($vale_dia_mk<$data_admissao_mk) { ?> disabled="disabled" <? } ?> class="tamanho20" type="checkbox" name="trabalha[<?=$i;?>]" value="1" <? if ($rs_esc->trabalha==1) echo "checked=\"checked\""; ?> />
                        </td>
                        <? $i++; } ?>
                    </tr>
                    <?
                    	$j++;
						
						if (($j%5)==0) {
						?>
                        <tr>
                            <th width="18%" align="left">Funcionário</th>
                            <?
                            $diferenca_tit= date("d", $data2_mk-$data1_mk);
                            
                            //repetir todos os dias do intervalo
                            for ($t=0; $t<=$diferenca_tit; $t++) {
                              $calculo_data_tit= $data1_mk+(86400*$t);
                              
                              $dia_tit= date("d", $calculo_data_tit);
                              $dia_semana_tit= date("w", $calculo_data_tit);
                              $vale_dia_tit= date("Y-m-d", $calculo_data_tit);
                              
                            ?>
                            <th align="left">
                                <?= $dia_tit; ?> <br />
                                <?= traduz_dia_resumido($dia_semana_tit); ?>
                            </th>
                            <? } ?>
                        </tr>
                        <?
						}
					}
					}
					?>
                </table>
                
            </fieldset>
            
            <? } ?>
        </fieldset>
    <? } ?>
	
    <br /><br />
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
	</form>
	<? } ?>
<? } ?>
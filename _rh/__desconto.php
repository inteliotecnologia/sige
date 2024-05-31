<?
require_once("conexao.php");
if (pode_algum("rh", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= formata_data_hifen($_POST["data1"]); $data1f= $_POST["data1"];
		$data2= formata_data_hifen($_POST["data2"]); $data2f= $_POST["data2"];
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	else {
		$periodo2= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(0, 0, 0, $periodo2[0], 1, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(0, 0, 0, $periodo[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
?>

    <div id="tela_mensagens2">
        <? include("__tratamento_msgs.php"); ?>
    </div>
    
    <? if ($_POST["geral"]=="1") { ?>
    <ul class="recuo1">
    	<? if ($_POST["formato"]=="pdf") { ?>
        <li><a target="_blank" href="index2.php?pagina=rh/folha_relatorio&amp;periodo=<?= $_POST["periodo"]; ?>&amp;id_departamento=<?= $_POST["id_departamento"]; ?>">gerar relatório com as informações referentes</a></li>
        <? } else { ?>
        <li><a target="_blank" href="index2.php?pagina=rh/folha_relatorio_xls&amp;periodo=<?= $_POST["periodo"]; ?>&amp;id_departamento=<?= $_POST["id_departamento"]; ?>">gerar relatório com as informações referentes</a></li>
        <? } ?>
    </ul>
    <? } else { ?>
    
    <p><?= traduz_mes($periodo2[0]); ?>/<?= $periodo2[1]; ?>.</p>
    
    <form action="<?= AJAX_FORM; ?>formDesconto" method="post" name="formDesconto" id="formDesconto" onsubmit="return ajaxForm('conteudo_interno', 'formDesconto', 'validacoes', true);">
    	
        <input class="escondido" type="hidden" id="validacoes" />
        
        <input class="escondido" type="hidden" id="id_departamento" name="id_departamento" value="<?= $_POST["id_departamento"]; ?>" />
        
        <input class="escondido" type="hidden" id="periodo" name="periodo" value="<?= $_POST["periodo"]; ?>" />
        
        <? /*
        <input class="escondido" type="hidden" id="data1" name="data1" value="<?= $data1f; ?>" />
        <input class="escondido" type="hidden" id="data2" name="data2" value="<?= $data2f; ?>" />
        */ ?>
        
        <input class="escondido" type="hidden" id="mes" name="mes" value="<?= $periodo2[0]; ?>" />
        <input class="escondido" type="hidden" id="ano" name="ano" value="<?= $periodo2[1]; ?>" />
        
		<?
		$i=0;
        $result_dep= mysql_query("select * from rh_departamentos
                                    where id_empresa = '". $id_empresa ."'
									and   id_departamento = '". $_POST["id_departamento"] ."'
                                    order by departamento asc
                                    ");
        while ($rs_dep = mysql_fetch_object($result_dep)) {
        ?>
        <fieldset class="fescala">
            <legend><?= $rs_dep->departamento; ?></legend>
                
            <?
            $result_tur= mysql_query("select * from rh_turnos
                                        where id_departamento = '". $rs_dep->id_departamento ."'
                                        order by turno asc
                                        ");
            while ($rs_tur = mysql_fetch_object($result_tur)) {
            ?>
            
            <fieldset>
                <legend><?= $rs_tur->turno; ?></legend>
                
                <table width="100%" cellspacing="0">
                    <tr>
                        <th width="40%" align="left">Funcionário</th>
                        <?
                        $result_des= mysql_query("select * from rh_motivos
												 	where tipo_motivo = 't'
													order by motivo asc");
						while ($rs_des= mysql_fetch_object($result_des)) {
                        ?>
                        <th align="left">
                            <?= $rs_des->motivo; ?>
                        </th>
                        <? } ?>
                    </tr>
                    <?
					$j=0;
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_carreiras.id_turno = '". $rs_tur->id_turno ."'
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   rh_funcionarios.status_funcionario = '1'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <tr id="funcionario_<?= $rs_fun->id_funcionario; ?>">
                        <th align="left" class="td_dia_semana">
                            <?= $rs_fun->nome_rz; ?>
                        </th>
                        <?
						$result_des= mysql_query("select * from rh_motivos
												 	where tipo_motivo = 't'
													order by motivo asc");
						while ($rs_des= mysql_fetch_object($result_des)) {
							$result= mysql_query("select * from rh_descontos
												 	where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_funcionario = '". $rs_fun->id_funcionario ."'
													and   mes = '". $periodo2[0] ."'
													and   ano = '". $periodo2[1] ."'
													and   id_motivo = '". $rs_des->id_motivo ."'
													");
							$rs= mysql_fetch_object($result);
                        ?>
                        <td align="center">
                            <input class="escondido" type="hidden" name="id_funcionario[<?=$i;?>]" value="<?= $rs_fun->id_funcionario; ?>" />
                            <input class="escondido" type="hidden" name="id_motivo[<?=$i;?>]" value="<?= $rs_des->id_motivo;?>" />
                            
							<? if ($rs_des->qtde_dias==0) { ?>
                            <select class="tamanho70" name="valor[<?=$i;?>]" >
                            	<option value="0" <? if ($rs->valor==0) echo "selected=\"selected\""; ?>>Não</option>
                                <option value="1" <? if ($rs->valor==1) echo "selected=\"selected\""; ?> class="cor_sim">Sim</option>
                            </select>
                            <?
                            }
							else {
								if (($rs->valor!="") && ($rs->valor!=0)) $valor_campo= fnum($rs->valor);
								else $valor_campo= "";
							?>
                            <input class="tamanho70" type="text" name="valor[<?=$i;?>]" value="<?= $valor_campo; ?>" onkeydown="formataValor(this,event);" />
                            <? } ?>
                        </td>
                        <? $i++; } ?>
                    </tr>
                    <?
                    	$j++;
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
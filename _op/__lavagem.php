<?
require_once("conexao.php");
if (pode("ps", $_SESSION["permissao"])) {
	if ($acao=="") $acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_suja_lavagem
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_lavagem = '". $_GET["id_lavagem"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<h2>Lavagem</h2>

<div class="parte80">
    <form action="<?= AJAX_FORM; ?>formLavagem&amp;acao=<?= $acao; ?>" method="post" name="formLavagem" id="formLavagem" onsubmit="return validaFormNormal('validacoes', false, 1);">
        
        <input class="escondido" type="hidden" id="validacoes" value="id_remessa@vazio@data_remessa|data_lavagem@data|hora_lavagem@vazio|<? if ($acao=='e') { ?>data_fim_lavagem@data|hora_fim_lavagem@vazio|<? } ?>id_equipamento@vazio@equipamento|id_processo@vazio@processo|peso_total@vazio" />
        <? if ($acao=='e') { ?>
        <input name="id_lavagem" class="escondido" type="hidden" id="id_lavagem" value="<?= $rs->id_lavagem; ?>" />
        <? } ?>
        
        <fieldset>
            <legend>Dados</legend>
            
            <div class="parte50 gigante">
                <?
                /*$result_remessa= mysql_query("select * from op_suja_remessas
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by data_remessa desc, hora_chegada desc limit 1
                                                ");
                $rs_remessa= mysql_fetch_object($result_remessa);*/
                
                //if ($acao=='i') $id_remessa= $rs_remessa->id_remessa;
                //else $id_remessa= $rs->id_remessa;
                ?>
                <label for="data_remessa">* Data/nº remessa:</label>
                <input id="data_remessa" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="data_remessa" class="tamanho25p" value="<?= desformata_data(pega_dado_remessa("data_remessa", $rs->id_remessa)); ?>" title="Data da remessa" onkeyup="formataData(this);" maxlength="10" onblur="pegaRemessa(); buscaRemessasDoDia();" />
                
                <input id="num_remessa" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="num_remessa" class="tamanho15p" value="<?= pega_dado_remessa("num_remessa", $rs->id_remessa); ?>" title="Número da remessa" onblur="pegaRemessa();" />
                <br />
				
                <label>&nbsp;</label>
                <div id="remessa_atualiza" class="gigante_textox">
                    <input id="id_remessa" name="id_remessa" value="<?= $rs->id_remessa; ?>" title="Remessa" class="escondido" />
                </div>
                <br /><br />  
                
                <?
				if (pode("p", $_SESSION["permissao"])) $pode_editar=1;
				else $pode_editar=0;
				?>
                
                <div <? if (!$pode_editar) echo "style=\"display:none;\""; ?>>
					
					<?
                    if ($acao=='i') $data_lavagem= date("d/m/Y");
                    else $data_lavagem= desformata_data($rs->data_lavagem);
                    ?>
                    <label for="data_lavagem">* Data/hora:</label>
                    <input id="data_lavagem" name="data_lavagem" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> class="tamanho25p" value="<?= $data_lavagem; ?>" title="Data da lavagem" onkeyup="formataData(this);" maxlength="10" />
                    
                    <?
                    if ($acao=='i') $hora_lavagem= date("H:i:s");
                    else $hora_lavagem= $rs->hora_lavagem;
                    ?>
                    <input id="hora_lavagem" name="hora_lavagem" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> class="tamanho25p" value="<?= $hora_lavagem; ?>" title="Hora da lavagem"  onkeyup="formataHora(this);" maxlength="8" />
                    <br />
                
					<?
                    if ($acao=='e') {
                        
                        if ($rs->data_fim_lavagem!='') $data_fim_lavagem= desformata_data($rs->data_fim_lavagem);
                        //else $data_fim_lavagem= date("d/m/Y");
                    ?>
                        <label for="data_fim_lavagem">* Data/hora (fim):</label>
                        <input id="data_fim_lavagem" name="data_fim_lavagem" class="tamanho25p" value="<?= $data_fim_lavagem; ?>" title="Data de fim da lavagem" onkeyup="formataData(this);" maxlength="10" />
                        
                        <?
                        if ($rs->hora_fim_lavagem!='') $hora_fim_lavagem= $rs->hora_fim_lavagem;
                        //else $hora_fim_lavagem= date("H:i:s");
                        ?>
                        
                        <input id="hora_fim_lavagem" name="hora_fim_lavagem" class="tamanho25p" value="<?= $hora_fim_lavagem; ?>" title="Hora de fim da lavagem"  onkeyup="formataHora(this);" maxlength="8" />
                        <br />
                    
                    <? } ?>
                </div>
                
                <label for="id_funcionario">* Lavador:</label>
                <select id="id_funcionario" name="id_funcionario">
                    <?
                    $j=0;
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_carreiras.id_departamento = '2'
                                                /* and   rh_carreiras.id_cargo = '4' */
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   rh_funcionarios.status_funcionario = '1'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= primeira_palavra($rs_fun->nome_rz); ?></option>
                    <? $j++; } ?>
                </select>
                <br />
                
                <?
                $codigo_equipamento= pega_codigo_do_equipamento($rs->id_equipamento);
                ?>
                <label for="equipamento">* Lavadora:</label>
                <input id="equipamento" name="equipamento" value="<?= $codigo_equipamento; ?>" class="tamanho25p espaco_dir" onblur="pegaEquipamento(this.value, 1, '<?=$acao;?>');" />
                <div id="equipamento_atualiza">
                    <input id="id_equipamento" name="id_equipamento" value="<?= $rs->id_equipamento; ?>" title="Lavadora" class="escondido" />
                </div>
                <br />
                
                <?
                $codigo_processo= pega_codigo_do_processo($rs->id_processo);
                ?>
                <label for="processo">* Processo:</label>
                <input id="processo" name="processo" value="<?= $codigo_processo; ?>" class="tamanho25p espaco_dir" onblur="pegaProcesso(this.value);" />
                <div id="processo_atualiza">
                    <input id="id_processo" name="id_processo" value="<?= $rs->id_processo; ?>" title="Processo" class="escondido" />
                </div>
                <br />
                
            </div>
            <div class="parte50 gigante">
                
                <? /*
                <div id="div_id_cliente" <? if (($acao=="e") && ($rs->id_cliente=="0")) { ?>class="escondido"<? } ?>>
					<?
                    $codigo_cliente= pega_codigo_do_cliente($rs->id_cliente);
                    ?>
                    <label for="cliente">Cliente:</label>
                    <input id="cliente" name="cliente" value="<?= $codigo_cliente; ?>" class="tamanho25p espaco_dir" onblur="pegaCliente(this.value); mostraTipoRoupaLavagem(1);" />
                    <div id="cliente_atualiza">
                    	<div id="nome_cliente"></div>
                        <input id="id_cliente" name="id_cliente" value="<?= $rs->id_cliente; ?>" title="Cliente" class="escondido" />
                    </div>
                    <br />
                </div>
                
                <div id="div_id_peca"  <? if (($acao=="i") || (($acao=="e") && ($rs->id_peca=="0"))) { ?>class="escondido"<? } ?>>
                    <label for="id_peca">Roupa:</label>
                    <select id="id_peca" name="id_peca" onblur="mostraTipoRoupaLavagem(2);">
                        <option value="">---</option>
						<?
                        $i=1;
                        $vetor= pega_tipo_roupa("l");
                        
                        while($vetor[$i]) {
                        ?>
                        <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $i; ?>" <? if ($rs->id_peca==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br />
                </div>
                */ ?>
                
                <? if ($acao=='i') { ?>
					
                    <fieldset class="borda_vermelha">
                    	<legend class="vermelho">Peças especiais</legend>
                        
                        <table cellpadding="0" cellspacing="0" class="menor">
                            <thead>
                                <th valign="top" align="left" class="tamanho160">Tipo de roupa</th>
                                <th valign="top" align="left" class="tamanho80">Quantidade</th>
                                <th valign="top" align="left" class="tamanho90">Cliente</th>
                                <th valign="top">&nbsp;</th>
                            </thead>
                        </table>
                        <br />
                        
                        <div id="pecas">
                        	<? for ($i=1; $i<2; $i++) { ?>
                            <div id="div_peca_<?=$i;?>">
                                <code class="escondido"></code>
                                
                                <div id="div_tipo_roupa_<?=$i;?>">
                                    <select class="tamanho160 espaco_dir" id="id_tipo_roupa_<?=$i;?>" name="id_tipo_roupa[]" title="Tipo de roupa">
                                        <option value="">-</option>
                                        <?
                                        $result_pecas= mysql_query("select * from op_limpa_pecas
                                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                                    and   status_peca = '1'
                                                                    order by peca asc
                                                                    ");
                                        $j=1;
                                        while ($rs_peca= mysql_fetch_object($result_pecas)) {
                                        ?>
                                        <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_peca->id_peca==$rs->id_tipo_roupa) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
                                        <? $j++; } ?>
                                    </select>
                    			</div>
                                
                                <input id="qtde_pecas_<?=$i;?>" name="qtde_pecas[]" class="espaco_dir tamanho70" value="<?= $qtde_pecas; ?>" title="Qtde peças" />
                                <input id="cliente_<?=$i;?>" name="cliente[]" class="espaco_dir tamanho70" value="<?= $id_cliente; ?>" title="Cliente" onblur="pegaClienteMultiploSimples(this.value, '<?=$i;?>');" />
                                
                                <div id="cliente_atualiza_<?=$i;?>" class="flutuar_esquerda tamanho100">
                                	<input type="hidden" class="escondido" name="id_cliente_peca[]" id="id_cliente_peca_<?=$i;?>" value="" />
                                    &nbsp;
                                </div>
                                
                                <a href="javascript:void(0);" onclick="removeDiv('pecas', 'div_peca_<?=$i;?>');">remover</a>
                                <br />
                            </div>
                        <? } ?>
                        </div>
                        
                        <br />
                    
                        <button type="button" id="adiciona_carrinho" onclick="criaEspacoSujaLavagem();">+ peça de roupa</button>
                        <br />
                    </fieldset>
					
					<? for ($c=1; $c<3; $c++) { ?>
                    <fieldset>
                        <legend>Cesto <?=$c;?></legend>
                        <? $i=1; ?>
                        
                        <div id="lavagens_<?=$c;?>">
                            <div id="div_lavagem_<?=$c;?>_<?=$i;?>">
                                <code class="escondido"></code>
                                
                                <input type="hidden" id="id_cesto_<?=$c;?>_<?=$i;?>" name="id_cesto[]" value="<?=$c;?>" class="escondido" />
                                
                                <label for="cliente_<?=$c;?>_<?=$i;?>">* Cliente:</label>
                                <input id="cliente_<?=$c;?>_<?=$i;?>" name="cliente_<?=$c;?>_<?=$i;?>" value="<?= $codigo_cliente; ?>" class="tamanho25p espaco_dir" onblur="pegaClienteMultiplo(<?=$c;?>, <?=$i;?>, this.value);" />
                                <div id="cliente_atualiza_<?=$c;?>_<?=$i;?>">
                                    <div id="nome_cliente_<?=$c;?>_<?=$i;?>"></div>
                                    <input id="id_cliente_peca_<?=$c;?>_<?=$i;?>" name="id_cliente_peca[]" value="<?= $rs->id_cliente; ?>" title="Cliente" class="escondido" />
                                </div>
                                <br />
                                
                                <label for="peso_<?=$c;?>_<?=$i;?>">* Peso:</label>
                                <? if ($acao=='i') $peso1= ""; else $peso1= fnum($rs->peso1); ?>
                                <input id="peso_<?=$c;?>_<?=$i;?>" name="peso[]" class="espaco_dir tamanho25p" value="<?= $peso1; ?>" onkeydown="formataValor(this,event);" onkeyup="calculaTotalPesoCestos();" onblur="copiaValorCesto(<?=$c;?>, this.value); calculaTotalPesoCestos();" title="Peso" />
                                <a href="javascript:void(0);" onclick="removeDiv('lavagens_<?=$c;?>', 'div_lavagem_<?=$c;?>_<?=$i;?>'); calculaTotalPesoCestos();">remover</a>
                                <br />
                            </div>
                        </div>
                        
                        <label>&nbsp;</label>
                        <a href="javascript:void(0);" onclick="criaEspacoCestoLavagem('<?=$c;?>');">+ cliente</a>
                        <br />
                        
                    </fieldset>
                    <? } ?>
                <?
                }//fim inserção
				//edição
				else {
					$peso_total= 0;
				?>
                	
                    <fieldset>
                    	<legend>Peças de roupa</legend>
                        
                        <table cellpadding="0" cellspacing="0" class="menor">
                            <thead>
                                <th valign="top" align="left" class="tamanho160">Tipo de roupa</th>
                                <th valign="top" align="left" class="tamanho80">Quantidade</th>
                                <th valign="top" align="left" class="tamanho90">Cliente</th>
                                <th valign="top">&nbsp;</th>
                            </thead>
                        </table>
                        <br />
                        
                        <div id="pecas">
                        	<?
                            $result_lavagem_pecas= mysql_query("select * from op_suja_lavagem_pecas
																	where id_lavagem = '". $rs->id_lavagem ."'
																	order by id_lavagem_peca asc
																	");
							$i=1;
							while ($rs_lavagem_pecas= mysql_fetch_object($result_lavagem_pecas)) {
								$codigo_cliente= pega_codigo_do_cliente($rs_lavagem_pecas->id_cliente);
							?>
                            <div id="div_peca_<?=$i;?>">
                                <code class="escondido"></code>
                                
                                <div id="div_tipo_roupa_<?=$i;?>">
                                    <select class="tamanho160 espaco_dir" id="id_tipo_roupa_<?=$i;?>" name="id_tipo_roupa[]" title="Tipo de roupa">
                                        <option value="">-</option>
                                        <?
                                        $result_pecas= mysql_query("select * from op_limpa_pecas
                                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                                    and   status_peca = '1'
                                                                    order by peca asc
                                                                    ");
                                        $j=1;
                                        while ($rs_peca= mysql_fetch_object($result_pecas)) {
                                        ?>
                                        <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_lavagem_pecas->id_tipo_roupa==$rs_peca->id_peca) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
                                        <? $j++; } ?>
                                    </select>
                    			</div>
                                
                                <input id="qtde_pecas_<?=$i;?>" name="qtde_pecas[]" class="espaco_dir tamanho70" value="<?= $rs_lavagem_pecas->qtde_pecas; ?>" title="Qtde peças" />
                                <input id="cliente_<?=$i;?>" name="cliente[]" class="espaco_dir tamanho70" value="<?= $codigo_cliente; ?>" title="Cliente" onblur="pegaClienteMultiploSimples(this.value, '<?=$i;?>');" />
                                
                                <div id="cliente_atualiza_<?=$i;?>" class="flutuar_esquerda tamanho100">
                                	<input type="hidden" class="escondido" name="id_cliente[]" id="id_cliente_<?=$i;?>" value="<?=$rs_lavagem_pecas->id_cliente;?>" />
                                    <?=pega_sigla_pessoa($rs_lavagem_pecas->id_cliente);?>
                                </div>
                                
                                <a href="javascript:void(0);" onclick="removeDiv('pecas', 'div_peca_<?=$i;?>');">remover</a>
                                <br />
                            </div>
	                        <? $i++; } ?>
                        </div>
                        
                        <br />
                    
                        <button type="button" id="adiciona_carrinho" onclick="criaEspacoSujaLavagem();">+ peça de roupa</button>
                        <br />
                    </fieldset>
                    
                	<? for ($c=1; $c<3; $c++) { ?>
                    <fieldset>
                        <legend>Cesto <?=$c;?></legend>
                        
                        <div id="lavagens_<?=$c;?>">
							<?
                            $result_cestos= mysql_query("select * from op_suja_lavagem_cestos
                                                            where id_lavagem = '". $rs->id_lavagem ."'
                                                            and   id_cesto = '". $c ."'
                                                            order by id_cesto_lavagem asc
                                                            ");
							$i=1;
                            while ($rs_cestos= mysql_fetch_object($result_cestos)) {
								if ($rs_cestos->id_cliente==0) $codigo_cliente= 0;
								else $codigo_cliente= pega_codigo_do_cliente($rs_cestos->id_cliente);
								
								$peso_total+= $rs_cestos->peso;
                            ?>
                            <div id="div_lavagem_<?=$c;?>_<?=$i;?>">
                                <code class="escondido"></code>
                                
                                <input type="hidden" id="id_cesto_<?=$c;?>_<?=$i;?>" name="id_cesto[]" value="<?=$c;?>" class="escondido" />
                                
                                <label for="cliente_<?=$c;?>_<?=$i;?>">* Cliente:</label>
                                <input id="cliente_<?=$c;?>_<?=$i;?>" name="cliente_<?=$c;?>_<?=$i;?>" value="<?= $codigo_cliente; ?>" class="tamanho25p espaco_dir" onblur="pegaClienteMultiplo(<?=$c;?>, <?=$i;?>, this.value);" />
                                <div id="cliente_atualiza_<?=$c;?>_<?=$i;?>">
                                    <div id="nome_cliente_<?=$c;?>_<?=$i;?>"></div>
                                    <input id="id_cliente_<?=$c;?>_<?=$i;?>" name="id_cliente[]" value="<?= $rs->id_cliente; ?>" title="Cliente" class="escondido" />
                                </div>
                                <br />
                                
                                <label for="peso_<?=$c;?>_<?=$i;?>">* Peso:</label>
                                <input id="peso_<?=$c;?>_<?=$i;?>" name="peso[]" class="espaco_dir tamanho25p" value="<?= fnum($rs_cestos->peso); ?>" onkeydown="formataValor(this,event);" onkeyup="calculaTotalPesoCestos();" title="Peso" />
                                <a href="javascript:void(0);" onclick="removeDiv('lavagens_<?=$c;?>', 'div_lavagem_<?=$c;?>_<?=$i;?>'); calculaTotalPesoCestos();">remover</a>
                                <br />
                            </div>
                            
                            <script language="javascript" type="text/javascript">
	                            pegaClienteMultiplo(<?=$c;?>, <?=$i;?>, '<?= $codigo_cliente; ?>');
							</script>
                            <?
                            	$i++;
							}
							?>
                        </div>
                        
                        <label>&nbsp;</label>
                        <a href="javascript:void(0);" onclick="criaEspacoCestoLavagem('<?=$c;?>');">+ cliente</a>
                        <br />
                        
                    </fieldset>
                    <? } ?>
                <? } ?>
                
                <label for="peso_total">Peso total:</label>
                <input id="peso_total" name="peso_total" class="espaco_dir tamanho25p" value="<? if ($acao=='e') echo fnum($peso_total); ?>" title="Peso" /> kg
                <br />
                
                <br /><br />
            </div>
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </fieldset>
    </form>
</div>
<div class="parte20">
	<fieldset>
    	<legend>Lavadoras</legend>
        
        <?
		$result= mysql_query("select * from op_equipamentos
								where id_empresa = '". $_SESSION["id_empresa"] ."' 
								and   tipo_equipamento = '1'
								order by codigo asc
								");
		?>
        
        <table cellspacing="0" width="100%">
        	<tr>
            	<th align="left">Lavadora</th>
                <th>Ocupada</th>
            </tr>
            <?
            $i=0;
            while ($rs= mysql_fetch_object($result)) {
                if (($i%2)==0) $classe= "cor_sim";
                else $classe= "cor_nao";
            ?>
            <tr class="<?= $classe; ?> corzinha">
                <td><?= $rs->id_equipamento .". ". $rs->equipamento; ?></td>
                <td align="center">
                	<?
					$id_lavagem= pega_id_lavagem_pelo_id_equipamento($rs->id_equipamento);
					
                    if ($rs->ocupado==1) {	
						if ($id_lavagem!="") {
					?>
                    <button onclick="var confirma= confirm('Tem certeza que deseja finalizar a lavagem?'); if (confirma) finalizaLavagem('<?= $id_lavagem; ?>');">
	                    <?= sim_nao($rs->ocupado); ?>
                    </button>
                    <?
						}
						else {
							$result2= mysql_query("update op_equipamentos
													set   ocupado= '0'
													where id_equipamento = '". $rs->id_equipamento ."'
													") or die(mysql_error());
							if (!$result2) $var++;
							
							echo sim_nao(0);
						}
                    }
					else echo sim_nao($rs->ocupado); ?>
                </td>
            </tr>
            <? $i++; } ?>
        </table>
    </fieldset>
    <br />
    
    <fieldset>
    	<legend>Remessas do dia</legend>
        
        <div id="div_remessa_lista">
        	Digite a data para buscar as remessas.
        </div>
        <br />

    </fieldset>
</div>

<script language="javascript" type="text/javascript">
	<? if ($acao=='e') { ?>
	pegaRemessa(<?= $id_remessa; ?>);
	pegaEquipamento(<?= $codigo_equipamento; ?>, 1, '<?=$acao;?>');
	pegaProcesso(<?= $codigo_processo; ?>);
	buscaRemessasDoDia();
	 
	daFoco("data_fim_lavagem");
	<? } else { ?>
	daFoco("data_remessa");
	<? } ?>
</script>
<? } ?>
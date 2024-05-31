<?
require_once("conexao.php");
if (pode("pl", $_SESSION["permissao"])) {
	
	if ($_GET["extra"]==1) $extra=1;
	if ($_POST["extra"]==1) $extra=1;
	
	if ($_GET["origem"]!="") $origem=$_GET["origem"];
	if ($_POST["origem"]!="") $origem=$_POST["origem"];
	
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_limpa_pesagem
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_pesagem = '". $_GET["id_pesagem"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$extra= $rs->extra;
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<div id="retorno" class="escondido">
</div>
    
<h2>Pesagem de roupa limpa</h2>

<div class="parte80">
    <form action="<?= AJAX_FORM; ?>formPesagemLimpa&amp;acao=<?= $acao; ?>" method="post" name="formPesagemLimpa" id="formPesagemLimpa" onsubmit="return validaFormNormal('validacoes', false, 1);">
        
        <input class="escondido" type="hidden" id="validacoes" value="id_cliente@vazio@cliente|data_pesagem@data|hora_pesagem@vazio|peso@vazio" />
        <? if ($acao=='e') { ?>
        <input name="id_pesagem" class="escondido" type="hidden" id="id_pesagem" value="<?= $rs->id_pesagem; ?>" />
        <? } ?>
        
        <input name="origem" class="escondido" type="hidden" id="origem" value="<?= $origem; ?>" />
        
        <fieldset>
            <legend>Dados</legend>
            
            <? /*if (($_SESSION["id_turno_sessao"]=="") && ($_SESSION["tipo_usuario"]!="a")) { ?>
            
            <h3 class="vermelho">ESCOLHA UM TURNO PARA CADASTRAR AS PESAGENS!</h3>
            
            <? } else { */ ?>
            
            <div class="parte50 gigante">
                
                <label>Turno:</label>
                <h3 class="vermelho">
                <?
                if ($_SESSION["id_turno_sessao"]!="-3")
					echo pega_turno(pega_turno_pelo_horario(date("Y-m-d H:i:s")));
				else
					echo pega_turno($_SESSION["id_turno_sessao"]);
				?></h3>
                
				<br />
                
				<?
                /*$result_remessa= mysql_query("select * from op_suja_remessas
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by data_remessa desc, hora_chegada desc limit 1
                                                ");
                $rs_remessa= mysql_fetch_object($result_remessa);
                
                
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
                */ ?>
                
                <? if ( ($acao=='i') && ($extra==1) ) { ?>
                <input name="extra" class="escondido" type="hidden" id="extra" value="<?= $extra; ?>" />
                
                <label>&nbsp;</label>
                <h3 class="vermelho">PESAGEM PARA ENTREGA EXTRA</h3>
                
                <? } elseif ($acao=='e') { ?>
                <label>Tipo:</label>
                <select name="extra" id="extra">
                	<option value="0" <? if ($rs->extra=="0") echo "selected='selected'"; ?>>Pesagem normal</option>
                	<option value="1" <? if ($rs->extra=="1") echo "selected='selected'"; ?> class="cor_sim">Pesagem extra</option>
                </select>
                <? } ?>
                
                <?
				if ($acao=='i') $data_pesagem= date("d/m/Y");
	            else $data_pesagem= desformata_data($rs->data_pesagem);
				
				if ($acao=='i') $hora_pesagem= date("H:i:s");
                else $hora_pesagem= $rs->hora_pesagem;
					
				if ($_SESSION["id_turno_sessao"]=="") {    
                ?>
                <label for="data_pesagem">* Data/hora:</label>
                <input id="data_pesagem" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="data_pesagem" class="tamanho25p" value="<?= $data_pesagem; ?>" title="Data da pesagem" onkeyup="formataData(this);" maxlength="10" />
                
                <input id="hora_pesagem" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="hora_pesagem" class="tamanho25p" value="<?= $hora_pesagem; ?>" title="Hora da pesagem"  onkeyup="formataHora(this);" maxlength="8" />
                <br />
                <? } else { ?>
                <label for="data_pesagem">* Data/hora:</label>
                <span class="gigante_texto"><?= date("d/m/Y H:i:s"); ?></span>
                <br /><br />
                
                <input id="data_pesagem" type="hidden" name="data_pesagem" class="escondido" value="<?= $data_pesagem; ?>" />
                <input id="hora_pesagem" type="hidden" name="hora_pesagem" class="escondido" value="<?= $hora_pesagem; ?>" />
                <? } ?>
                
                <?
                $codigo_cliente= pega_codigo_do_cliente($rs->id_cliente)
                ?>
                <label for="cliente">* Cliente:</label>
                <input id="cliente" name="cliente" value="<?= $codigo_cliente; ?>" class="tamanho15p espaco_dir" onblur="pegaCliente(this.value); carregaPesagensSuja();" />
                <div id="cliente_atualiza">
                    <input id="id_cliente" name="id_cliente" value="<?= $rs->id_cliente; ?>" title="Cliente" class="escondido" />
                </div>
                <br />
                
                <? /*if (($acao=='i') && ($extra==1)) { ?>
                <label for="extra_modo">Pesagem:</label>
                <select name="extra_modo" id="extra_modo">
                	<option value="1">Já foi pesado</option>
                    <option value="2" class="cor_sim">Não foi pesado</option>
                </select>
                <br />
                <? } */ ?>
                
                <? /*
                <label for="id_tipo_roupa">* Roupa:</label>
                <select id="id_tipo_roupa" name="id_tipo_roupa" title="Tipo de roupa" onblur="verificaQtdePacote(this.value);">
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
                <br />
                */ ?>
                <br />
				
                <label for="costura">Costura:</label>
                <input type="checkbox" class="tamanho30" value="1" name="costura" id="costura" <? if ($rs->costura==1) echo "checked=\"checked\""; ?> />
                <br /><br />
                
                <label for="goma">Goma:</label>
                <input type="checkbox" class="tamanho30" value="1" name="goma" id="goma" <? if ($rs->goma==1) echo "checked=\"checked\""; ?> />
                <br /><br />
                
                <label for="roupa_alheia">Roupa de outras unidades:</label>
                <input type="checkbox" class="tamanho30" value="1" name="roupa_alheia" id="roupa_alheia" <? if ($rs->roupa_alheia==1) echo "checked=\"checked\""; ?> />
                <br /><br />
                
                <? /* if ($acao=='e') { ?>
                <br />
                <label for="id_turno">Turno:</label>
                <select name="id_turno" id="id_turno" title="Turno">
                    <option value="">---</option>
                    <?
                    $result_turno= mysql_query("select * from rh_turnos
												where status_turno = '1'
												and   id_turno_index <> '0'
												order by ordem asc ");
                    $i=0;
                    while ($rs_turno= mysql_fetch_object($result_turno)) {
                        if (($i%2)==1) $classe= " class=\"cor_sim\"";
                        else $classe= " ";
                    ?>
                    <option <?=$classe;?> value="<?=$rs_turno->id_turno;?>" <? if ($rs->id_turno==$rs_turno->id_turno) echo "selected=\"selected\""; ?>><?=$rs_turno->turno;?></option>
                    <?
                        $i++;
                    }
					
					if (($i%2)==1) {
						$classe1= " class=\"cor_sim\"";
						$classe2= " ";
					}
                    else {
						$classe1= " ";
						$classe2= " class=\"cor_sim\"";
					}
                    ?>
                    <option <?=$classe1;?> value="-1" <? if ($rs->id_turno=="-1") echo "selected=\"selected\""; ?>>PLANTÃO SÁBADO</option>
                    <option <?=$classe2;?> value="-2" <? if ($rs->id_turno=="-2") echo "selected=\"selected\""; ?>>PLANTÃO DOMINGO</option>
                    <option <?=$classe1;?> value="-3" <? if ($rs->id_turno=="-3") echo "selected=\"selected\""; ?>>COSTURA</option>
                </select>
                <br />
                <? } */ ?>
                
            </div>
            <div class="parte50 gigante">
                
                <fieldset>
                	<legend>Peças de roupa</legend>
                    
                    <table cellpadding="0" cellspacing="0" class="menor">
                    	<thead>
                        	<th valign="top" align="left" class="tamanho160">Tipo de roupa</th>
                            <th valign="top" align="left" class="tamanho80">Pacotes completos</th>
                            <th valign="top" align="left" class="tamanho90">Pacotes com sobra</th>
                            <th valign="top" align="left" class="tamanho120">Peças (sobra)</th>
                            <th valign="top">&nbsp;</th>
                        </thead>
                    </table>
                    <br />
                    
                    <div id="pesagens">
						<?
						if ($acao=='i') {
							for ($i=1; $i<2; $i++) {
								$tab= $i+7;
							?>
							<div id="div_pesagem_<?=$i;?>">
								<code class="escondido"></code>
								
								<div id="div_tipo_roupa_<?=$i;?>">
									<select class="tamanho160 espaco_dir" id="id_tipo_roupa_<?=$i;?>" name="id_tipo_roupa[]" title="Tipo de roupa" <? if ($i==1) { ?> onblur="verificaGrupoRoupa(this.value);" <? } ?>>
										<option value="">-</option>
										<?
										
										if ($i>1) $str_aqui= "and   id_grupo = '2' ";
										
										$result_pecas= mysql_query("select * from op_limpa_pecas
																	where id_empresa = '". $_SESSION["id_empresa"] ."'
																	and   status_peca = '1'
																	$str_aqui
																	order by peca asc
																	");
										$j=1;
										while ($rs_peca= mysql_fetch_object($result_pecas)) {
										?>
										<option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_peca->id_peca==$rs->id_tipo_roupa) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
										<? $j++; } ?>
									</select>
								</div>
					
								<? $peso= ""; ?>
								<input id="num_pacotes_<?=$i;?>" name="num_pacotes[]" class="espaco_dir tamanho70" value="<?= $num_pacotes; ?>" title="Pacotes" />
                                <input id="pacotes_sobra_<?=$i;?>" name="pacotes_sobra[]" class="espaco_dir tamanho70" value="<?= $pacotes_sobra; ?>" title="Pacotes de sobra" />
                                <input id="qtde_pecas_sobra_<?=$i;?>" name="qtde_pecas_sobra[]" class="espaco_dir tamanho70" value="<?= $qtde_pecas_sobra; ?>" title="Sobra" />
								
								<a href="javascript:void(0);" onclick="removeDiv('pesagens', 'div_pesagem_<?=$i;?>');">remover</a>
								<br />
							</div>
						<?
                            }
						}
						else {
							
							$result_pesagens_pecas= mysql_query("select * from op_limpa_pesagem_pecas
																	where id_pesagem = '". $rs->id_pesagem ."'
																	order by id_pesagem_peca asc
																	");
							$i=1;
							while ($rs_pesagens_pecas= mysql_fetch_object($result_pesagens_pecas)) {
								if ($i==1) $tipo_roupa_1_e= pega_id_grupo_da_peca($rs_pesagens_pecas->id_tipo_roupa);
							?>
							<div id="div_pesagem_<?=$i;?>">
								<code class="escondido"></code>
								
								<div id="div_tipo_roupa_<?=$i;?>">
									<select class="tamanho160 espaco_dir" id="id_tipo_roupa_<?=$i;?>" name="id_tipo_roupa[]" title="Tipo de roupa" <? if ($i==1) { ?> onblur="verificaGrupoRoupa(this.value);" <? } ?>>
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
										<option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_peca->id_peca==$rs_pesagens_pecas->id_tipo_roupa) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
										<? $j++; } ?>
									</select>
								</div>
					
								<? $peso= ""; ?>
								<input id="num_pacotes_<?=$i;?>" name="num_pacotes[]" class="espaco_dir tamanho70" value="<?= $rs_pesagens_pecas->num_pacotes; ?>" title="Pacotes" />
								<input id="pacotes_sobra_<?=$i;?>" name="pacotes_sobra[]" class="espaco_dir tamanho70" value="<?= $rs_pesagens_pecas->pacotes_sobra; ?>" title="Pacotes de sobra" />
								<input id="qtde_pecas_sobra_<?=$i;?>" name="qtde_pecas_sobra[]" class="espaco_dir tamanho70" value="<?= $rs_pesagens_pecas->qtde_pecas_sobra; ?>" title="Sobra" />
								
								<a href="javascript:void(0);" onclick="removeDiv('pesagens', 'div_pesagem_<?=$i;?>');">remover</a>
								<br />
							</div>
                            <? $i++; } ?>
                        <? } ?>
                    </div>
	                <br />
                    
                    <button type="button" id="adiciona_carrinho" <? if ($tipo_roupa_1_e==1) { ?> disabled="disabled" <? } ?> onclick="criaEspacoLimpaPesagem();">+ carrinho</button>
                    <br />
                    
                </fieldset>
                
                <label for="peso">* Peso total:</label>
                <? if ($acao=='i') $peso= ""; else $peso= fnum($rs->peso); ?>
                <input id="peso" name="peso" class="espaco_dir tamanho15p" value="<?= $peso; ?>" onkeydown="formataValor(this,event);" title="Peso do carrinho" /> kg
                <br />
                
                <br /><br />
            </div>
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
            <? //} ?>
            
        </fieldset>
    </form>
</div>

<div class="parte20">
	<fieldset>
    	<legend>Roupa suja</legend>
        
        <div id="div_remessa_lista">
        	Identifique o cliente para carregar.	
        </div>
        <br />

    </fieldset>
</div>


<script language="javascript" type="text/javascript">
	<? if ($acao=='i') { ?>
	daFoco("cliente");
	<? } ?>
	
	<? if ($acao=='e') { ?>
	daFoco("costura");
	//pegaRemessa();
	pegaCliente("<?= $codigo_cliente; ?>");
	//pegaNumeroPacotes();
	//buscaRemessasDoDia();
	<? } ?>
</script>
<? } ?>
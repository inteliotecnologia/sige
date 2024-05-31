<?
require_once("conexao.php");
if (pode("ey", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from tr_percursos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_percurso = '". $_GET["id_percurso"] ."'
								");
							
		$rs= mysql_fetch_object($result);
	}
?>

<div id="assinatura_digital" class="telinha1 screen">
	<a href="javascript:void(0);" onclick="fechaDiv('assinatura_digital');" class="fechar">x</a>
    
    <h2>Assinatura digital</h2>
    
    <div id="assinatura_digital_conteudo">
    
    </div>
</div>

<h2>Controle de percursos</h2>

<form action="<?= AJAX_FORM; ?>formPercurso&amp;acao=<?= $acao; ?>" method="post" name="formPercurso" id="formPercurso" onsubmit="return validaFormNormal('validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_veiculo@vazio|id_motorista@vazio|tipo@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_percurso" class="escondido" type="hidden" id="id_percurso" value="<?= $rs->id_percurso; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
        	
            <label for="tipo">Tipo:</label>
            <select name="tipo" id="tipo" title="Tipo" onchange="alteraTipoPercurso(this.value, '<?=$rs->id_percurso;?>', '<?=$acao;?>');">
				<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                <?
                $vetor= pega_coleta_entrega('l');
                $i=1;
                while ($vetor[$i]) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>" <? if ($rs->tipo==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <div id="div_regiao" class="nao_mostra">
                <label for="id_regiao">Região:</label>
                <select name="id_regiao" id="id_regiao" title="Região" onchange="alteraRegiao(this.value, '<?=$rs->id_percurso;?>', '<?=$acao;?>');">
                    <option value="">- TODAS -</option>
                    <?
                    $result_reg= mysql_query("select * from fi_clientes_regioes
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   status_regiao = '1'
                                                order by regiao asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_reg = mysql_fetch_object($result_reg)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_reg->id_regiao; ?>" <? if ($rs_reg->id_regiao==$rs->id_regiao) echo "selected=\"selected\""; ?>><?= $rs_reg->regiao; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
            </div>
            <br />
            
            <div id="percurso">
            
            </div>
            <br />
            
            <label for="veiculo_permissao">Abrir todos veículos:</label>
            <input onclick="permiteTodosVeiculosPercurso(this);" <? if ($rs->veiculo_permissao==1) echo "checked=\"checked\""; ?> type="checkbox" class="tamanho15" name="veiculo_permissao" id="veiculo_permissao" title="Abrir todos" value="1" />
            <br /><br />
            
            <label for="id_veiculo">Veículo:</label>
            <div id="id_veiculo_atualiza">
                <select name="id_veiculo" id="id_veiculo" title="Veículo">
                    <? if ($acao=='i') { ?>
                    <option value="">-</option>
                    <? } ?>
                    
                    <?
					if ($rs->veiculo_permissao!=1) $str= " and   tipo_padrao = '". $rs->tipo ."' ";
					
                    $result_vei= mysql_query("select * from op_veiculos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												". $str ."
												and   status_veiculo = '1'
                                                order by veiculo asc,
                                                placa asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_vei = mysql_fetch_object($result_vei)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_vei->id_veiculo; ?>" <? if ($rs_vei->id_veiculo==$rs->id_veiculo) echo "selected=\"selected\""; ?>><?= $rs_vei->veiculo ." ". $rs_vei->placa; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <br />
            
            <label for="id_motorista">Motorista:</label>
            <select id="id_motorista" name="id_motorista" title="Motorista">
				<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                
				<?
                $j=0;
                $result_fun= mysql_query("select *
                                            from  pessoas, rh_funcionarios, rh_carreiras
                                            where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                            and   pessoas.tipo = 'f'
                                            and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                            and   rh_carreiras.atual = '1'
                                            and   
											(
											 	(rh_carreiras.id_departamento = '1' and rh_carreiras.id_cargo = '10')
												or
												(rh_carreiras.id_departamento = '3')
												or
												(rh_carreiras.id_departamento = '5')
												or
												(rh_carreiras.id_departamento = '6')
												or
												(rh_carreiras.id_departamento = '8')
												or
												(rh_carreiras.id_departamento = '12')
												or
												(rh_carreiras.id_departamento = '13')
												or
												(rh_carreiras.id_departamento = '14')
												or
												(rh_carreiras.id_departamento = '15')
												or
												(rh_carreiras.id_departamento = '16')
												or
												(rh_carreiras.id_departamento = '17')
												or
												(rh_carreiras.id_departamento = '19')
												or
												(rh_carreiras.id_departamento = '20')
												or
												(rh_carreiras.id_departamento = '22')
												or
												(rh_carreiras.id_departamento = '23')
												
											)
											and   rh_funcionarios.afastado <> '1'
                                            and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
                                            order by pessoas.nome_rz asc
                                            ") or die(mysql_error());
                while ($rs_fun= mysql_fetch_object($result_fun)) {
                ?>
                <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_motorista) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $j++; } ?>
            </select>
            <br />

        </div>
        <div class="parte50">
        	<div id="espaco">
				<?
                if ($acao=='i') {
                    $j=1;
                ?>
                <fieldset>
                    <legend>Saída da empresa</legend>
                    
                    <input name="passo[]" id="passo_<?=$j;?>" type="hidden" class="escondido" value="<?= $j; ?>" />
                    
                    <label class="tamanho50" for="data_percurso_<?=$j;?>">Data:</label>
                    <input name="data_percurso[]" id="data_percurso_<?=$j;?>" class="tamanho80" onkeyup="formataData(this);" maxlength="10" value="<?= date("d/m/Y"); ?>" title="Data" />
                    
                    <label class="tamanho40" for="hora_percurso_<?=$j;?>">Hora:</label>
                    <input name="hora_percurso[]" id="hora_percurso_<?=$j;?>" class="tamanho50" onkeyup="formataHora(this);" maxlength="5" value="<?= date("H:i"); ?>" title="Hora" />
                    
                    <label class="tamanho30" for="km_<?=$j;?>">Km:</label>
                    <input name="km[]" id="km_<?=$j;?>" class="tamanho70" value="<?= $km; ?>" title="Km" />
                    <br />
    
                </fieldset>
                <? } else {
                
                $result_pre= mysql_query("select * from tr_percursos_passos
											where id_percurso = '". $_GET["id_percurso"] ."'
											and   passo = '1'
											") or die(mysql_error());
				$rs_pre= mysql_fetch_object($result_pre);
                
                ?>
                
                <label class="tamanho80">Data:</label>
                <?= desformata_data($rs_pre->data_percurso) ." ". $rs_pre->hora_percurso; ?> &nbsp; <a href="./?pagina=transporte/percurso_dados&id_percurso=<?= $rs->id_percurso; ?>">editar</a>
                <br /><br />
                
                <? } ?>
                
                <label for="obs" class="tamanho80">Observações:</label>
                <textarea name="obs" id="obs" title="Observações"><?=$rs->obs;?></textarea>
                <br />
				
				<?
				/*
                $result_percurso= mysql_query("select * from tr_percursos_passos
                                                where id_percurso= '". $rs->id_percurso ."'
												order by passo asc
                                                ");
                $j=0;
                while ($rs_percurso= mysql_fetch_object($result_percurso)) {
					$ultimo_passo= $rs_percurso->passo;
                ?>
                <div id="div_percurso_<?=$j;?>">
                    <fieldset>
                        <legend><?= pega_passo_percurso($rs_percurso->passo); ?></legend>
                        
                        <code class="escondido"></code>
                        
                        <input name="id_ad[]" id="id_ad_<?=$j;?>" type="hidden" class="escondido" value="<?= $rs_percurso->id_ad; ?>" />
                        <input name="passo[]" id="passo_<?=$j;?>" type="hidden" class="escondido" value="<?= $rs_percurso->passo; ?>" />
                        
                        <?
                        if ($acao=='i') $data_percurso= date("d/m/Y");
                        else $data_percurso= desformata_data($rs_percurso->data_percurso);
                        ?>
                        <label class="tamanho50" for="data_percurso_<?=$j;?>">Data:</label>
                        <input name="data_percurso[]" id="data_percurso_<?=$j;?>" class="tamanho80" onkeyup="formataData(this);" maxlength="10" value="<?= $data_percurso; ?>" title="Data" />
                        
                        <?
                        if ($acao=='i') $hora_percurso= date("H:i:s");
                        else $hora_percurso= $rs_percurso->hora_percurso;
                        ?>
                        <label class="tamanho40" for="hora_percurso_<?=$j;?>">Hora:</label>
                        <input name="hora_percurso[]" id="hora_percurso_<?=$j;?>" class="tamanho50" onkeyup="formataHora(this);" maxlength="5" value="<?= substr($hora_percurso, 0, 5); ?>" title="Hora" />
                        
                        <?
                        if ($acao=='i') $km= "";
                        else $km= fnum($rs_percurso->km);
                        
                        if ($km==0) $km= "";
                        ?>
                        <label class="tamanho30" for="km_<?=$j;?>">Km:</label>
                        <input name="km[]" id="km_<?=$j;?>" class="tamanho70" value="<?= $km; ?>" onkeydown="formataValor(this,event);" title="Km" />
                        <br />
                        
                        <div id="div_id_cliente_<?=$j;?>">
							<? if (($rs_percurso->passo==3) || ($rs_percurso->passo==1)) { ?>
                            <input type="hidden" class="escondido" name="id_cliente[]" value="0" />
                            <? } else { ?>
                            <label class="tamanho50" for="id_cliente_<?=$j;?>">Cliente:</label>
                            <select name="id_cliente[]" id="id_cliente_<?=$j;?>" title="Cliente">
                                <? if ($acao=='i') { ?>
                                <option value="">-</option>
                                <? } ?>
                                
                                <?
                                $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                                            where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                            and   pessoas_tipos.tipo_pessoa = 'c'
                                                            order by 
                                                            pessoas.nome_rz asc
                                                            ") or die(mysql_error());
                                $k=0;
                                while ($rs_ced = mysql_fetch_object($result_ced)) {
                                ?>
                                <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if ($rs_ced->id_cedente==$rs_percurso->id_cliente) echo "selected=\"selected\""; ?>><?= $rs_ced->nome_rz; ?></option>
                                <? $k++; } ?>
                            </select>
                            <br />
                            <? } ?>
                        </div>
                        
                        <? if ($rs_percurso->passo==2) { ?>
                        <div id="div_id_ad_<?=$j;?>">
                        	<label class="tamanho50">Ass. por:</label>
                            <? if ($rs_percurso->id_ad=="0") { ?>
                            <span class="vermelho">Não assinado.</span>
                            <? } else { ?>
                            <span class="verde"><?= pega_nome_ad($rs_percurso->id_ad); ?></span>
                            <? } ?>
                            <br />
                        </div>
                        <? }//fim 2 ?>
                        
                        <? if ($rs_percurso->passo!=1) { ?>
                        <label class="tamanho50">&nbsp;</label>
                        <a href="javascript:removeDiv('espaco', 'div_percurso_<?=$j?>');" onclick="return confirm('Tem certeza que deseja remover este registro?');">remover</a></center>

                        <? if ($rs_percurso->passo==2) { ?>
                        | <a href="javascript:void(0);" onclick="abreTelinhaAd('<?=$j;?>');">assinar</a></center>
                        <? } ?>
                        
                        <? if (($rs_percurso->passo==3) && ($rs->tipo==1)) { ?>
                        <br /><br /><fieldset><legend>Associar à remessa (Área Suja)</legend>
						<label for="data_remessa">Data/número:</label>
						<input name="data_remessa" id="data_remessa" class="tamanho80 espaco_dir" onkeyup="formataData(this);"  onblur="pegaRemessa();" maxlength="10" value="<?= desformata_data(pega_dado_remessa("data_remessa", $rs->id_remessa)); ?>" title="Data da remessa" />
						<input id="num_remessa" name="num_remessa" class="tamanho15p" value="<?= pega_dado_remessa("num_remessa", $rs->id_remessa); ?>" title="Número da remessa" onblur="pegaRemessa();" />
						<div id="remessa_atualiza">
                    		<input id="id_remessa" name="id_remessa" value="" title="Remessa" class="escondido" />
                		</div>
						</fieldset>
                        
                        <script language="javascript">
							pegaRemessa();
						</script>
                        <? } ?>
                        
                        <? } ?>
                        
                    </fieldset>
                </div>
                <? $j++; } ?>
            </div>
            
			<? if (($acao=='e') && ($ultimo_passo!=3)) { ?>
            <div>
                <fieldset class="contraste">
                    <legend>Adicionar percurso</legend>
                    
                    <label class="tamanho50" for="tipo_percurso">Tipo:</label>
                    <select class="tamanho200 espaco_dir" name="tipo_percurso" id="tipo_percurso">
                        <? if ($rs->tipo!="3") { ?>
                        <option value="1">Passagem em cliente</option>
                        <? } ?>
                        <option value="2" class="cor_sim">Retorno à empresa</option>
                    </select>
                    
                    <button type="button" onclick="criaEspacoPercurso('<?= $rs->id_percurso; ?>', '<?= $rs->tipo_percurso; ?>');">criar</button>
                </fieldset>
            </div>
            <? } ?>
            */ ?>
        </div>
        
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>

<script language="javascript">
	daFoco("cronograma");
	
	<? if ($acao=='e') { ?>
	alteraTipoPercurso('<?=$rs->tipo;?>', '<?=$rs->id_percurso;?>', '<?=$acao;?>');
	<? } ?>
</script>
<? } ?>
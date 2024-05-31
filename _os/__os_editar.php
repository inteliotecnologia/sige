<?
if (!$conexao)
	require_once("conexao.php");

$result= mysql_query("select * from oss where id_os = '". $_GET["id_os"] ."' ");
$rs= mysql_fetch_object($result);
?>
<h2 class="titulos">Cadastro de ordem de serviço</h2>
	
<form action="<?= AJAX_FORM; ?>formOsEditar" method="post" name="formOsEditar" id="formOsEditar" onsubmit="return ajaxForm('conteudo', 'formOsEditar');">
	<input name="acao" type="hidden" id="acao" value="1" class="escondido" />
	<input name="id_os" type="hidden" id="id_os" value="<?= $rs->id_os; ?>" class="escondido" />
	<input name="id_s" type="hidden" id="id_s" value="<?= $rs->id_servico; ?>" class="escondido" />
	
	<div class="parte50">
    	<fieldset>
        	<legend class="escuro">Dados da ordem de serviço</legend>
    
            <label>Empresa:</label>
            <?= $_SESSION["nome_fantasia"]; ?>
            <br />
            
            <label>Data/Hora:</label>
            <?= date("d/m/Y H:i:s"); ?>
            <br />
            
            <label for="status_os">Situação:</label>
            <select name="status_os" id="status_os" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');">
                <option value="1" <? if ($rs->status_os=="1") echo "selected=\"selected\""; ?>>Em andamento</option>
                <option value="2" class="cor_sim"<? if ($rs->status_os=="2") echo "selected=\"selected\""; ?>>Finalizada</option>
            </select>
            <br />
            
            <label for="solicitante">Solicitante:</label>
            <input value="<?= $rs->solicitante; ?>" disabled="disabled" name="solicitante" id="solicitante" />
            <br />
            
            <label for="tel_solicitante">Telefone do solicitante:</label>
            <input value="<?= $rs->tel_solicitante; ?>" disabled="disabled" name="tel_solicitante" id="tel_solicitante" />
            <div class="lado_campo">
                <span class="vermelho">"(99) 9999-999"</span>
            </div>
            <br />
        
            <label for="id_tecnico">Técnico destacado:</label>
            <select name="id_tecnico" id="id_tecnico">
                <?
                $result_tec= mysql_query("select * from tecnicos order by tecnico");
                $i=1;
                while ($rs_tec= mysql_fetch_object($result_tec)) {
                ?>
                <option <? if ($i%2) echo "class=\"cor_sim\""; $i++; ?> value="<?= $rs_tec->id_tecnico; ?>" <? if ($rs->id_tecnico==$rs_tec->id_tecnico) echo "selected=\"selected\""; ?>><?= $rs_tec->tecnico; ?></option>
                <? } ?>
            </select>
            <br />
            
            <label for="tipo_atendimento">Tipo de atendimento:</label>
            <select name="tipo_atendimento" id="tipo_atendimento" disabled="disabled">
                <option value="i" class="cor_sim" <? if ($rs->tipo_atendimento=="i") echo "selected=\"selected\""; ?>>Instalação</option>
                <option value="m"<? if ($rs->tipo_atendimento=="m") echo "selected=\"selected\""; ?>>Manutenção</option>
                <option value="c" class="cor_sim"<? if ($rs->tipo_atendimento=="c") echo "selected=\"selected\""; ?>>Checklist</option>
            </select>
            <br />
        
            <label for="prioridade">Prioridade:</label>
            <select name="prioridade" id="prioridade" disabled="disabled">
                <option value="0" class="bg_verde" <? if ($rs->prioridade=="0") echo "selected=\"selected\""; ?>>Baixa</option>
                <option value="1" class="bg_amarelo" <? if ($rs->prioridade=="1") echo "selected=\"selected\""; ?>>Média</option>
                <option value="2" class="bg_vermelho" <? if ($rs->prioridade=="2") echo "selected=\"selected\""; ?>>Alta</option>
            </select>
            <br />
        
            <label for="id_servico">Serviço:</label>
            <select name="id_servico" id="id_servico" onblur="setaClasse(this, 'campo_normal');" disabled="disabled">
                <?
                $result_serv= mysql_query("select * from servicos");
                $i=1;
                while ($rs_serv= mysql_fetch_object($result_serv)) {
                ?>
                <option <? if ($i%2) echo "class=\"cor_sim\""; $i++; ?> value="<?= $rs_serv->id_servico; ?>" <? if ($rs->id_servico==$rs_serv->id_servico) echo "selected=\"selected\""; ?>><?= $rs_serv->servico; ?></option>
                <? } ?>
            </select>
            <br />
        </fieldset>
        
        <br />
        
        <fieldset>
        	<legend class="escuro">Equipamento</legend>
            
            <table cellspacing="0">
                <tr>
                    <th align="left">Equipamento</th>
                    <th align="left">Nº de série</th>
                    <th align="left">Observações</th>
                </tr>
                <tr>
                    <td class="sem"><input value="<?= $rs->equipamento; ?>" disabled="disabled" name="equipamento" id="equipamento" class="tamanho130" /></td>
                    <td class="sem"><input value="<?= $rs->nserie; ?>" disabled="disabled" name="nserie" id="nserie" /></td>
                    <td class="sem"><textarea disabled="disabled" name="obs" id="obs"><?= $rs->obs; ?></textarea></td>
                </tr>
            </table>
            
            <?
            switch ($rs->id_servico) {
                case "1":
                        $result2= mysql_query("select * from os_hemodialise where id_os = '$rs->id_os' ");
                        $rs2= mysql_fetch_object($result2);
                        ?>
                        <label for="servico_executado">Serviço executado:</label>
                        <input value="<?= $rs2->servico_executado; ?>" name="servico_executado" id="servico_executado" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
                        <br />
                        
                        <label for="material_utilizado">Material utilizado:</label>
                        <input value="<?= $rs2->material_utilizado; ?>" name="material_utilizado" id="material_utilizado" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
                        <br />
                        <?
                        break;
                case "2":
                        $result2= mysql_query("select * from os_agua where id_os = '$rs->id_os' ");
                        $rs2= mysql_fetch_object($result2);
                        ?>
                        <label for="servico_executado">Serviço executado:</label>
                        <input value="<?= $rs2->servico_executado; ?>" name="servico_executado" id="servico_executado" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
                        <br />
        </fieldset>
        
        <fieldset>
        	<legend>Observações</legend>
            
            <br />
    
            <label for="obs_gerais">Obs. gerais cliente:</label>
            <textarea name="obs_gerais" id="obs_gerais" disabled="disabled"><?= $rs->obs_gerais; ?></textarea>
            <br />
        
            <label for="obs_gerais_tecnico">Obs. gerais técnico:</label>
            <textarea name="obs_gerais_tecnico" id="obs_gerais_tecnico"><?= $rs->obs_gerais_tecnico; ?></textarea>
            <br />
        </fieldset>
	</div>
    <div class="parte50">
<fieldset>
<legend class="escuro">An&aacute;lise da &aacute;gua tratada</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <th align="left">Caracter&iacute;stica</th>
    <th>Par&acirc;metro aceit&aacute;vel</th>
    <th>Freq&uuml;&ecirc;ncia da verifica&ccedil;&atilde;o</th>
    <th>Par&acirc;metros encontrados</th>
  </tr>
  <?
	$i=1;
	$vetor= pega_analise_agua_tratada("l");
	while ($vetor[$i][0]) {
	?>
  <tr>
    <td><?= $vetor[$i][0]; ?></td>
    <td align="center"><?= $vetor[$i][1]; ?></td>
    <td align="center">Di&aacute;ria</td>
    <td><input type="hidden" class="escondido" name="id_carac[]" id="id_carac_<?=$i;?>" value="<?=$i;?>" />
        <select name="parametro[]" id="parametro_<?=$i;?>">
          <?
							$j=1;

							switch ($i) {
								case 1: $vetor_parametros= pega_cor_aparente("l"); break;
								case 2: $vetor_parametros= pega_turvacao("l"); break;
								case 3: $vetor_parametros= pega_sabor("l"); break;
								case 4: $vetor_parametros= pega_odor("l"); break;
								case 5: $vetor_parametros= pega_cloro_residual_livre("l"); break;
								case 6: $vetor_parametros= pega_ph("l"); break;
								case 7: $vetor_parametros= pega_cloro_livre("l"); break;
								case 8: $vetor_parametros= pega_temperatura("l"); break;
								case 9: $vetor_parametros= pega_dureza("l"); break;
							}
                            while ($vetor_parametros[$j]) {
								$rs_par= mysql_fetch_object(mysql_query("select * from os_agua_analise
																			where id_os = '". $rs->id_os ."'
																			and   id_carac = '". $i ."'
																			"));
                            ?>
          <option <? if (($j%2)==0) echo "class=\"cor_sim\""; ?> <? if ($rs_par->parametro==$j) echo "selected=\"selected\""; ?> value="<?=$j;?>">
            <?= $vetor_parametros[$j]; ?>
            </option>
          <?
                                $j++;
                            }
                            ?>
        </select>
    </td>
  </tr>
  <?
            	$i++;
			}
			?>
</table>
</fieldset>
<fieldset>
        	<legend class="escuro">Verificação e/ou substituição de filtros de cartuchos</legend>
        
            <ul class="quebra">
                <li>
                    <input <? if ($rs2->ver_50micras==1) echo "checked=\"checked\""; ?> class="tamanho20 sem_borda" type="checkbox" name="ver_50micras" id="ver_50micras" value="1" onchange="ativaDesativa('obs_50micras');" />
                    <label for="ver_50micras">50/50 Micras</label>
                    
                    <textarea name="obs_50micras" id="obs_50micras" <? if ($rs2->ver_50micras!=1) echo "disabled=\"disabled\" class=\"desativado\""; ?>><?= $rs2->obs_50micras; ?></textarea>
                </li>
                <li>
                    <input <? if ($rs2->ver_20micras==1) echo "checked=\"checked\""; ?>  class="tamanho20 sem_borda" type="checkbox" name="ver_20micras" id="ver_20micras" value="1" onchange="ativaDesativa('obs_20micras');" />
                    <label for="ver_20micras">20 Micras</label>
                    
                    <textarea name="obs_20micras" id="obs_20micras" <? if ($rs2->ver_20micras!=1) echo "disabled=\"disabled\" class=\"desativado\""; ?>><?= $rs2->obs_20micras; ?></textarea>
                </li>
                <li>
                    <input <? if ($rs2->ver_01micra==1) echo "checked=\"checked\""; ?>  class="tamanho20 sem_borda" type="checkbox" name="ver_01micra" id="ver_01micra" value="1" onchange="ativaDesativa('obs_01micra');" />
                    <label for="ver_01micra">01 Micra</label>
                    
                    <textarea name="obs_01micra" id="obs_01micra" <? if ($rs2->ver_01micra!=1) echo "disabled=\"disabled\" class=\"desativado\""; ?>><?= $rs2->obs_01micra; ?></textarea>
                </li>
            </ul>
            
            <br />
            
            <br />
            
            <input type="checkbox" id="ntroca" name="ntroca" value="1" class="tamanho20" />
            <label for="ntroca" class="tamanho160 nao_negrito">Não é necessário troca</label>
            
            <br />
        </fieldset>
        
        <fieldset>
        	<legend class="escuro">Desinfecção: produtos e quantidades utilizadas</legend>
            
            <ul class="quebra">
                <li>
                    <input class="tamanho20" <? if ($rs2->ver_formol40==1) echo "checked=\"checked\""; ?> type="checkbox" name="ver_formol40" id="ver_formol40" value="1" onchange="ativaDesativa('obs_formol40');" />
                    <label for="ver_formol40">Formol 40%</label>
                    
                    <input value="<?= $rs2->obs_formol40; ?>" name="obs_formol40" id="obs_formol40" <? if ($rs2->ver_formol40!=1) echo "disabled=\"disabled\" class=\"desativado\""; ?> onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
                </li>
                <li>
                    <input class="tamanho20" <? if ($rs2->ver_hipoclorito12==1) echo "checked=\"checked\""; ?> type="checkbox" name="ver_hipoclorito12" id="ver_hipoclorito12" value="1" onchange="ativaDesativa('obs_hipoclorito12');" />
                    <label for="ver_hipoclorito12">Hipoclorito 20%</label>
                    
                    <input value="<?= $rs2->obs_hipoclorito12; ?>" name="obs_hipoclorito12" id="obs_hipoclorito12" <? if ($rs2->ver_hipoclorito12!=1) echo "disabled=\"disabled\" class=\"desativado\""; ?> onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
                </li>
                <li>
                    <input class="tamanho20" <? if ($rs2->ver_salgrosso==1) echo "checked=\"checked\""; ?> type="checkbox" name="ver_salgrosso" id="ver_salgrosso" value="1" onchange="ativaDesativa('obs_salgrosso');" />
                    <label for="ver_salgrosso">Sal grosso</label>
                    
                    <input value="<?= $rs2->obs_salgrosso; ?>" name="obs_salgrosso" id="obs_salgrosso" <? if ($rs2->ver_salgrosso!=1) echo "disabled=\"disabled\" class=\"desativado\""; ?> onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
                </li>
                <li>
                    <input class="tamanho20" <? if ($rs2->ver_desibac==1) echo "checked=\"checked\""; ?> type="checkbox" name="ver_desibac" id="ver_desibac" value="1" onchange="ativaDesativa('obs_desibac');" />
                    <label for="ver_desibac">Desibac</label>
                    
                    <input value="<?= $rs2->obs_desibac; ?>" name="obs_desibac" id="obs_desibac" <? if ($rs2->ver_desibac!=1) echo "disabled=\"disabled\" class=\"desativado\""; ?> onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
                </li>
            </ul>
            <br />
            <?
            break;
            }
            ?>
        </fieldset>
	</div>
    
	<br />
    
    <center>
        <button type="submit" id="enviar">enviar &gt;&gt;</button>
        <!--
        <button onclick="window.open('index2.php?pagina=_os/os_pdf&amp;id_os=<?= $rs->id_os; ?>', '', '');" id="enviar">versão p/ impressão</button>
        -->
	</center>
	<br />
</form>
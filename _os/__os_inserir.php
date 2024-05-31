<?
if (!$conexao)
	require_once("conexao.php");
?>
<h2 class="titulos">Nova ordem de serviço</h2>

<br />
	
<form action="<?= AJAX_FORM; ?>formOsInserir" method="post" name="formOsInserir" id="formOsInserir" onsubmit="return ajaxForm('conteudo', 'formOsInserir', 'validacoes', true);">
	
    <input class="escondido" type="hidden" id="validacoes" value="solicitante@vazio|tel_solicitante@vazio|tipo_atendimento@vazio|prioridade@vazio|id_servico@vazio|obs_gerais@vazio" />
    
    <div class="parte50">
        <label>Empresa:</label>
        <?= pega_empresa($_SESSION["id_empresa"]); ?>
        <br />
        
        <label for="solicitante">Solicitante:</label>
        <input name="solicitante" id="solicitante" title="Solicitante" />
        <br />
        
        <label for="tel_solicitante">Telefone do solicitante:</label>
        <input title="Telefone do solicitante" name="tel_solicitante" id="tel_solicitante" class="tamanho25p" onkeypress="return formataCampo(formOsInserir, 'tel_solicitante', '(99) 9999-9999', event);" maxlength="14"  />
        
        <div class="lado_campo">
            <span class="vermelho">"(99) 9999-999"</span>
        </div>
        <br />
    	
        <!--
        <label for="id_tecnico">Técnico destacado:</label>
        <select name="id_tecnico" id="id_tecnico">
            <option value="" selected="selected">--- selecione ---</option>
            <?
            $result_tec= mysql_query("select * from tecnicos order by tecnico");
            $i=1;
            while ($rs_tec= mysql_fetch_object($result_tec)) {
            ?>
            <option <? if ($i%2) echo "class=\"cor_sim\""; $i++; ?> value="<?= $rs_tec->id_tecnico; ?>"><?= $rs_tec->tecnico; ?></option>
            <? } ?>
        </select>
        <br />
        -->
        
        <label for="tipo_atendimento">Tipo de atendimento:</label>
        <select name="tipo_atendimento" id="tipo_atendimento" title="Tipo de atendimento">
            <option value="" selected="selected">--- selecione ---</option>
            <option value="i" class="cor_sim">Instalação</option>
            <option value="m">Manutenção</option>
            <option value="c" class="cor_sim">Checklist</option>
        </select>
        <br />
    
        <label for="prioridade">Prioridade:</label>
        <select name="prioridade" id="prioridade" title="Prioridade">
            <option value="" selected="selected">--- selecione ---</option>
            <option value="0" class="bg_verde">Baixa</option>
            <option value="1" class="bg_amarelo">Média</option>
            <option value="2" class="bg_vermelho">Alta</option>
        </select>
        <br />
	</div>
    <div class="parte50">
    	<fieldset>
        	<legend>Serviço a ser executado</legend>
            
            <label for="id_servico">Serviço:</label>
            <select name="id_servico" id="id_servico" title="Serviço" onchange="alteraCamadasEquipamento();">
                <option value="" selected="selected">--- selecione ---</option>
                <?
                $result_serv= mysql_query("select * from servicos");
                $i=1;
                while ($rs_serv= mysql_fetch_object($result_serv)) {
                ?>
                <option <? if ($i%2) echo "class=\"cor_sim\""; $i++; ?> value="<?= $rs_serv->id_servico; ?>"><?= $rs_serv->servico; ?></option>
                <? } ?>
            </select>
            <br />
            
            <div id="equipamentos" class="escondido">
                <table cellspacing="0">
                    <tr>
                        <th align="left">Equipamento</th>
                        <th align="left">Nº de série</th>
                        <th align="left">Observações</th>
                    </tr>
                    <tr>
                        <td class="sem"><input name="equipamento" id="equipamento" class="tamanho130" onblur="setaClasse(this, 'campo_normal tamanho130');" onfocus="setaClasse(this, 'campo_hover tamanho130');" /></td>
                        <td class="sem"><input name="nserie" id="nserie" class="tamanho100" onblur="setaClasse(this, 'campo_normal tamanho100');" onfocus="setaClasse(this, 'campo_hover tamanho100');" /></td>
                        <td class="sem"><textarea name="obs" id="obs"></textarea></td>
                    </tr>
                </table>
            </div>
        </fieldset>
        
        <label for="obs_gerais">Obs. gerais:</label>
        <textarea name="obs_gerais" id="obs_gerais" title="Observações gerais"></textarea>
        <br />
    </div>
    
	<br /><br />
    
	<center>
		<button type="submit" id="enviar">enviar &gt;&gt;</button>
	</center>
</form>
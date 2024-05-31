<?
require_once("conexao.php");
if (pode("rm", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select rh_cartoes.*
								from  rh_cartoes, rh_funcionarios
								where rh_cartoes.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_cartoes.id_cartao = '". $_GET["id_cartao"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Cartão ponto</h2>

<form action="<?= AJAX_FORM; ?>formCartao&amp;acao=<?= $acao; ?>" method="post" name="formCartao" id="formCartao" onsubmit="return ajaxForm('conteudo', 'formCartao', 'validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_funcionario@vazio|tipo_cartao@vazio|numero_cartao@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_cartao" class="escondido" type="hidden" id="id_cartao" value="<?= $rs->id_cartao; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados do funcionário</legend>
        
        <div class="parte50">
            
            <label for="id_funcionario">Funcionário:</label>
            <?
            if ($acao=='e') {
				echo pega_funcionario($rs->id_funcionario);
			?>
            <input type="hidden" class="escondido" name="id_funcionario" id="id_funcionario" value="<?= $rs->id_funcionario; ?>" title="Funcionário">
            <? } else { ?>
            <select name="id_funcionario" id="id_funcionario" title="Funcionário">
                <option value="">- SELECIONE -</option>
                <?
				$result_fun= mysql_query("select *
											from  pessoas, rh_funcionarios, rh_carreiras
											where pessoas.id_pessoa = rh_funcionarios.id_pessoa
											and   pessoas.tipo = 'f'
											and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_funcionarios.status_funcionario <> '0'
											and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
											
											/*and   rh_funcionarios.id_funcionario not in
											(select id_funcionario from rh_cartoes where tipo_cartao='1')*/
											
											order by pessoas.nome_rz asc
											") or die(mysql_error());
											//and   rh_funcionarios.id_funcionario NOT IN (select id_funcionario from rh_cartoes)
                $i=0;
                while ($rs_fun= mysql_fetch_object($result_fun)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <? } ?>
            <br />
            
            <label for="tipo_cartao">* Tipo:</label>
            <select name="tipo_cartao" id="tipo_cartao" title="Tipo de cartão"> 
                <option value="1" <? if ($rs->tipo_cartao=='1') echo "selected=\"selected\""; ?>>Normal</option>
                <option value="2" <? if ($rs->tipo_cartao=='2') echo "selected=\"selected\""; ?> class="cor_sim">Supervisor</option>
            </select>
            <br />
            
            <label for="numero_cartao">* Número do cartão:</label>
            <input title="Número do cartão" name="numero_cartao" id="numero_cartao" value="<?= $rs->numero_cartao; ?>" onblur="verificaCartao(this.value);" maxlength="8" />
            <br />
            
            <label>&nbsp;</label>
            <div id="cartao_atualiza">
            
            </div>
            
            <br />
        </div>
        <div class="parte50">
			
        </div>
    </fieldset>
                
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>
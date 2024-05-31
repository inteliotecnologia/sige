<?
require_once("conexao.php");
if (pode_algum("p", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_limpa_pecas
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_peca = '". $_GET["id_peca"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Peças de roupa</h2>

<form action="<?= AJAX_FORM; ?>formPeca&amp;acao=<?= $acao; ?>" method="post" name="formPeca" id="formPeca" onsubmit="return ajaxForm('conteudo', 'formPeca', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="peca@vazio|qtde_padrao_pacote@vazio|id_grupo@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_peca" class="escondido" type="hidden" id="id_peca" value="<?= $rs->id_peca; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            
            <label for="peca">* Peça:</label>
            <input title="peca" name="peca" value="<?= $rs->peca; ?>" id="peca" />
            <br />
            
            <label for="qtde_padrao_pacote">* Qtde padrão:</label>
            <input title="qtde_padrao_pacote" name="qtde_padrao_pacote" value="<?= $rs->qtde_padrao_pacote; ?>" id="Qtde padrão" />
            <br />
            
            <label for="id_grupo">* Grupo:</label>
            <select id="id_grupo" name="id_grupo" title="Grupo">
            	<option value="">---</option>
				<?
				$j=1;
				$vetor= pega_grupo_roupa('l');
				
				while ($vetor[$j]) {
				?>
                <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $j; ?>" <? if ($j==$rs->id_grupo) echo "selected=\"selected\""; ?>><?= $vetor[$j]; ?></option>
                <? $j++; } ?>
            </select>
            
            <br />
            
        </div>
    	<br />
        
    </fieldset>
    
    <br />
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>
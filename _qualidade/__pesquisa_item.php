<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from  qual_pesquisa_itens
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_pesquisa_item = '". $_GET["id_pesquisa_item"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Pesquisa de satisfação - Itens</h2>

<form action="<?= AJAX_FORM; ?>formPesquisaItem&amp;acao=<?= $acao; ?>" method="post" name="formPesquisaItem" id="formPesquisaItem" onsubmit="return ajaxForm('conteudo', 'formPesquisaItem', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_pesquisa_categoria@vazio|pesquisa_item@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_pesquisa_item" class="escondido" type="hidden" id="id_pesquisa_item" value="<?= $rs->id_pesquisa_item; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
        	
            <label for="id_pesquisa_categoria">* Categoria:</label>
			<?
			$result_categoria= mysql_query("select * from qual_pesquisa_categorias
											where id_empresa = '". $_SESSION["id_empresa"] ."' 
											order by id_pesquisa_categoria asc
											") or die(mysql_error());
			?>
            <select name="id_pesquisa_categoria" id="id_pesquisa_categoria" title="Categoria">
                <option value="">-</option>
                <?
                $i=0;
                while ($rs_categoria= mysql_fetch_object($result_categoria)) {
                ?>
                <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> <? if (($rs_categoria->id_pesquisa_categoria==$_GET["id_pesquisa_categoria"]) || ($rs_categoria->id_pesquisa_categoria==$rs->id_pesquisa_categoria)) echo "selected=\"selected\""; ?> value="<?= $rs_categoria->id_pesquisa_categoria; ?>"><?= $rs_categoria->pesquisa_categoria; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="pesquisa_item">* Item:</label>
            <input title="Item" name="pesquisa_item" value="<?= $rs->pesquisa_item; ?>" id="pesquisa_item" />
            <br />
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>
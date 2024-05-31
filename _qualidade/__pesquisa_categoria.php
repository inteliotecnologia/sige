<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  qual_pesquisa_categorias
								where id_pesquisa_categoria = '". $_GET["id_pesquisa_categoria"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Pesquisa de satisfação - categoria</h2>

<form action="<?= AJAX_FORM; ?>formPesquisaCategoria&amp;acao=<?= $acao; ?>" method="post" name="formPesquisaCategoria" id="formPesquisaCategoria" onsubmit="return ajaxForm('conteudo', 'formPesquisaCategoria', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="pesquisa_categoria@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_pesquisa_categoria" class="escondido" type="hidden" id="id_pesquisa_categoria" value="<?= $rs->id_pesquisa_categoria; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="pesquisa_categoria">* Categoria:</label>
            <input title="Centro de custo" name="pesquisa_categoria" value="<?= $rs->pesquisa_categoria; ?>" id="pesquisa_categoria" />
            <br />
        </div>
        <br /><br /><br />
        
        <center>
        	<button type="submit" id="enviar">Enviar &raquo;</button>
    	</center>
	    <br />
    </fieldset>
</form>
<? } ?>
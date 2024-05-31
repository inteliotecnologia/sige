<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from rh_vt_linhas
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_linha = '". $_GET["id_linha"] ."'
								");
							
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Linhas de ônibus</h2>

<p>* Campos de preenchimento obrigatório.</p>

<form action="<?= AJAX_FORM; ?>formVTLinha&amp;acao=<?= $acao; ?>" method="post" name="formVTLinha" id="formVTLinha" onsubmit="return ajaxForm('conteudo', 'formVTLinha', 'validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="linha@vazio|valor@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_linha" class="escondido" type="hidden" id="id_linha" value="<?= $rs->id_linha; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="linha">* Linha:</label>
            <input title="linha" name="linha" value="<?= $rs->linha; ?>" id="linha" />
            <br />
            
            <label for="valor">* Valor (R$):</label>
            <input class="tamanho25p" title="valor" name="valor" onkeydown="formataValor(this,event);" value="<?= fnum($rs->valor); ?>" id="valor" />
            <br />
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>

<script language="javascript">
	daFoco("linha");
</script>
<? } ?>
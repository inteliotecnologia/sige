<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	if ($_GET["id_peca"]!="") $id_peca= $_GET["id_peca"];
	if ($_POST["id_peca"]!="") $id_peca= $_POST["id_peca"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($_GET["id_cliente_peca_dobra"]!="") $id_cliente_peca_dobra= $_GET["id_cliente_peca_dobra"];
	if ($_POST["id_cliente_peca_dobra"]!="") $id_cliente_peca_dobra= $_POST["id_cliente_peca_dobra"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from fi_clientes_pecas_dobra
							 	where id_cliente_peca_dobra = '". $id_cliente_peca_dobra ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_cliente= $rs->id_cliente;
		$id_peca= $rs->id_cliente;
	}
?>

<form action="<?= AJAX_FORM; ?>formClientePecasDobra&amp;acao=<?= $acao; ?>" enctype="multipart/form-data" method="post" name="formClientePecasDobra" id="formClientePecasDobra" onsubmit="return validaFormNormal('validacoes_peca_dobra', false);">

    <input class="escondido" type="hidden" id="validacoes_peca_dobra" value="id_cliente@vazio|id_peca@vazio|legenda@vazio" />
    
    <? if ($acao=="e") { ?>
    <input name="id_cliente_peca_dobra" class="escondido" type="hidden" id="id_cliente_peca_dobra" value="<?= $rs->id_cliente_peca_dobra; ?>" />
    <? } ?>
    
    <label for="id_cliente">Cliente:</label>
    <select name="id_cliente" id="id_cliente" title="Cliente">
        <? if ($acao=='i') { ?>
        <option value="">-</option>
        <? } ?>
        <?
        $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                    where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                    and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                    and   pessoas_tipos.tipo_pessoa = 'c'
                                    order by 
                                    pessoas.apelido_fantasia asc
                                    ") or die(mysql_error());
        $k=0;
        while ($rs_ced = mysql_fetch_object($result_ced)) {
        ?>
        <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if (($rs_ced->id_cedente==$rs->id_cliente) || ($rs_ced->id_cedente==$id_cliente)) echo "selected=\"selected\""; ?>><?= $rs_ced->apelido_fantasia; ?></option>
        <? $k++; } ?>
    </select>
    <br />
    
    <label for="id_peca">Tipo de roupa:</label>
    <select name="id_peca" id="id_peca" title="Tipo de roupa">
        <? if ($acao=='i') { ?>
        <option value="">-</option>
        <? } ?>
        <?
        $result_pecas= mysql_query("select * from op_limpa_pecas
									where id_empresa = '". $_SESSION["id_empresa"] ."' 
									order by peca asc
									");
        $k=0;
        while ($rs_pecas = mysql_fetch_object($result_pecas)) {
        ?>
        <option <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_pecas->id_peca; ?>" <? if (($rs_pecas->id_peca==$rs->id_peca) || ($rs_pecas->id_peca==$id_peca)) echo "selected=\"selected\""; ?>><?= $rs_pecas->peca; ?></option>
        <? $k++; } ?>
    </select>
    <br />

	<label for="foto">Foto:</label>
    <input type="file" name="foto" id="foto" />
    <br />
    
    <?
	if (file_exists(CAMINHO . "cliente_peca_dobra_". $rs->id_cliente_peca_dobra .".jpg")) {
	?>
    <label>&nbsp;</label>
    <div id="peca_foto">
        <img src="<?= CAMINHO; ?>cliente_peca_dobra_<?= $rs->id_cliente_peca_dobra; ?>.jpg" alt="<?= $rs->nome;?>" width="300" />
        <br />
        <? if (pode("1", $_SESSION["permissao"])) { ?>
        <a href="javascript:ajaxLink('peca_foto', 'arquivoExcluir&amp;arquivo=cliente_peca_dobra_<?= $rs->id_cliente_peca_dobra; ?>.jpg&amp;id_cliente_peca_dobra=<?=$rs->id_cliente_peca_dobra;?>');" onclick="return confirm('Tem certeza que deseja excluir a foto deste(a) funcionário(a)?');">excluir</a>
        <? } ?>
    </div>
	<br />
    <? } ?>
    
    <label for="legenda_foto">Legenda:</label>
    <input name="legenda_foto" id="legenda_foto" value="<?=$rs->legenda_foto;?>" />
    <br /><br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>
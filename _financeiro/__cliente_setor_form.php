<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	if ($_GET["id_cliente_setor"]!="") $id_cliente_setor= $_GET["id_cliente_setor"];
	if ($_POST["id_cliente_setor"]!="") $id_cliente_setor= $_POST["id_cliente_setor"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from fi_clientes_setores
							 	where id_cliente_setor = '". $id_cliente_setor ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_cliente= $rs->id_cliente;
	}
?>
<form action="<?= AJAX_FORM; ?>formClienteSetor&amp;acao=<?= $acao; ?>" method="post" name="formClienteSetor" id="formClienteSetor" onsubmit="return ajaxForm('conteudo_interno', 'formClienteSetor', 'validacoes_setor', true);">

    <input class="escondido" type="hidden" id="validacoes_setor" value="id_cliente@vazio|setor@vazio" />
    <input name="id_cliente" class="escondido" type="hidden" id="id_cliente" value="<?= $id_cliente; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_cliente_setor" class="escondido" type="hidden" id="id_cliente_setor" value="<?= $rs->id_cliente_setor; ?>" />
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

    <label for="setor">Setor:</label>
    <input name="setor" id="setor" value="<?=$rs->setor;?>" />
    <br /><br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>
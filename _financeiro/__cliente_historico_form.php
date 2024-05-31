<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	if ($_GET["id_livro"]!="") $id_livro= $_GET["id_livro"];
	if ($_POST["id_livro"]!="") $id_livro= $_POST["id_livro"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from com_livro
							 	where id_livro = '". $id_livro ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								and   restrito = '1'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_cliente= $rs->reclamacao_id_cliente;
	}
?>
<form action="<?= AJAX_FORM; ?>formClienteHistorico&amp;acao=<?= $acao; ?>" method="post" name="formClienteHistorico" id="formClienteHistorico" onsubmit="return ajaxForm('conteudo_interno', 'formClienteHistorico', 'validacoes_historico', true);">

    <input class="escondido" type="hidden" id="validacoes_historico" value="reclamacao_id_cliente@vazio|mensagem@vazio" />
    
    <input name="id_cliente" class="escondido" type="hidden" id="id_cliente" value="<?= $id_cliente; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_livro" class="escondido" type="hidden" id="id_livro" value="<?= $rs->id_livro; ?>" />
    <? } ?>
    
    <div class="parte50">
        <label for="reclamacao_id_cliente">Cliente:</label>
        <select name="reclamacao_id_cliente" id="reclamacao_id_cliente" title="Cliente">
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
            <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if (($rs_ced->id_cedente==$rs->reclamacao_id_cliente) || ($rs_ced->id_cedente==$id_cliente)) echo "selected=\"selected\""; ?>><?= $rs_ced->apelido_fantasia; ?></option>
            <? $k++; } ?>
        </select>
        <br />
    	
        <?
		if ($acao=="i") {
			$data_livro= date("d/m/Y");
			$hora_livro= date("H:i:s");
		}
		else {
			$data_livro= desformata_data($rs->data_livro);
			$hora_livro= $rs->hora_livro;
		}
		?>
        
        <label for="data_livro">Data/hora:</label>
        <input id="data_livro" name="data_livro" class="tamanho15p espaco_dir" value="<?= $data_livro; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
        
        <input id="hora_livro" name="hora_livro" class="tamanho15p" value="<?= $hora_livro; ?>" title="Data" onkeyup="formataHora(this);" maxlength="8" />
        <br />
	</div>
    <div class="parte50">
        <label for="mensagem">Mensagem:</label>
        <textarea name="mensagem" id="mensagem" class="altura80"><?=$rs->mensagem;?></textarea>
        <br /><br />
	</div>
        
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>
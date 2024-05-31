<?
require_once("conexao.php");
if (pode("n", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  usuarios
								where id_usuario = '". $_GET["id_usuario"] ."'
								and   tipo_usuario = 'e'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Nova mensagem</h2>

<form action="<?= AJAX_FORM; ?>formMensagem&amp;acao=<?= $acao; ?>" enctype="multipart/form-data" method="post" name="formUsuario" id="formUsuario" onsubmit="return validaFormNormal('validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="titulo@vazio" />
    
    <fieldset>
        <legend>Dados da mensagem</legend>
        
        <div class="parte50">
            
            <?
			$result_para= mysql_query("select * from usuarios, pessoas, rh_funcionarios
										where 1=1
										and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
										". $str ."
										and   usuarios.situacao = '1'
										and   usuarios.status_usuario = '1'
										order by pessoas.nome_rz asc
										") or die(mysql_error());
			?>
            
            <label for="para">Para:</label>
            <select name="para[]" size="10" multiple="multiple" id="para">
                <?
                while($rs_para= mysql_fetch_object($result_para)) {
                ?>
                <option value="<?= $rs_para->id_pessoa; ?>" <? if ($rs_para->id_pessoa==$_GET["id_pessoa"]) echo "selected=\"selected\""; ?>><?= $rs_para->nome_rz; ?></option>
                <? } ?>
            </select>
            <br />
        
            <label for="titulo">Assunto:</label>
            <input title="Assunto" name="titulo" value="<?= $rs->titulo; ?>" id="titulo" />
            <br />
            
			<label for="mensagem">Mensagem:</label>
            <textarea class="ta" name="mensagem" id="mensagem" title="Mensagem"><?= $rs->mensagem; ?></textarea>
            <br />
            
        </div>
        <div class="parte50">
            
            <fieldset>
            	<legend>Anexos:</legend>
                
                <div id="anexos">
                    <? for ($i=1; $i<1; $i++) { ?>
                    <div id="div_anexo_<?=$i;?>">
                        <code class="escondido"></code>
                        
                        <label for="anexo_<?=$i;?>">Anexo <?=$i;?>:</label>
                        <input type="file" id="anexo_<?=$i;?>" name="anexo[]" class="espaco_dir tamanho25p" title="Anexo" />
                        <a href="javascript:void(0);" onclick="removeDiv('anexos', 'anexo_<?=$i;?>');">remover</a>
                        <br />
                    </div>
                    <? } ?>
                </div>
                
                <label>&nbsp;</label>
                <a href="javascript:void(0);" onclick="criaEspacoAnexo();">+ anexo</a>
                <br />
                
            </fieldset>
      </div>
    </fieldset>
                
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>
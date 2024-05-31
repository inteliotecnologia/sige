<?
require_once("conexao.php");
if (pode("d", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  dc_documentos_pastas
								where id_pasta = '". $_GET["id_pasta"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Arquivo de pastas</h2>

<form action="<?= AJAX_FORM; ?>formPasta&amp;acao=<?= $acao; ?>" method="post" name="formPasta" id="formPasta" onsubmit="return validaFormNormal('validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_empresa@vazio|id_departamento@vazio|pasta@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_pasta" class="escondido" type="hidden" id="id_pasta" value="<?= $rs->id_pasta; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
        	
            <label for="id_empresa">* Empresa:</label>
            <select name="id_empresa" id="id_empresa" title="Empresa" onchange="alteraDepartamentos();">
                <? if ($acao=="i") { ?>
                <option value="">---</option>
                <? } ?>
				
				<?
                $result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
                                            where pessoas.id_pessoa = empresas.id_pessoa
                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = 'a'
                                            order by 
                                            pessoas.nome_rz asc");
                $i=0;
                while ($rs_emp = mysql_fetch_object($result_emp)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_emp->id_empresa; ?>" <? if (($rs_emp->id_empresa==$rs->id_empresa) || ($rs_emp->id_empresa==$_SESSION["id_empresa"])) echo "selected=\"selected\""; ?>><?= $rs_emp->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_departamento">* Departamento:</label>
            <div id="id_departamento_atualiza">
                <select name="id_departamento" id="id_departamento" title="Departamento">
                    <? if ($acao=='i') { ?>
                    <option value="">---</option>
                    <? } ?>
					<?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												order by departamento asc
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ( ($rs_dep->id_departamento==$rs->id_departamento) || ($rs_dep->id_departamento==$_GET[id_departamento]) ) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <br />
            
            <label for="pasta">* Pasta:</label>
            <input title="Pasta" name="pasta" value="<?= $rs->pasta; ?>" id="pasta" class="tamanho25p" />
            <br />
            
            <label for="nome_pasta">* Nome da pasta:</label>
            <input title="Pasta" name="nome_pasta" value="<?= $rs->nome_pasta; ?>" id="nome_pasta" class="tamanho25p" />
            <br />
            
            <label for="status_pasta">* Situação:</label>
            <select name="status_pasta" id="status_pasta" title="Situação">
                <option value="1">Ativa</option>
                <option value="0" class="cor_sim">Inativa</option>
            </select>
            <br />
        </div>
        
        <br /><br /><br />
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        
    </fieldset>
</form>

<script language="javascript">
	daFoco("id_departamento");
</script>
<? } ?>


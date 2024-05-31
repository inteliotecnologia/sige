<?
require_once("conexao.php");
if (pode("rhv", $_SESSION["permissao"])) {
	if ($_GET["id_he_autorizacao"]!="") $id_he_autorizacao= $_GET["id_he_autorizacao"];
	if ($_POST["id_he_autorizacao"]!="") $id_he_autorizacao= $_POST["id_he_autorizacao"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from rh_he_autorizacao
							 	where id_he_autorizacao = '". $id_he_autorizacao ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_funcionario= $rs->id_funcionario;
	}
?>
<form action="<?= AJAX_FORM; ?>formHEAutorizacao&amp;acao=<?= $acao; ?>" method="post" name="formHEAutorizacao" id="formHEAutorizacao" onsubmit="return ajaxForm('conteudo_interno', 'formHEAutorizacao', 'validacoes', true);">

    <input class="escondido" type="hidden" id="validacoes" value="id_funcionario@vazio|data@data_he|hora_he@vazio|qtde_horas@vazio" />
    <input name="id_funcionario" class="escondido" type="hidden" id="id_funcionario" value="<?= $id_funcionario; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_he_autorizacao" class="escondido" type="hidden" id="id_he_autorizacao" value="<?= $rs->id_he_autorizacao; ?>" />
    <? } ?>
       
	<label for="id_funcionario">Funcionário:</label>
    <select name="id_funcionario" id="id_funcionario" title="Funcionário">
        <?
        $result_fun= mysql_query("select *
                                    from  pessoas, rh_funcionarios, rh_carreiras
                                    where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                    and   pessoas.tipo = 'f'
                                    and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
                                    and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                    and   rh_carreiras.atual = '1'
                                    and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                    order by pessoas.nome_rz asc
                                    ") or die(mysql_error());
        $i=0;
        while ($rs_fun= mysql_fetch_object($result_fun)) {
        ?>
        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    <label for="motivo">Motivo:</label>
    <input name="motivo" id="motivo" class="" value="<?=$rs->motivo;?>" />
    <br />
    
    <label for="data_he">Data da HE:</label>
    <? if ($acao=='i') $data_he= date("d/m/Y"); else $data_he= desformata_data($rs->data_he); ?>
    <input name="data_he" id="data_he" class="tamanho15p" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?=$data_he;?>" />
    <br />
    
    <label for="hora_he">Horário HE:</label>
    <input name="hora_he" id="hora_he" class="tamanho15p" onkeyup="formataHora(this);" maxlength="8" value="<?=$rs->hora_he;?>" />
    <br />
    
    <label for="data_compensacao">Data de compensação:</label>
    <input name="data_compensacao" id="data_compensacao" class="tamanho15p" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?=$rs->data_compensacao;?>" />
    <br />
    
    <label for="qtde_horas">Duração (em horas):</label>
    <input name="qtde_horas" id="qtde_horas" class="tamanho15p" onkeyup="formataHora(this);" maxlength="8" value="<?=$rs->qtde_horas;?>" />
    <br />
    
    
    <br /><br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>
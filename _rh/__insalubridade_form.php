<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	if ($_GET["id_insalubridade"]!="") $id_insalubridade= $_GET["id_insalubridade"];
	if ($_POST["id_insalubridade"]!="") $id_insalubridade= $_POST["id_insalubridade"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from rh_insalubridade
							 	where id_insalubridade = '". $id_insalubridade ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_funcionario= $rs->id_funcionario;
	}
?>
<form action="<?= AJAX_FORM; ?>formInsalubridade&amp;acao=<?= $acao; ?>" method="post" name="formInsalubridade" id="formInsalubridade" onsubmit="return ajaxForm('conteudo_interno', 'formInsalubridade', 'validacoes', true);">

    <input class="escondido" type="hidden" id="validacoes" value="id_funcionario@vazio|id_departamento@vazio|data_insalubridade@vazio" />
    <input name="id_funcionario" class="escondido" type="hidden" id="id_funcionario" value="<?= $id_funcionario; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_insalubridade" class="escondido" type="hidden" id="id_insalubridade" value="<?= $rs->id_insalubridade; ?>" />
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
    
    <label for="id_departamento">Departamento:</label>
    <select name="id_departamento" id="id_departamento" title="Departamento">
        <?
        $result_dep= mysql_query("select * from rh_departamentos
                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                     ");
        $i=0;
        while ($rs_dep = mysql_fetch_object($result_dep)) {
        ?>
        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    <label for="data_insalubridade">Data:</label>
    <input name="data_insalubridade" id="data_insalubridade" class="tamanho20p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= desformata_data($rs->data_insalubridade); ?>" title="Data" />
    <br />
    
    <label for="hora_inicio">Hora início/fim:</label>
    <input name="hora_inicio" id="hora_inicio" class="tamanho20p espaco_dir" onkeyup="formataHora(this);" value="<?= $rs->hora_inicio; ?>" maxlength="8" title="Hora" />
    <div class="flutuar_esquerda espaco_dir">até</div>
    <input name="hora_fim" id="hora_fim" class="tamanho20p espaco_dir" onkeyup="formataHora(this);" value="<?= $rs->hora_fim; ?>" maxlength="8" title="Hora" />
    <br />
    
    <br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>
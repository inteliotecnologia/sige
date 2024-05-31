<?
require_once("conexao.php");
if (pode("o", $_SESSION["permissao"])) {
?>

<div class="parte50">
    <input type="hidden" class="escondido" name="origem" value="2" />
    
    <input type="hidden" class="escondido" name="minhas" value="<?=$_GET["minhas"];?>" />
    
    <label class="tamanho100" for="id_funcionario">Funcionário:</label>
    <select name="id_funcionario" id="id_funcionario" title="Funcionário">
        <option value="">- TODOS -</option>
        <?
        $result_fun= mysql_query("select distinct(rh_funcionarios.id_funcionario)
                                    from  pessoas, rh_funcionarios, rh_carreiras, com_livro
                                    where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                    and   pessoas.tipo = 'f'
                                    and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                    and   rh_carreiras.atual = '1'
                                    and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                    and   rh_funcionarios.id_funcionario = com_livro.de
                                    and   com_livro.tipo_de = 'f'
                                    order by pessoas.nome_rz asc
                                    ") or die(mysql_error());
        $i=0;
        while ($rs_fun= mysql_fetch_object($result_fun)) {
        ?>
        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($_POST["id_funcionario"]==$rs_fun->id_funcionario) echo "selected=\"selected\""; ?>><?= pega_funcionario($rs_fun->id_funcionario); ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    <label class="tamanho100" for="parte" onmouseover="Tip('Mensagem que contenha a palavra...');">Palavra:</label>
    <input name="parte" id="parte" value="<?= $_POST["parte"]; ?>" />
    <br />
    
    <!--
    <?
    $result_deptos_principal= mysql_query("select * from rh_departamentos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                            order by departamento asc
                                            ");
    $linhas_deptos_principal= mysql_num_rows($result_deptos_principal);
    ?>
    <label class="tamanho100" for="depto_para">Para:</label>
    <select name="depto_para" id="depto_para" title="Depto principal">
        <option value="">- TODOS -</option>
        <?
        $i=0;
        while ($rs_deptos_principal= mysql_fetch_object($result_deptos_principal)) {
        ?>
        <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_deptos_principal->id_departamento; ?>" <? if ($depto_para==$rs_deptos_principal->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_deptos_principal->departamento; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    -->
    
    <?
	$result_deptos_principal= mysql_query("select * from rh_departamentos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   presente_livro = '1'
											order by departamento asc
											");
	$linhas_deptos_principal= mysql_num_rows($result_deptos_principal);
	?>
	<label class="tamanho100" for="id_departamento_principal">Setor responsável:</label>
	<select name="id_departamento_principal" id="id_departamento_principal" title="Depto principal">
		<option value="">-</option>
		<?
		$i=0;
		while ($rs_deptos_principal= mysql_fetch_object($result_deptos_principal)) {
			if ($rs_deptos_principal->departamento_livro!="") $departamento_nome= $rs_deptos_principal->departamento_livro;
			else $departamento_nome= $rs_deptos_principal->departamento;
		?>
		<option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> <? if ($rs_deptos_principal->id_departamento==$id_departamento_principal) echo "selected=\"selected\""; ?> value="<?= $rs_deptos_principal->id_departamento; ?>"><?= $departamento_nome; ?></option>
		<? $i++; } ?>
	</select>
	<br />
</div>
<div class="parte50">
    <label class="tamanho100" for="id_motivo">Motivo:</label>
    <select name="id_motivo" id="id_motivo" title="Motivo">
        <option value="">- TODOS -</option>
        <?
        $i=0;
        $result_mot= mysql_query("select * from rh_motivos where tipo_motivo = 'l' order by motivo asc ");
        while ($rs_mot= mysql_fetch_object($result_mot)) {
        ?>
        <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_mot->id_motivo; ?>"><?= $rs_mot->motivo; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    <label class="tamanho100">&nbsp;</label>
    <input type="checkbox" class="tamanho20" name="resposta_requerida" id="resposta_requerida" value="1" />
    <label for="resposta_requerida" class="nao_negrito alinhar_esquerda">Aguardando resposta</label>
    <br /><br />
    
    <label class="tamanho100" for="periodo">Período:</label>
    <select name="periodo" id="periodo" title="Período">	  		
        <?
        $i=0;
        $result_per= mysql_query("select distinct(DATE_FORMAT(data_livro, '%m/%Y')) as data_batida2 from com_livro order by data_livro desc ");
        
        while ($rs_per= mysql_fetch_object($result_per)) {
            $data_batida= explode('/', $rs_per->data_batida2);
        ?>
        <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>" <? if ($_POST["periodo"]==$rs_per->data_batida2) echo "selected=\"selected\""; ?>><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    <label class="tamanho100">&nbsp;</label>
    ou
    <br />
    
    <label class="tamanho100" for="data1">Datas:</label>
    <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= $_POST["data1"];?>" title="Data 1" />
    <div class="flutuar_esquerda espaco_dir">à</div>
    <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= $_POST["data2"];?>" title="Data 2" />
    <br />
</div>

<? } ?>
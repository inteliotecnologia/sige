<?
if (pode("ps", $_SESSION["permissao"])) {
	
	$result= mysql_query("select * from op_suja_remessas_separacoes
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_separacao = '". $_GET["id_separacao"] ."'
								and   id_remessa = '". $_GET["id_remessa"] ."'
								and   tipo_separacao = '". $_GET["tipo_separacao"] ."'
								") or die(mysql_error());
	$linhas= mysql_num_rows($result);
	
	/*if ($linhas==0) {
		$data_remessa= pega_dado_remessa("data_remessa", $_GET["id_remessa"]);
		
		$result_insere= mysql_query("insert into op_suja_remessas_separacoes
									(id_empresa, id_remessa, data_separacao, hora_separacao, tipo_separacao, id_usuario)
									values
									('". $_SESSION["id_empresa"] ."', '". $_GET["id_remessa"] ."',
									'". $data_remessa ."', '00:00:01', '". $_GET["tipo_separacao"] ."', '". $_SESSION["id_usuario"] ."')
									") or die(mysql_error());
		$id_separacao= mysql_insert_id();
		
		$result= mysql_query("select * from op_suja_remessas_separacoes
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_separacao = '". $id_separacao ."'
								");
	}*/
	
	$rs= mysql_fetch_object($result);
?>


<h2>Horário de separação</h2>

<a href="javascript:void(0);" onclick="fechaDiv('tela_aux');" class="fechar">x</a>

<div id="formulario">
    <form action="<?= AJAX_FORM; ?>formSeparacaoHorario" method="post" id="formSeparacaoHorario" name="formSeparacaoHorario" onsubmit="return ajaxForm('conteudo', 'formSeparacaoHorario', 'validacoes');">
        <input name="id_separacao" id="id_separacao" class="escondido" type="hidden" value="<?= $rs->id_separacao; ?>" />
        <input id="validacoes" class="escondido" type="hidden" value="id_separacao@vazio|qtde_minina@numeros" />
        
        <label>Remessa:</label>
        <?= desformata_data(pega_dado_remessa("data_remessa", $rs->id_remessa)) ." nº ". pega_dado_remessa("num_remessa", $rs->id_remessa); ?>
        <br />
        
        <label>Tipo</label>
        <?= pega_tipo_separacao($rs->tipo_separacao); ?>
        <br /><br />
        
        <label for="data_separacao">Data:</label>
        <input name="data_separacao" id="data_separacao" onkeyup="formataData(this);" maxlength="10" class="tamanho80" value="<?= desformata_data($rs->data_separacao); ?>" />
        <input name="hora_separacao" id="hora_separacao" onkeyup="formataHora(this);" maxlength="8" class="tamanho60" value="<?= $rs->hora_separacao; ?>" />
        <br /><br />
    
        <label>&nbsp;</label>
        <button>Atualizar</button>
    </form>
</div>
        
<script language="javascript" type="text/javascript">daFoco('hora_separacao');</script>
<?
}
else {
	$erro_a= 1;
	include("__erro_acesso.php");
}
?>
<?
if (pode("r", $_SESSION["permissao"])) {
	
	$result= mysql_query("select * from rh_departamentos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							order by departamento asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Departamentos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/departamento&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="5%">Cód.</th>
		<th width="14%" align="left">Empresa</th>
		<th width="20%" align="left">Departamento</th>
		<th width="14%">Ativos</th>
		<th width="19%">Trabalhando agora</th>
		<th width="13%">Presente no livro</th>
		<th width="15%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		if ($rs->status_departamento==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_departamento; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
		<td><?= $rs->departamento; ?></td>
		<td align="center">
		<?
		$result_ativos= mysql_query("select count(rh_carreiras.id_carreira) as total_ativos from rh_funcionarios, rh_carreiras
										where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_departamento = '". $rs->id_departamento ."'
										and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
										and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
										") or die(mysql_error());
		$rs_ativos= mysql_fetch_object($result_ativos);
		
		echo $rs_ativos->total_ativos;
		?>
        </td>
		<td align="center"><?= pega_funcionarios_trabalhando($rs->id_departamento); ?></td>
		<td align="center"><?= sim_nao($rs->presente_livro); ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/departamento&amp;acao=e&amp;id_departamento=<?= $rs->id_departamento; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'departamentoStatus&amp;id_departamento=<?= $rs->id_departamento; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
        
	        <? if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'departamentoExcluir&amp;id_departamento=<?= $rs->id_departamento; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? } ?>        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>
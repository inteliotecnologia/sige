<?
if (pode("a", $_SESSION["permissao"])) {
	if ($_SESSION["id_empresa"]!="")
		$str= "and   id_empresa = '". $_SESSION["id_empresa"] ."' ";
	
	$result= mysql_query("select * from usuarios
							where 1=1
							". $str ."
							and   situacao = '1'
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Usuários</h2>

<ul class="recuo1">
	<li><a href="./?pagina=acesso/usuario&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" class="sortable" id="tabela">
	<tr>
		<th width="7%">Cód.</th>
		<th width="23%" align="left">Empresa</th>
		<th width="33%" align="left">Identifica&ccedil;&atilde;o</th>
		<th width="18%" align="left">Usuário</th>
		<th width="19%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ($rs->status_usuario==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_usuario; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
		<td>
		<?
        if ($rs->id_departamento!="0") echo "<strong>DEPARTAMENTO:</strong> ". pega_departamento($rs->id_departamento);
		else echo pega_funcionario($rs->id_funcionario);
		?>
        </td>
		<td><?= $rs->usuario; ?></td>
		<td align="center">
			<a href="./?pagina=acesso/usuario&amp;acao=e&amp;id_usuario=<?= $rs->id_usuario; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'usuarioStatus&amp;id_usuario=<?= $rs->id_usuario; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
        
	        <? if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'usuarioExcluir&amp;id_usuario=<?= $rs->id_usuario; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? } ?>
        
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>
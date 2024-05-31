<?
if (pode_algum("j", $_SESSION["permissao"])) {
	if ($_GET["tipo_tecnico"]!="") $tipo_tecnico= $_GET["tipo_tecnico"];
	
	$result= mysql_query("select * from man_tecnicos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   status_tecnico = '1'
							order by num_tecnico asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Técnicos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=manutencao/tecnico&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="9%">Cód.</th>
		<th width="15%" align="left">Tipo</th>
		<th width="19%" align="left">Número</th>
		<th width="36%" align="left">Nome</th>
		<th width="21%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		if ($rs->status_tecnico) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_tecnico; ?></td>
		<td>
			<?= pega_tipo_tecnico($rs->tipo_tecnico); ?>
        </td>
		<td><?= $rs->num_tecnico; ?></td>
		<td>
			<?
            if ($rs->id_funcionario==0) echo $rs->nome_tecnico;
			else echo pega_funcionario($rs->id_funcionario);
			?>
		</td>
		<td align="center">
			<a href="./?pagina=manutencao/tecnico&amp;acao=e&amp;id_tecnico=<?= $rs->id_tecnico; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			<? /*|
            <a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'tecnicoStatus&amp;id_tecnico=<?= $rs->id_tecnico; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
            
	        <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
			*/ ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'tecnicoManutencaoStatus&amp;id_tecnico=<?= $rs->id_tecnico; ?>&amp;status=<?= $status; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? //} ?>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>
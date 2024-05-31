<?
if (pode_algum("p", $_SESSION["permissao"])) {
	if ($_GET["tipo_processo"]!="") $tipo_processo= $_GET["tipo_processo"];
	
	$result= mysql_query("select * from op_equipamentos_processos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   status_processo = '1'
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Processos de lavagem</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/processo&amp;tipo_processo=<?= $tipo_processo; ?>&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="8%">Cód.</th>
		<th width="16%" align="left">Identifica&ccedil;&atilde;o</th>
		<th width="18%" align="left">Processo</th>
		<th width="19%">Tempo</th>
		<th width="21%">Carga m&aacute;xima</th>
		<th width="18%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_processo; ?></td>
		<td><?= $rs->codigo; ?></td>
		<td>
		<?
        echo $rs->processo;
		
		if ($rs->relave==1) echo "<br /><span class=\"menor\"><strong>(RELAVE)</strong></span>";
		?>
        </td>
		<td align="center"><?= $rs->tempo; ?></td>
		<td align="center"><?= number_format($rs->carga_maxima, 2, ',', '.') ." kg"; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=op/processo&amp;acao=e&amp;id_processo=<?= $rs->id_processo; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			
	        <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'processoExcluir&amp;id_processo=<?= $rs->id_processo; ?>&amp;tipo_processo=<?= $rs->tipo_processo; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? //} ?>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>
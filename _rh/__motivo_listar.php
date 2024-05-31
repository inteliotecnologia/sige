<?
if (pode("r", $_SESSION["permissao"])) {
	if ($_GET["tipo_motivo"]!="") $tipo_motivo= $_GET["tipo_motivo"];
	
	$result= mysql_query("select * from rh_motivos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   tipo_motivo = '". $tipo_motivo ."' 
							and   bloqueado <> '1'
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Motivos - <?= pega_tipo_motivo($tipo_motivo); ?></h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/motivo&amp;tipo_motivo=<?= $tipo_motivo; ?>&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="8%">Cód.</th>
		<th width="29%" align="left">Empresa</th>
		<th width="20%" align="left">Motivo</th>
		<th width="20%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		if ($rs->status_motivo==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_motivo; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
		<td><?= $rs->motivo; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/motivo&amp;acao=e&amp;id_motivo=<?= $rs->id_motivo; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			
	        <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'motivoExcluir&amp;id_motivo=<?= $rs->id_motivo; ?>&amp;tipo_motivo=<?= $rs->tipo_motivo; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? //} ?>
        
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>
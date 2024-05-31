<?
if (pode("rm", $_SESSION["permissao"])) {
	
	$result= mysql_query("select *
								from  pessoas, rh_funcionarios, rh_cartoes, rh_departamentos, rh_carreiras
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   pessoas.tipo = 'f'
								and   rh_funcionarios.id_funcionario = rh_cartoes.id_funcionario
								and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
								and   rh_carreiras.atual = '1'
								and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
								and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by rh_departamentos.departamento asc,
								pessoas.nome_rz asc
								") or die(mysql_error());
	
	$linhas= mysql_num_rows($result);
	
	$total= ($linhas/5)+1;
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Cartões ponto</h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/cartao&amp;acao=i">inserir</a></li>
    <!--<li><a href="index2.php?pagina=rh/cartao_todos_impressao" target="_blank">imprimir todos</a></li>-->
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
	  <th width="18%" align="left">Departamento</th>
      <th width="15%" align="left">Turno</th>
	  <th width="21%" align="left">Nome</th>
      <th width="8%" align="left">Turnante</th>
      <th width="10%" align="left">Cartão</th>
	  <th width="12%">Tipo</th>
	  <th width="12%" class="unsortable">Ações</th>
  </tr>
	<?
	$j=0;
	$parte=1;
	while ($rs= mysql_fetch_object($result)) {
		if (($j%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		if ($rs->status_funcionario==1) $status= 0;
		else $status= 1;
		
		if ( (($j%5)==0) && ($j>4)) $parte++;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td><?= $rs->departamento; ?></td>
        <td><a onmouseover="Tip('<?
		for ($i=0; $i<=6; $i++) {
			$result_dia= mysql_query("select *, DATE_FORMAT(entrada, '%H:%i') as entrada,
										DATE_FORMAT(saida, '%H:%i') as saida
										from rh_turnos_horarios
										where id_turno = '". $rs->id_turno ."'
										and   id_dia = '$i'
										");
			$rs_dia= mysql_fetch_object($result_dia);
			
			if (mysql_num_rows($result_dia)>0)
				echo "<strong>". traduz_dia($i) .":</strong> ". $rs_dia->entrada ." (". pega_detalhes_intervalo($rs->id_intervalo, $i, 0) .") ". $rs_dia->saida ." <br />";
			else
				echo "<strong>". traduz_dia($i) .":</strong> sem expediente <br />";
		}
		?>');" href="javascript:void(0);">
          <?= pega_turno($rs->id_turno); ?>
        </a></td>
      <td><a href="./?pagina=rh/funcionario_esquema&amp;acao=e&amp;id_funcionario=<?= $rs->id_funcionario; ?>"><?= $rs->nome_rz; ?></a></td>
        <td><?= sim_nao($rs->turnante); ?></td>
        <td><?= $rs->numero_cartao; ?></td>
		<td align="center"><?= pega_tipo_cartao($rs->tipo_cartao); ?></td>
		<td align="center">
			<a href="index2.php?pagina=rh/cartao_impressao&amp;id_cartao=<?= $rs->id_cartao; ?>" target="_blank">
                <img border="0" src="images/ico_pdf.png" alt="Relatório" />
            </a>
            |
            <a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/cartao&amp;acao=e&amp;id_cartao=<?= $rs->id_cartao; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
                |
			<a href="javascript:ajaxLink('conteudo', 'cartaoExcluir&amp;id_cartao=<?= $rs->id_cartao; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
                </a>
                </td>
	</tr>
	<? $j++; } ?>
</table>

<br />
<br />

<? /*
<h2>Impressão de cartões</h2>

<ul class="recuo1">
<?
for ($i=1; $i<=$total; $i++) {
	$j= $i-1;
?>
	<li class="flutuar_esquerda tamanho160"><a href="index2.php?pagina=rh/cartao_todos_impressao&amp;parte=<?=$j;?>" target="_blank">imprimir parte <?=$i;?></a></li>
<? } ?>
</ul>
*/ ?>

<br />
<br />

<? } ?>
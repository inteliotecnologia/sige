<?
if (pode_algum("rhv4", $_SESSION["permissao"])) {
	
	if ($_GET["ordenacao"]=="") $ordenacao= "rh_carreiras.id_departamento asc, rh_carreiras.id_turno asc, pessoas.nome_rz asc";
	else $ordenacao= $_GET["ordenacao"];
	
	if ($_GET["ordem"]=="") $ordem= "";
	else $ordem= $_GET["ordem"];
	
	if ($ordem=="asc") $ordem_inversa= "desc";
	else $ordem_inversa= "asc";
	
	if ($_POST["nome_rz"]!="") $nome_rz= $_POST["nome_rz"];
	if ($_GET["nome_rz"]!="") $nome_rz= $_GET["nome_rz"];
	if ($nome_rz!="") $str2= " and   pessoas.nome_rz like '%". $nome_rz ."%' ";
	
	if ($_GET["status_funcionario"]!="")
		$str2= "and   rh_funcionarios.status_funcionario = '". $_GET["status_funcionario"] ."' ";
	
	if ($_GET["temp"]=="1")
		$result= mysql_query("select *
								from  pessoas, rh_funcionarios
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   pessoas.tipo = 'f'
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								". $str2 ."
								and   rh_funcionarios.id_funcionario NOT IN
								
								(
								select rh_funcionarios.id_funcionario
								from  pessoas, rh_funcionarios, rh_enderecos, rh_carreiras, rh_departamentos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   pessoas.tipo = 'f'
								and   rh_enderecos.id_pessoa = pessoas.id_pessoa
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
								and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								". $str2 ."
								order by rh_carreiras.id_departamento asc, rh_carreiras.id_turno asc, pessoas.nome_rz asc
								)
								
								order by rh_funcionarios.id_funcionario asc
								") or die(mysql_error());
	else
		$result= mysql_query("select *
								from  pessoas, rh_funcionarios, rh_enderecos, rh_carreiras, rh_departamentos, rh_turnos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   pessoas.tipo = 'f'
								and   rh_enderecos.id_pessoa = pessoas.id_pessoa
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   rh_funcionarios.status_funcionario <> '2'
								". $str2 ."
								order by ". $ordenacao ." ". $ordem ."
								") or die(mysql_error());
								
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Funcionários</h2>

<div class="parte50">
    <ul class="recuo1 screen">
        <li><a href="./?pagina=rh/funcionario&amp;acao=i">inserir</a></li>
    </ul>
</div>
<div class="parte50">
	<fieldset>
    	<legend>Busca rápida</legend>
        
            <form action="./?pagina=rh/funcionario_listar" method="post" onsubmit="return validaFormNormal('validacoes_func', false, 1);">
            
                <input class="escondido" type="hidden" id="validacoes_func" value="nome_rz@vazio" />
                <input class="escondido" type="hidden" id="busca_geral" value="1" />
               
                <label for="nome_rz">Nome:</label>
                <input name="nome_rz" id="nome_rz" value="<?=$nome_rz;?>" class="" title="Nome" />
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </form>
        
    </fieldset>
</div>
<br />

<table cellspacing="0" width="100%" class="sortable" id="tabela">
	<? /*
    <tr>
		<th width="7%">
        	<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/funcionario_listar&amp;temp=<?=$_GET["temp"];?>&amp;status_funcionario=<?=$_GET["status_funcionario"];?>&amp;ordenacao=rh_funcionarios.id_funcionario&amp;ordem=<?=$ordem_inversa;?>');">Cód.</a>
            <?
            if (($ordenacao=="rh_funcionarios.id_funcionario") && (($ordem=="asc") || ($ordem=="")) ) echo "<img src=\"images/baixo.gif\" alt=\"\" align=\"middle\" />";
		  	if (($ordenacao=="rh_funcionarios.id_funcionario") && ($ordem=="desc")) echo "<img src=\"images/cima.gif\" alt=\"\" align=\"middle\" />";
		    ?>        </th>
          <th width="21%" align="left">
          	<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/funcionario_listar&amp;temp=<?=$_GET["temp"];?>&amp;status_funcionario=<?=$_GET["status_funcionario"];?>&amp;ordenacao=rh_departamentos.departamento&amp;ordem=<?=$ordem_inversa;?>');">Departamento</a>
            <?
            if ((($ordenacao=="rh_carreiras.id_departamento asc, rh_carreiras.id_turno asc, pessoas.nome_rz asc") || ($ordenacao=="rh_departamentos.departamento")) && (($ordem=="asc") || ($ordem==""))) echo "<img src=\"images/baixo.gif\" alt=\"\" align=\"middle\" />";
		  	if ((($ordenacao=="rh_carreiras.id_departamento asc, rh_carreiras.id_turno asc, pessoas.nome_rz asc") || ($ordenacao=="rh_departamentos.departamento")) && ($ordem=="desc")) echo "<img src=\"images/cima.gif\" alt=\"\" align=\"middle\" />";
		    ?>          </th>
          <th width="17%" align="left">
            <a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/funcionario_listar&amp;temp=<?=$_GET["temp"];?>&amp;status_funcionario=<?=$_GET["status_funcionario"];?>&amp;ordenacao=rh_turnos.turno&amp;ordem=<?=$ordem_inversa;?>');">Turno</a>
            <?
            if (($ordenacao=="rh_turnos.turno") && (($ordem=="asc") || ($ordem=="")) ) echo "<img src=\"images/baixo.gif\" alt=\"\" align=\"middle\" />";
		  	if (($ordenacao=="rh_turnos.turno") && ($ordem=="desc")) echo "<img src=\"images/cima.gif\" alt=\"\" align=\"middle\" />";
		    ?>          </th>
          <th width="35%" align="left">
            <a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/funcionario_listar&amp;temp=<?=$_GET["temp"];?>&amp;status_funcionario=<?=$_GET["status_funcionario"];?>&amp;ordenacao=pessoas.nome_rz&amp;ordem=<?=$ordem_inversa;?>');">Nome</a>
            <?
            if (($ordenacao=="pessoas.nome_rz") && (($ordem=="asc") || ($ordem=="")) ) echo "<img src=\"images/baixo.gif\" alt=\"\" align=\"middle\" />";
		  	if (($ordenacao=="pessoas.nome_rz") && ($ordem=="desc")) echo "<img src=\"images/cima.gif\" alt=\"\" align=\"middle\" />";
		    ?>          </th>
        <th width="20%" align="left">
        	<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/funcionario_listar&amp;temp=<?=$_GET["temp"];?>&amp;status_funcionario=<?=$_GET["status_funcionario"];?>&amp;ordenacao=pessoas.cpf_cnpj&amp;ordem=<?=$ordem_inversa;?>');">CPF</a>
            <?
            if (($ordenacao=="pessoas.cpf_cnpj") && (($ordem=="asc") || ($ordem=="")) ) echo "<img src=\"images/baixo.gif\" alt=\"\" align=\"middle\" />";
		  	if (($ordenacao=="pessoas.cpf_cnpj") && ($ordem=="desc")) echo "<img src=\"images/cima.gif\" alt=\"\" align=\"middle\" />";
		    ?>        </th>
  </tr>
  */ ?>
  <tr>
		<th width="6%">Cód.</th>
          <th width="18%" align="left">Departamento</th>
          <th width="14%" align="left">Turno</th>
          <th width="29%" align="left">Nome</th>
          <th width="11%" align="left">Admiss&atilde;o</th>
          <? if ($_GET["status_funcionario"]==0) { ?>
          <th width="11%" align="left">Desligamento</th>
          <? } ?>
        <th width="11%" align="left">CPF</th>
  </tr>
  
	<?
	$j=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($j%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ($rs->status_funcionario==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_pessoa; ?></td>
		<td><?= pega_departamento($rs->id_departamento); ?></td>
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
		?>');" href="javascript:void(0);"><?= pega_turno($rs->id_turno); ?></a></td>
        <td><a href="./?pagina=rh/funcionario_esquema&amp;acao=e&amp;id_funcionario=<?= $rs->id_funcionario; ?>"><?= $rs->nome_rz; ?></a> <? if ($rs->afastado==1) echo "<span class=\"vermelho menor\">(afastado)</span>"; ?></td>
        <td><span class="escondido"><?= formata_data_hifen(pega_data_admissao($rs->id_funcionario)); ?></span> <?= pega_data_admissao($rs->id_funcionario); ?></td>
        <?
        if ($_GET["status_funcionario"]==0) {
		?>
        <td>
        	<?
            if ($rs->status_funcionario==0) {
				$data_demissao_formatada= pega_data_demissao($rs->id_funcionario);
			?>
            <span class="escondido"><?= desformata_data($data_demissao_formatada); ?></span> <?= $data_demissao_formatada; ?>
            <? } else echo "-"; ?>
            </td>
        <? } ?>
		<td><?= $rs->cpf_cnpj; ?></td>
	</tr>
	<? $j++; } ?>
</table>

<? } ?>
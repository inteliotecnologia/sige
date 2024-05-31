<?
if (pode("t", $_SESSION["permissao"])) {
	if ($_GET["letra"]!="") $letra= $_GET["letra"];
	if ($_POST["letra"]!="") $letra= $_POST["letra"];
	if ($letra!="") $str .= " and   nome like '". $letra ."%' ";
	
	if ($_GET["tipo_contato"]!="") $tipo_contato= $_GET["tipo_contato"];
	if ($_POST["tipo_contato"]!="") $tipo_contato= $_POST["tipo_contato"];
	
	if ($_GET["status_funcionario"]!="") $status_funcionario= $_GET["status_funcionario"];
	if ($_POST["status_funcionario"]!="") $status_funcionario= $_POST["status_funcionario"];
	
	if (($_POST["geral"]=="1") && ($_POST["nome"]!="")) $str .= " and   nome like '%". $_POST["nome"] ."%' ";
	if (($_POST["geral"]=="1") && ($_POST["email"]!="")) $str .= " and   nome like '%". $_POST["email"] ."%' ";
	if (($_POST["geral"]=="1") && ($_POST["obs"]!="")) $str .= " and   obs like '%". $_POST["obs"] ."%' ";
	
	if ($tipo_contato!="") $str .= " and   tipo_contato = '". $tipo_contato ."' ";
	
	//funcionários
	if ($tipo_contato==2) {
		//ilhados
		if ($status_funcionario==-2)
			$result= mysql_query("select * from  tel_contatos
										where tel_contatos.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   tel_contatos.id_pessoa = '0'
										". $str ."
										order by nome asc
										") or die(mysql_error());
		else
			$result= mysql_query("select * from  tel_contatos, pessoas, rh_funcionarios
										where tel_contatos.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   tel_contatos.id_pessoa = pessoas.id_pessoa
										and   pessoas.id_pessoa = rh_funcionarios.id_pessoa
										and   ABS(rh_funcionarios.status_funcionario) = '". $status_funcionario ."'
										". $str ."
										order by nome asc
										") or die(mysql_error());
	}
	else {
		$result= mysql_query("select * from  tel_contatos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									". $str ."
									order by nome asc
									") or die(mysql_error());
	}
?>

<? if ($_POST["geral"]==1) { ?>
<h2>Busca de contato</h2>
<? } ?>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
    <tr>
      <th width="32%" align="left">Nome</th>
      <? if ($_POST["geral"]==1) { ?>
      <th width="10%" align="left">Tipo</th>
      <? } ?>
      <? for ($i=1; $i<=5; $i++) { ?>
      <th width="10%" align="left">Telefone <?= $i; ?></th>
      <? } ?>
      <th width="10%" class="unsortable">Ações</th>
  </tr>
    <?
    $j=0;
    while ($rs= mysql_fetch_object($result)) {
        if (($j%2)==0) $classe= "odd";
        else $classe= "even";
		
		$result_tel= mysql_query("select * from tel_contatos_telefones
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_contato = '". $rs->id_contato ."'
									order by id asc
									");
		unset($telefone);
		unset($classe_tel);
		$k=1;
		while ($rs_tel= mysql_fetch_object($result_tel)) {
			$telefone[$k] = $rs_tel->telefone ."";
			$obs[$k] = $rs_tel->obs ."";
			
			switch($rs_tel->tipo) {
				case 1: $classe_tel[$k]= "preto"; break;
				case 2: $classe_tel[$k]= "azul"; break;
				case 3: $classe_tel[$k]= "verde"; break;
				case 4: $classe_tel[$k]= "vermelho"; break;
				case 5: $classe_tel[$k]= "cinza"; break;
			}
			
			$k++;
		}
    ?>
    <tr class="<?= $classe; ?> corzinha">
        <td><?= $rs->nome; ?></td>
		<? if ($_POST["geral"]==1) { ?>
        <th width="10%" align="left"><?= pega_tipo_contato($rs->tipo_contato);?></th>
        <? } ?>
        <? for ($i=1; $i<=5; $i++) { ?>
      	<td><a <? if ($obs[$i]!="") { ?> onmouseover="Tip('<?= $obs[$i];?>');" <? } ?> href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=contatos/ligacao&amp;acao=i&amp;telefone=<?= $telefone[$i]; ?>');" class="<?= $classe_tel[$i]; ?>"><?= $telefone[$i]; ?></a></td>
        <? } ?>
        <td align="center">
            <a href="./?pagina=contatos/contato&amp;acao=e&amp;id_contato=<?= $rs->id_contato; ?>">
                <img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
                |
            <a href="javascript:ajaxLink('conteudo', 'contatoExcluir&amp;id_contato=<?= $rs->id_contato; ?>&amp;tipo_contato=<?= $rs->tipo_contato; ?>&amp;status_funcionario=<?= $rs->status_funcionario; ?>&amp;letra=<?= substr($rs->nome, 0, 1); ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
                <img border="0" src="images/ico_lixeira.png" alt="Status" />
                </a>
        </td>
    </tr>
    <? $j++; } ?>
</table>

<? } ?>
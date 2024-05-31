<?
if (pode_algum("psl", $_SESSION["permissao"])) {
	
	if ($_POST["tipo_relatorio"]=="r") {
		if ( ($_POST["data_pesagem1"]!="") && ($_POST["data_pesagem2"]!="") ) {
			$str = " and   op_limpa_pesagem.data_pesagem >= '". formata_data($_POST["data_pesagem1"]) ."'
					 and   op_limpa_pesagem.data_pesagem <= '". formata_data($_POST["data_pesagem2"]) ."'
					";
		}
		
		if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
		if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
		if ($id_cliente!="") $str .= " and   id_cliente= '". $id_cliente ."' ";
		
		if ($_POST["id_tipo_roupa"]!="") $id_tipo_roupa= $_POST["id_tipo_roupa"];
		if ($_GET["id_tipo_roupa"]!="") $id_tipo_roupa= $_GET["id_tipo_roupa"];
		if ($id_tipo_roupa!="") $str .= " and   id_tipo_roupa= '". $id_tipo_roupa ."' ";
		
		$result= mysql_query("select * from op_limpa_pesagem
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								". $str ."
								order by data_pesagem desc, hora_pesagem desc, id_pesagem desc
								");
								
		$num= 200;
		$total = mysql_num_rows($result);
		$num_paginas = ceil($total/$num);
		if ($_GET["num_pagina"]=="") $num_pagina= 0;
		else $num_pagina= $_GET["num_pagina"];
		$inicio = $num_pagina*$num;
	
		$result= mysql_query("select * from op_limpa_pesagem
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								". $str ."
								order by data_pesagem desc, hora_pesagem desc, id_pesagem desc
								limit $inicio, $num
								");
	}
	else {
		
		if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
		if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
		if ($periodo!="") $str.= " and   DATE_FORMAT(op_limpa_pesagem.data_pesagem, '%m/%Y') = '". $periodo ."' ";
		
		if ($_POST["data"]!="") $data2= $_POST["data"];
		if ($_GET["data"]!="") $data2= $_GET["data"];
		if ($data2!="") $str .= " and   op_limpa_pesagem.data_pesagem = '". formata_data_hifen($data2) ."' ";
		
		if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
		if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
		if ($id_cliente!="") $str .= " and   op_limpa_pesagem.id_cliente= '". $id_cliente ."' ";
		
		if ($_POST["extra"]!="") $extra= $_POST["extra"];
		if ($_GET["extra"]!="") $extra= $_GET["extra"];
		if ($extra!="") $str .= " and   op_limpa_pesagem.extra= '". $extra ."' ";
		
		if ($_POST["goma"]!="") $goma= $_POST["goma"];
		if ($_GET["goma"]!="") $goma= $_GET["goma"];
		if ($goma!="") $str .= " and   op_limpa_pesagem.goma= '". $goma ."' ";
		
		if ($_POST["roupa_alheia"]!="") $roupa_alheia= $_POST["roupa_alheia"];
		if ($_GET["roupa_alheia"]!="") $roupa_alheia= $_GET["roupa_alheia"];
		if ($roupa_alheia!="") $str .= " and   op_limpa_pesagem.roupa_alheia= '". $roupa_alheia ."' ";
		
		if ($_POST["id_turno"]!="") $id_turno= $_POST["id_turno"];
		if ($_GET["id_turno"]!="") $id_turno= $_GET["id_turno"];
		if ($id_turno!="") $str .= " and   op_limpa_pesagem.id_turno= '". $id_turno ."' ";
		
		if ($_POST["id_tipo_roupa"]!="") $id_tipo_roupa= $_POST["id_tipo_roupa"];
		if ($_GET["id_tipo_roupa"]!="") $id_tipo_roupa= $_GET["id_tipo_roupa"];
		if ($id_tipo_roupa!="") $str .= " and   op_limpa_pesagem.id_pesagem IN
										(
										 select op_limpa_pesagem_pecas.id_pesagem from op_limpa_pesagem_pecas, op_limpa_pesagem
										 where  op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
										 and    op_limpa_pesagem_pecas.id_tipo_roupa = '". $id_tipo_roupa ."'
										 $str
										) ";
		
		$result= mysql_query("select * from op_limpa_pesagem
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								". $str ."
								order by data_pesagem desc, hora_pesagem desc, id_pesagem desc
								");
		
		$num= 50;
		$total = mysql_num_rows($result);
		$num_paginas = ceil($total/$num);
		if ($_GET["num_pagina"]=="") $num_pagina= 0;
		else $num_pagina= $_GET["num_pagina"];
		$inicio = $num_pagina*$num;
		
		$result= mysql_query("select * from op_limpa_pesagem
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								". $str ."
								order by data_pesagem desc, hora_pesagem desc, id_pesagem desc
								limit $inicio, $num
								");
		
		/*if ($_SESSION["tipo_usuario"]=="a") {
			echo "select * from op_limpa_pesagem
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								". $str ."
								order by data_pesagem desc, hora_pesagem desc, id_pesagem desc
								limit $inicio, $num
								";
			die();
		}*/
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Área limpa - Pesagens</h2>

<? if ( ($_POST["geral"]=="1") && (pode('p', $_SESSION["permissao"])) ) { ?>
<ul class="recuo1">
    <li><a target="_blank" href="index2.php?pagina=op/pesagem_limpa_relatorio&amp;data=<?= desformata_data($data); ?>&amp;id_cliente=<?= $_POST["id_cliente"]; ?>&amp;id_turno=<?= $_POST["id_turno"]; ?>&amp;id_tipo_roupa=<?= $_POST["id_tipo_roupa"]; ?>">gerar relatório com as informações referentes</a></li>
</ul>
<? } else { ?>
<ul class="recuo1">
	<li class="flutuar_esquerda tamanho80"><a href="./?pagina=op/pesagem_limpa&amp;acao=i">inserir</a></li>
    <li class="flutuar_esquerda tamanho120"><a href="./?pagina=op/pesagem_limpa&amp;acao=i&amp;extra=1">inserir (extra)</a></li>
    
    <li class="flutuar_esquerda tamanho80"><a href="./?pagina=op/pesagem_limpa_busca&amp;acao=i&amp;extra=1">buscar</a></li>
    <li class="flutuar_esquerda tamanho120"><a href="./?pagina=op/pesagem_limpa_listar">listar todas</a></li>
    
    <li><strong>entregas por cliente</strong>
    
    <select name="id_cliente" id="id_cliente_troca" class="tamanho300" title="Cliente" onchange="apontaPesagensCliente(this.value);">
        <option value="">- TODOS -</option>
        <?
        $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                    where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                    and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                    and   pessoas_tipos.tipo_pessoa = 'c'
                                    and   pessoas.status_pessoa = '1'
									and   pessoas.id_cliente_tipo = '1'
									and   pessoas.basear_nota_data= '1'
                                    order by pessoas.apelido_fantasia asc
                                    ") or die(mysql_error());
        $i=0;
        while ($rs_cli = mysql_fetch_object($result_cli)) {
        ?>
        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
        <? $i++; } ?>
    </select>

    
    </li>
</ul>
<br />

<? /*
<span class="menor">*(pacotes completos/pacotes com sobra/peças de sobra)</span>
<br />*/ ?>

<?
if ($num_paginas > 1) {
	/*echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link= $i+1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=op/pesagem_limpa_listar&amp;data=". $data2 ."&amp;periodo=". $periodo ."&amp;id_cliente=". $id_cliente ."&amp;id_tipo_roupa=". $id_tipo_roupa ."&amp;id_turno=". $id_turno ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/pesagem_limpa_listar&amp;data=<?=$data2;?>&amp;periodo=<?=$periodo;?>&amp;id_cliente=<?=$id_cliente;?>&amp;id_tipo_roupa=<?=$id_tipo_roupa;?>&amp;id_turno=<?=$id_turno;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>


<table cellspacing="0" width="100%">
	<tr>
	    <th width="15%" align="left">Data da pesagem</th>
        <th width="16%" align="left">Cliente</th>
	    <th width="10%">Grupo</th>
	    <th width="19%" align="left">Tipo de roupa</th>
	    <th width="13%">Peso</th>
	    <th width="12%">Turno</th>
      <th width="15%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha">
		<? /*<td><?= desformata_data(pega_dado_remessa("data_remessa", $rs->id_remessa)) ." nº <strong>". pega_dado_remessa("num_remessa", $rs->id_remessa) ."</strong>"; ?></td>*/ ?>
		<td><?= desformata_data($rs->data_pesagem) ." ". substr($rs->hora_pesagem, 0, 5); ?></td>
        <td>
			<?= pega_sigla_pessoa($rs->id_cliente); ?>
            <? if ($rs->extra==1) { ?><span class="vermelho menor"><strong>EXTRA</strong></span><? } ?>
            <? if ($rs->goma==1) { ?><span class="menor"><strong>GOMA</strong></span><? } ?>
            <? if ($rs->roupa_alheia==1) { ?><span class="menor"><strong>ROUPA DE OUTRA UNIDADE</strong></span><? } ?>
        </td>
		<td align="center"><?= pega_grupo_roupa($rs->id_grupo); ?></td>
		<td>
            <span class="menor">
              <?
                $result_pesagens_pecas= mysql_query("select * from op_limpa_pesagem_pecas
                                                        where id_pesagem = '". $rs->id_pesagem ."'
                                                        order by id_pesagem_peca asc
                                                        ");
                $linhas_pesagens_pecas= mysql_num_rows($result_pesagens_pecas);
                
                $k=1;
                while ($rs_pesagens_pecas= mysql_fetch_object($result_pesagens_pecas)) {
                    echo "<span class='item_roupa'>". pega_pecas_roupa($rs_pesagens_pecas->id_tipo_roupa) ." (". $rs_pesagens_pecas->num_pacotes ."/". $rs_pesagens_pecas->pacotes_sobra ."/". $rs_pesagens_pecas->qtde_pecas_sobra .")</span>";
                    
                    //if ($k!=$linhas_pesagens_pecas) echo " ";
                    
                    $k++;
                }
                ?>
            </span>
        </td>
		<td align="center"><?= fnum($rs->peso); ?> kg</td>
		<td align="center"><?= pega_turno($rs->id_turno); ?></td>
		<td align="center">
			<? if ($_SESSION["id_departamento_sessao"]!="") { ?>
            <button class="tamanho50 espaco_dir" onclick="window.top.location.href='./?pagina=op/pesagem_limpa&amp;acao=e&amp;id_pesagem=<?= $rs->id_pesagem; ?>';" id="link_edita<?=$i;?>">edita</button>
            <button class="tamanho50" onclick="var confirma= confirm('Tem certeza que deseja excluir esta pesagem?'); if (confirma) ajaxLink('linha_<?=$i;?>', 'pesagemLimpaExcluir&amp;id_pesagem=<?= $rs->id_pesagem; ?>');" id="link_exclui<?=$i;?>">exclui</button>
            <? } else { ?>
            <a id="link_edita<?=$i;?>" href="./?pagina=op/pesagem_limpa&amp;acao=e&amp;id_pesagem=<?= $rs->id_pesagem; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'pesagemLimpaExcluir&amp;id_pesagem=<?= $rs->id_pesagem; ?>');" onclick="return confirm('Tem certeza que deseja excluir esta pesagem?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
            <? } ?>
        </td>
	</tr>
	<? $i++; } ?>
</table>
<br /><br />

<?
if ($num_paginas > 1) {
	/*echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link= $i+1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=op/pesagem_limpa_listar&amp;data=". $data2 ."&amp;periodo=". $periodo ."&amp;id_cliente=". $id_cliente ."&amp;id_tipo_roupa=". $id_tipo_roupa ."&amp;id_turno=". $id_turno ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/pesagem_limpa_listar&amp;data=<?=$data2;?>&amp;periodo=<?=$periodo;?>&amp;id_cliente=<?=$id_cliente;?>&amp;extra=<?=$extra;?>&amp;goma=<?=$goma;?>&amp;roupa_alheia=<?=$roupa_alheia;?>&amp;id_tipo_roupa=<?=$id_tipo_roupa;?>&amp;id_turno=<?=$id_turno;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<script type="text/javascript">
	daFoco("link_edita0");
</script>

<? } } ?>
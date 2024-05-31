<?
if (pode_algum("12", $_SESSION["permissao"])) {
	
	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($periodo!="") {
		$str.= " and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $periodo ."' ";
		$num= 9999;
	}
	else $num= 50;
	
	if ($_POST["data_livro"]!="") $data_livro= $_POST["data_livro"];
	if ($_GET["data_livro"]!="") $data_livro= $_GET["data_livro"];
	if ($data_livro!="") $str .= " and   com_livro.data_livro= '". formata_data($data_livro) ."' ";
	
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($id_funcionario!="") $str .= " and   de = '". $id_funcionario ."' ";
	
	if ($_POST["id_motivo"]!="") $id_motivo= $_POST["id_motivo"];
	if ($_GET["id_motivo"]!="") $id_motivo= $_GET["id_motivo"];
	if ($id_motivo!="") $str .= " and   id_motivo = '". $id_motivo ."' ";
	
	if ($_POST["motivo"]!="") $motivo= $_POST["motivo"];
	if ($_GET["motivo"]!="") $motivo= $_GET["motivo"];
	
	if ($motivo=="r") {
		$str .= " and   ( id_motivo = '37' or id_motivo = '34' ) ";
		$tit_pag= "Reclamações";
	}
	elseif ($motivo=="n") {
		$str .= " and   ( id_motivo = '41' or id_motivo = '42' ) ";
		$tit_pag= "Não conformidades";
	}
	else $tit_pag= "Busca";
	
	if ($_POST["resposta_requerida"]!="") $resposta_requerida= $_POST["resposta_requerida"];
	if ($_GET["resposta_requerida"]!="") $resposta_requerida= $_GET["resposta_requerida"];
	if ($resposta_requerida!="") $str .= " and   resposta_requerida = '". $resposta_requerida ."' ";
	
	if ($_POST["parte"]!="") $parte= $_POST["parte"];
	if ($_GET["parte"]!="") $parte= $_GET["parte"];
	if ($parte!="") $str .= " and   mensagem like '%". $parte ."%' ";
	
	if ($_POST["id_situacao_reclamacao"]!="") $id_situacao_reclamacao= $_POST["id_situacao_reclamacao"];
	if ($_GET["id_situacao_reclamacao"]!="") $id_situacao_reclamacao= $_GET["id_situacao_reclamacao"];
	
	$result= mysql_query("select * from com_livro
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   (id_motivo = '34' or id_motivo = '37' or id_motivo = '41' or id_motivo = '42')
							and   reclamacao_original = '1'
							". $str ."
							order by data_livro desc, hora_livro desc
							");
	
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select *, DATE_FORMAT(data_livro, '%Y') as ano from com_livro
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   (id_motivo = '34' or id_motivo = '37' or id_motivo = '41' or id_motivo = '42')
							and   reclamacao_original = '1'
							and   restrito <> '1'
							". $str ."
							order by data_livro desc, hora_livro desc
							limit $inicio, $num
							");
	
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2><?=$tit_pag;?></h2>

<ul class="recuo1">
    <li class="flutuar_esquerda tamanho200"><a href="./?pagina=qualidade/reclamacao_listar&amp;id_motivo=37">listar reclamações internas</a></li>
    <li class="flutuar_esquerda tamanho200"><a href="./?pagina=qualidade/reclamacao_listar&amp;id_motivo=34">listar reclamações externas</a></li>
    <li class="flutuar_esquerda tamanho160"><a href="./?pagina=qualidade/reclamacao_listar&amp;id_motivo=41">listar NC internas</a></li>
    <li class="flutuar_esquerda tamanho160"><a href="./?pagina=qualidade/reclamacao_listar&amp;id_motivo=42">listar NC externas</a></li>
    <li><a href="./?pagina=qualidade/reclamacao_listar">listar todas</a></li>
</ul>

<ul class="recuo1">
    <li class="flutuar_esquerda tamanho200"><a href="./?pagina=qualidade/reclamacao_listar&amp;id_situacao_reclamacao=1">listar tudo em aberto</a></li>
    <li class="flutuar_esquerda tamanho200"><a href="./?pagina=qualidade/reclamacao_listar&amp;id_situacao_reclamacao=2">listar tudo finalizado</a></li>
</ul>
<br /><br />

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="8%" align="left">Data/hora</th>
	    <th width="15%" align="left">Reclamante</th>
	    <th width="15%">Depto reclamado</th>
	    <th width="35%" align="left" class="unsortable">Mensagem</th>
	    <th width="7%">Prazo</th>
	    <th width="9%" class="unsortable">Situa&ccedil;&atilde;o</th>
      <th width="11%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		$result_reclamacoes_acoes= mysql_query("select * from qual_reclamacoes_andamento
												where id_livro = '". $rs->id_livro ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
		$linhas_reclamacoes_acoes= mysql_num_rows($result_reclamacoes_acoes);
		
		$result_reclamacoes_acoes_ultima= mysql_query("select * from qual_reclamacoes_andamento
														where id_livro = '". $rs->id_livro ."'
														and   id_empresa = '". $_SESSION["id_empresa"] ."'
														order by id_reclamacao_andamento desc limit 1
														");
		$rs_reclamacoes_acoes_ultima= mysql_fetch_object($result_reclamacoes_acoes_ultima);
		$linhas_reclamacoes_acoes_ultima= mysql_num_rows($result_reclamacoes_acoes_ultima);
		
		if (($id_situacao_reclamacao=="") || (($id_situacao_reclamacao=="1") && ($rs_reclamacoes_acoes_ultima->id_situacao!=6))
		   || (($id_situacao_reclamacao=="1") && ($linhas_reclamacoes_acoes_ultima==0)) || (($id_situacao_reclamacao=="2") && ($rs_reclamacoes_acoes_ultima->id_situacao==6))) {
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td valign="top">
        	<span class="escondido"><?= $rs->data_livro ." ". $rs->hora_livro; ?></span><?= desformata_data($rs->data_livro) ."<br />". $rs->hora_livro; ?>
        	<br /><br />
            <strong>Nº <?= fnumi($rs->num_livro)."/".$rs->ano; ?></strong>
            
        </td>
		<td valign="top">
        <?
		if ($rs->tipo_de=="f") {
			if (($rs->id_outro_departamento!="") && ($rs->id_outro_departamento!="0")) $id_departamento= $rs->id_outro_departamento;	
			else $id_departamento= pega_dado_carreira("id_departamento", $rs->de);	
			
			echo pega_funcionario($rs->de);
			
			$id_deixou= $rs->de;
			$id_agora= $_SESSION["id_funcionario_sessao"];
		}
		else {
			$id_departamento= $rs->de;
			$id_deixou= $rs->de;
			$id_agora= $_SESSION["id_departamento_sessao"];
		}
		?>
        <br /><br />

        <span class="menor"><strong><?= pega_motivo($rs->id_motivo); ?></strong></span>
        </td>
		<td valign="top" align="center">
			<?
            if ($rs->id_departamento_principal!="") echo pega_departamento($rs->id_departamento_principal);
			else echo "-";
			?>
        </td>
		<td valign="top">
			<div id="reclamacao_<?= $rs->id_livro; ?>">
				<?= $rs->mensagem; ?>
                <br />
                
                <? if (($rs->id_motivo==34) && ($rs->reclamacao_id_cliente!=0)) { ?>
                <span class="menor"><strong>CLIENTE:</strong> <?= pega_pessoa($rs->reclamacao_id_cliente); ?></span> <br />
                <span class="menor caps"><strong>CAUSA:</strong> <?= pega_reclamacao_causa($rs->id_causa); ?></span> <br />
                <? } ?>

            </div>
        </td>
		<td valign="top" align="center">
			<?
            if (($rs->prioridade_dias!="") && ($rs->prioridade_dias!="0")) {
				
				echo $rs->prioridade_dias ." dia(s)";
				
				switch ($rs_reclamacoes_acoes_ultima->id_situacao) {
					case 1:
					case 2:
					case 3:
					case 4:
						$data_mk_abertura= faz_mk_data($rs->data_livro);
						$data_mk_atual= faz_mk_data(date("Y-m-d"));
						$diferenca= $data_mk_atual-$data_mk_abertura;
						$dias= round(($diferenca/60/60/24));
						
						if ($dias>$rs->prioridade_dias) echo "<br /><br /><span class=\"vermelho menor\"><strong>ATRASADO</strong></span>";
					break;
				}
				
			}
			else echo "-";
            ?>
        </td>
		<td valign="top" align="center">
        	<?
			if ($rs_reclamacoes_acoes_ultima->id_situacao!="")
				echo pega_situacao_reclamacao($rs_reclamacoes_acoes_ultima->id_situacao) ."<br />";
			
			if ($rs_reclamacoes_acoes_ultima->nota!="") {
				$descricao_nota= pega_descricao_nota($rs_reclamacoes_acoes_ultima->nota);
				$descricao_nota= explode("@", $descricao_nota);
							
				echo " Nota ". $rs_reclamacoes_acoes_ultima->nota ." <span class=\"menor ". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span><br /><br />";
			}
			else echo "<br />";
			?>
            
            <span class="menor caps"><strong>
            <?
			if ($linhas_reclamacoes_acoes==0) echo "Nenhuma ação";
			elseif ($linhas_reclamacoes_acoes==1) echo $linhas_reclamacoes_acoes ." ação tomada";
			else echo $linhas_reclamacoes_acoes ." ações tomadas";
			?></strong>
            </span>
        </td>
		<td valign="top" align="center">
            <a id="link_edita<?=$i;?>" target="_blank" href="index2.php?pagina=qualidade/reclamacao_pdf&amp;id_livro=<?= $rs->id_livro; ?>">
				<img border="0" src="images/ico_pdf.png" alt="Edita" /></a>
                |
            <a id="link_edita<?=$i;?>" target="_blank" href="./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=<?= $rs->id_livro; ?>&amp;num_pagina=<?= $num_pagina; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
                
            <? if ($_SESSION["tipo_usuario"]=="a") { ?>
            |
            <a href="link.php?livroExcluir&amp;id_livro=<?= $rs->id_livro; ?>&amp;retorno=r" onclick="return confirm('Tem certeza que deseja excluir?');">
                <img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
            <? } ?>
                    
			<? /*|
			<a href="javascript:ajaxLink('conteudo', 'livroExcluir&amp;id_livro=<?= $rs->id_livro; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>*/ ?>
        </td>
	</tr>
	<? $i++; } } ?>
</table>

<?
if ($num_paginas > 1) {
	echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=qualidade/reclamacao_listar&amp;motivo=". $motivo ."&amp;id_situacao_reclamacao=". $id_situacao_reclamacao ."&amp;data_livro=". $data_livro ."&amp;periodo=". $periodo ."&amp;id_funcionario=". $id_funcionario ."&amp;id_motivo=". $id_motivo ."&amp;resposta_requerida=". $resposta_requerida ."&amp;parte=". $parte ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}

$_SESSION["reclamacao_num_pagina"]= "";
$_SESSION["reclamacao_ancora"]= "";
$_SESSION["reclamacao_origem"]= "";
?>

<? } ?>
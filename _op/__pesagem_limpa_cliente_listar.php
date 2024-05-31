<?
if (pode_algum("psl", $_SESSION["permissao"])) {
	
	
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	
	/*
	$result_tt1= mysql_query("CREATE TEMPORARY TABLE tr_percursos_virtual
							 ( id_percurso int, id_empresa int, id_veiculo int, tipo int, data_hora_percurso timestamp, id_regiao int )
							 TYPE=MEMORY;
							 ") or die("1: ". mysql_error());
							 
	$result_tt2= mysql_query("CREATE TEMPORARY TABLE tr_percursos_clientes_virtual
							 ( id_percurso_cliente int, id_empresa int, id_percurso int, id_cliente int )
							 TYPE=MEMORY;
							 ") or die("2: ". mysql_error());
							 
	
	$result_tt_percurso= mysql_query("select tr_percursos.* from tr_percursos, tr_percursos_clientes
										where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
										and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
										order by tr_percursos.data_hora_percurso desc limit 5
										");
	while ($rs_tt_percurso= mysql_fetch_object($result_tt_percurso)) {
		
		$result_tt_clientes= mysql_query("select * from tr_percursos_clientes
										where id_percurso = '". $rs_tt_percurso->id_percurso ."'
										");
										
		while ($rs_tt_clientes= mysql_fetch_object($result_tt_clientes)) {
			
			$result_tt_insere1= mysql_query("insert into tr_percursos_clientes_virtual
												(id_percurso_cliente, id_empresa, id_percurso, id_cliente)
												values
												('". $rs_tt_clientes->id_percurso_cliente ."', '". $rs_tt_clientes->id_empresa ."', '". $rs_tt_clientes->id_percurso ."', '". $rs_tt_clientes->id_cliente ."' )
												") or die("3: ". mysql_error());
			
		}
		
		$result_tt_insere3= mysql_query("insert into tr_percursos_virtual
												(id_percurso, id_empresa, id_veiculo, tipo, data_hora_percurso, id_regiao)
												values
												('". $rs_tt_percurso->id_percurso ."', '". $rs_tt_percurso->id_empresa ."',
												'". $rs_tt_percurso->id_veiculo ."', '". $rs_tt_percurso->tipo ."',
												'". $rs_tt_percurso->data_hora_percurso ."', '". $rs_tt_percurso->id_regiao ."' 
												)
												") or die("4: ". mysql_error());
		
	}
	*/
	
	// ======================================================
	
	
	$result_percursos= mysql_query("select * from  tr_percursos, tr_percursos_clientes
									where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   tr_percursos_clientes.id_percurso = tr_percursos.id_percurso
									and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
									and   ( tr_percursos.tipo = '2' or tr_percursos.tipo = '5' )
									order by tr_percursos.data_hora_percurso desc
									") or die(mysql_error());
	
	$num= 8;
	$num_valido= $num-3;
	$total = mysql_num_rows($result_percursos);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result_percursos= mysql_query("select *, DATE_FORMAT(tr_percursos.data_hora_percurso, '%d/%m/%Y') as data_percurso2,
									DATE_FORMAT(tr_percursos.data_hora_percurso, '%H:%i:%s') as hora_percurso2
									from tr_percursos, tr_percursos_clientes
									where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   tr_percursos_clientes.id_percurso = tr_percursos.id_percurso
									and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
									and   ( tr_percursos.tipo = '2' or tr_percursos.tipo = '5' )
									order by tr_percursos.data_hora_percurso desc
									limit $inicio, $num
									") or die(mysql_error());
	$linhas_percursos= mysql_num_rows($result_percursos);
?>

<div id="tela_aux" class="telinha1 screen">
</div>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Área limpa - Pesagens x Entregas</h2>


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
        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> <? if ($rs_cli->id_pessoa==$id_cliente) echo "selected=\"selected\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
        <? $i++; } ?>
    </select>

    
    </li>
</ul>
<br />

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

<?

$i=0;

while ($rs_percursos= mysql_fetch_object($result_percursos)) {
	$id_percurso[$i]= $rs_percursos->id_percurso;
	$tipo[$i]= $rs_percursos->tipo;
	$num_percurso[$i]= $rs_percursos->num_percurso;
	$num_percurso_tipo[$i]= $rs_percursos->num_percurso_tipo;
	
	$data_percurso2[$i]= $rs_percursos->data_percurso2;
	$hora_percurso2[$i]= $rs_percursos->hora_percurso2;
	$data_hora_percurso[$i]= $rs_percursos->data_hora_percurso;
	
	$i++;
}
?>

<?
if ($num_pagina==0) $p_index= -2;
else $p_index= 0;

if ($linhas_percursos==0) $num_valido=0;

$r=0;
for ($p=$p_index; $p<$num_valido; $p++) {
?>
<fieldset>
	
		<?
		$i=0;
		$str="";
		
		//se for entregas a fazer
		if ($p<0) {
			
			$nav_p= 0;
			
			while ( ($tipo[$nav_p]=="5") && ($nav_p<$num) ) {
				$nav_p++;
			}
			
			if ($p==-2) {
				$tit= "Pesagens para extra";
				
				$str.= " and data_hora_pesagem > '". $data_hora_percurso[0] ."'
						 and extra = '1'
						 ";
			}
			elseif ($p==-1) {
				$tit= "Na mesa | <small>Pesagens desde ". desformata_data_completa($data_hora_percurso[$nav_p]) ."</small>";
				
				$str.= " and data_hora_pesagem > '". $data_hora_percurso[$nav_p] ."'
						 and extra='0'
						 ";
			}	
		}
		else {
			
			//se for entrega extra, ele vai puxar as pesagens desde a última, não interessando se for extra ou normal
			if ($tipo[$p]=="5") {
				$str.= " and   data_hora_pesagem <= '". $data_hora_percurso[$p] ."'
					     and   data_hora_pesagem > '". $data_hora_percurso[$p+1] ."'
					     and   extra = '1'
					     ";
			}
			else {
				//buscando a última entrega normal
				$nav_p= $p+1;
				
				while ( ($tipo[$nav_p]=="5") && ($nav_p<$num) ) {
					$nav_p++;
				}
				
				$str.= " and data_hora_pesagem <= '". $data_hora_percurso[$p] ."'
					     and data_hora_pesagem > '". $data_hora_percurso[$nav_p] ."'
					     and   extra = '0'
					     ";
								
			}
			
			$tit= pega_coleta_entrega($tipo[$p]) ." nº". $num_percurso_tipo[$p] ." (". $data_percurso2[$p] ." ". substr($hora_percurso2[$p], 0, 5) .") | <span class='menor'>Pesagens entre ". desformata_data_completa($data_hora_percurso[$nav_p]) ." e ". desformata_data_completa($data_hora_percurso[$p]) ."</span>";
			
		}
		?>
		
		<legend><? echo $tit; ?></legend>
		
		<?
		$result= mysql_query("select * from op_limpa_pesagem
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								and   op_limpa_pesagem.id_cliente = '". $id_cliente ."'
								
								$str
								
								order by data_pesagem desc, hora_pesagem desc, id_pesagem desc
								");
		$linhas= mysql_num_rows($result);
		
		if ($linhas==0) echo "Nenhuma pesagem ainda.";
		else {
		?>
		
		<table cellspacing="0" width="100%">
			<tr>
			    <th width="15%" align="left">Data da pesagem</th>
		        <th width="8%" align="left">Cliente</th>
			    <th width="10%">Grupo</th>
			    <th width="32%" align="left">Tipo de roupa</th>
			    <th width="13%">Peso</th>
			    <th width="10%">Turno</th>
		        <th width="12%">Ações</th>
			</tr>
		
			<?
			$peso_total=0;
			
			while ($rs= mysql_fetch_object($result)) {
				if (($i%2)==0) $classe= "cor_sim";
				else $classe= "cor_nao";
				
				$data_hora_pesagem= faz_mk_data_completa($rs->data_pesagem ." ". $rs->hora_pesagem);
				
				$peso_total+=$rs->peso;
			?>
			<tr id="linha_<?=$r;?>_<?=$i;?>" class="<?= $classe; ?> corzinha">
				<td valign="top"><?= desformata_data($rs->data_pesagem) ." ". substr($rs->hora_pesagem, 0, 5); ?></td>
		        <td valign="top">
					<?= pega_sigla_pessoa($rs->id_cliente); ?>
		            <? if ($rs->extra==1) { ?><span class="vermelho menor"><strong>EXTRA</strong></span><? } ?>
		            <? if ($rs->goma==1) { ?><span class="menor"><strong>GOMA</strong></span><? } ?>
		            <? if ($rs->roupa_alheia==1) { ?><span class="menor"><strong>ROUPA DE OUTRA UNIDADE</strong></span><? } ?>
		        </td>
				<td valign="top" align="center">
					<?= pega_grupo_roupa($rs->id_grupo); ?> 
					
					<? /*if ($p==-2) { ?>
						<br /><br />
                    	<button onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=op/pesagem_limpa_extra_rotina2&amp;id_pesagem_peca=<?= $rs_pesagens_pecas->id_pesagem_peca; ?>&amp;extra=0');" id="pesagem_peca_<?= $rs_pesagens_pecas->id_pesagem_peca; ?>" class="botao_extra">tudo p/ mesa</button>
                    <? }
                    elseif ($p==-1) { ?>
                    	<br /><br />
                    	<button onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=op/pesagem_limpa_extra_rotina&amp;id_pesagem_peca=<?= $rs_pesagens_pecas->id_pesagem_peca; ?>&amp;extra=1');" id="pesagem_peca_<?= $rs_pesagens_pecas->id_pesagem_peca; ?>" class="botao_extra">tudo p/ extra</button>
                    <? } */ ?>

				</td>
				<td valign="top">		            
		            <span class="menor">
		            
		              <?
		                $result_pesagens_pecas= mysql_query("select * from op_limpa_pesagem_pecas
		                                                        where id_pesagem = '". $rs->id_pesagem ."'
		                                                        order by id_pesagem_peca asc
		                                                        ");
		                $linhas_pesagens_pecas= mysql_num_rows($result_pesagens_pecas);
		                
		                $k=1;
		                while ($rs_pesagens_pecas= mysql_fetch_object($result_pesagens_pecas)) {
		                    
		                    echo "<span class='item_roupa' id='item_roupa_". $rs_pesagens_pecas->id_pesagem_peca ."'>";
		                    
		                    if ($p==-2) { ?>
		                    	<button onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=op/pesagem_limpa_extra_rotina&amp;id_pesagem_peca=<?= $rs_pesagens_pecas->id_pesagem_peca; ?>&amp;extra=0');" id="pesagem_peca_<?= $rs_pesagens_pecas->id_pesagem_peca; ?>" class="botao_extra">mesa</button>
		                    <? }
		                    elseif ($p==-1) { ?>
		                    	<button onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=op/pesagem_limpa_extra_rotina&amp;id_pesagem_peca=<?= $rs_pesagens_pecas->id_pesagem_peca; ?>&amp;extra=1');" id="pesagem_peca_<?= $rs_pesagens_pecas->id_pesagem_peca; ?>" class="botao_extra">ex</button>
		                    <? }
		                    
		                    echo pega_pecas_roupa($rs_pesagens_pecas->id_tipo_roupa) ." (". $rs_pesagens_pecas->num_pacotes ."/". $rs_pesagens_pecas->pacotes_sobra ."/". $rs_pesagens_pecas->qtde_pecas_sobra .")</span>";
		                    
		                    //if ($k!=$linhas_pesagens_pecas) echo " ";
		                    
		                    $k++;
		                }
		                ?>
		            </span>
		        </td>
				<td valign="top" align="center"><?= fnum($rs->peso); ?> kg</td>
				<td valign="top" align="center"><?= pega_turno($rs->id_turno); ?></td>
				<td valign="top" align="center">
					<? if ($_SESSION["id_departamento_sessao"]!="") { ?>
		            <button class="tamanho50 espaco_dir" onclick="window.top.location.href='./?pagina=op/pesagem_limpa&amp;acao=e&amp;id_pesagem=<?= $rs->id_pesagem; ?>&amp;origem=2';" id="link_edita<?=$i;?>">edita</button>
		            <button class="tamanho50" onclick="var confirma= confirm('Tem certeza que deseja excluir esta pesagem?'); if (confirma) ajaxLink('linha_<?=$r;?>_<?=$i;?>', 'pesagemLimpaExcluir&amp;id_pesagem=<?= $rs->id_pesagem; ?>');" id="link_exclui<?=$i;?>">exclui</button>
		            <? } else { ?>
		            <a id="link_edita<?=$i;?>" href="./?pagina=op/pesagem_limpa&amp;acao=e&amp;id_pesagem=<?= $rs->id_pesagem; ?>&amp;origem=2">
						<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
					|
					<a href="javascript:ajaxLink('linha_<?=$r;?>_<?=$i;?>', 'pesagemLimpaExcluir&amp;id_pesagem=<?= $rs->id_pesagem; ?>');" onclick="return confirm('Tem certeza que deseja excluir esta pesagem?');">
						<img border="0" src="images/ico_lixeira.png" alt="Status" />
		            </a>
		            <? } ?>
		        </td>
			</tr>
		<? $i++; } ?>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td class="maior" align="center"><strong><?= fnum($peso_total); ?> kg</strong></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	
		<? } ?>
	
		<br /><br />
	
	
</fieldset>

<? $r++; }//fim for ?>

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
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/pesagem_limpa_cliente_listar&amp;data=<?=$data2;?>&amp;periodo=<?=$periodo;?>&amp;id_cliente=<?=$id_cliente;?>&amp;extra=<?=$extra;?>&amp;goma=<?=$goma;?>&amp;roupa_alheia=<?=$roupa_alheia;?>&amp;id_tipo_roupa=<?=$id_tipo_roupa;?>&amp;id_turno=<?=$id_turno;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<script type="text/javascript">
	daFoco("link_edita0");
</script>

<?
	$result_tt3= mysql_query("DROP TABLE IF EXISTS tr_percursos_virtual ") or die(mysql_error());
	$result_tt4= mysql_query("DROP TABLE IF EXISTS tr_percursos_clientes_virtual ") or die(mysql_error());

}
?>
<?
require_once("conexao.php");
if (pode("o", $_SESSION["permissao"])) {
	
	$result_topo= mysql_query("select * from com_livro
							  	where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_livro = '". $_GET["id_livro"] ."'
								and   restrito <> '1'
								");
	
	$rs_topo= mysql_fetch_object($result_topo);
	
	do {
		
		if ($rs_topo->id_livro!="") {
			$id_livro_original= $rs_topo->id_livro;
			//echo "<br />= ". $rs_topo->id_livro;
		}
		
		$result_topo= mysql_query("select * from com_livro
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   resposta_id_livro = '". $rs_topo->id_livro ."'
									and   restrito <> '1'
									");
		$rs_topo= mysql_fetch_object($result_topo);
		
	} while ($rs_topo->resposta_id_livro!="0");
	
	//echo "<strong>". $id_livro_original ."</strong>";
	
	//die();
?>

<h2>Histórico da conversa</h2>

<a href="javascript:void(0);" onclick="fechaDiv('tela_aux');" class="fechar">x</a>

<table cellspacing="0" width="100%">
<tr>
        <th width="13%" align="left" valign="bottom">Data</th>
        <th width="33%" align="left" valign="bottom">Identifica&ccedil;&atilde;o</th>
        <th width="54%" align="left" valign="bottom">Mensagem</th>
    </tr>
	<?
	
	//if ($_GET["resposta_id_livro"]!="0")
	//	$str_add= " or id_livro = '". $_GET["resposta_id_livro"] ."'
	//							   or resposta_id_livro = '". $_GET["resposta_id_livro"] ."' ";
	
	$result= mysql_query("select *, DATE_FORMAT(data_livro, '%v') as semana, DATE_FORMAT(data_livro, '%Y') as ano
							from com_livro
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							and   id_livro = '". $id_livro_original ."'
							and   restrito <> '1'
							order by  id_livro desc
							") or die(mysql_error());
	$linhas= mysql_num_rows($result);
	
	$rs= mysql_fetch_object($result);
	
	$i=0;
	$condicao=1;
	$mensagem_original= 0;
	
	if ($_SESSION["id_funcionario_sessao"]!="") $id_departamento_usuario2= pega_dado_carreira("id_departamento", $_SESSION["id_funcionario_sessao"]);
	else $id_departamento_usuario2= $_SESSION["id_departamento_sessao"];
	
	do {
		if ($mensagem_original==1) $condicao=0;
		
		$result_permissao= mysql_query("select * from com_livro_permissoes
										where id_livro = '". $rs->id_livro ."'
										and   id_departamento = '". $id_departamento_usuario2 ."'
										");
		$linhas_permissao= mysql_num_rows($result_permissao);
		
		if ($_SESSION["id_funcionario_sessao"]!="") {
			$result_permissao2= mysql_query("select * from rh_carreiras_departamentos, com_livro_permissoes
												where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
												and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   rh_carreiras_departamentos.id_departamento = com_livro_permissoes.id_departamento
												and   com_livro_permissoes.id_livro = '". $rs->id_livro ."'
											") or die(mysql_error());
			$linhas_permissao2= mysql_num_rows($result_permissao2);
		}
		else $linhas_permissao2=0;
		
		//se for admin, se for quem mandou ou se for um destinatário
		if (($_SESSION["tipo_usuario"]=="a") || (($id_departamento_usuario2==$rs->de) && ($rs->tipo_de=="d")) || (($_SESSION["id_funcionario_sessao"]==$rs->de) && ($rs->tipo_de=="f")) || ($linhas_permissao>0) || ($linhas_permissao2>0)) {
	?>
    <tr>
        <td valign="top"><?= desformata_data($rs->data_livro) ." ". $rs->hora_livro . $novo; ?></td>
        <td valign="top">
        	<strong>De:</strong>
			<?
			if ($rs->tipo_de=="f") {
				if (($rs->id_outro_departamento!="") && ($rs->id_outro_departamento!="0")) $id_departamento= $rs->id_outro_departamento;	
				else $id_departamento= pega_dado_carreira("id_departamento", $rs->de);	
				
				echo pega_funcionario($rs->de) ."<br />";
				
				$id_deixou= $rs->de;
				$id_agora= $_SESSION["id_funcionario_sessao"];
			}
			else {
				$id_departamento= $rs->de;
				$id_deixou= $rs->de;
				$id_agora= $_SESSION["id_departamento_sessao"];
			}
			
			echo "<strong>". pega_departamento($id_departamento) ."</strong>";
			?>
            
            <?
			$result_para= mysql_query("select * from com_livro_permissoes
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_livro = '". $rs->id_livro ."'
										");
			$para= "";
			while ($rs_para= mysql_fetch_object($result_para)) {
				if ($rs_para->departamento_livro!="") $departamento_nome= $rs_para->departamento_livro;
				else $departamento_nome= $rs_para->departamento;
				
				if ($rs_para->resposta_requerida_depto==1) $depois= " <span class=\'vermelho\'><strong>(*)</strong></span> ";
				elseif ($rs_para->resposta_requerida_depto==2) $depois= " <span class=\'verde\'><strong>(OK)</strong></span> ";
				else $depois= "";
				
				if ($rs_para->id_departamento==$rs->id_departamento_principal)
					$para.= "- <strong><u>". pega_departamento($rs_para->id_departamento) ."</u></strong> ". $depois ."; <br />";
				else
					$para.= "- ". pega_departamento($rs_para->id_departamento) ." ". $depois ."; <br />";
			}
			?>
            <br /><br />
            <strong>Para:</strong> <a href="javascript:void(0);" onmouseover="Tip('<?= $para; ?>');">veja a lista</a><br />
        </td>
        <td valign="top">
        	<div id="livro_<?= $rs->id_livro; ?>">
                <a name="livro_<?= $rs->id_livro; ?>"></a>
                <?= $rs->mensagem; ?><br /><br />
                <? // "id_livro: ". $rs->id_livro ." resposta_id_livro: ". $rs->resposta_id_livro; ?>
                <br /><br />
                <?
                //só mostra aguardando resposta quando não é NC ou reclamação
				if (($rs->id_motivo!=34) && ($rs->id_motivo!=37) && ($rs->id_motivo!=41) && ($rs->id_motivo!=42)) { ?>
                
					<? if ($rs->resposta_requerida==1) { ?>
                    <blink><strong class="vermelho">AGUARDANDO RESPOSTA</strong></blink><br /><br />
                    <? } elseif ($rs->resposta_requerida==2) { ?>
                    <strong class="azul">RESPONDIDO</strong><br /><br />
                    <? } ?>
                    
                <? } ?>
                
                <strong><?= pega_motivo($rs->id_motivo); ?> Nº <?= fnumi($rs->num_livro)."/".$rs->ano; ?></strong><br />
                
                
                <?
                if (($rs->prioridade_dias!="") && ($rs->prioridade_dias!="0"))
					echo "<strong>PRAZO:</strong> ". $rs->prioridade_dias ." dia(s)";
				?>
                <br /><br />
                <?
				//if ( ((($rs->id_motivo==34) || ($rs->id_motivo==37)) && ($_SESSION["tipo_usuario"]=="a")) || ( (($rs->id_motivo==34) || ($rs->id_motivo==37)) && ($id_departamento_usuario2==$rs->id_departamento_principal) ) ) {
				if (($rs->id_motivo==34) || ($rs->id_motivo==37) || ($rs->id_motivo==41) || ($rs->id_motivo==42)) {
					if (($rs->reclamacao_original_id_livro!="0") && ($rs->reclamacao_original_id_livro!="")) $id_livro_aqui= $rs->reclamacao_original_id_livro;
					else $id_livro_aqui= $rs->id_livro;
					
					$id_situacao_atual= pega_situacao_atual_reclamacao($id_livro_aqui);
					
					if ($id_situacao_atual==0) $situacao_atual= "NENHUMA RESPOSTA";
					else $situacao_atual= pega_situacao_reclamacao($id_situacao_atual);
				?>
				<br />
				<? } ?>
				
				<? if ($rs->reclamacao_id_cliente!=0) { ?>
				<span class="menor"><strong>CLIENTE:</strong> <?= pega_pessoa($rs->reclamacao_id_cliente); ?></span> <br />
				<? } ?>
				
				<? if (($rs->id_causa!=0)) { ?>
				<span class="menor caps"><strong>CAUSA:</strong> <?= pega_reclamacao_causa($rs->id_causa); ?></span> <br /><br />
				<? } ?>
				
				<? if (($rs->id_motivo==34) || ($rs->id_motivo==37) || ($rs->id_motivo==41) || ($rs->id_motivo==42)) { ?>
				<span class="menor"><strong>SITUAÇÃO ATUAL:</strong> <?= strtoupper($situacao_atual); ?></span> <br /><br />
				
				<a class="menor vermelho" href="./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=<?=$id_livro_aqui;?>&amp;origem=l&amp;data=<?=$data;?>"><strong>ACESSAR</strong></a>
				<br />
				
				<? } ?>
            </div>
        </td>
    </tr>
    <?
	}
    	$result= mysql_query("select *, DATE_FORMAT(data_livro, '%v') as semana, DATE_FORMAT(data_livro, '%Y') as ano
								from com_livro
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_livro = '". $rs->resposta_id_livro ."'
								order by  id_livro desc
								") or die(mysql_error());
		$linhas= mysql_num_rows($result);
		$rs= mysql_fetch_object($result);
		
		//chegamos na mensagem original
		if ($rs->resposta_id_livro=="0")
			$mensagem_original=1;
		
		} while ($condicao!=0); ?>
<? } ?>
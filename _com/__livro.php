<?
require_once("conexao.php");
if (pode("o", $_SESSION["permissao"])) {
	$acao= 'i'; //$_GET["acao"];
	
	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	
	if ($_SESSION["id_funcionario_sessao"]!="") $id_departamento_usuario2= pega_dado_carreira("id_departamento", $_SESSION["id_funcionario_sessao"]);
	else $id_departamento_usuario2= $_SESSION["id_departamento_sessao"];
	
	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= $_POST["data1"];
		$data2= $_POST["data2"];
	}
	else {
		if ( ($_GET["data1"]!="") && ($_GET["data2"]!="") ) {
			$data1= $_GET["data1"];
			$data2= $_GET["data2"];
		}
	}
	
	if ( ($data1!="") && ($data2!="") ) {
		$data1f= $data1;
		$data1= formata_data_hifen($data1);
		
		$data2f= $data2;
		$data2= formata_data_hifen($data2);
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	elseif ($periodo!="") {
		$periodo2= explode('/', $periodo);
		
		$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	if ($_GET["data"]!="") $data= $_GET["data"];
	if ($_POST["data"]!="") $data= $_POST["data"];
	if ($data=="") $data= date("d/m/Y");
	
	$dia= substr($data, 0, 2);
	$mes= substr($data, 3, 2);
	$ano= substr($data, 6, 4);
	
	//$semana_do_ano= date("W", mktime(0, 0, 0, $mes, $dia, $ano));
	
	//$ano_da_semana_do_ano= date("o", mktime(0, 0, 0, $mes, $dia, $ano));
	
	//if (($ano==$ano_da_semana_do_ano) && ($_POST["data"]!="")) $str .= "and   DATE_FORMAT(data_livro, '%Y') = '". $ano ."'";
	
	if (($data1!="") && ($data2!="")) $str .= " and   data_livro >= '". $data1 ."' and data_livro <= '". $data2 ."' ";
	else {
		$titulo_livro= " (dia ". $data .")";
		$str .= " and   data_livro = '". formata_data($data) ."' ";
	}
	
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($id_funcionario!="") $str .= " and   tipo_de = 'f' and   de = '". $id_funcionario ."' ";
	
	if ($_POST["id_departamento_principal"]!="") $id_departamento_principal= $_POST["id_departamento_principal"];
	if ($_GET["id_departamento_principal"]!="") $id_departamento_principal= $_GET["id_departamento_principal"];
	if ($id_departamento_principal!="") $str .= " and   id_departamento_principal = '". $id_departamento_principal ."' ";
	
	
	if ($_POST["id_motivo"]!="") $id_motivo= $_POST["id_motivo"];
	if ($_GET["id_motivo"]!="") $id_motivo= $_GET["id_motivo"];
	if ($id_motivo!="") $str .= " and   id_motivo = '". $id_motivo ."' ";
	
	if ($_POST["resposta_requerida"]!="") $resposta_requerida= $_POST["resposta_requerida"];
	if ($_GET["resposta_requerida"]!="") $resposta_requerida= $_GET["resposta_requerida"];
	if ($resposta_requerida!="") $str .= " and   resposta_requerida = '1' ";
	
	if ($_POST["parte"]!="") $parte= $_POST["parte"];
	if ($_GET["parte"]!="") $parte= $_GET["parte"];
	if ($parte!="") $str .= "and   mensagem like '%". htmlentities($parte) ."%' ";
	
	if ($_POST["minhas"]!="") $minhas= $_POST["minhas"];
	if ($_GET["minhas"]!="") $minhas= $_GET["minhas"];
	if (($minhas==1) || (($_SESSION["id_usuario"]==13) && ($minhas!=2)))
		$str .= " and   id_livro IN (
								   select id_livro from com_livro_permissoes
								   where  id_departamento = '". $id_departamento_usuario2 ."'
								   ) ";
	/*
	if ($_POST["depto_para"]!="") $depto_para= $_POST["depto_para"];
	if ($_GET["depto_para"]!="") $depto_para= $_GET["depto_para"];
	if ($depto_para!="")
		$str .= " and   id_livro IN (
								   select id_livro from com_livro_permissoes
								   where  id_departamento = '". $depto_para ."'
								   ) ";
	*/
	
	/*if ($_SESSION["tipo_usuario"]=="a")
		echo "select *, DATE_FORMAT(data_livro, '%v') as semana, DATE_FORMAT(data_livro, '%Y') as ano
							from com_livro
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							and   restrito <> '1'
							$str
							order by  id_livro desc
							";
	*/
	
	
	$result= mysql_query("select *, DATE_FORMAT(data_livro, '%v') as semana, DATE_FORMAT(data_livro, '%Y') as ano
							from com_livro
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							and   restrito <> '1'
							$str
							order by  id_livro desc
							") or die(mysql_error());
	$linhas= mysql_num_rows($result);
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<div id="tela_aux" class="telinha2 screen menor telinha_rolagem">
</div>
    
<h2>Livro <?= $titulo_livro; ?></h2>

<fieldset class="borda_vermelha">
	<legend class="vermelho">Suas pendências</legend>
    
    <?
	//pegar todos os deptos do qual o usuário é responsável
	$result_departamento_auxiliares= mysql_query("select * from rh_carreiras_departamentos
													where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
													and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
												") or die(mysql_error());
	$linhas_departamento_auxiliares= mysql_num_rows($result_departamento_auxiliares);
	
	if ($linhas_departamento_auxiliares>1) {
	?>
    
    <div class="div_abas screen" id="aba_pendencias_busca">
        <ul class="abas">
            <li id="aba_minhas" class="atual"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_minhas', 'aba_pendencias_busca'); abreDiv('pendencias_minhas'); fechaDiv('pendencias_responsaveis');">Minhas</a></li>
            <li id="aba_responsaveis"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_responsaveis', 'aba_pendencias_busca'); abreDiv('pendencias_responsaveis'); fechaDiv('pendencias_minhas');">Sob minha supervisão</a></li>
        </ul>
    </div>
    <br />
    <? } ?>
    
    <div id="pendencias_minhas">
    
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('1', '1', '0');">* O que preciso responder:</a></h3>
        
        <div id="div_pendencias_1_1" class="nao_mostra">
            
        </div>
        
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('1', '5', '0');">* O que estou esperando resposta:</a></h3>
        
        <div id="div_pendencias_1_5" class="nao_mostra">
            
        </div>
        
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('1', '2', '0');">* Reclamações:</a></h3>
        
        <div id="div_pendencias_1_2" class="nao_mostra">
        </div>
        
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('1', '3', '0');">* Não-conformidades:</a></h3>
        
        <div id="div_pendencias_1_3" class="nao_mostra">
        </div>
        
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('1', '4', '0');">* RM's não finalizadas:</a></h3>
        
        <div id="div_pendencias_1_4" class="nao_mostra">
        </div>
    </div>
    
    <div id="pendencias_responsaveis" class="nao_mostra">
    
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('2', '1', '0');">* O que precisam responder:</a></h3>
        
        <div id="div_pendencias_2_1" class="nao_mostra">
            
        </div>
        
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('2', '5', '0');">* O que estão esperando resposta:</a></h3>
        
        <div id="div_pendencias_2_5" class="nao_mostra">
            
        </div>
        
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('2', '2', '0');">* Reclamações:</a></h3>
        
        <div id="div_pendencias_2_2" class="nao_mostra">
        </div>
        
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('2', '3', '0');">* Não-conformidades:</a></h3>
        
        <div id="div_pendencias_2_3" class="nao_mostra">
        </div>
        
        <h3><a class="vermelho" href="javascript:void(0);" onclick="checaPendencias('2', '4', '0');">* RM's não finalizadas:</a></h3>
        
        <div id="div_pendencias_2_4" class="nao_mostra">
        </div>
    </div>
    
    
</fieldset>

<fieldset>
    <legend>Deixar mensagem</legend>
    <div id="conteudo_form">
        <?
        $acao= "i";
		require_once("_com/__livro_form.php");
		?>
    </div>
</fieldset>

<fieldset id="pesquisa">
	<legend>Pesquisar mensagens</legend>
    
    <form action="./?pagina=com/livro#pesquisa" method="post" name="formLivro" id="formLivro" onsubmit="return validaFormNormal('validacoes_busca');">
    	
        
        <div class="div_abas screen" id="aba_opcoes_busca">
            <ul class="abas">
                <li id="aba_simples" <? if (($_POST["origem"]==1) || ($_POST["origem"]=="")) { ?>class="atual"<? } ?>><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_simples', 'aba_opcoes_busca'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=com/livro_form_busca1&minhas=<?=$minhas;?>');">Por dia</a></li>
                <li id="aba_avancada" <? if ($_POST["origem"]==2) { ?>class="atual"<? } ?>><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_avancada', 'aba_opcoes_busca'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=com/livro_form_busca2&minhas=<?=$minhas;?>');">Avançada</a></li>
            </ul>
        </div>
        <br />
        
        <? /*
        <label for="ano" class="tamanho100">Ano:</label>
        <select name="ano" id="ano" title="Ano" class="tamanho200 espaco_dir" onchange="carregaSemanas();">	  		
            <?
            $i=0;
            $result_per= mysql_query("select distinct(DATE_FORMAT(data_livro, '%Y')) as ano
                                        from com_livro order by data_livro asc limit 1 ");
            
			$rs_ano= mysql_fetch_object($result_ano);
			
			if ($rs_ano->ano=="") $ano_inicial= date("Y");
			else $ano_inicial= $rs_ano->ano;
			
			for ($i=date("Y"); $i>=$ano_inicial; $i--) {
			?>
			<option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> <? if ($i==date("Y")) echo "selected=\"selected\""; ?> value="<?= $i; ?>"><?= $i; ?></option>
			<? } ?>
		</select>
		
        <label for="semana" class="tamanho100">Semana:</label>
        <div id="semanas_atualiza" class="espaco_dir">
            <select name="semana" id="semana" title="Semana" class="tamanho200 espaco_dir">	  		
				<?
				for ($i=1; $i<13; $i++) {
				?>
                <optgroup label="<?= traduz_mes($i); ?>">
					<?
					$dias_mes= date("t", mktime(14, 0, 0, $i, 1, $ano));
					
					//$ultima_semana_desse_mes= pega_numero_semana($ano, $i, $dias_mes);
					
					for ($j=1; $j<=$dias_mes; $j++) {
						$semana_mes= pega_numero_semana($ano, $i, $j);
						$semana_ano= date("W", mktime(14, 0, 0, $i, $j, $ano));
						
						$jah[$j]= $semana_mes;
						
						if ($jah[$j-1]!=$jah[$j]) {
					?>
                    <option <? if ($j%2==0) echo "class=\"cor_sim\""; ?> <? if ($j==date("Y")) echo "selected=\"selected\""; ?> value="<?= $semana_ano; ?>"><?= $semana_mes; ?></option>
                    <? } } ?>
                </optgroup>
                <? } ?>
            </select>
        </div>
        */ ?>
        
        <div id="conteudo_interno">
	        
			<?
            if (($_POST["origem"]==1) || ($_POST["origem"]=="")) require_once("_com/__livro_form_busca1.php");
			else require_once("_com/__livro_form_busca2.php");
			?>
        </div>
        <br /><br />
        
        <label class="tamanho100">&nbsp;</label>
        <button type="submit">buscar mensagens &raquo;</button>
    </form>
    <br />
</fieldset>

<fieldset>
    <legend>Mensagens</legend>
    	
        <? /*<p>Total de <strong><?=$linhas;?></strong> mensagens nesta página.</p>*/ ?>
        
        <? if ($_SESSION["tipo_usuario"]=="a") { ?>
        <ul class="recuo1">
        	<li><a href="./?pagina=com/livro&amp;minhas=1&amp;id_funcionario=<?= $id_funcionario; ?>&amp;parte=<?= $parte; ?>&amp;id_motivo=<?= $id_motivo; ?>&amp;resposta_requerida=<?= $resposta_requerida; ?>&amp;data1=<?= desformata_data($data1); ?>&amp;data2=<?= desformata_data($data2); ?>&amp;data=<?= $data;?>">Abrir somente mensagens para o meu departamento</a>.</li>
            <li><a href="./?pagina=com/livro&amp;minhas=2&amp;id_funcionario=<?= $id_funcionario; ?>&amp;parte=<?= $parte; ?>&amp;id_motivo=<?= $id_motivo; ?>&amp;resposta_requerida=<?= $resposta_requerida; ?>&amp;data1=<?= desformata_data($data1); ?>&amp;data2=<?= desformata_data($data2); ?>&amp;data=<?= $data;?>">Abrir tudo</a>.</li>
        </ul>
        <br />
        <? } ?>
        
        <table cellspacing="0" width="100%">
        <tr>
          <th width="8%" align="left" valign="bottom">Data</th>
          <th width="21%" align="left" valign="bottom">Identifica&ccedil;&atilde;o</th>
          <th width="60%" align="left" valign="bottom">Mensagem</th>
          <th width="11%" align="left" valign="bottom">&nbsp;</th>
        </tr>
        <?
		
		$i=0;
		while ($rs= mysql_fetch_object($result)) {
			
			$result_permissao= mysql_query("select * from com_livro_permissoes
										   	where id_livro = '". $rs->id_livro ."'
											and   id_departamento = '". $id_departamento_usuario2 ."'
											");
			$linhas_permissao= mysql_num_rows($result_permissao);
			
			$precisar=0;
			
			if ($_SESSION["id_funcionario_sessao"]!="") {
				$result_permissao2= mysql_query("select * from rh_carreiras_departamentos, com_livro_permissoes
													where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
													and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   rh_carreiras_departamentos.id_departamento = com_livro_permissoes.id_departamento
													and   com_livro_permissoes.id_livro = '". $rs->id_livro ."'
												") or die(mysql_error());
				$linhas_permissao2= mysql_num_rows($result_permissao2);
				
				
				//-----------------
				
				$result_permissao3= mysql_query("select * from rh_carreiras_departamentos, com_livro_permissoes
													where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
													and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   rh_carreiras_departamentos.id_departamento = com_livro_permissoes.id_departamento
													and   com_livro_permissoes.id_livro = '". $rs->id_livro ."'
													and   rh_carreiras_departamentos.valido = '1'
												") or die(mysql_error());
				$linhas_permissao3= mysql_num_rows($result_permissao3);
				
				while ($rs_permissao3= mysql_fetch_object($result_permissao3)) {
					if ($rs_permissao3->id_departamento==$rs->id_departamento_principal) {
						$classe= "cor_sim_destaque";
						$precisar=1;
						
						break;
					}
				}
			}
			else $linhas_permissao2=0;
			
			
			//se for admin, se for quem mandou ou se for um destinatário
			if (($_SESSION["tipo_usuario"]=="a") || (($id_departamento_usuario2==$rs->de) && ($rs->tipo_de=="d")) || (($_SESSION["id_funcionario_sessao"]==$rs->de) && ($rs->tipo_de=="f")) || ($linhas_permissao>0) || ($linhas_permissao2>0)) {
				
				$result_lida= @mysql_query("select * from com_livro_lidas
											where id_livro = '". $rs->id_livro ."'
											and   id_usuario = '". $_SESSION["id_usuario"] ."'
											");
				if (@mysql_num_rows($result_lida)==0) {
					$novo="<br /><blink><span class=\"vermelho menor\"><strong>NOVO</strong></span></blink>";
					
					$result_ler= mysql_query("insert into com_livro_lidas
									 	(id_empresa, id_livro, data_leitura, hora_leitura, id_usuario)
										values
										('". $_SESSION["id_empresa"] ."', '". $rs->id_livro ."',
										 '". date("Ymd") ."', '". date("H:i:s") ."', '". $_SESSION["id_usuario"] ."')
										");
				}
				else $novo= "";
				
				if ($precisar==0) {
					if ($id_departamento_usuario2==$rs->id_departamento_principal) $classe= "cor_sim_destaque";
					elseif ($i%2==0) $classe= "cor_sim";
					else $classe= "cor_nao";
				}
        ?>
        <tr class="<?= $classe; ?>">
            <td valign="top">
				<?= desformata_data($rs->data_livro) ."<br />". $rs->hora_livro . $novo; ?>
            </td>
            <td valign="top">
            <strong>De:</strong>
			<?
			if ($rs->tipo_de=="f") {
				if (($rs->id_outro_departamento!="") && ($rs->id_outro_departamento!="0")) $id_departamento= $rs->id_outro_departamento;	
				else $id_departamento= pega_dado_carreira("id_departamento", $rs->de);	
				
				if ($rs->de==0) echo "Sistema SiGE<br />";
				else echo pega_funcionario($rs->de) ."<br />";
				
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
			$aguardando_resposta=0;
			
			$result_para= mysql_query("select * from com_livro_permissoes, rh_departamentos
										where com_livro_permissoes.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   com_livro_permissoes.id_departamento = rh_departamentos.id_departamento
										and   com_livro_permissoes.id_livro = '". $rs->id_livro ."'
										");
			$para= "";
			while ($rs_para= mysql_fetch_object($result_para)) {
				
				if ($rs_para->departamento_livro!="") $departamento_nome= $rs_para->departamento_livro;
				else $departamento_nome= $rs_para->departamento;
				
				if ($rs_para->resposta_requerida_depto==1) $depois= " <span class=\'vermelho\'><strong>(*)</strong></span> ";
				elseif ($rs_para->resposta_requerida_depto==2) $depois= " <span class=\'verde\'><strong>(OK)</strong></span> ";
				else $depois= "";
				
				if ($rs_para->id_departamento==$rs->id_departamento_principal)
					$para.= "- <strong><u>". $departamento_nome ."</u></strong> ". $depois ."; <br />";
				else
					$para.= "- ". $departamento_nome ." ". $depois ."; <br />";
				
				if ($aguardando_resposta==0) {
					if (($rs_para->resposta_requerida_depto=="1") && ($id_departamento_usuario2==$rs_para->id_departamento)) $aguardando_resposta=1;
					elseif (($rs_para->resposta_requerida_depto=="2") && ($id_departamento_usuario2==$rs_para->id_departamento)) $aguardando_resposta=2;
					else $aguardando_resposta= 0;
				}
			}
			?>
            <br /><br />
            <strong>Para:</strong> <a href="javascript:void(0);" onmouseover="Tip('<?= $para; ?>');">veja a lista</a><br />
            
            <?
            if ($_SESSION["tipo_usuario"]=="a") {
				$result_quemleu= @mysql_query("select * from com_livro_lidas
											 	where id_livro = '". $rs->id_livro ."' 
												order by data_leitura asc, hora_leitura asc
												");
				$quem_leu="";
				
				while ($rs_quemleu= @mysql_fetch_object($result_quemleu))
					$quem_leu.= "- <strong>". pega_nome_pelo_id_usuario($rs_quemleu->id_usuario) ."</strong> em <strong>". desformata_data($rs_quemleu->data_leitura) ."</strong> às <strong>". $rs_quemleu->hora_leitura ."</strong>;<br />";
			?>
            <strong>Quem leu:</strong> <a href="javascript:void(0);" onmouseover="Tip('<?= $quem_leu; ?>');">veja a lista</a><br />
            <? } ?>
            
            </td>
            <td valign="top">
                <div id="livro_<?= $rs->id_livro; ?>">
                	<a name="livro_<?= $rs->id_livro; ?>"></a>
					<?= $rs->mensagem; ?>
                    
                    <br />
                    
                    <?
                    if (($rs->resposta_id_livro!="0") && ($rs->resposta_id_livro!="")) {
						if ($rs->tipo_resposta=="f") {
							if ($rs->resposta=="0") {
								$em_resposta= "Sistema SiGE";
								$id_departamento_resposta= "0";
								$departamento_resposta= "";
							}
							else {
								$em_resposta= pega_funcionario($rs->resposta);
								$id_departamento_resposta= pega_dado_carreira("id_departamento", $rs->resposta);
								$departamento_resposta= " (". pega_departamento($id_departamento_resposta) .")";
							}
						}
						else {
							$em_resposta= pega_departamento($rs->resposta);
							$departamento_resposta="";
						}
						
						$livro_original= addslashes(strip_tags(pega_livro($rs->resposta_id_livro)));
						
						$id_motivo_resposta= pega_campo_livro($rs->resposta_id_livro, "id_motivo");
						
						if (($id_motivo_resposta==34) || ($id_motivo_resposta==37) || ($id_motivo_resposta==41) || ($id_motivo_resposta==42)) {
							
							$num_livro_resposta= pega_campo_livro($rs->resposta_id_livro, "num_livro");
							$ano_livro_resposta= substr(pega_campo_livro($rs->resposta_id_livro, "data_livro"), 0, 4);
							
							?>
                            <br />
                            <span class="menor"><? if ($livro_original!="") { ?><a href="javascript:void(0);" class="contexto">Em resposta<span><?= nl2br($livro_original); ?></span></a><? } else { ?>Em resposta<? } ?> a <strong><a href="./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=<?= $rs->resposta_id_livro; ?>" target="_blank"><?= pega_motivo($id_motivo_resposta); ?> Nº <?= $num_livro_resposta ."/". $ano_livro_resposta; ?></a></strong>, criada por <strong><?= $em_resposta; ?></strong> <?= $departamento_resposta; ?></span>
                            <?
						}
						else {
						//$livro_original= substr($livro_original, 0, -8);
					?>
                    <br />
					<span class="menor"><? if ($livro_original!="") { ?><a href="javascript:void(0);" class="contexto">Em resposta<span><?= nl2br($livro_original); ?></span></a><? } else { ?>Em resposta<? } ?> a <strong><?= $em_resposta; ?></strong> <?= $departamento_resposta; ?></span>
                    <? } ?>
                    |
                    <a class="menor" href="javascript:void(0);" onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=com/livro_conversa&amp;id_livro=<?= $rs->id_livro; ?>&amp;resposta_id_livro=<?= $rs->resposta_id_livro; ?>');">ver conversa</a>
                    <? } ?>
                    
                    <br />
                    
                    <? if ($rs->id_departamento_principal!=0) { ?>
                    <br />
					<span class="menor caps"><strong>SETOR RESPONSÁVEL:</strong> <?= pega_departamento($rs->id_departamento_principal); ?></span> <br />
                    <? } ?>
                    
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
                    <span class="menor caps"><strong>CAUSA:</strong> <?= pega_reclamacao_causa($rs->id_causa); ?></span> <br />
                    <? } ?>
					
                    <? if (($rs->id_motivo==34) || ($rs->id_motivo==37) || ($rs->id_motivo==41) || ($rs->id_motivo==42)) { ?>
					<span class="menor"><strong>SITUAÇÃO ATUAL:</strong> <?= strtoupper($situacao_atual); ?></span> <br /><br />
					
                    <br />
                    <a id="link_edita<?=$i;?>" target="_blank" href="index2.php?pagina=qualidade/reclamacao_pdf&amp;id_livro=<?=$id_livro_aqui;?>">
					<img border="0" src="images/ico_pdf.png" alt="Edita" /></a><br /><br />
                
                    <a class="menor vermelho" href="./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=<?=$id_livro_aqui;?>&amp;origem=l&amp;data=<?=$data;?>"><strong>ACESSAR</strong></a>
					<br />
                    
					<? } ?>
					
                    <br />
                    
					
                    <div id="livro_resposta_<?=$rs->id_livro;?>">
	                    <a href="javascript:void(0);" onclick="respondeLivro('<?= $rs->id_livro; ?>', '<?= $id_departamento; ?>', '<?=$rs->tipo_de;?>', '<?=$rs->de;?>', '<?=$minhas;?>', '<?=$id_funcionario;?>', '<?=$parte;?>', '<?=$id_motivo;?>', '<?=$resposta_requerida;?>', '<?=desformata_data($data1);?>', '<?=desformata_data($data2);?>', '<?=$data;?>', '<?=$depto_para;?>', '<?=$id_departamento_principal;?>');">responder</a>
                    </div>
                    
                    
                    <? if ($_SESSION["tipo_usuario"]=="a") { ?>
                    <br />
                    <a href="link.php?livroExcluir&amp;id_livro=<?= $rs->id_livro; ?>&amp;data=<?= $data; ?>" onclick="return confirm('Tem certeza que deseja excluir?');">
                        <img border="0" src="images/ico_lixeira.png" alt="Status" />
                    </a>
                    <? } ?>
                </div>
            </td>
            <td valign="top" class="menor">
            	<?
                //só mostra aguardando resposta quando não é NC ou reclamação
				if (($rs->id_motivo!=34) && ($rs->id_motivo!=37) && ($rs->id_motivo!=41) && ($rs->id_motivo!=42)) { ?>
                
					<? if ($aguardando_resposta===1) { ?>
                    <blink><strong class="vermelho">AGUARDANDO MINHA RESPOSTA</strong></blink><br /><br />
                    <? } elseif ($aguardando_resposta==2) { ?>
                    <strong class="azul">JÁ RESPONDI</strong><br /><br />
                    <? } ?>
                    
                <? } ?>
                
                <strong><?= pega_motivo($rs->id_motivo); ?> Nº <?= fnumi($rs->num_livro)."/".$rs->ano; ?></strong><br />
                
                <?
                if (($rs->prioridade_dias!="") && ($rs->prioridade_dias!="0"))
					echo "<strong>PRAZO:</strong> ". $rs->prioridade_dias ." dia(s)";
				?>
            </td>
        </tr>
        <?
				$i++;
			}
        }
		?>
    </table>
    <?
    $_SESSION["reclamacao_origem"]= "";
	$_SESSION["livro_reclamacao_ancora"]= "";
	$_SESSION["livro_reclamacao_data"]= "";
	?>
</fieldset>
<br />

<br />

<? if ($_GET["msg"]=="") { ?>
<script language="javascript">
	//daFoco("para_19");
</script>
<? } ?>

<? } ?>
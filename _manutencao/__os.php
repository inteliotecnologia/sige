<?
require_once("conexao.php");
if (pode("kj", $_SESSION["permissao"])) {
	
	if ($id_os=="") $id_os= $_GET["id_os"];
	
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from man_oss
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_os = '". $id_os ."'
								");
		$linhas= mysql_num_rows($result);
		
		if ($linhas==0) {
			die("OS não encontrada.");
		}
		
		$rs= mysql_fetch_object($result);
		
		$titulo2= " nº ". $rs->num_os;
		
		$result_lida= mysql_query("select * from man_oss_lidas
								  	where id_os = '". $rs->id_os ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_usuario = '". $_SESSION["id_usuario"] ."'
									");
		if (mysql_num_rows($result_lida)==0)
			$result_ler= mysql_query("insert into man_oss_lidas
									 	(id_empresa, id_os, data_leitura, hora_leitura, id_usuario)
										values
										('". $_SESSION["id_empresa"] ."', '". $rs->id_os ."',
										 '". date("Ymd") ."', '". date("H:i:s") ."', '". $_SESSION["id_usuario"] ."')
										");
	}
?>

<div id="tela_tempo_servico" class="telinha1 screen">
	
</div>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Ordem de serviço <?= $titulo2; ?></h2>

<form action="<?= AJAX_FORM; ?>formOS&amp;acao=<?= $acao; ?>" method="post" name="formOS" id="formOS" onsubmit="return validaFormNormal('validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="data_os@data|hora_os@vazio|id_servico_tipo@vazio|id_departamento@vazio|id_tecnico@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_os" class="escondido" type="hidden" id="id_os" value="<?= $rs->id_os; ?>" />
    <? } ?>
    
    <fieldset id="primeiro">
        <legend>Reclamante</legend>
        
        <div class="parte50">
            
            <?
			if ($acao=='i') {
				$data_os= date("d/m/Y");
			?>
			<label for="data_os">Data:</label>
			<input name="data_os" id="data_os" class="tamanho25p" onkeyup="formataData(this);" maxlength="10" value="<?= $data_os; ?>" title="Data" />
			<br />
            
			<?
			$hora_os= date("H:i:s");
			?>
			<label for="hora_os">Hora:</label>
			<input name="hora_os" id="hora_os" class="tamanho25p" onkeyup="formataHora(this);" maxlength="5" value="<?= $hora_os; ?>" title="Hora" />
            <br /><br />
            <? } ?>
            
            <? /*
            <label for="local_os">Local:</label>
            <select name="local_os" id="local_os" title="Tipo" onchange="alteraLocalOS(this.value);">
            	<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                
                <option class="cor_sim" value="1" <? if ($rs->local_os=='1') echo "selected=\"selected\""; ?>>Interna</option>
                <option value="2" <? if ($rs->local_os=='2') echo "selected=\"selected\""; ?>>Externa</option>
            </select>
            <br />
            */ ?>
            
            <? /*<div id="div_clientes" <? if (($acao=="i") || (($acao=="e") && ($rs->local_os=="1"))) { ?>class="nao_mostra"<? } ?>>
                <label for="id_cliente">Cliente:</label>
                <select name="id_cliente" id="id_cliente" title="Cliente">
                    <option value="">-</option>
                    <?
                    $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                                where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'c'
                                                and   pessoas.id_cliente_tipo = '1'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_cli = mysql_fetch_object($result_cli)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> <? if ($rs->id_cliente==$rs_cli->id_cliente) echo "selected=\"selected\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
            </div>*/ ?>
            
            <? /*
            <label for="tipo_os">Tipo:</label>
            <select name="tipo_os" id="tipo_os" title="Tipo" onchange="alteraTipoRM(this.value);">
            	<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                
                <option class="cor_sim" value="e" <? if (($rs->tipo_os=='e') || (($acao=='i') && ($_GET["id_equipamento"]!=""))) echo "selected=\"selected\""; ?>>Equipamento</option>
                <option value="p" <? if ($rs->tipo_os=='p') echo "selected=\"selected\""; ?>>Predial</option>
            </select>
            <br />
            
            <div id="div_id_equipamento" <? if ((($acao=='i') && ($_GET["id_equipamento"]=="")) || ($rs->tipo_os=='p')) { ?>class="escondido"<? } ?>>
                <label for="id_equipamento">Equipamento:</label>
                <select name="id_equipamento" id="id_equipamento" title="Equipamento">
                    <? if ($acao=='i') { ?>
                    <option value="">-</option>
                    <? } ?>
                    
                    <?
                    $result_eq= mysql_query("select * from op_equipamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by tipo_equipamento asc,
												equipamento asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_eq = mysql_fetch_object($result_eq)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_eq->id_equipamento; ?>" <? if (($rs_eq->id_equipamento==$rs->id_equipamento) || ($rs_eq->id_equipamento==$_GET["id_equipamento"])) echo "selected=\"selected\""; ?>><?= $rs_eq->equipamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                <label>&nbsp;</label>
                <a href="./?pagina=op/equipamento_listar" target="_blank">cadastre outros aqui</a>
            </div>
            <br />
            */ ?>
            
            <label for="id_departamento">* Setor:</label>
            <select name="id_departamento" id="id_departamento" title="Departamento">
                <option value="">- SETOR -</option>
				<?
                $result_dep= mysql_query("select * from rh_departamentos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
											order by departamento asc
                                             ");
                $i=0;
                while ($rs_dep = mysql_fetch_object($result_dep)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                <? $i++; } ?>
            </select>
            <br />
        	
        	<label for="id_servico_tipo">* Tipo serviço:</label>
            <select name="id_servico_tipo" id="id_servico_tipo" title="Tipo serviço">
                <option value="">- TIPO -</option>
				<?
                $result_ts= mysql_query("select * from man_servicos_tipos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
											order by servico_tipo asc
                                             ");
                $i=0;
                while ($rs_ts = mysql_fetch_object($result_ts)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ts->id_servico_tipo; ?>" <? if ($rs_ts->id_servico_tipo==$rs->id_servico_tipo) echo "selected=\"selected\""; ?>><?= $rs_ts->servico_tipo; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            
            
            <label for="prioridade_dias">Prioridade:</label>
            <select name="prioridade_dias" id="prioridade_dias" title="Prioridade" class="tamanho25p">
            	<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                
                <option class="cor_sim" value="0" <? if ($rs->prioridade_dias=='0') echo "selected=\"selected\""; ?>>Baixa</option>
                <option value="1" <? if ($rs->prioridade_dias=='1') echo "selected=\"selected\""; ?>>Média</option>
                <option class="cor_sim" value="2" <? if ($rs->prioridade_dias=='2') echo "selected=\"selected\""; ?>>Alta</option>
                <option value="5" <? if ($rs->prioridade_dias=='5') echo "selected=\"selected\""; ?>>Urgente</option>
            </select>
            <br /><br />
            
            <label for="area">Área:</label>
			<input name="area" id="area" value="<?= $rs->area; ?>" title="Área" />
            <br />
            
            
        </div>
        <div class="parte50">
        	
        	
            
            
        	
            <label for="id_tecnico">Técnico executor:</label>
            <select name="id_tecnico" id="id_tecnico" title="Técnico">
                <option value="">-</option>
                <?
                $result_tec= mysql_query("select * from  man_tecnicos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											order by num_tecnico asc
											") or die(mysql_error());
                $i=0;
                while ($rs_tec = mysql_fetch_object($result_tec)) {
					if ($rs_tec->id_funcionario==0) $nome_tecnico= $rs_tec->nome_tecnico;
					else $nome_tecnico= pega_funcionario($rs_tec->id_funcionario);
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> <? if ($rs->id_tecnico==$rs_tec->id_tecnico) echo "selected=\"selected\""; ?> value="<?= $rs_tec->id_tecnico; ?>"><?= $nome_tecnico; ?></option>
                <? $i++; } ?>
            </select>
            <br />
        
            <label for="descricao">Descrição<br />do serviço:</label>
            <textarea name="descricao" id="descricao" title="Descrição"><?=$rs->descricao;?></textarea>
            <br />
        </div>
        <br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        <br />
        
    </fieldset>
    
</form>

<? if (($acao=='e') && (pode("jk", $_SESSION["permissao"])) ) { ?>
    

<div class="parte50">
	   
	<fieldset>
    	<legend>Situação da OS</legend>
        
        <ul>
        <?
		$result_andamento= mysql_query("select * from man_oss_andamento
									   	where id_os= '". $rs->id_os ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										");
		$i=1;
		while ($rs_andamento= mysql_fetch_object($result_andamento)) {
			$andamento_final= $rs_andamento->id_situacao;
			
			if ($i==1) {
				$data_os_aqui= $rs_andamento->data_os_andamento;
			}
		?>
            <li>
                <div class="parte10">
                	<div class="index_gigante"><?=$i;?>)</div>
                </div>
                <div class="parte80">
                    <label class="tamanho80">Data/hora:</label> <?= desformata_data($rs_andamento->data_os_andamento) ." ". $rs_andamento->hora_os_andamento; ?><br />
                    <label class="tamanho80">Por:</label> <?= pega_nome_pelo_id_usuario($rs_andamento->id_usuario); ?><br />
                    <label class="tamanho80">Situação:</label> <?= pega_situacao_os($rs_andamento->id_situacao); ?><br />
                    <? if ($rs_andamento->obs!="") { ?>
                    <label class="tamanho80">OBS:</label> <?= strip_tags($rs_andamento->obs); ?><br />
                    <? } ?>
                    
                    <?
                    //se for finalizada
					if ($rs_andamento->id_situacao=="5") {
						if (($rs_andamento->nota=="") && (($rs->id_usuario==$_SESSION["id_usuario"]) || ($_SESSION["tipo_usuario"]=='a')) ) {
					?>
					<br/>
                    <label class="menor vermelho"><strong>AVALIE AGORA:</strong></label>
                    <select name="nota" id="nota_<?=$rs_andamento->id_situacao;?>" onchange="avaliaServicoOS('<?= $rs_andamento->id_os; ?>', '<?= $rs_andamento->id_os_andamento; ?>', this, this.value);">
                    	<option value="">selecione</option>
						<?
                        for ($i=0; $i<2; $i++) {
							//$descricao_nota= pega_descricao_nota($i);
							$descricao_nota= pega_descricao_contento($i);
							$descricao_nota= explode("@", $descricao_nota);
						?>
                        <option class="<?= $descricao_nota[1]; ?>" value="<?=$i;?>"><?= $i ." - ". $descricao_nota[0]; ?></option>
                        <? } ?>
                    </select>
                    <br />
                    <?
                    	}
						elseif ($rs_andamento->nota!="") {
							//$descricao_nota= pega_descricao_nota($rs_andamento->nota);
							$descricao_nota= pega_descricao_contento($rs_andamento->nota);
							$descricao_nota= explode("@", $descricao_nota);
							
							echo "<label class=\"tamanho80\">Avaliação:</label>  <span class=\"menor ". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span>";
							?>
                            <a onmouseover="Tip('Excluir esta avaliação.');" href="link.php?rmAndamentoNotaExcluir&amp;id_os_andamento=<?= $rs_andamento->id_os_andamento; ?>&amp;id_os=<?= $rs_andamento->id_os; ?>" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?');">
                                <img border="0" src="images/ico_lixeira.png" alt="Status" />
                            </a>
                            <?
						}
					}
					?>
                </div>
                <div class="parte10 alinhar_direita">
                	<? if ((($rs->id_usuario==$_SESSION["id_usuario"]) || ($_SESSION["tipo_usuario"]=='a')) && ($rs_andamento->id_situacao!="1")) { ?>
                    <a onmouseover="Tip('Excluir este andamento da RM.');" href="link.php?rmAndamentoExcluir&amp;id_os_andamento=<?= $rs_andamento->id_os_andamento; ?>&amp;id_os=<?= $rs_andamento->id_os; ?>" onclick="return confirm('Tem certeza que deseja excluir?');">
                        <img border="0" src="images/ico_lixeira.png" alt="Status" />
                    </a>
                    <? } ?>
                </div>
                
                <br /><br />
            </li>
        <? $i++; } ?>
        </ul>
        <br />
        
    </fieldset>
    
    <? if ( (pode("/", $_SESSION["permissao"])) || ($rs->id_usuario==$_SESSION["id_usuario"]) ) { ?>
    <fieldset>
        <legend>Alterar situação da OS</legend>
    
        <form action="<?= AJAX_FORM; ?>formOSAndamento&amp;acao=<?= $acao; ?>" method="post" name="formLivro" id="formLivro" onsubmit="return validaFormNormal('validacoes_situacao', true);">
        	
            <input name="id_os" class="escondido" type="hidden" id="id_os" value="<?= $rs->id_os; ?>" />
            <input class="escondido" type="hidden" id="validacoes_situacao" value="id_situacao@vazio" />
            
            <label for="id_situacao" class="tamanho80">Situação:</label>
            <select name="id_situacao" id="id_situacao" title="Situação">
                <? if ($acao=="i") { ?>
                <option value="">---</option>
                <? } ?>
                
                <?
				if ($andamento_final==5) {
					$i=6;
					$limite=6;
				}
				else {
					$i=2;
					$limite=5;
				}
				
                $vetor= pega_situacao_os('l');
                while ($i<=$limite) {
                ?>
                <option value="<?=$i;?>" <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> <? if ($rs->id_acao_carreira==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
        	
            <label for="obs" class="tamanho80">OBS:</label>
            <textarea name="obs" id="obs" title="OBS"><?=$rs->obs;?></textarea>
            <br />
            
            <label class="tamanho80">&nbsp;</label>
            <button type="submit" id="enviar">Enviar &raquo;</button>
            <br />
            
        </form>
        
    </fieldset>
    <? } ?>
    
</div>
<div class="parte50">    
    
    <? if (pode("kj", $_SESSION["permissao"])) { ?>
    <fieldset>
    	<legend>Tempo de serviço</legend>
        
        <ul class="recuo1">
            <li><a href="javascript:void(0);" onclick="ajaxLink('tempo_servico', 'iniciaOSServico&amp;id_os=<?= $rs->id_os;?>&amp;data_inicio=<?= date("d/m/Y"); ?>&amp;hora_inicio=<?= date("H:i:s"); ?>');">iniciar serviço &raquo;</a></li>
        </ul>
        <br />
        
        <div id="tempo_servico">
            <?
            $id_os= $rs->id_os;
			require_once("_manutencao/__os_tempo_trabalho_listar.php");
			?>
        </div>
        <br /><br />
    </fieldset>
    <? } ?>
    
    <? if ( (pode("kj", $_SESSION["permissao"])) || (($rs->id_usuario==$_SESSION["id_usuario"]) || ($_SESSION["tipo_usuario"]=='a'))) { ?>
    <fieldset>
    	<legend>Acessos à esta OS</legend>
        
        <ul class="recuo1">
        <?
		$result_lida= mysql_query("select * from man_oss_lidas
								  	where id_os = '". $rs->id_os ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									order by id_os_lida desc
									");
		while ($rs_lida= mysql_fetch_object($result_lida)) {
		?>
        	<li><?= "<strong>". pega_nome_pelo_id_usuario($rs_lida->id_usuario) ."</strong> acessou esta OS em <strong>". desformata_data($rs_lida->data_leitura) ."</strong> às <strong>". $rs_lida->hora_leitura ."</strong>."; ?></li>
        <? } ?>
        </ul>
        
    </fieldset>
    <? } ?>
    
    <fieldset>
    	<legend>Estoque</legend>
        
        <? if (pode("kj", $_SESSION["permissao"])) { ?>
        <fieldset>
	    	<legend>Inserir novo item nesta OS</legend>

            <form action="<?= AJAX_FORM; ?>formOSEstoqueSaida" method="post" name="formOSEstoqueSaida" id="formOSEstoqueSaida" onsubmit="return ajaxForm('estoque_os', 'formOSEstoqueSaida', 'validacoes_os_estoque', true);">
            	
                <input class="escondido" type="hidden" id="validacoes_os_estoque" value="id_item@vazio|qtde@vazio" />
                <input class="escondido" type="hidden" id="id_os" name="id_os" value="<?=$id_os;?>" />
                
                <label for="item_busca">Pesquisa:</label>
                <input title="Item" name="item" id="item_busca" value="" class="tamanho25p espaco_dir" onkeyup="itemBuscaUnico();" />
                <br />
                
                <label for="id_item">Item:</label>
                <div id="item_atualiza_unico">
                <select name="id_item" id="id_item" title="Item" onchange="processaDecimalUnico();">
                    <option value="">-</option>
                </select>
                </div>
                <br />
                
                <label for="qtde">Quantidade:</label>
                <input class="tamanho25p" title="Quantidade" name="qtde" id="qtde" value="" />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit">Enviar &raquo;</button>
            </form>
            <br />
        </fieldset>
        <? } ?>
        
        <fieldset>
	    	<legend>Itens utilizados nesta OS</legend>
            
            <div id="estoque_os">
                <?
                require_once("_manutencao/__os_estoque_listar.php");
                ?>
            </div>
            <br />
        </fieldset>
        
        <br />
    </fieldset>
</div>

<br /><br /><br />

<? } ?>

<? } ?>
<?
require_once("conexao.php");
if (pode("kj", $_SESSION["permissao"])) {
	
	if ($id_rm=="") $id_rm= $_GET["id_rm"];
	
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from man_rms
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_rm = '". $id_rm ."'
								");
		$linhas= mysql_num_rows($result);
		
		if ($linhas==0) {
			die("RM não encontrada.");
		}
		
		$rs= mysql_fetch_object($result);
		
		$titulo2= " nº ". $rs->num_rm;
		
		$result_lida= mysql_query("select * from man_rms_lidas
								  	where id_rm = '". $rs->id_rm ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_usuario = '". $_SESSION["id_usuario"] ."'
									");
		if (mysql_num_rows($result_lida)==0)
			$result_ler= mysql_query("insert into man_rms_lidas
									 	(id_empresa, id_rm, data_leitura, hora_leitura, id_usuario)
										values
										('". $_SESSION["id_empresa"] ."', '". $rs->id_rm ."',
										 '". date("Ymd") ."', '". date("H:i:s") ."', '". $_SESSION["id_usuario"] ."')
										");
	}
?>

<div id="tela_tempo_servico" class="telinha1 screen">
	
</div>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Requisição de manutenção <?= $titulo2; ?></h2>

<form action="<?= AJAX_FORM; ?>formRM&amp;acao=<?= $acao; ?>" method="post" name="formRM" id="formRM" onsubmit="return validaFormNormal('validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="<? if ($acao=='i') { ?>data_rm@data|hora_rm@vazio|<? } ?>finalidade_rm@vazio|tipo_rm@vazio|id_departamento@vazio|id_tipo_servico@vazio|prioridade_dias@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_rm" class="escondido" type="hidden" id="id_rm" value="<?= $rs->id_rm; ?>" />
    <? } ?>
    
    <fieldset id="primeiro">
        <legend>Reclamante</legend>
        
        <div class="parte50">
            
            <?
			if ($acao=='i') {
				$data_rm= date("d/m/Y");
			?>
			<label for="data_rm">Data:</label>
			<input name="data_rm" id="data_rm" class="tamanho25p" onkeyup="formataData(this);" maxlength="10" value="<?= $data_rm; ?>" title="Data" />
			<br />
            
			<?
			$hora_rm= date("H:i:s");
			?>
			<label for="hora_rm">Hora:</label>
			<input name="hora_rm" id="hora_rm" class="tamanho25p" onkeyup="formataHora(this);" maxlength="5" value="<?= $hora_rm; ?>" title="Hora" />
            <br /><br />
            <? } ?>
            
            <label for="finalidade_rm">Finalidade:</label>
            <select name="finalidade_rm" id="finalidade_rm" title="Finalidade">
            	<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                
                <option class="cor_sim" value="p" <? if ($rs->finalidade_rm=='p') echo "selected=\"selected\""; ?>>Preventiva</option>
                <option value="c" <? if ($rs->finalidade_rm=='c') echo "selected=\"selected\""; ?>>Corretiva</option>
            </select>
            <br />
            
            <? /*
            <label for="tipo_rm">Tipo:</label>
            <select name="tipo_rm" id="tipo_rm" title="Tipo" onchange="alteraTipoRM(this.value);">
            	<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                
                <option class="cor_sim" value="e" <? if (($rs->tipo_rm=='e') || (($acao=='i') && ($_GET["id_equipamento"]!=""))) echo "selected=\"selected\""; ?>>Equipamento</option>
                <option value="p" <? if ($rs->tipo_rm=='p') echo "selected=\"selected\""; ?>>Predial</option>
            </select>
            <br />
            
            <div id="div_id_equipamento" <? if ((($acao=='i') && ($_GET["id_equipamento"]=="")) || ($rs->tipo_rm=='p')) { ?>class="escondido"<? } ?>>
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
            */ ?>
            
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
            
            <div id="div_item" <? if (($acao=='i') || ($rs->tipo_rm=='e')) { ?>class="escondido"<? } ?>>
                <label for="item">Item:</label>
                <input name="item" id="item" value="<?= $rs->item; ?>" title="Item" />
                <br />
            </div>
            
            <br />
            
            <? /*
            <label for="id_tecnico_preferencial">Técnico preferencial:</label>
            <select id="id_tecnico_preferencial" name="id_tecnico_preferencial" title="Técnico preferencial">
				
                <option value="">-</option>
                
				<?
                $j=0;
                $result_fun= mysql_query("select *
                                            from  pessoas, rh_funcionarios, rh_carreiras
                                            where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                            and   pessoas.tipo = 'f'
                                            and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                            and   rh_carreiras.atual = '1'
                                            and   rh_carreiras.id_departamento = '8'
											and   rh_funcionarios.afastado <> '1'
                                            and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
                                            order by pessoas.nome_rz asc
                                            ") or die(mysql_error());
                while ($rs_fun= mysql_fetch_object($result_fun)) {
                ?>
                <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_tecnico_preferencial) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $j++; } ?>
            </select>
            <br />
            */ ?>
        </div>
        <div class="parte50">
            <? /*<label for="area">Área:</label>
			<input name="area" id="area" value="<?= $rs->area; ?>" title="Área" />
            <br />
            */ ?>
            
            
            
            <label for="problema" style="text-align:left;">Problema:</label>
            <? if (($acao=='i') || ($rs->id_usuario==$_SESSION["id_usuario"])) { ?>
            <textarea name="problema" id="problema" title="Problema"><?=$rs->problema;?></textarea>
            <? } else { ?>
            <br />
            <?=$rs->problema;?>
            <? } ?>
            <br />
        </div>
        <br /><br />
        
        <? if (($acao=='i') || ($rs->id_usuario==$_SESSION["id_usuario"]) || ($_SESSION[id_usuario]==19) ) { ?>
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        <? } else { ?>
        <script language="javascript">
            bloqueaCampos("primeiro");
        </script>
        <? } ?>
        <br />
        
    </fieldset>
    
</form>

<? if (($acao=='e') && (pode("jk", $_SESSION["permissao"])) ) { ?>

<a name="situacao"></a>

<div class="parte50">
	<fieldset>
    	<legend>Situação da RM</legend>
        
        <ul>
        <?
		$result_andamento= mysql_query("select * from man_rms_andamento
									   	where id_rm= '". $rs->id_rm ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										");
		$i=1;
		while ($rs_andamento= mysql_fetch_object($result_andamento)) {
			$andamento_final= $rs_andamento->id_situacao;
			
			if ($i==1) {
				$data_rm_aqui= $rs_andamento->data_rm_andamento;
			}
		?>
            <li>
                <div class="parte10">
                	<div class="index_gigante"><?=$i;?>)</div>
                </div>
                <div class="parte80">
                    <label class="tamanho80">Data/hora:</label> <?= desformata_data($rs_andamento->data_rm_andamento) ." ". $rs_andamento->hora_rm_andamento; ?><br />
                    <label class="tamanho80">Por:</label> <?= pega_nome_pelo_id_usuario($rs_andamento->id_usuario); ?><br />
                    <label class="tamanho80">Situação:</label> <?= pega_situacao_rm($rs_andamento->id_situacao); ?><br />
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
                    <select name="nota" id="nota_<?=$rs_andamento->id_situacao;?>" onchange="avaliaServicoRM('<?= $rs_andamento->id_rm; ?>', '<?= $rs_andamento->id_rm_andamento; ?>', this, this.value);">
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
                            <a onmouseover="Tip('Excluir esta avaliação.');" href="link.php?rmAndamentoNotaExcluir&amp;id_rm_andamento=<?= $rs_andamento->id_rm_andamento; ?>&amp;id_rm=<?= $rs_andamento->id_rm; ?>" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?');">
                                <img border="0" src="images/ico_lixeira.png" alt="Status" />
                            </a>
                            <?
						}
					}
					?>
                </div>
                <div class="parte10 alinhar_direita">
                	<? if ((($rs->id_usuario==$_SESSION["id_usuario"]) || ($_SESSION["tipo_usuario"]=='a')) && ($rs_andamento->id_situacao!="1")) { ?>
                    <a onmouseover="Tip('Excluir este andamento da RM.');" href="link.php?rmAndamentoExcluir&amp;id_rm_andamento=<?= $rs_andamento->id_rm_andamento; ?>&amp;id_rm=<?= $rs_andamento->id_rm; ?>" onclick="return confirm('Tem certeza que deseja excluir?');">
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
    
    <? if ( (pode("jk", $_SESSION["permissao"])) || ($rs->id_usuario==$_SESSION["id_usuario"]) ) { ?>
    <fieldset>
        <legend>Alterar situação da RM</legend>
    
        <form action="<?= AJAX_FORM; ?>formRMAndamento&amp;acao=<?= $acao; ?>" method="post" name="formLivro" id="formLivro" onsubmit="return validaFormNormal('validacoes_situacao', true);">
        	
            <input name="id_rm" class="escondido" type="hidden" id="id_rm" value="<?= $rs->id_rm; ?>" />
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
				
                $vetor= pega_situacao_rm('l');
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
	
	<? if (pode("12", $_SESSION["permissao"])) { ?>
        <fieldset>
            <legend>Anular:</legend>
            
            <form action="<?= AJAX_FORM; ?>formRMAnular&amp;acao=<?= $acao; ?>" method="post" name="formRMAnular" id="formRMAnular" onsubmit="return validaFormNormal('validacoes_alerta2', true);">
        
                <input name="id_rm" class="escondido" type="hidden" id="id_rm" value="<?= $rs->id_rm; ?>" />
                <input name="data_rm" class="escondido" type="hidden" id="data_rm" value="<?= $data_rm_aqui; ?>" />
                <input class="escondido" type="hidden" id="validacoes_alerta2" value="" />
           		
                <p>Anular uma reclamação fará com que seja apenas criada uma mensagem no livro destinada a manutenção:</p>
                
                <center>
    	            <button type="submit" id="enviar">Anular &raquo;</button>
	            </center>
                
            </form>
                
        </fieldset>
    <? } ?>
       
	<? if (pode("j", $_SESSION["permissao"])) { ?>
    <fieldset>
    	<legend>Tempo de serviço</legend>
        
        <ul class="recuo1">
            <li><a href="javascript:void(0);" onclick="ajaxLink('tempo_servico', 'iniciaRMServico&amp;id_rm=<?= $rs->id_rm;?>&amp;data_inicio=<?= date("d/m/Y"); ?>&amp;hora_inicio=<?= date("H:i:s"); ?>');">iniciar serviço &raquo;</a></li>
        </ul>
        <br />
        
        <div id="tempo_servico">
            <?
            $id_rm= $rs->id_rm;
			require_once("_manutencao/__rm_tempo_trabalho_listar.php");
			?>
        </div>
        <br /><br />
    </fieldset>
    <? } ?>
    
    <fieldset>
    	<legend>Estoque</legend>
        
        <? if (pode("j", $_SESSION["permissao"])) { ?>
        <fieldset>
	    	<legend>Inserir novo item nesta RM</legend>

            <form action="<?= AJAX_FORM; ?>formRMEstoqueSaida" method="post" name="formRMEstoqueSaida" id="formRMEstoqueSaida" onsubmit="return ajaxForm('estoque_rm', 'formRMEstoqueSaida', 'validacoes_rm_estoque', true);">
            	
                <input class="escondido" type="hidden" id="validacoes_rm_estoque" value="id_item@vazio|qtde@vazio" />
                <input class="escondido" type="hidden" id="id_rm" name="id_rm" value="<?=$id_rm;?>" />
                
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
	    	<legend>Itens utilizados nesta RM</legend>
            
            <div id="estoque_rm">
                <?
                require_once("_manutencao/__rm_estoque_listar.php");
                ?>
            </div>
            <br />
        </fieldset>
        
        <br />
    </fieldset>
    
    <? if ( (pode("j", $_SESSION["permissao"])) || (($rs->id_usuario==$_SESSION["id_usuario"]) || ($_SESSION["tipo_usuario"]=='a'))) { ?>
    <fieldset>
    	<legend>Acessos à esta RM</legend>
        
        <ul class="recuo1">
        <?
		$result_lida= mysql_query("select * from man_rms_lidas
								  	where id_rm = '". $rs->id_rm ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									order by id_rm_lida desc
									");
		while ($rs_lida= mysql_fetch_object($result_lida)) {
		?>
        	<li><?= "<strong>". pega_nome_pelo_id_usuario($rs_lida->id_usuario) ."</strong> leu esta RM em <strong>". desformata_data($rs_lida->data_leitura) ."</strong> às <strong>". $rs_lida->hora_leitura ."</strong>."; ?></li>
        <? } ?>
        </ul>
        
    </fieldset>
    <? } ?>
</div>

<br /><br /><br />

<? } ?>

<? } ?>
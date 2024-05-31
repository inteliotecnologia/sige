<?
require_once("conexao.php");
if (pode("12o", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  com_livro
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_livro = '". $_GET["id_livro"] ."'
								and   reclamacao_original = '1'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
	
	if ($_SESSION["reclamacao_origem"]=="") {
		//se está vindo do livro
		if ($_GET["origem"]=="l") {
			$_SESSION["livro_reclamacao_ancora"]= $_GET["id_livro"];
			$_SESSION["livro_reclamacao_data"]= $_GET["data"];
			$_SESSION["reclamacao_origem"]= "l";
		}
		//vindo da listagem de reclamações
		else {
			$_SESSION["reclamacao_ancora"]= $_GET["id_livro"];
			$_SESSION["reclamacao_num_pagina"]= $_GET["num_pagina"];
			
			if (($rs->id_motivo==37) || ($rs->id_motivo==34)) $_SESSION["reclamacao_origem"]= "r";
			else $_SESSION["reclamacao_origem"]= "n";
		}
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<div id="retorno" class="escondido">
</div>
    
<h2>Registro de reclamações/não conformidades</h2>

<? //echo "x:". $_SESSION["reclamacao_origem"]; ?>

<? if ($_SESSION["reclamacao_origem"]=="l") { ?>
<a href="./?pagina=com/livro&voltando=1">&laquo; voltar ao livro</a>
<br /><br />
<? } elseif ($_SESSION["reclamacao_origem"]=="r") { ?>
<a href="./?pagina=qualidade/reclamacao_listar&voltando=1">&laquo; voltar à lista de reclamações/não conformidades</a>
<br /><br />
<? } ?>

<fieldset>
    <legend>Detalhes</legend>
    
    <div class="parte50">
        <label>Data/hora:</label>
        <?= desformata_data($rs->data_livro) ." às ". $rs->hora_livro; ?>
        <br />
        
        <label>Tipo:</label>
        <strong><?= pega_motivo($rs->id_motivo); ?></strong>
        <br />
        
        <? if (($rs->id_motivo==34) && ($rs->reclamacao_id_cliente!=0)) { ?>
        <label>Cliente:</label>
        <?= pega_pessoa($rs->reclamacao_id_cliente); ?>
        <br />
        <? } ?>
        <br />
        
        <label>Reclamante:</label>
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
        <br />
        
        <label>Depto. referente:</label>
        <?
        if ($rs->id_departamento_principal!="") echo pega_departamento($rs->id_departamento_principal);
        else echo "-";
        ?>
        <br />
        
        <? if ($rs->prioridade_dias!="0") { ?>
        <label>Prazo:</label>
        <?= $rs->prioridade_dias; ?> dia(s)
        <br />
        <? } ?>
        <br /><br />
        
        <label class="alinhar_esquerda">Descrição:</label><br />
        <?= $rs->mensagem; ?>
        
    </div>
    <div class="parte50">
        <? if (pode("12", $_SESSION["permissao"])) { ?>
        <fieldset>
            <legend>Anular:</legend>
            
            <form action="<?= AJAX_FORM; ?>formReclamacaoAnular&amp;acao=<?= $acao; ?>" method="post" name="formReclamacaoAlertar" id="formReclamacaoAlertar" onsubmit="return validaFormNormal('validacoes_alerta', true);">
        
                <input name="id_livro" class="escondido" type="hidden" id="id_livro" value="<?= $rs->id_livro; ?>" />
                <input name="data_livro" class="escondido" type="hidden" id="id_livro" value="<?= desformata_data($rs->data_livro); ?>" />
                <input class="escondido" type="hidden" id="validacoes_alerta" value="id_departamento_principal@vazio" />
           		
                <p>Anular uma NC/reclamação fará com que ela se transforme em uma mensagem normal no livro:</p>
                
                <center>
    	            <button type="submit" id="enviar">Anular &raquo;</button>
	            </center>
                
            </form>
                
        </fieldset>
        
        <fieldset>
            <legend>Editar departamento responsável:</legend>
            
            <form action="<?= AJAX_FORM; ?>formReclamacaoDepartamentoAlterar&amp;acao=<?= $acao; ?>" method="post" name="formReclamacaoAlertar" id="formReclamacaoAlertar" onsubmit="return validaFormNormal('validacoes_alerta', true);">
        
                <input name="id_livro" class="escondido" type="hidden" id="id_livro" value="<?= $rs->id_livro; ?>" />
                <input class="escondido" type="hidden" id="validacoes_alerta" value="id_departamento_principal@vazio" />
           
				<?
                $result_deptos_principal= mysql_query("select * from rh_departamentos
                                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                        and   presente_livro = '1'
                                                        order by departamento asc
                                                        ");
                $linhas_deptos_principal= mysql_num_rows($result_deptos_principal);
                ?>
                <label class="tamanho80" for="id_departamento_principal">Depto. reclamado:</label>
                <select name="id_departamento_principal" id="id_departamento_principal" title="Depto principal">
                    <option value="">-</option>
                    <?
                    $i=0;
                    while ($rs_deptos_principal= mysql_fetch_object($result_deptos_principal)) {
                        if ($rs_deptos_principal->departamento_livro!="") $departamento_nome= $rs_deptos_principal->departamento_livro;
                        else $departamento_nome= $rs_deptos_principal->departamento;
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> <? if ($rs_deptos_principal->id_departamento==$rs->id_departamento_principal) echo "selected=\"selected\""; ?> value="<?= $rs_deptos_principal->id_departamento; ?>"><?= $rs_deptos_principal->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br /><br />
            	
                <center>
    	            <button type="submit" id="enviar">Atualizar &raquo;</button>
	            </center>
                
            </form>
                
        </fieldset>
        <? } ?>
        
		<? if (pode("1", $_SESSION["permissao"])) { ?>
        <fieldset>
            <legend>Solicitar atenção:</legend>
            
            <form action="<?= AJAX_FORM; ?>formReclamacaoAlertar&amp;acao=<?= $acao; ?>" method="post" name="formReclamacaoAlertar" id="formReclamacaoAlertar" onsubmit="return validaFormNormal('validacoes_alerta', true);">
        
                <input name="id_livro" class="escondido" type="hidden" id="id_livro" value="<?= $rs->id_livro; ?>" />
                <input class="escondido" type="hidden" id="validacoes_alerta" value="id_departamento_principal@vazio" />
                
                <?
                $result_deptos_principal= mysql_query("select * from rh_departamentos
                                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
														and   presente_livro = '1'
                                                        order by departamento asc
                                                        ");
                $linhas_deptos_principal= mysql_num_rows($result_deptos_principal);
                ?>
                <label class="tamanho80" for="id_departamento_principal">Depto. responsável:</label>
                <select name="id_departamento_principal" id="id_departamento_principal" title="Depto principal">
                    <option value="">-</option>
                    <?
                    $i=0;
                    while ($rs_deptos_principal= mysql_fetch_object($result_deptos_principal)) {
						if ($rs_deptos_principal->departamento_livro!="") $departamento_nome= $rs_deptos_principal->departamento_livro;
						else $departamento_nome= $rs_deptos_principal->departamento;
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_deptos_principal->id_departamento; ?>"><?= $rs_deptos_principal->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br /><br />
                
                <label class="tamanho80">Cópia para:</label>
                <br /><br />
                <?
                $result_dep= mysql_query("select * from rh_departamentos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."' 
                                            and   status_departamento = '1' 
                                            order by departamento asc
                                            ");
                $i=1;
                while ($rs_dep= mysql_fetch_object($result_dep)) {
                    $result_permissao= mysql_query("select * from com_livro_permissoes
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    and   id_livro = '". $_GET["id_livro"] ."'
                                                    and   id_departamento = '". $rs_dep->id_departamento ."'
                                                    ");
                    $linhas_permissao= mysql_num_rows($result_permissao);
                ?>
                <input <? /* if (($id_departamento_usuario2==$rs_dep->id_departamento) || ($linhas_permissao>0)) echo "checked=\"checked\""; */ ?> class="tamanho15 espaco_dir" type="checkbox" name="para[]" id="resposta_para_<?= $rs_dep->id_departamento;?>" value="<?= $rs_dep->id_departamento;?>" />
                <label for="resposta_para_<?= $rs_dep->id_departamento;?>" class="alinhar_esquerda menor2 nao_negrito"><?= $rs_dep->departamento;?></label>
                
                <?
                    if (($i%3)==0) echo "<br />";
                    
                    $i++;
                }
                ?>
                <br /><br />
                
                <? /*
                <label for="obs" class="tamanho80">OBS:</label>
                <textarea name="obs" id="obs" title="OBS"><?=$rs->obs;?></textarea>
                <br />
                */ ?>
                
                <center>
    	            <button type="submit" id="enviar">Enviar &raquo;</button>
	            </center>
                
            </form>
            
        </fieldset>
        <? } ?>
    </div>
</fieldset>

<fieldset>
    <legend>Ações tomadas</legend>
    
    <ul>
    <?
    $result_andamento= mysql_query("select * from qual_reclamacoes_andamento
                                    where id_livro= '". $rs->id_livro ."'
                                    and   id_empresa = '". $_SESSION["id_empresa"] ."'
                                    ");
    
    $linhas_andamento= mysql_num_rows($result_andamento);
    
    if ($linhas_andamento==0) echo "Nenhuma ação tomada até o momento.<br />";
    else {
        $i=1;
        while ($rs_andamento= mysql_fetch_object($result_andamento)) {
            $andamento_final= $rs_andamento->id_situacao;
    ?>
        <li>
            <div class="parte10">
                <div class="index_gigante"><?=$i;?>)</div>
            </div>
            <div class="parte80">
                <?
                //fake
                
                //$data_acao= soma_data($rs->data_livro, 1, 0, 0);
                //desformata_data($rs_andamento->data_andamento)
                
                ?>
                
                <label class="tamanho80">Data/hora:</label> <?= desformata_data($rs_andamento->data_andamento) ." ". $rs_andamento->hora_andamento; ?><br />
                <label class="tamanho80">Por:</label> <?= pega_nome_pelo_id_usuario($rs_andamento->id_usuario); ?><br />
                <label class="tamanho80">Situação:</label> <?= pega_situacao_reclamacao($rs_andamento->id_situacao); ?><br />
                <? if ($rs_andamento->obs!="") { ?>
                <label class="tamanho80">OBS:</label> <?= strip_tags($rs_andamento->obs); ?><br />
                <? } ?>
                <br />
                
                <?
                //se for finalizada
                if (($rs_andamento->id_situacao=="6") || ($rs_andamento->id_situacao=="8")) {
                    if (($rs_andamento->nota=="") && (($rs->de==$_SESSION["id_usuario"]) || ($_SESSION["tipo_usuario"]=='a')) ) {
                ?>
                <label class="menor vermelho tamanho80"><strong>AVALIE AGORA:</strong></label>
                <select class="tamanho35p" name="nota" id="nota_<?=$rs_andamento->id_situacao;?>" onchange="avaliaReclamacaoAcao('<?= $rs_andamento->id_livro; ?>', '<?= $rs_andamento->id_reclamacao_andamento; ?>', this, this.value);">
                    <option value="">selecione uma nota para a resolução da reclamação</option>
                    <?
                    for ($i=1; $i<11; $i++) {
                        $descricao_nota= pega_descricao_nota($i);
                        $descricao_nota= explode("@", $descricao_nota);
                    ?>
                    <option class="<?= $descricao_nota[1]; ?>" value="<?=$i;?>"><?= $i ." - ". $descricao_nota[0]; ?></option>
                    <? } ?>
                </select>
                <br />
                <?
                    }
                    elseif ($rs_andamento->nota!="") {
                        $descricao_nota= pega_descricao_nota($rs_andamento->nota);
                        $descricao_nota= explode("@", $descricao_nota);
                        
                        echo "<label class=\"tamanho80\">Avaliação:</label> Nota ". $rs_andamento->nota ." (<span class=\"". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span>)";
                        ?>
                        <a onmouseover="Tip('Excluir esta avaliação.');" href="link.php?reclamacaoAcaoNotaExcluir&amp;id_reclamacao_andamento=<?= $rs_andamento->id_reclamacao_andamento; ?>&amp;id_livro=<?= $rs_andamento->id_livro; ?>" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?');">
                            <img border="0" src="images/ico_lixeira.png" alt="Status" />
                        </a>
                        <?
                    }
                }
                ?>
            </div>
            <div class="parte10 alinhar_direita">
                <? if ($_SESSION["tipo_usuario"]=='a') { ?>
                <a onmouseover="Tip('Excluir esta ação.');" href="link.php?reclamacaoAndamentoExcluir&amp;id_reclamacao_andamento=<?= $rs_andamento->id_reclamacao_andamento; ?>&amp;id_livro=<?= $rs_andamento->id_livro; ?>" onclick="return confirm('Tem certeza que deseja excluir?');">
                    <img border="0" src="images/ico_lixeira.png" alt="Status" />
                </a>
                <? } ?>
            </div>
            
            <br /><br />
        </li>
    <? $i++; } ?>
    </ul>
    <br />
    <? }//fim linhas ?>
    <br />
</fieldset>

<fieldset>
    <legend>Nova ação</legend>

    <form action="<?= AJAX_FORM; ?>formReclamacaoAndamento&amp;acao=<?= $acao; ?>" method="post" name="formReclamacaoAndamento" id="formReclamacaoAndamento" onsubmit="return validaFormNormal('validacoes', true, true);">
        
        <input name="id_livro" class="escondido" type="hidden" id="id_livro" value="<?= $rs->id_livro; ?>" />
        
        <label for="id_situacao" class="tamanho80">Situação:</label>
        <select name="id_situacao" id="id_situacao" class="tamanho25p" title="Situação">
            <? if ($acao=="i") { ?>
            <option value="">---</option>
            <? } ?>
            
            <?
            if (($andamento_final==6) || ($andamento_final==8)) {
                $i=7;
                $limite=7;
				$linha_add= "";
            }
            else {
                $i=1;
                $limite=6;
				$linha_add= "<option value='8'>Não solucionada</option>";
            }
            
            $vetor= pega_situacao_reclamacao('l');
            while ($i<=$limite) {
            ?>
            <option value="<?=$i;?>" <? if (($i%2)==0) echo "class=\"cor_sim\""; ?>><?= $vetor[$i]; ?></option>
            <? $i++; } ?>
            <?= $linha_add; ?>
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

<script language="javascript" type="text/javascript">
    //daFoco("data_chegada");
</script>
<? } ?>
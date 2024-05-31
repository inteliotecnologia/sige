<?
require_once("conexao.php");
if (pode_algum("pl", $_SESSION["permissao"])) {
	
	$result_pre= mysql_query("select * from op_coletas
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								order by num_coleta desc limit 1
								") or die(mysql_error());

	$rs_pre= mysql_fetch_object($result_pre);
	
	$proximo= $rs_pre->num_coleta;
	
	if ($proximo=="") $proximo2= "Nenhum.";
	else $proximo2= $proximo;
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Relatório de coleta</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Criar novos relatórios</legend>
            
            <form action="<?= AJAX_FORM; ?>formRelatorioColeta" target="_blank" method="post" name="formRelatorioColeta" id="formRelatorioColeta" onsubmit="return ajaxForm('conteudo', 'formRelatorioColeta', 'validacoes', true);">
    
                <input class="escondido" type="hidden" id="validacoes" value="qtde@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label>Último:</label>
                <?= $proximo2; ?>
                <br /><br />
                
                <label for="qtde">* Quantidade:</label>
                <input name="qtde" id="qtde" class="tamanho25p espaco_dir" title="Quantidade" />
                <br /><br /><br />
                
                <? /*
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" title="tipo">
                    <option value="1">Roupa limpa</option>
                    <option value="2" class="cor_sim">Roupa suja</option>
                </select>
                <br />
                */ ?>
                
                <center>
                	<button type="submit" id="enviar">Enviar &raquo;</button>
            	</center>
        	</form>
        </fieldset>
    </div>
    <div class="parte50">
        <fieldset>
            <legend>Gerar relação</legend>
            
            <? if (($proximo!="") && ($proximo>0)) { ?>
            <form action="index2.php?pagina=op/coleta_relatorio" target="_blank" method="post" name="formRelatorioColeta" id="formRelatorioColeta" onsubmit="return validaFormNormal('validacoes2');">
    
                <input class="escondido" type="hidden" id="validacoes2" value="qtde_gerar@vazio|iniciar@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label>Último:</label>
                <?= $proximo2; ?>
                <br /><br />
                
                <label for="qtde_gerar">* Quantidade:</label>
                <input name="qtde_gerar" id="qtde_gerar" class="tamanho25p espaco_dir" title="Quantidade a gerar" />
                <br />
                
                <label for="iniciar">* Iniciar em:</label>
                <input name="iniciar" id="iniciar" class="tamanho25p espaco_dir" title="Iniciar em" />
                <br /><br /><br />
                
                <? /*
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" title="tipo">
                    <option value="1">Roupa limpa</option>
                    <option value="2" class="cor_sim">Roupa suja</option>
                </select>
                <br />
                */ ?>
                
                <center>
                	<button type="submit" id="enviar">Enviar &raquo;</button>
            	</center>
        	</form>
            <? } else { ?>
            <p>Não foi cadastrado nenhuma numeração, por isso não é possível gerar nenhum relatório.</p>
            <? } ?>
        </fieldset>
    </div>
  
</div>

<? } ?>
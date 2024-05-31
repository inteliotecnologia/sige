<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Relatório de funcionários por idade</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="<?= AJAX_FORM; ?>formIdadeBuscar" method="post" name="formIdadeBuscar" id="formIdadeBuscar" onsubmit="return ajaxForm('conteudo_interno', 'formIdadeBuscar', 'validacoes');">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="p1@numeros|p2@numeros" />
                    
                    <label for="p1">* Entre:</label>
                    <input name="p1" id="p1" class="tamanho25p" />
                    <br />
                    
                    <label for="p2">* E:</label>
                    <input name="p2" id="p2" class="tamanho25p" />
                    <br />
                    
                    <label>&nbsp;</label>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                    <br />
                
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>
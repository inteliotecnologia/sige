<?
require_once("conexao.php");
if (pode_algum("rh", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Não faltantes</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="index2.php?pagina=rh/nao_faltantes_relatorio" target="_blank" method="post">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                    
                    <label for="periodo">Período:</label>
                    <select name="periodo" id="periodo" title="Período">	  		
                        <?
                        $i=0;
                        $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%Y')) as ano
													from rh_ponto order by data_batida desc ");
                        while ($rs_per= mysql_fetch_object($result_per)) {
						?>
						<option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->ano; ?>"><?= $rs_per->ano; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br /><br /><br />
                    
                    <center>
                        <button type="submit" id="enviar">Enviar &raquo;</button>
                    </center>
                    
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>
<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Relat&oacute;rio de f&eacute;rias</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="index2.php?pagina=rh/ferias_relatorio" target="_blank" method="post" name="formFeriasBuscar" id="formFeriasBuscar" onsubmit="return validaFormNormal('validacoes');">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="" />
                    
                    <label for="ano">* Ano:</label>
                    <select name="ano" id="ano" title="Ano">	  		
						<?
						$result_ano= mysql_query("select distinct(DATE_FORMAT(data, '%Y')) as ano
													from rh_carreiras
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													order by data asc
													limit 1
													");
                        $rs_ano= mysql_fetch_object($result_ano);
						
						$ano_atual=date("Y")+2;
						for ($i=$ano_atual; $i>$rs_ano->ano; $i--) {
						?>
                        <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> <? if ($i==date("Y")) echo "selected=\"selected\""; ?> value="<?= $i; ?>"><?= $i; ?></option>
                        <? } ?>
                    </select>
                    <br />
                    
                    <label>&nbsp;</label>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                    <br />
                
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>
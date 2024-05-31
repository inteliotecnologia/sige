<?
require_once("conexao.php");
if (pode_algum("ey", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Cronograma</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Cronograma</legend>
            
            <form action="index2.php?pagina=transporte/cronograma_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
    
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="c" />
                
                <label for="id_contrato">Contrato:</label>
                <select name="id_contrato" id="id_contrato" title="Contrato">
                    <option value="">- TODOS - </option>
					<?
                    $result_contrato= mysql_query("select * from fi_contratos
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    order by id_contrato asc ");
                    $i=0;
                    while ($rs_contrato = mysql_fetch_object($result_contrato)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_contrato->id_contrato; ?>"<? if ($rs_contrato->id_contrato==$rs->id_contrato) echo "selected=\"selected\""; ?>><?= $rs_contrato->contrato; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                    
                <br /><br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <div class="parte50">
        <fieldset>
            <legend>Real x programado</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=transporte/cronograma_relatorio" target="_blank" method="post" name="formPercursoBuscar" id="formPercursoBuscar" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=transporte/percurso_listar" method="post" name="formPercursoBuscar" id="formPercursoBuscar" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
    
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="d" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%m/%Y')) as data_percurso2
                                                from  tr_percursos_passos
												where DATE_FORMAT(data_percurso, '%m/%Y') <> '00/0000'
												order by data_percurso desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_percurso= explode('/', $rs_per->data_percurso2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= traduz_mes($data_percurso[0]) .'/'. $data_percurso[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" title="Tipo">
                    <option value="1" <? if ($rs->tipo=="1") echo "selected=\"selected\""; ?>>Coleta</option>
                    <option value="2" <? if ($rs->tipo=="2") echo "selected=\"selected\""; ?> class="cor_sim">Entrega</option>
                </select>
                <br />
                
                <label for="id_contrato">Contrato:</label>
                <select name="id_contrato" id="id_contrato" title="Contrato">
                    <option value="">- TODOS - </option>
					<?
                    $result_contrato= mysql_query("select * from fi_contratos
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    order by id_contrato asc ");
                    $i=0;
                    while ($rs_contrato = mysql_fetch_object($result_contrato)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_contrato->id_contrato; ?>"<? if ($rs_contrato->id_contrato==$rs->id_contrato) echo "selected=\"selected\""; ?>><?= $rs_contrato->contrato; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                <br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <br />
    
</div>

<? } ?>
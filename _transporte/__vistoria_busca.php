<?
require_once("conexao.php");
if (pode_algum("ey", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Vistorias</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca</legend>
        
        <? if ($_GET["geral"]==1) { ?>
        <form action="index2.php?pagina=transporte/vistoria_relatorio" target="_blank" method="post" name="formVistoriaBuscar" id="formVistoriaBuscar" onsubmit="return validaFormNormal('validacoes');">
        <? } else { ?>
        <form action="./?pagina=transporte/vistoria_listar" method="post" name="formVistoriaBuscar" id="formVistoriaBuscar" onsubmit="return validaFormNormal('validacoes');">
		<? } ?>

            <div class="parte50">
                
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label for="id_veiculo">Veículo:</label>
                <select name="id_veiculo" id="id_veiculo" title="Veículo">
                    <option value="">- TODOS -</option>
                    
                    <?
                    $result_vei= mysql_query("select * from op_veiculos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by veiculo asc,
                                                placa asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_vei = mysql_fetch_object($result_vei)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_vei->id_veiculo; ?>" <? if ($rs_vei->id_veiculo==$rs->id_veiculo) echo "selected=\"selected\""; ?>><?= $rs_vei->veiculo ." ". $rs_vei->placa; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
				<label for="periodo">Período:</label>
                <select class="tamanho25p" name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%m/%Y')) as data_percurso2
                                                from tr_percursos_passos order by data_percurso desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
						$data_percurso= explode('/', $rs_per->data_percurso2);
					?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= traduz_mes($data_percurso[0]) .'/'. $data_percurso[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
				
            </div>
            <br /><br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<? } ?>
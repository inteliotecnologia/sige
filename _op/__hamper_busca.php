<?
require_once("conexao.php");
if (pode("pls", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>�rea suja - Hampers</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Mensal</legend>
        	
            <form action="index2.php?pagina=op/hamper_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            
                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="m" />
                
                <label for="periodo">Per�odo:</label>
                <select name="periodo" id="periodo" title="Per�odo">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_remessa, '%m/%Y')) as data_remessa2 from op_suja_remessas order by data_remessa desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_remessa= explode('/', $rs_per->data_remessa2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_remessa2; ?>"><?= traduz_mes($data_remessa[0]) .'/'. $data_remessa[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_cliente">Cliente:</label>
                <select name="id_cliente" id="id_cliente" title="Cliente">
                    <option value="">- TODOS -</option>
                    <?
                    $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                                where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'c'
												and   pessoas.id_cliente_tipo = '1'
                                                order by 
                                                pessoas.apelido_fantasia asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_cli = mysql_fetch_object($result_cli)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
</div>

<? } ?>
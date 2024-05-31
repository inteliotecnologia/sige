<?
require_once("conexao.php");
if (pode("o", $_SESSION["permissao"])) {
?>
<fieldset>
	<legend>Detalhes</legend>
    	
        <? if (($_GET["id_motivo"]==34) || ($_GET["historico"]==1)) { ?>
        <label for="reclamacao_id_cliente">Cliente:</label>
        <select name="reclamacao_id_cliente" id="reclamacao_id_cliente" title="Cliente">
            <option value="">- SELECIONE -</option>
            <?
            $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                        where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                        and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                        and   pessoas_tipos.tipo_pessoa = 'c'
                                        and   pessoas.status_pessoa = '1'
                                        order by 
                                        pessoas.nome_rz asc
                                        ") or die(mysql_error());
            $i=0;
            while ($rs_cli = mysql_fetch_object($result_cli)) {
            ?>
            <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->nome_rz; ?></option>
            <? $i++; } ?>
        </select>
        <br />
        <? } ?>
        
        <? if ($_GET["id_motivo"]!="") { ?>
        <label for="prioridade_dias">Prioridade:</label>
        <select name="prioridade_dias" id="prioridade_dias" title="Prioridade">
            <option value="1">24 horas</option>
            <option class="cor_sim" value="2">2 dias</option>
            <option value="3">3 dias</option>
            <option class="cor_sim" value="4">4 dias</option>
            <option value="5">5 dias</option>
            <option class="cor_sim" value="6">6 dias</option>
            <option value="7">7 dias</option>
            <option class="cor_sim" value="14">2 semanas</option>
        </select>
        <br />
        <? } ?>
        
        <? /* if (pode("1", $_SESSION["permissao"])) { ?>
        <label for="data_livro">* Data:</label>
        <input id="data_livro" name="data_livro" class="tamanho35p" value="<?= date("d/m/Y"); ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
        <br />
        
        <label for="hora_livro">* Hora:</label>
        <input id="hora_livro" name="hora_livro" class="tamanho35p" value="<?= date("H:i:s"); ?>" title="Data" onkeyup="formataHora(this);" maxlength="8" />
        <br />
        
        <? } */ ?>
        
    </fieldset>
<? } ?>
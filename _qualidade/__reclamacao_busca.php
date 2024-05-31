<?
require_once("conexao.php");
if (pode("12", $_SESSION["permissao"])) {
?>

<h2>Reclamações</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=qualidade/pesagem_limpa_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=qualidade/reclamacao_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
               
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="motivo" value="<?= $_GET["motivo"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="d" />
                
                <label for="data_livro">Data:</label>
                <input name="data_livro" id="data_livro" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="" title="Data" />
                <br />
                
                <label for="id_funcionario">Funcionário:</label>
                <select name="id_funcionario" id="id_funcionario" title="Funcionário">
                    <option value="">- TODOS -</option>
                    <?
                    $result_fun= mysql_query("select distinct(rh_funcionarios.id_funcionario)
                                                from  pessoas, rh_funcionarios, rh_carreiras, com_livro
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   rh_funcionarios.id_funcionario = com_livro.de
                                                and   com_livro.tipo_de = 'f'
                                                order by pessoas.apelido_fantasia asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($_POST["id_funcionario"]==$rs_fun->id_funcionario) echo "selected=\"selected\""; ?>><?= pega_funcionario($rs_fun->id_funcionario); ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_situacao_reclamacao">Situação:</label>
                <select name="id_situacao_reclamacao" id="id_situacao_reclamacao" title="Situação">
                    <option value="">- TODAS -</option>
                    <option value="1" class="cor_sim">Aberta</option>
                    <option value="2">Finalizada</option>
                </select>
                <br />
                
                <label for="id_motivo">Motivo:</label>
                <select name="id_motivo" id="id_motivo" title="Motivo">
                    <option value="">- TODOS -</option>
                    <?
                    $i=0;
                    $result_mot= mysql_query("select * from rh_motivos where tipo_motivo = 'l' order by motivo asc ");
                    while ($rs_mot= mysql_fetch_object($result_mot)) {
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_mot->id_motivo; ?>"><?= $rs_mot->motivo; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="parte" onmouseover="Tip('Mensagem que contenha a palavra...');">Palavra:</label>
                <input name="parte" id="parte" value="<?= $_POST["parte"]; ?>" />
                <br />
                
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <div class="parte50">
        <fieldset>
            <legend>Mensal</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=qualidade/pesagem_limpa_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=qualidade/reclamacao_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
               
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="m" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_livro, '%m/%Y')) as data_batida2 from com_livro order by data_livro desc ");
                    
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_batida= explode('/', $rs_per->data_batida2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>" <? if ($_POST["periodo"]==$rs_per->data_batida2) echo "selected=\"selected\""; ?>><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_situacao_reclamacao">Situação:</label>
                <select name="id_situacao_reclamacao" id="id_situacao_reclamacao" title="Situação">
                    <option value="">- TODAS -</option>
                    <option value="1" class="cor_sim">Aberta</option>
                    <option value="2">Finalizada</option>
                </select>
                <br />
                
                <label for="id_motivo">Motivo:</label>
                <select name="id_motivo" id="id_motivo" title="Motivo">
                    <option value="">- TODOS -</option>
                    <?
                    $i=0;
                    $result_mot= mysql_query("select * from rh_motivos where tipo_motivo = 'l' order by motivo asc ");
                    while ($rs_mot= mysql_fetch_object($result_mot)) {
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_mot->id_motivo; ?>"><?= $rs_mot->motivo; ?></option>
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

<script type="text/javascript">
	daFoco("data_chegada");
</script>

<? } ?>
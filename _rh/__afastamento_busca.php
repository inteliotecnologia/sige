<?
require_once("conexao.php");
if (pode("rhv4", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Relatório de afastamento por período</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="<?= AJAX_FORM; ?>formAfastamentoBuscar" method="post" name="formAfastamentoBuscar" id="formAfastamentoBuscar" onsubmit="return ajaxForm('conteudo', 'formAfastamentoBuscar', 'validacoes');">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="p1@numeros|p2@numeros" />
                    
                    <label>Empresa:</label>
                    <?= pega_empresa($_SESSION["id_empresa"]); ?>
                    <br />
                    
                    <label for="id_funcionario">* Funcionário:</label>
                    <select name="id_funcionario" id="id_funcionario" title="Funcionário">
                        <option value="">- SELECIONE -</option>
                        <?
                        $result_fun= mysql_query("select *
                                                    from  pessoas, rh_funcionarios, rh_carreiras
                                                    where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                    and   pessoas.tipo = 'f'
                                                    and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                    and   rh_carreiras.atual = '1'
                                                    and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    order by pessoas.nome_rz asc
                                                    ") or die(mysql_error());
                        $i=0;
                        while ($rs_fun= mysql_fetch_object($result_fun)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$_GET["id_funcionario"]) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br />
                    
                    <label for="tipo_afastamento">* Tipo:</label>
                    <select name="tipo_afastamento" id="tipo_afastamento" title="Tipo de afastamento">
                        <option value="">- SELECIONE -</option> 
                        <option value="a" class="cor_sim">Atestado</option>
                        <option value="p" class="cor_nao">Perícia</option>
                        <option value="o" class="cor_sim">Outros abonos</option>
                        <option value="f" class="cor_nao">Férias</option>
                    </select>
                    <br />
                    
                    <label for="data1">Datas:</label>
                    <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" title="Data 1" />
                    <div class="flutuar_esquerda espaco_dir">à</div>
                    <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" title="Data 1" />
                    <br />
                    
                    <label>&nbsp;</label>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                    <br />
                
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>
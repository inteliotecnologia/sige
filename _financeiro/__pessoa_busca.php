<?
require_once("conexao.php");
if (pode("iapr", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Busca de <?= pega_tipo_pessoa($_GET["tipo_pessoa"]); ?></h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca</legend>
            
            <div class="parte50">
                <form action="./?pagina=financeiro/pessoa_listar" method="post" name="formCCCustosBuscar" id="formCCCustosBuscar">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                    <input class="escondido" type="hidden" name="tipo_pessoa" id="tipo_pessoa" value="<?= $_GET["tipo_pessoa"]; ?>" />
                    <input class="escondido" type="hidden" name="geral" value="1" />
                    
                    <label for="tipo">Tipo:</label>
                    <select name="tipo" id="tipo">
                        <option value="">Todos</option>
                        <option value="j">Jurídica</option>
                        <option value="f">Física</option>
                    </select>
                    <br />
                    
                    <label for="cpf_cnpj">CPF/CNPJ:</label>
                    <input title="CPF/CNPJ" name="cpf_cnpj" id="cpf_cnpj" />
                    <br />
                    
                    <label for="nome_rz">Nome/razão social:</label>
                    <input title="Nome/razão social" name="nome_rz" id="nome_rz" />
                    <br />
                    
                    
                    <label for="id_empresa_atendente">Atendente:</label>
                    <select name="id_empresa_atendente" id="id_empresa_atendente" title="Empresa atendente">
                    	<option value="">- TODOS -</option>
						<?
                        if ($acao=='i') $id_empresa_aqui= $_SESSION["id_empresa"];
                        else $id_empresa_aqui= $rs->id_empresa_atendente;
                        
                        $result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
                                                    where pessoas.id_pessoa = empresas.id_pessoa
                                                    and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                    and   pessoas_tipos.tipo_pessoa = 'a'
                                                    order by 
                                                    pessoas.apelido_fantasia asc");
                        $i=0;
                        while ($rs_emp = mysql_fetch_object($result_emp)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_emp->id_empresa; ?>"<? if ($rs_emp->id_empresa==$id_empresa_aqui) echo "selected=\"selected\""; ?>><?= $rs_emp->apelido_fantasia; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br />
                    
                    <br /><br />
                    <center>
                        <button type="submit" id="enviar">Enviar &raquo;</button>
                    </center>
                    
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>
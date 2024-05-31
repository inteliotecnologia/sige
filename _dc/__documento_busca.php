<?
require_once("conexao.php");
if (pode("d5", $_SESSION["permissao"])) {
?>

<h2>Busca de documento</h2>

<div id="conteudo_interno">
	
    <div class="parte50">
    	<fieldset>
        	<legend>Documentos</legend>
        
			<? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=dc/documento_relatorio" target="_blank" method="post" name="formDocumentoBuscar" id="formDocumentoBuscar" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=dc/documento_listar" method="post" name="formDocumentoBuscar" id="formDocumentoBuscar">
            <? } ?>
                
                <input class="escondido" type="hidden" id="validacoes" value="data@data" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="1" />
                
                <label for="id_empresa">Empresa:</label>
                <select name="id_empresa" id="id_empresa" title="Empresa" onchange="alteraDepartamentos('3');">
                    <option value="">---</option>
                    
                    <?
                    $result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
                                                where pessoas.id_pessoa = empresas.id_pessoa
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'a'
                                                order by 
                                                pessoas.nome_rz asc");
                    $i=0;
                    while ($rs_emp = mysql_fetch_object($result_emp)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_emp->id_empresa; ?>"><?= $rs_emp->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_departamento">Departamento:</label>
                <div id="id_departamento_atualiza">
                    <select name="id_departamento" id="id_departamento" title="Departamento">
                        <option value="">---</option>
                    </select>
                </div>
                <br />
                
                <label for="id_pasta">Pasta:</label>
                <div id="id_pasta_atualiza">
                    <select name="id_pasta" id="id_pasta" title="Pasta">
                        <option value="">---</option>
                    </select>
                </div>
                <br />
				
                <label for="nome_pasta">Nome da pasta:</label>
                <input name="nome_pasta" id="nome_pasta" class="" value="" title="Nome da pasta" />
                <br />
                
                <? /*<label for="id_documento">Cód. documento:</label>
                <input name="id_documento" id="id_documento" class="tamanho25p" value="" title="Código do documento" />
                <br />*/ ?>
                
                <label for="documento">Documento:</label>
                <input name="documento" id="documento" class="" value="" title="Documento" />
                <br />
                
                <label for="periodo">Vencimento:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <option value="">- TODOS -</option>
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_emissao, '%m/%Y')) as data2
                                                from fi_notas order by data_emissao desc ");
                    
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data= explode('/', $rs_per->data2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data2; ?>"><?= traduz_mes($data[0]) .'/'. $data[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br />
                
                <label for="status_pasta">Situação:</label>
                <select name="status_pasta" id="status_pasta" title="Situação">
                    <option value="">- TODAS -</option>
                    <option value="1" class="cor_sim">Ativa</option>
                    <option value="0">Inativa</option>
                </select>
                <br />
                
                <? /*<label for="ordenacao">Ordenação:</label>
                <select name="ordenacao" id="ordenacao" title="Ordenação">	  		
                    <option value="">--- TODOS ---</option>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data2; ?>"><?= traduz_mes($data[0]) .'/'. $data[1]; ?></option>
                </select>
                <br />
                */ ?>
                <br /><br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <div class="parte50">
    	<fieldset>
        	<legend>Pastas</legend>
        
			<? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=dc/documento_relatorio" target="_blank" method="post" name="formDocumentoBuscar" id="formDocumentoBuscar" onsubmit="return validaFormNormal('validacoes_2');">
            <? } else { ?>
            <form action="./?pagina=dc/documento_pasta_listar" method="post" name="formDocumentoBuscar" id="formDocumentoBuscar" onsubmit="return validaFormNormal('validacoes_2');">
            <? } ?>
                
                <input class="escondido" type="hidden" id="validacoes_2" value="id_empresa2@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="2" />
				
                <label for="id_empresa_aqui">Empresa:</label>
                <select name="id_empresa_aqui" id="id_empresa_aqui" title="Empresa" onchange="alteraDepartamentos2(this.value, '2', '3');">
                    <option value="">---</option>
                    
                    <?
                    $result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
                                                where pessoas.id_pessoa = empresas.id_pessoa
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'a'
                                                order by 
                                                pessoas.nome_rz asc");
                    $i=0;
                    while ($rs_emp = mysql_fetch_object($result_emp)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_emp->id_empresa; ?>"><?= $rs_emp->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_departamento">Departamento:</label>
                <div id="id_departamento_atualiza_2">
                    <select name="id_departamento" id="id_departamento" title="Departamento">
                        <option value="">---</option>
                    </select>
                </div>
                <br />
                
                <label for="nome_pasta">Nome da pasta:</label>
                <input name="nome_pasta" id="nome_pasta" class="" value="" title="Nome da pasta" />
                <br />
                
                <label for="status_pasta">Situação:</label>
                <select name="status_pasta" id="status_pasta" title="Pasta">
                    <option value="">- TODAS -</option>
                    <option value="1" class="cor_sim">Ativas</option>
                    <option value="0">Inativas</option>
                </select>
                <br />
                
                <br /><br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    
</div>

<? } ?>
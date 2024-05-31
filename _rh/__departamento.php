<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  rh_departamentos
								where id_departamento = '". $_GET["id_departamento"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Departamento</h2>

<p>* Campos de preenchimento obrigatório.</p>

<form action="<?= AJAX_FORM; ?>formDepartamento&amp;acao=<?= $acao; ?>" method="post" name="formDepartamento" id="formDepartamento" onsubmit="return ajaxForm('conteudo', 'formDepartamento');">
    
    <input class="escondido" type="hidden" id="validacoes" value="departamento@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_departamento" class="escondido" type="hidden" id="id_departamento" value="<?= $rs->id_departamento; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="razao_social">Empresa:</label>
            <?= pega_empresa($_SESSION["id_empresa"]); ?>
            <br />		
            
            <label for="departamento">* Departamento:</label>
            <input title="Departamento" name="departamento" value="<?= $rs->departamento; ?>" id="departamento" />
            <br />
            
            <label for="alerta_aniversariantes">Alerta (funcionários):</label>
            <select class="espaco_dir" name="alerta_aniversariantes" id="alerta_aniversariantes" title="Alerta">	  		
                <option value="0" <? if ($rs->alerta_aniversariantes==0) echo "selected=\"selected\""; ?>>Não</option>
                <option value="1" <? if ($rs->alerta_aniversariantes==1) echo "selected=\"selected\""; ?> class="cor_sim">Sim</option>
            </select>
            <br />
            <label>&nbsp;</label>
            <span class="menor">Alertar usuários deste departamento nos aniversários dos funcionários).</span>
            <br />

			<label for="alerta_aniversariantes_clientes">Alerta (clientes):</label>
            <select class="espaco_dir" name="alerta_aniversariantes_clientes" id="alerta_aniversariantes_clientes" title="Alerta 2">	  		
                <option value="0" <? if ($rs->alerta_aniversariantes_clientes==0) echo "selected=\"selected\""; ?>>Não</option>
                <option value="1" <? if ($rs->alerta_aniversariantes_clientes==1) echo "selected=\"selected\""; ?> class="cor_sim">Sim</option>
            </select>
            <br />
            <label>&nbsp;</label>
            <span class="menor">Alertar usuários deste departamento acerca de aniversários dos clientes da empresa).</span>
            <br />

			<label for="bate_ponto">Bate ponto:</label>
            <select class="espaco_dir" name="bate_ponto" id="bate_ponto" title="Bate ponto">	  		
                <option value="0" <? if ($rs->bate_ponto==0) echo "selected=\"selected\""; ?>>Não</option>
                <option value="1" <? if ($rs->bate_ponto==1) echo "selected=\"selected\""; ?> class="cor_sim">Sim</option>
            </select>
            <br />
            
            <label for="presente_livro">Presente no livro:</label>
            <select class="espaco_dir" name="presente_livro" id="presente_livro" title="Alerta">	  		
                <option value="0" <? if ($rs->presente_livro==0) echo "selected=\"selected\""; ?>>Não</option>
                <option value="1" <? if ($rs->presente_livro==1) echo "selected=\"selected\""; ?> class="cor_sim">Sim</option>
            </select>
            <br />
            
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>
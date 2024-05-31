<?
require_once("conexao.php");
if (pode("o", $_SESSION["permissao"])) {
	if ($_GET["id_livro"]!="") $id_livro= $_GET["id_livro"];
	if ($_POST["id_livro"]!="") $id_livro= $_POST["id_livro"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from rh_livros
							 	where id_livro = '". $id_livro ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_livro= $rs->id_livro;
	}
?>
<form action="<?= AJAX_FORM; ?>formLivro&amp;acao=<?= $acao; ?>" method="post" name="formLivro" id="formLivro" onsubmit="return validaFormNormal('validacoes', true, 1);">

    <input class="escondido" type="hidden" id="validacoes" value="" />
    
    <? if ($acao=="e") { ?>
    <input name="id_livro" class="escondido" type="hidden" id="id_livro" value="<?= $rs->id_livro; ?>" />
    <? } ?>
    
    <div class="parte50" id="tudo1">
        <fieldset>
        	<legend>Para:</legend>
        
			<?
            $result_dep= mysql_query("select * from rh_departamentos
										where id_empresa = '". $_SESSION["id_empresa"] ."' 
										and   status_departamento = '1' 
										and   presente_livro = '1'
										order by departamento asc
										");
			$i=1;
			while ($rs_dep= mysql_fetch_object($result_dep)) {
				$result_permissao= mysql_query("select * from rh_carreiras_departamentos
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
												and   id_departamento = '". $rs_dep->id_departamento ."'
												");
				$linhas_permissao= mysql_num_rows($result_permissao);
				
				if ($rs_dep->departamento_livro!="") $departamento_nome= $rs_dep->departamento_livro;
				else $departamento_nome= $rs_dep->departamento;
            ?>
        	<input <? if ($id_departamento_usuario2==$rs_dep->id_departamento) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="para[]" id="para_<?= $rs_dep->id_departamento;?>" value="<?= $rs_dep->id_departamento;?>" />
            <label for="para_<?= $rs_dep->id_departamento;?>" class="alinhar_esquerda menor"><?= $departamento_nome;?></label>
            
            <?
            	if (($i%3)==0) echo "<br /><br />";
				
				$i++;
			}
			?>
            <br /><br />
            <a href="javascript:void(0);" class="menor" onclick="checarTudo('tudo1');">checar/deschecar tudo</a>
        </fieldset>    
    </div>
    
    <div class="parte50">
    	<fieldset>
            <legend>Mensagem:</legend>
            
           	<?
            $result_deptos= mysql_query("select * from rh_carreiras_departamentos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   valido = '1'
											and   id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
											");
			$linhas_deptos= mysql_num_rows($result_deptos);
			
			if ($linhas_deptos>1) {
				$id_departamento_usuario2= pega_dado_carreira("id_departamento", $_SESSION["id_funcionario_sessao"]);
            ?>
            <label for="id_outro_departamento">De:</label>
            <select name="id_outro_departamento" id="id_outro_departamento" title="De">
				<?
                $i=0;
                while ($rs_deptos= mysql_fetch_object($result_deptos)) {
                ?>
                <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_deptos->id_departamento; ?>" <? if ($id_departamento_usuario2==$rs_deptos->id_departamento) echo "selected=\"selected\""; ?>><?= pega_departamento($rs_deptos->id_departamento); ?></option>
                <? $i++; } ?>
            </select>
            <br />
            <? } ?>
            
            <?
			if ($_SESSION["mensagem_livro"]!="") $mensagem_aqui= $_SESSION["mensagem_livro"];
			else $mensagem_aqui= "";
			?>
            <label for="mensagem">Mensagem:</label>
            <textarea name="mensagem" id="mensagem"><?=$mensagem_aqui;?></textarea>
            <br />
            
            <?
            $result_deptos_principal= mysql_query("select * from rh_departamentos
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   presente_livro = '1'
													order by departamento asc
													");
			$linhas_deptos_principal= mysql_num_rows($result_deptos_principal);
            ?>
            <label for="id_departamento_principal">Setor responsável:</label>
            <select name="id_departamento_principal" id="id_departamento_principal" title="Depto principal">
				<option value="">-</option>
				<?
                $i=0;
                while ($rs_deptos_principal= mysql_fetch_object($result_deptos_principal)) {
					if ($rs_deptos_principal->departamento_livro!="") $departamento_nome= $rs_deptos_principal->departamento_livro;
					else $departamento_nome= $rs_deptos_principal->departamento;
                ?>
                <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_deptos_principal->id_departamento; ?>"><?= $departamento_nome; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_motivo">Motivo:</label>
            <select name="id_motivo" id="id_motivo" title="Motivo" onchange="abreFechaReclamacaoFormulario(this.value);">
				<?
                $i=0;
                $result_mot= mysql_query("select * from rh_motivos
										 where tipo_motivo = 'l'
										 and   status_motivo = '1'
										 order by motivo asc ");
                while ($rs_mot= mysql_fetch_object($result_mot)) {
                ?>
                <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> <? if ($rs_mot->id_motivo==35) echo "selected=\"selected\""; ?> value="<?= $rs_mot->id_motivo; ?>"><?= $rs_mot->motivo; ?></option>
                <? $i++; } ?>
            </select>
            <br /><br />
            
            <? if (pode("12", $_SESSION["permissao"])) { ?>
                <label>&nbsp;</label>
                <input type="checkbox" class="tamanho20" name="historico_cliente" id="historico_cliente" value="1" onclick="checaLivroHistoricoCliente(this);" />
                <label for="historico_cliente" class="nao_negrito alinhar_esquerda tamanho300">Enviar para o histórico de cliente</label>
                <br />
            <? } ?>
            
            <div id="reclamacao_formulario">
            	
            </div>
            
            <label>&nbsp;</label>
            <input type="checkbox" class="tamanho20" name="resposta_requerida" id="resposta_requerida" value="1" />
            <label for="resposta_requerida" class="nao_negrito alinhar_esquerda">Solicitar resposta</label>
            
            <br /><br />
        </fieldset>
    </div>
    <br />

    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>
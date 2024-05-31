<?
require_once("conexao.php");
if (pode("o", $_SESSION["permissao"])) {
	?>
    <div id="tudo2">
		<?
        if ($_SESSION["id_funcionario_sessao"]!="") $id_departamento_usuario2= pega_dado_carreira("id_departamento", $_SESSION["id_funcionario_sessao"]);
        else $id_departamento_usuario2= $_SESSION["id_departamento_sessao"];
        
        $result_dep= mysql_query("select * from rh_departamentos
                                    where id_empresa = '". $_SESSION["id_empresa"] ."' 
									and   presente_livro = '1'
                                    and   status_departamento = '1' 
                                    order by departamento asc
                                    ");
        $i=1;
        while ($rs_dep= mysql_fetch_object($result_dep)) {
            $result_permissao= mysql_query("select * from com_livro_permissoes
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   id_livro = '". $_GET["id_livro"] ."'
                                            and   id_departamento = '". $rs_dep->id_departamento ."'
                                            ");
            $linhas_permissao= mysql_num_rows($result_permissao);
			
			if ($rs_dep->departamento_livro!="") $departamento_nome= $rs_dep->departamento_livro;
			else $departamento_nome= $rs_dep->departamento;
        ?>
        <input <? if (($id_departamento_usuario2==$rs_dep->id_departamento) || ($linhas_permissao>0)) echo "checked=\"checked\""; ?> class="tamanho15 espaco_dir" type="checkbox" name="para[]" id="resposta_para_<?= $rs_dep->id_departamento;?>" value="<?= $rs_dep->id_departamento;?>" />
        <label for="resposta_para_<?= $rs_dep->id_departamento;?>" class="alinhar_esquerda menor2 nao_negrito"><?= $departamento_nome;?></label>
        
        <?
            if (($i%3)==0) echo "<br />";
            
            $i++;
        }
        ?>
        
        <a href="javascript:void(0);" class="menor" onclick="checarTudo('tudo2');">checar/deschecar tudo</a>
    </div>
    <br /><br />

    <?
	$result_deptos_principal= mysql_query("select * from rh_departamentos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   presente_livro = '1'
											order by departamento asc
											");
	$linhas_deptos_principal= mysql_num_rows($result_deptos_principal);
	?>
	<label for="id_departamento_principal">Setor responsável:</label>
	<select name="id_departamento_principal" id="id_departamento_principal" title="Depto principal" class="tamanho300">
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
	<br /><br />
    
    <label>&nbsp;</label>
    <input type="checkbox" class="tamanho20" name="resposta_requerida" id="resposta_requerida2" value="1" />
    <label for="resposta_requerida2" class="nao_negrito alinhar_esquerda">Solicitar resposta</label>
    <br /><br />
    
    <script language="javascript">
		habilitaCampo("enviar_id_livro_<?=$_GET["id_livro"];?>");
		habilitaCampo("cancela_id_livro_<?=$_GET["id_livro"];?>");
	</script>
<? } ?>
<?
require_once("conexao.php");
if (pode("ey", $_SESSION["permissao"])) {
?>
<div id="percurso_clientes">
	<?
	/*if (($_GET["id_percurso"]!='') && ($_GET["id_regiao"]=="")) {
		$result_percurso= mysql_query("select * from tr_percursos
									  	where id_percurso = '". $_GET["id_percurso"] ."' ");
		$rs_percurso= mysql_fetch_object($result_percurso);
		
		$id_regiao= $rs_percurso->id_regiao;
	}
	else */ $id_regiao= $_GET["id_regiao"];
	
	if ($id_regiao!="") $str= " and   pessoas.id_regiao = '". $id_regiao ."' ";
	
	$result_clientes= mysql_query("select * from pessoas, pessoas_tipos
									where pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas_tipos.tipo_pessoa = 'c'
									and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   pessoas.status_pessoa = '1'
									and   pessoas.id_cliente_tipo = '1'
									$str
									order by 
									pessoas.nome_rz asc
									") or die(mysql_error());
	?>
    <label>Clientes:</label>
    <?
	$i=1;
	while ($rs_clientes= mysql_fetch_object($result_clientes)) {
		if ($_GET["id_percurso"]!='') {
			$result_permissao= mysql_query("select * from tr_percursos_clientes
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_percurso= '". $_GET["id_percurso"] ."'
											and   id_cliente = '". $rs_clientes->id_pessoa."'
											");
			$linhas_permissao= mysql_num_rows($result_permissao);
		}
	?>
    
	<input <? if ($linhas_permissao>0) echo "checked=\"checked\""; ?> class="tamanho15 espaco_dir" type="checkbox" name="id_cliente_entrega[]" id="id_cliente_<?= $rs_clientes->id_pessoa;?>" value="<?= $rs_clientes->id_pessoa;?>" />
	<label for="id_cliente_<?= $rs_clientes->id_pessoa;?>" class="alinhar_esquerda menor2 nao_negrito tamanho70"><?= $rs_clientes->codigo .". ". $rs_clientes->sigla;?></label>
	
	<?
		if (($i%3)==0) echo "<br /> <label>&nbsp;</label>";
		
		$i++;
	}
	?>
    <br /><br />
</div>

<? /*
<div id="percurso_remessas">
	<?
    if ($acao=='e') {
        $result_remessas= mysql_query("select * from tr_percursos_remessas
                                        where id_percurso = '". $_GET["id_percurso"] ."'
                                        order by id_remessa asc
                                        ");
        $i=1;
        while ($rs_remessas= mysql_fetch_object($result_remessas)) {
        ?>
        <div id="div_remessa_<?=$i;?>">
            <code class="escondido"></code>
            
            <label for="data_remessa_<?=$i;?>">Data remessa/num:</label>
            <input id="data_remessa_<?=$i;?>" name="data_remessa[]" class="espaco_dir tamanho15p" value="<?= desformata_data(pega_dado_remessa("data_remessa", $rs_remessas->id_remessa)); ?>" onkeyup="formataData(this);" maxlength="10" title="Data da remessa" />
            <input id="num_remessa_<?=$i;?>" name="num_remessa[]" class="tamanho10p" value="<?= pega_dado_remessa("num_remessa", $rs_remessas->id_remessa); ?>" title="Número da remessa" onblur="pegaRemessaVetor('<?=$i;?>');" />
            
            <div id="remessa_atualiza_<?=$i;?>">
                <input id="id_remessa_<?=$i;?>" name="id_remessa[]" value="<?= $rs->id_remessa; ?>" title="Remessa" class="escondido" />
            </div>
            
            <br /><label>&nbsp;</label>
            <a href="javascript:void(0);" onclick="removeDiv('percurso_remessas', 'div_remessa_<?=$i;?>');">remover</a>
            <br /><br />
        </div>
        <script language="javascript">
            pegaRemessaVetor('<?=$i;?>');
        </script>
        <?
            $i++;
        }
    }
    else {
        for ($i=1; $i<2; $i++) {
        ?>
        <div id="div_remessa_<?=$i;?>">
            <code class="escondido"></code>
            
            <label for="data_remessa_<?=$i;?>">Data remessa/num:</label>
            <input id="data_remessa_<?=$i;?>" name="data_remessa[]" class="espaco_dir tamanho15p" value="<?= $data_remessa; ?>" onkeyup="formataData(this);" maxlength="10" title="Data da remessa" />
            <input id="num_remessa_<?=$i;?>" name="num_remessa[]" class="tamanho10p" value="" title="Número da remessa" onblur="pegaRemessaVetor('<?=$i;?>');" />
            
            <div id="remessa_atualiza_<?=$i;?>">
                <input id="id_remessa_<?=$i;?>" name="id_remessa[]" value="<?= $rs->id_remessa; ?>" title="Remessa" class="escondido" />
            </div>
            
            <br /><label>&nbsp;</label>
            <a href="javascript:void(0);" onclick="removeDiv('percurso_remessas', 'div_remessa_<?=$i;?>');">remover</a>
            <br /><br />
        </div>
    <? } } ?>
</div>

<br />

<label>&nbsp;</label>
<button type="button" onclick="criaEspacoPercursoRemessa();">nova remessa</button>*/ ?>
<br /><br /><br />

<? } ?>
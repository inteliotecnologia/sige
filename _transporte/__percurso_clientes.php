<?
require_once("conexao.php");
if (pode("ey", $_SESSION["permissao"])) {
	
	$result_clientes= mysql_query("select * from pessoas, pessoas_tipos
									where pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas_tipos.tipo_pessoa = 'c'
									and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   pessoas.status_pessoa = '1'
									and   pessoas.id_cliente_tipo = '1'
									order by 
									pessoas.nome_rz asc
									") or die(mysql_error());
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
	<label for="id_cliente_<?= $rs_clientes->id_pessoa;?>" class="alinhar_esquerda menor2 nao_negrito tamanho80"><?= $rs_clientes->codigo .". ". $rs_clientes->sigla;?></label>
	
	<?
		if (($i%3)==0) echo "<br /> <label>&nbsp;</label>";
		
		$i++;
	}
	?>
    <br /><br />

<? } ?>
<?
require_once("conexao.php");
if (pode("o", $_SESSION["permissao"])) {
	
	$result_livro= mysql_query("select * from com_livro
								where resposta_requerida= '1'
								and   id_departamento_principal <> ''
								");
	
	while ($rs_livro= mysql_fetch_object($result_livro)) {
		
		$result_atualiza= mysql_query("update com_livro_permissoes
										set resposta_requerida_depto= '1'
										where id_livro = '". $rs_livro->id_livro ."'
										and   id_departamento = '". $rs_livro->id_departamento_principal ."'
										");
		
	}
	
}//fim pode
?>
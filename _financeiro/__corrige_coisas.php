<?
require_once("conexao.php");

/*
$result= mysql_query("select * from pessoas, rh_funcionarios
						where pessoas.id_pessoa = rh_funcionarios.id_pessoa
						order by pessoas.id_pessoa asc");

if (mysql_num_rows($result)==0) echo "nada encontrado";

while ($rs= mysql_fetch_object($result)) {
	
	$result2= mysql_query("insert into pessoas_tipos
						  		(id_pessoa, tipo_pessoa, id_empresa)
								values
								('". $rs->id_pessoa ."', 'u', '1')
								");
	
}
*/

$result= mysql_query("select * from tel_contatos
						where id_funcionario <> '' ");

while ($rs= mysql_fetch_object($result)) {
	$id_pessoa= pega_id_pessoa_do_funcionario($rs->id_funcionario);;
	
	$result2= mysql_query("update tel_contatos
						  		set id_pessoa= '$id_pessoa'
								where id_contato = '". $rs->id_contato ."'
								
								");
	
}
?>
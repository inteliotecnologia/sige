<?
require_once("conexao.php");
if (pode_algum("j", $_SESSION["permissao"])) {
	
	$result1= mysql_query("update man_checklist set id_tecnico='1' where id_funcionario= '48' ");
	$result2= mysql_query("update man_checklist set id_tecnico='2' where id_funcionario= '73' ");
	
}
?>
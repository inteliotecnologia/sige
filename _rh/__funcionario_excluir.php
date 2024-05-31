<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("rhv", $_SESSION["permissao"])) {
	
	$var=0;
	inicia_transacao();
	
	$result= mysql_query("update rh_funcionarios
						 	set status_funcionario = '2'
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							and   id_funcionario = '". $_GET["id_funcionario"] ."'
							");
	if (!$result) $var++;
	
	$result2= mysql_query("delete from rh_cartoes
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							and   id_funcionario = '". $_GET["id_funcionario"] ."'
							");
	if (!$result2) $var++;
	
	finaliza_transacao($var);
	
	header("location: ./?pagina=rh/funcionario_listar&status_funcionario=1&msg=". $var);
	
}
?>
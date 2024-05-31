<?
require_once("conexao.php");
if (pode_algum("r", $_SESSION["permissao"])) {
	
	$var= 0;
	$result= mysql_query("select * from rh_carreiras
						 	where atual = '1'
							");
	
	while ($rs= mysql_fetch_object($result)) {
		
		$result_pre= mysql_query("select * from rh_carreiras_departamentos
								 	where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_funcionario = '". $rs->id_funcionario ."'
									and   id_departamento = '". $rs->id_departamento ."'
									") or die(mysql_error());
		
		if (mysql_num_rows($result_pre)==0) {
			echo "Adicionando <strong>". pega_funcionario($rs->id_funcionario) ."</strong>...<br />";
			$result1= mysql_query("insert into rh_carreiras_departamentos
									(id_empresa, id_funcionario, id_departamento, id_usuario) values
									('". $_SESSION["id_empresa"] ."', '". $rs->id_funcionario ."',
									'". $rs->id_departamento  ."', '". $_SESSION["id_usuario"] ."')
									") or die(mysql_error());
			if (!$result1) $var++;
		}
		
	}
	
	echo $var;
}
?>

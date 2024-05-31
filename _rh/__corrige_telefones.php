<?
require_once("conexao.php");

$result= mysql_query("select * from pessoas, rh_funcionarios, rh_enderecos
						where pessoas.id_pessoa = rh_funcionarios.id_pessoa
						and   pessoas.id_pessoa = rh_enderecos.id_pessoa
						order by pessoas.id_pessoa asc");

if (mysql_num_rows($result)==0) echo "nada encontrado";

while ($rs= mysql_fetch_object($result)) {
	
	
	if (($rs->tel_res!="") || ($rs->tel_cel!="")) {
		echo  $rs->nome_rz ."<br>";
		echo $rs->tel_res ."<br>";
		echo $rs->tel_cel ."<br>";
		
		$result5= mysql_query("insert into tel_contatos (id_empresa, tipo_contato, nome, email, id_usuario)
										values ('1', '2', '". $rs->nome_rz ."',
													'". $rs->email ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
		if (!$result5) $var++;
		$id_contato= mysql_insert_id();
		
		if ($rs->tel_res!="") {
			$result5= mysql_query("insert into tel_contatos_telefones (id_empresa, id_contato, telefone, tipo, obs)
								values ('1', '$id_contato', '". $rs->tel_res ."', '1', '". $rs->tel_res_com ."' ) ") or die(mysql_error());
			if (!$result5) $var++;
		}
		if ($rs->tel_cel!="") {
			$result6= mysql_query("insert into tel_contatos_telefones (id_empresa, id_contato, telefone, tipo, obs)
								values ('1', '$id_contato', '". $rs->tel_cel ."', '3', '". $rs->tel_cel_com ."' ) ") or die(mysql_error());
			if (!$result6) $var++;
		}
	}
	
}

?>
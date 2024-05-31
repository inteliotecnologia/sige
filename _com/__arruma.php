<?
require_once("conexao.php");
if (pode("n", $_SESSION["permissao"])) {
	
	$result_livro= mysql_query("select * from com_livro
							   	where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   DATE_FORMAT(data_livro, '%Y') = '2010'
								order by id_livro
								");
	
	$i_livro=1;
	$i_nc=1;
	$i_r=1;
	
	while ($rs_livro= mysql_fetch_object($result_livro)) {
		
		switch ($rs_livro->id_motivo) {
			//reclamacao
			case 34:
			case 37:
				$i_aqui= $i_r;
				$i_r++;
				break;
			//nc
			case 41:
			case 42:
				$i_aqui= $i_nc;
				$i_nc++;
				break;
			//demais
			default:
				$i_aqui= $i_livro;
				$i_livro++;
		}
		
		
		switch ($rs_livro->id_motivo) {
			
			case 34:
			case 37:
			case 41:
			case 42:
				if ($rs_livro->reclamacao_original==1) {
					$result_atualiza= mysql_query("update com_livro
													set num_livro = '". $i_aqui ."'
													where id_livro = '". $rs_livro->id_livro ."'
													");
					
					echo "<strong>atualizando id_livro: ". $rs_livro->id_livro ." para número: ". $i_aqui ."</strong><br />";
				}
				else {
					$result_pre= mysql_query("select num_livro from com_livro
											 	where id_livro = '". $rs_livro->reclamacao_original_id_livro ."'
												");
					$rs_pre= mysql_fetch_object($result_pre);
					
					$result_atualiza= mysql_query("update com_livro
													set num_livro = '". $rs_pre->num_livro ."'
													where id_livro = '". $rs_livro->id_livro ."'
													");
					
					echo "atualizando id_livro: ". $rs_livro->id_livro ." para número: ". $i_aqui ."<br />";
				}
				break;
			
			default:	
				$result_atualiza= mysql_query("update com_livro
												set num_livro = '". $i_aqui ."'
												where id_livro = '". $rs_livro->id_livro ."'
												");
				
				echo "<strong>atualizando id_livro: ". $rs_livro->id_livro ." para número: ". $i_aqui ."</strong><br />";
				
				break;
		}
		
	}
	
}
?>
<?
require_once("../../conexao.php");
require_once("../phpzip/phpzip.inc.php");


$result= mysql_list_tables($conf_db);
$total= mysql_num_rows($result);

for ($i=0; $i<$total; $i++)
	$tabelas[$i]= mysql_tablename($result, $i);

$dir= "./";
$agora= date('Y-m-d_H-i-s');
$filename= "backup_". $_SESSION["id_usuario"] ."_". $agora .".bak";
$filename_zip= "backup_". $_SESSION["id_usuario"] ."_". $agora .".zip";

chdir($dir);
$fp = fopen($filename,"w"); 

for ($x=0; $x<count($tabelas); $x++) {
   $saida = getTableDef($conf_db, $tabelas[$x], "\n");
   fputs($fp,$saida."\n\n");

	getTableContentFast($conf_db, $tabelas[$x], '', '');
	fputs($fp,"\n\n");
}
fclose($fp);

// gerar o arquivo zipado
$filename_zip= ereg_replace("bak$","zip",$filename);
//$zipname= $filename_zip;

//$zip= new PHPZip();
//$files[]= $filename;
//$zip->Zip($files, $filename_zip);

//$tamanho= filesize($filename_zip);

//$fp = fopen($filename_zip, "r");
//fclose($fp);
//unlink($filename);

//header("location: ../../");

echo "<h2>Backups</h2>";

if (file_exists($dir . $filename_zip)) {
	$result2= mysql_query("insert into backups (data_backup, arquivo)
											values
											('". date("Y-m-d") ."', '". $filename_zip ."' ) ") or die(mysql_error());
	
	$ftp_server= "cayman.dreamhost.com";
	$ftp_user_name= "pcesconetto";
	$ftp_user_pass= "patimacro09";
	
	// set up basic connection
	$conn_id = ftp_connect($ftp_server); 
	
	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 
	
	//ftp_pasv($conn_id, true);
	
	// check connection
	if ((!$conn_id) || (!$login_result)) { 
		echo "<p>Conexão com o servidor remoto falhou!</p>";
		//echo "Attempted to connect to $ftp_server for user $ftp_user_name"; 
		exit; 
	} else {
		echo "<p>Conectado ao servidor remoto como <strong>$ftp_user_name</strong>...</p>";
	}
	
	ftp_chdir($conn_id, '/sige_backups/'); 
	// upload the file
	$upload= ftp_put($conn_id, $dir.$filename_zip, $dir.$filename_zip, FTP_BINARY); 
	
	// check upload status
	if (!$upload) { 
		echo "<p>Backup criado, mas falhou envio para o servidor de segurança, <a href=\"". $dir . $filename_zip ."\">clique aqui</a> e salve esse arquivo localmente!</p>";
	} else {
		echo "<p>Arquivo <strong>$filename_zip</strong> enviado para o servidor de segurança com sucesso!";
	}
	
	// close the FTP stream 
	ftp_close($conn_id);
	
}
elseif (file_exists($dir . $filename)) {
	echo "<p>Backup <strong>". $filename ."</strong> criado com sucesso no servidor, <a href=\"". $dir . $filename ."\">clique aqui</a> para salvar o arquivo de backup!</p>";
	
	$result2= mysql_query("insert into backups (data_backup, arquivo)
											values
											('". date("Y-m-d") ."', '". $filename ."' ) ") or die(mysql_error());
	
	
}
else
	echo "<p>Não foi possível realizar o backup para <strong>". $filename ."</strong>, tente mais tarde. Se o problema persistir, entre em contato com o desenvolvedor.</p>";


// FIM DO PROGRAMA
// --------------------------------------------------------


// --------------------------------------------------------
// PROCEDIMENTOS - Baseado no csdigo do phpmyadmin
function sqlAddslashes($a_string = '', $is_like = FALSE) {
  if ($is_like) {
    $a_string = str_replace('\\', '\\\\\\\\', $a_string);
  } else {
    $a_string = str_replace('\\', '\\\\', $a_string);
  }
  $a_string = str_replace('\'', '\\\'', $a_string);

  return $a_string;
} // end of the 'sqlAddslashes()' function


function backquote($a_name, $do_it = TRUE) {
  if ($do_it && PMA_MYSQL_INT_VERSION >= 32306 && !empty($a_name) 
      && $a_name != '*') {

     if (is_array($a_name)) {
        $result = array();
        reset($a_name);
        while(list($key, $val) = each($a_name)) {
           $result[$key] = '`' . $val . '`';
        }
        return $result;
     } else {
        return '`' . $a_name . '`';
     }
  } else {
     return $a_name;
  }
} // end of the 'backquote()' function

function getTableDef($db, $table, $crlf) {
   global $drop;
   global $use_backquotes;
   global $conexao;

   $schema_create = '';
   if (!empty($drop)) {
      $schema_create .= 'DROP TABLE IF EXISTS ' . 
      backquote($table) . ';' . $crlf;
   }

   // For MySQL < 3.23.20
   $schema_create .= 'CREATE TABLE ' . 
   backquote($table) . ' (' . $crlf;

   $local_query   = 'SHOW FIELDS FROM ' . backquote($table) . ' FROM ' 
   . backquote($db);

   $result = mysql_query($local_query,$conexao);

   while ($row = mysql_fetch_array($result)) {
      $schema_create     .= '   ' . 
      backquote($row['Field']) 
      . ' ' . $row['Type'];

      if (isset($row['Default']) && $row['Default'] != '') {
           $schema_create .= ' DEFAULT \'' . 
           sqlAddslashes($row['Default']) . '\'';
      }

      if ($row['Null'] != 'YES') {
           $schema_create .= ' NOT NULL';
      }

      if ($row['Extra'] != '') {
           $schema_create .= ' ' . $row['Extra'];
      }

      $schema_create     .= ',' . $crlf;
   } // end while

   mysql_free_result($result);
   $schema_create = ereg_replace(',' . $crlf . '$', '', $schema_create);

   $local_query = 'SHOW KEYS FROM ' . backquote($table) . ' FROM ' 
   . backquote($db);

   $result = mysql_query($local_query,$conexao);
   while ($row = mysql_fetch_array($result)) {
       $kname    = $row['Key_name'];
       $comment  = (isset($row['Comment'])) ? $row['Comment'] : '';
       $sub_part = (isset($row['Sub_part'])) ? $row['Sub_part'] : '';

       if ($kname != 'PRIMARY' && $row['Non_unique'] == 0) {
           $kname = "UNIQUE|$kname";
       }

       if ($comment == 'FULLTEXT') {
           $kname = 'FULLTEXT|$kname';
       }

       if (!isset($index[$kname])) {
           $index[$kname] = array();
       }

       if ($sub_part > 1) {
           $index[$kname][] = backquote($row['Column_name']) . '(' . $sub_part . ')';
       } else {
           $index[$kname][] = backquote($row['Column_name']);
       }
   } // end while
   mysql_free_result($result);

   while (list($x, $columns) = @each($index)) {
       $schema_create .= ',' . $crlf;
       if ($x == 'PRIMARY') {
          $schema_create .= '   PRIMARY KEY (';
       } else if (substr($x, 0, 6) == 'UNIQUE') {
          $schema_create .= '   UNIQUE ' . substr($x, 7) . ' (';
       } else if (substr($x, 0, 8) == 'FULLTEXT') {
          $schema_create .= '   FULLTEXT ' . substr($x, 9) . ' (';
       } else {
          $schema_create .= '   KEY ' . $x . ' (';
       }
       $schema_create .= implode($columns, ', ') . ')';
   } // end while

   $schema_create .= $crlf . ');';

   return $schema_create;
} // end of the 'getTableDef()' function

function getTableContentFast($db, $table, $add_query = '', $handler) {
   global $use_backquotes;
   global $rows_cnt;
   global $current_row;
   global $conexao;
   global $fp;

  $local_query = 'SELECT * FROM ' . backquote($db) . '.' . backquote($table) 
  . $add_query;

  $result = mysql_query($local_query,$conexao);
  if ($result != FALSE) {
     $fields_cnt = mysql_num_fields($result);
     $rows_cnt   = mysql_num_rows($result);

     // Checks whether the field is an integer or not
     for ($j = 0; $j < $fields_cnt; $j++) {
         $field_set[$j] = backquote(mysql_field_name($result, $j), $use_backquotes);
         $type = mysql_field_type($result, $j);
         if ($type == 'tinyint' || $type == 'smallint' || 
             $type == 'mediumint' || $type == 'int' ||
             $type == 'bigint'  ||$type == 'timestamp') {
             $field_num[$j] = TRUE;
         } else {
             $field_num[$j] = FALSE;
         }
     } // end for

     // Sets the scheme
     if (isset($GLOBALS['showcolumns'])) {
         $fields = implode(', ', $field_set);
         $schema_insert = 'INSERT INTO ' . backquote($table)
         . ' (' . $fields . ') VALUES (';
     } else {
         $schema_insert = 'INSERT INTO ' . 
         backquote($table) . ' VALUES (';
     }

     $search = array("\x00", "\x0a", "\x0d", "\x1a"); //\x08\\x09, not required
     $replace      = array('\0', '\n', '\r', '\Z');
     $current_row  = 0;

     @set_time_limit($GLOBALS['cfg']['ExecTimeLimit']);

     // loic1: send a fake header to bypass browser timeout if data
     //        are bufferized - part 1
     if (!empty($GLOBALS['ob_mode']) || (isset($GLOBALS['zip']) 
         || isset($GLOBALS['bzip']) || isset($GLOBALS['gzip']))) {
         $time0 = time();
     }

     while ($row = mysql_fetch_row($result)) {
         $current_row++;
         for ($j = 0; $j < $fields_cnt; $j++) {
            if (!isset($row[$j])) {
                 $values[] = 'NULL';
            } else if ($row[$j] == '0' || $row[$j] != '') {
                 // a number
                 if ($field_num[$j]) {
                     $values[] = $row[$j];
                 } else {
                    // a string
                    $values[] = "'" . str_replace($search, $replace, 
                    sqlAddslashes($row[$j])) . "'";
                 }
           } else {
              $values[] = "''";
           } // end if
        } // end for

        // Extended inserts case
        if (isset($GLOBALS['extended_ins'])) {
            if ($current_row == 1) {
               $insert_line  = $schema_insert . implode(', ', $values) . ');';
            } else {
               $insert_line  = '(' . implode(', ', $values) . ');';
            }
        } else {
        // Other inserts case
           $insert_line = $schema_insert . implode(', ', $values) . ');';
        }
        unset($values);

        // Call the handler
        fputs($fp,$insert_line . "\n");

        // loic1: send a fake header to bypass browser timeout if data
        //        are bufferized - part 2
        if (isset($time0)) {
            $time1 = time();
            if ($time1 >= $time0 + 30) {
               $time0 = $time1;
               header('X-pmaPing: Pong');
            }
        } // end if
     } // end while
  } // end if ($result != FALSE)
  mysql_free_result($result);

  return TRUE;
} // end of the 'getTableContentFast()' function


?>
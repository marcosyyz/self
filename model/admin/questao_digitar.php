<?php
include_once ROOT."model/mysql.php";

class Questao_Digitar
{
	private $db;      // conexao com o banco
	
	public  $questaodigitar_cdg;
        public  $campo;
        	
	public $ThrowExceptions = false;

	public function __construct($_questaodigitar_cdg = null) {
	     $this->db = new Mysql();
             $this->questaodigitar_cdg = isset($_questaodigitar_cdg) ? $_questaodigitar_cdg : -1;
             if($this->questaodigitar_cdg != -1)
                $this->carregar_questao();
	}
	
        
	public function __destruct() {
		
	}


	 // carrega a questao atual 
	public function carregar_questao() {	            
             $this->db->Query("SELECT QUESTAODIGITAR_CDG,QUESTAODIGITAR_PERGUNTA,"
                     . "QUESTAODIGITAR_IMAGEM,QUESTAODIGITAR_IMAGEM_POS,QUESTAODIGITAR_TEXTO,"
                     . "QUESTAODIGITAR_LACUNA1,QUESTAODIGITAR_LACUNA2,QUESTAODIGITAR_LACUNA3,"
                     . "QUESTAODIGITAR_LACUNA4,QUESTAODIGITAR_LACUNA5,QUESTAODIGITAR_CRIADOR,"
                     . "DATE_FORMAT(QUESTAODIGITAR_DTCRIACAO,'%d/%m/%Y %H:%i') as QUESTAODIGITAR_DTCRIACAO,"
                     . "DATE_FORMAT(QUESTAODIGITAR_DTMOD,'%d/%m/%Y %H:%i') as QUESTAODIGITAR_DTMOD,"  
                     . "QUESTAODIGITAR_MATERIA,"
                     . "QUESTAODIGITAR_ASSUNTO,QUESTAODIGITAR_DESCRITOR"
                     . " FROM QUESTAO_DIGITAR WHERE QUESTAODIGITAR_CDG = ".$this->questaodigitar_cdg ); 
	     
             $this->campo =  $this->db->Row();
            /* $this->QUESTAO_PERGUNTA = $this->campo->QUESTAO_PERGUNTA;
             $this->QUESTAO_IMAGEM = $this->campo->QUESTAO_IMAGEM;
             $this->QUESTAO_IMAGEM_POS = $this->campo->QUESTAO_IMAGEM_POS;
             $this->QUESTAO_TEXTO = $this->campo->QUESTAO_TEXTO ;
             $this->QUESTAO_OPCAO1 = $this->campo->QUESTAO_OPCAO1 ;
             $this->QUESTAO_OPCAO2  = $this->campo->QUESTAO_OPCAO2  ;
             $this->QUESTAO_OPCAO3 = $this->campo->QUESTAO_OPCAO3 ;
             $this->QUESTAO_OPCAO4  = $this->campo->QUESTAO_OPCAO4  ;
             $this->QUESTAO_OPCAO5  = $this->campo->QUESTAO_OPCAO5  ;
             $this->QUESTAO_CRIADOR = $this->campo->QUESTAO_CRIADOR ;
             */
	}
			       
        //grava atividade finalizada e suas notas no banco
        public function gravar($pergunta,$imagem,$imagem_pos,$texto,
                                $opcao1,$opcao2,$opcao3,$opcao4,$opcao5,$criador,$revisor,$materia,
                                $assunto,$tipo,$descritor){
                      
            
            
            // valores a serem inseridos
           $valores["QUESTAO_PERGUNTA"]  = MySQL::SQLValue($pergunta, MySQL::SQLVALUE_TEXT);
           $valores["QUESTAO_IMAGEM"]  = MySQL::SQLValue($imagem, MySQL::SQLVALUE_TEXT);
           $valores["QUESTAO_IMAGEM_POS"] = MySQL::SQLValue($imagem_pos, MySQL::SQLVALUE_NUMBER);
           $valores["QUESTAO_TEXTO"]  = MySQL::SQLValue($texto, MySQL::SQLVALUE_TEXT);
           $valores["QUESTAO_OPCAO1"]  = MySQL::SQLValue($opcao1, MySQL::SQLVALUE_TEXT);
           $valores["QUESTAO_OPCAO2"]  = MySQL::SQLValue($opcao2, MySQL::SQLVALUE_TEXT);
           $valores["QUESTAO_OPCAO3"]  = MySQL::SQLValue($opcao3, MySQL::SQLVALUE_TEXT);
           $valores["QUESTAO_OPCAO4"]  = MySQL::SQLValue($opcao4, MySQL::SQLVALUE_TEXT);
           $valores["QUESTAO_OPCAO5"]  = MySQL::SQLValue($opcao5, MySQL::SQLVALUE_TEXT);           
           
           
         //  $valores["QUESTAO_MATERIA"]  = MySQL::SQLValue($materia, MySQL::SQLVALUE_TEXT);
           //$valores["QUESTAO_ASSUNTO"]  = MySQL::SQLValue($assunto, MySQL::SQLVALUE_TEXT);           
           //$valores["QUESTAO_DESCRITOR"]  = MySQL::SQLValue($descritor, MySQL::SQLVALUE_TEXT);
           
           
            
            
            //consultar se ja existe
            $this->db->Query(" SELECT * FROM QUESTAO WHERE QUESTAO_CDG = ".  $this->questao_cdg);
            $this->db->MoveFirst();		
                                                      
            
            // se  ja existe
            if($this->db->RowCount() > 0){               
                // update                             
                $where["QUESTAO_CDG"]  = MySQL::SQLValue($this->questao_cdg, MySQL::SQLVALUE_NUMBER);
                $valores["QUESTAO_DTMOD"]  = "'".date("Y-m-d G:i:s")."'";
                $this->db->UpdateRows("QUESTAO", $valores, $where);
                return -1; 
            }else{
                // se nao, executa insert                                   
                $valores["QUESTAO_CRIADOR"]  = MySQL::SQLValue($criador, MySQL::SQLVALUE_TEXT);                                
                $this->db->InsertRow("QUESTAO", $valores);
                return $this->db->GetLastInsertID();
            }
           		
        }
        
         public function atualizar_atividade_feita($aluno_cdg,$acertos,$erros,$totalquestoes){
                         
            $values["ALUNOATIV_ACERTOS"] = MySQL::SQLValue($acertos, MySQL::SQLVALUE_NUMBER);
            $values["ALUNOATIV_ERROS"]  = MySQL::SQLValue($erros, MySQL::SQLVALUE_NUMBER);
            $values["ALUNOATIV_TOTALQUESTOES"]  = MySQL::SQLValue($totalquestoes, MySQL::SQLVALUE_NUMBER);            
            
            $where["ALUNOATIV_ALUNO_CDG"]  = MySQL::SQLValue($aluno_cdg, MySQL::SQLVALUE_NUMBER);
            $where["ALUNOATIV_ATIVIDADE_CDG"]  = $this->atividade_cdg;
            
            return $this->db->UpdateRows("ALUNO_ATIVIDADE", $values, $where);
            
         }
         
         
        public function listar_questoes(){
            $this->db->Query(" SELECT QUESTAO_CDG ,QUESTAO_CDG Código, 
                                   QUESTAO_PERGUNTA Pergunta,
                                   QUESTAO_SOM Som,
                                   QUESTAO_IMAGEM Imagem,
                                   QUESTAO_OPCAO1 'Alternativa Correta',                                                                  
                                   MATERIA_NOME Matéria,
                                   QUESTAO_ASSUNTO Assunto,
                                   QUESTAO_DESCRITOR Descritor                                   
                               FROM QUESTAO LEFT JOIN MATERIA ON MATERIA_CDG = QUESTAO_MATERIA
                                   LEFT JOIN USUARIO ON USUARIO_CDG = QUESTAO_CRIADOR ");

          //  return $this->getHTMLQuestoes( false,false);
              return $this->getHTMLQuestoes(true,true,ROOT_URL.'view/admin/edit_questao.php?q=');
        }
        
        
        public function duplicar_questao($questao_cdg){
            return $this->db->Query("INSERT INTO QUESTAO(QUESTAO_PERGUNTA,QUESTAO_IMAGEM,QUESTAO_IMAGEM_POS,
                                                    QUESTAO_SOM,QUESTAO_TEXTO,QUESTAO_OPCAO1,QUESTAO_OPCAO2,
                                                    QUESTAO_OPCAO3,QUESTAO_OPCAO4,QUESTAO_OPCAO5,QUESTAO_CRIADOR,
                                                    QUESTAO_REVISOR,QUESTAO_DTCRIACAO,QUESTAO_DTMOD,QUESTAO_MATERIA,
                                                    QUESTAO_ASSUNTO,QUESTAO_DESCRITOR)

                                          SELECT QUESTAO_PERGUNTA,QUESTAO_IMAGEM,QUESTAO_IMAGEM_POS,
                                                 QUESTAO_SOM,QUESTAO_TEXTO,QUESTAO_OPCAO1,QUESTAO_OPCAO2,
                                                 QUESTAO_OPCAO3,QUESTAO_OPCAO4,QUESTAO_OPCAO5,QUESTAO_CRIADOR,
                                                 QUESTAO_REVISOR,QUESTAO_DTCRIACAO,QUESTAO_DTMOD,QUESTAO_MATERIA,
                                                 QUESTAO_ASSUNTO,QUESTAO_DESCRITOR
                                          FROM QUESTAO WHERE QUESTAO_CDG = ".$questao_cdg." ");            
            
        } 
        
        public function ultima_questao_inserida(){
           return  $this->db->QuerySingleValue(' SELECT MAX(QUESTAO_CDG) FROM QUESTAO ');            
        }
        
        public function proximoid($ID){
            return $this->db->NextId('QUESTAO', 'QUESTAO_CDG',$ID );
        }
        
        public function anteriorid($ID){            
                return $this->db->PriorId('QUESTAO', 'QUESTAO_CDG',$ID, 'QUESTAO_CDG' , 'QUESTAO_CDG');            
        }

        

        public function getHTMLQuestoes($showCount = true, $SQL_com_link = true, $link = ''){                   
        	if ($this->db->last_result) {
			if ($this->db->RowCount() > 0) {
				$html = "";
				if ($showCount) $html = "Total de Questões: " . $this->db->RowCount() . "<br />\n";
				$html .= "<table cellpadding=\"2\" cellspacing=\"2\">\n";
				$this->db->MoveFirst();
				$header = false;
                                $linha_count = 1;
                                //loop para as linhas 
				while ($member = mysqli_fetch_object($this->db->last_result)) {
                                        $coluna_count = 1;
                                        //campos de cabecalho /linha 1
					if (!$header) {
						$html .= "\t<tr>\n";
						foreach ($member as $key => $value) {
                                                    if((!$SQL_com_link) && ($coluna_count == 1)){
							$html .= "\t\t<td class='cabecalho_table'><strong>" . htmlspecialchars($key) . "</strong></th>\n";
                                                    }                                                    
                                                    if($coluna_count > 1){
							$html .= "\t\t<td class='cabecalho_table'><strong>" . htmlspecialchars($key) . "</strong></th>\n";
                                                    }
                                                    $coluna_count += 1;
						}
						$html .= "\t</tr>\n";
						$header = true;
                                              
					}
                                        $coluna_count = 1;
					$html .= "\t<tr>\n";
                                        $filtro_da_pagina = ''; 
                                        $fecha_link =  "";
                                        $fecha_tag_a = "";                                        
					foreach ($member as $key => $value) {
                                                // se for a primeira coluna e sql com link, nao mostra a primeira coluna
                                                // e grava a 1º coluna como o link das linhas seguintes
                                                if( ($coluna_count == 1) && ($SQL_com_link == true)){
                                                    $filtro_da_pagina = htmlspecialchars($value);
                                                    $fecha_link =  "'>";
                                                    $fecha_tag_a = "</a>";
                                                }else{
                                                    //alinhar a segunda coluna para esquerda
                                                    $alinhamento = "";                                                        
                                                    if($coluna_count == 2 ){
                                                        $alinhamento = " class='texto_left' ";                                                            
                                                    }                                                       
                                                    $html .= "\t\t<td ".$alinhamento."><a href='".$link.$filtro_da_pagina .$fecha_link . htmlspecialchars($value) . $fecha_tag_a."</td>\n";
                                                        
                                                }
                                                $coluna_count += 1;
					}
					$html .= "\t</tr>\n";
                                        $linha_count += 1;
				}
				$this->db->MoveFirst();
				$html .= "</table>";
			} else {
				$html = "Nenhum registro.";
			}
		} else {
			$this->db->active_row = -1;
			$html = false;
		}
		return $html;   
    }
    
		 
     
      
         
 

          
          
        

	
	
}
?>

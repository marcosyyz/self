<?php


$historico = new Historico();
if(isset($_SESSION['TURMA_ATUAL'])) {
    echo '<p class="centro branco titulo"> ► Ranking Masculino '.$_SESSION['TURMA_ATUAL_NOME'].' ►  ';
    echo $historico->listar_ranking_sexo($_SESSION['TURMA_ATUAL'],'M');    
}else{
    //looping pelas turmas do prof
    for ($i = 0; $i < count($_SESSION['MINHAS_TURMAS_CDG']); $i++) {        
        echo '<p class="centro branco titulo"> ► Ranking Masculino '.$_SESSION['MINHAS_TURMAS_NOME'][$i].' ►  ';
        echo $historico->listar_ranking_sexo($_SESSION['MINHAS_TURMAS_CDG'][$i],'M');    
    }
}

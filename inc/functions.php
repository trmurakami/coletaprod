<?php
/**
 * Arquivo de classes e funções do ColetaProd
 */

include('functions_core/functions_core.php');

function pregReplaceVariableName($string) {

    $arrayString = explode("-", $string);
    $arrayString = array_map('ucwords', $arrayString);
    $result =  implode("", $arrayString);
    $result = lcfirst($result);
    return $result;
}

/**
 * Compara registros que estão entrando com os já existentes na base
 */
class compararRegistros {
    
    /**
    * Consulta registros por DOI
    * 
    * @param string $doi DOI
    * 
    */      
    public static function doi($doi) {
        global $index;
        global $client;
        global $type;        
        $body = '
            {
                "query":{
                    "match" : {
                        "doi": "'.$doi.'"
                    }
                }
            }
        ';    
        $response = elasticsearch::elastic_search($type,NULL,$size,$body);
        return $response; 
    }    
    
    /**
     * Consulta trabalhos em eventos já existentes
     * 
     * @param string $ano Ano
     * @param string $titulo Título do trabalho de evento
     * @param string $nome_do_evento Nome do evento
     * @param string $tipo Tipo do registro                                      
     * 
     */     
    public static function lattesEventos ($ano,$titulo,$nome_do_evento,$tipo) {
        global $index;
        global $client;        
        $body = '
        {
            "min_score": 30,
            "query":{
                "bool": {
                    "should": [
                        {
                            "multi_match" : {
                                "query":      "'.$tipo.'",
                                "type":       "cross_fields",
                                "fields":     [ "tipo" ],
                                "minimum_should_match": "100%" 
                             }
                        },		
                        {
                            "multi_match" : {
                                "query":      "'.$titulo.'",
                                "type":       "cross_fields",
                                "fields":     [ "titulo" ],
                                "minimum_should_match": "90%" 
                             }
                        },
                        {
                            "multi_match" : {
                                "query":      "'.$nome_do_evento.'",
                                "type":       "cross_fields",
                                "fields":     [ "evento.nome_do_evento" ],
                                "minimum_should_match": "80%" 
                             }
                        },		    
                        {
                            "multi_match" : {
                                "query":      "'.$ano.'",
                                "type":       "best_fields",
                                "fields":     [ "ano" ],
                                "minimum_should_match": "75%" 
                            }
                        }
                    ],
                    "minimum_should_match" : 1               
                }
            }
        }
        ';
        $type = "trabalhos";
        $response = elasticsearch::elastic_search($type,NULL,NULL,$body);
        return $response;
    }
    
    public static function lattesArtigos ($ano,$titulo,$titulo_do_periodico,$doi,$tipo) {
        global $index;
        global $client;        
        $body = '
            {
                "min_score": 10,
                "query":{
                    "bool": {
                        "should": [
                            {
                                "multi_match" : {
                                    "query":      "'.$tipo.'",
                                    "type":       "cross_fields",
                                    "fields":     [ "tipo" ],
                                    "minimum_should_match": "100%" 
                                 }
                            },
                            {
                                "multi_match" : {
                                    "query":      "'.$doi.'",
                                    "type":       "cross_fields",
                                    "fields":     [ "doi" ],
                                    "minimum_should_match": "100%" 
                                 }
                            },			    		
                            {
                                "multi_match" : {
                                    "query":      "'.$titulo.'",
                                    "type":       "cross_fields",
                                    "fields":     [ "titulo" ],
                                    "minimum_should_match": "90%" 
                                 }
                            },
                            {
                                "multi_match" : {
                                    "query":      "'.$titulo_do_periodico.'",
                                    "type":       "cross_fields",
                                    "fields":     [ "periodico.titulo_do_periodico" ],
                                    "minimum_should_match": "80%" 
                                 }
                            },		    
                            {
                                "multi_match" : {
                                    "query":      "'.$ano.'",
                                    "type":       "best_fields",
                                    "fields":     [ "ano" ],
                                    "minimum_should_match": "75%" 
                                }
                            }
                        ],
                        "minimum_should_match" : 3               
                    }
                }
            }
        ';   

        $type = "trabalhos";
        $response = elasticsearch::elastic_search($type,NULL,NULL,$body);
        return $response;
    }

    public static function lattesLivros ($titulo,$isbn,$tipo) {
        global $index;
        global $client;
        $body = '
        {
            "min_score": 10,
            "query":{
                "bool": {
                    "should": [
                        {
                            "multi_match" : {
                                "query":      "'.$tipo.'",
                                "type":       "cross_fields",
                                "fields":     [ "tipo" ],
                                "minimum_should_match": "100%" 
                             }
                        },
                        {
                            "multi_match" : {
                                "query":      "'.$isbn.'",
                                "type":       "cross_fields",
                                "fields":     [ "isbn" ],
                                "minimum_should_match": "100%" 
                             }
                        },			    		
                        {
                            "multi_match" : {
                                "query":      "'.$titulo.'",
                                "type":       "cross_fields",
                                "fields":     [ "titulo" ],
                                "minimum_should_match": "90%" 
                             }
                        }
                    ],
                    "minimum_should_match" : 2               
                }
            }
        }
        '; 
        $type = "trabalhos";
        $response = elasticsearch::elastic_search($type,NULL,NULL,$body);
        return $response;
    }
    
    public static function lattesCapitulos ($titulo,$titulo_do_livro,$tipo) {
        global $index;
        global $client;
        $body = '
            {
                "min_score": 2,
                "query":{
                    "bool": {
                        "should": [
                            {
                                "multi_match" : {
                                    "query":      "'.$tipo.'",
                                    "type":       "cross_fields",
                                    "fields":     [ "tipo" ],
                                    "minimum_should_match": "100%" 
                                 }
                            },		    		
                            {
                                "multi_match" : {
                                    "query":      "'.$titulo.'",
                                    "type":       "cross_fields",
                                    "fields":     [ "titulo" ],
                                    "minimum_should_match": "90%" 
                                 }
                            },
                            {
                                "multi_match" : {
                                    "query":      "'.$titulo_do_livro.'",
                                    "type":       "cross_fields",
                                    "fields":     [ "capitulo_do_livro.titulo_do_livro" ],
                                    "minimum_should_match": "90%" 
                                 }
                            }                    
                        ],
                        "minimum_should_match" : 3               
                    }
                }
            }
        ';
        $type = "trabalhos";
        $response = elasticsearch::elastic_search($type,NULL,NULL,$body);
        return $response;
    }
    
    public static function lattesMidiaSocial ($titulo,$url,$tipo) {
        global $index;
        global $client;
        $body = '
        {
            "min_score": 3,
            "query":{
                "bool": {
                    "should": [
                        {
                            "multi_match" : {
                                "query":      "'.$tipo.'",
                                "type":       "cross_fields",
                                "fields":     [ "tipo" ],
                                "minimum_should_match": "100%" 
                             }
                        },		    		
                        {
                            "multi_match" : {
                                "query":      "'.$titulo.'",
                                "type":       "cross_fields",
                                "fields":     [ "titulo" ],
                                "minimum_should_match": "90%" 
                             }
                        },
                        {
                            "multi_match" : {
                                "query":      "'.$url.'",
                                "type":       "cross_fields",
                                "fields":     [ "url" ],
                                "minimum_should_match": "100%" 
                             }
                        }                    
                    ],
                    "minimum_should_match" : 3               
                }
            }
        }
        ';
        $type = "trabalhos";
        $response = elasticsearch::elastic_search($type,NULL,NULL,$body);
        return $response;
    }    

    public static function match_id ($_id,$nota) {
        $fields = ['titulo','tipo','ano'];
        $response = elasticsearch::elastic_get($_id,"trabalhos",$fields);

        echo '<div class="uk-alert uk-alert-danger">';
        echo '<h3>Registros similares no Coleta Produção USP</h3>';
            echo '<p><a href="result_trabalhos.php?&search[]=+_id:&quot;'.$_id.'&quot;">'.$response["_source"]["tipo"].' - '.$response["_source"]["titulo"].' ('.$response["_source"]["ano"].') - Nota de proximidade: '.$nota.'</a></p>';
        echo '</div>';

    }    
    
    
}

/**
 * Funções executadas na página principal
 */
class paginaInicial {
    
    static function contar_tipo_de_registro($type) {
        $body = '
            {
                "query": {
                    "match_all": {}
                }
            }        
        ';    
        $size = 0;
        $response = elasticsearch::elastic_search($type,NULL,$size,$body);
        return number_format($response['hits']['total'],0,',','.');
    } 

    static function contar_registros_match ($type) {
        $body = '
            {
                "query": {
                    "exists" : { "field" : "ids_match" }
                }
            }          
        ';
        $size = 0;
        $response = elasticsearch::elastic_search($type,NULL,$size,$body);
        return number_format($response['hits']['total'],0,',','.');
    }
    
    static function fonte_inicio() {
        global $client;
        global $index;
        $query = '{
            "aggs": {
                "group_by_state": {
                    "terms": {
                        "field": "source.keyword",                    
                        "size" : 100
                    }
                }
            }
        }';

        $params = [
            'index' => $index,
            'type' => 'trabalhos',
            'size'=> 0,
            'body' => $query
        ];    

        $response = $client->search($params);
        foreach ($response["aggregations"]["group_by_state"]["buckets"] as $facets) {
            echo '<li><a href="result_trabalhos.php?search[]=source.keyword:&quot;'.$facets['key'].'&quot;">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
        }   

    }
    
    static function tipo_inicio() {
        global $client;
        global $index;
        $query = '{
            "aggs": {
                "group_by_state": {
                    "terms": {
                        "field": "tipo.keyword",                    
                        "size" : 50
                    }
                }
            }
        }';

        $params = [
            'index' => $index,
            'type' => 'trabalhos',
            'size'=> 0,
            'body' => $query
        ];    

        $response = $client->search($params);
        foreach ($response["aggregations"]["group_by_state"]["buckets"] as $facets) {
            echo '<li><a href="result_trabalhos.php?search[]=tipo.keyword:&quot;'.$facets['key'].'&quot;">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
        }   

    }

    static function unidadeUSP_inicio() {
        global $client;
        global $index;
        $query = '{
            "aggs": {
                "group_by_state": {
                    "terms": {
                        "field": "unidadeUSP.keyword",                    
                        "size" : 200
                    }
                }
            }
        }';

        $params = [
            'index' => $index,
            'type' => 'trabalhos',
            'size'=> 0,
            'body' => $query
        ];    

        $response = $client->search($params);
        foreach ($response["aggregations"]["group_by_state"]["buckets"] as $facets) {
            echo '<li><a href="result_trabalhos.php?search[]=unidadeUSP.keyword:&quot;'.$facets['key'].'&quot;">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
        }   

    }           
    
}

/**
 * Classe que obtem dados de fontes externas
 */
class dadosExternos {
    
    static function query_bdpi($query_title,$query_year,$sha256) {  
        
        $query_title =  str_replace('"','',$query_title);
        $query = '
        {
            "min_score": 35,
            "query":{
                "bool": {
                    "should": [	
                        {
                            "multi_match" : {
                                "query":      "'.$query_title.'",
                                "type":       "cross_fields",
                                "fields":     [ "name" ],
                                "minimum_should_match": "85%" 
                            }
                        },	    
                        {
                            "multi_match" : {
                                "query":      "'.$query_year.'",
                                "type":       "best_fields",
                                "fields":     [ "datePublished" ],
                                "operator":   "and",
                                "minimum_should_match": "75%" 
                            }
                        }
                    ],
                    "minimum_should_match" : 2               
                }
            }
        }
        ';

        $ch = curl_init();
        $method = "POST";
        $url = "http://172.31.0.90/sibi/producao/_search";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, 9200);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);

        if ($data["hits"]["total"] > 0){
            echo '<div class="uk-alert">';
            echo '<h3>Registros similares na BDPI</h3>';
            foreach ($data["hits"]["hits"] as $match){
                echo '<p>Nota de proximidade: '.$match["_score"].' - <a href="http://bdpi.usp.br/single.php?_id='.$match["_id"].'">'.$match["_source"]["type"].' - '.$match["_source"]["name"].' ('.$match["_source"]["datePublished"].')</a><br/> Autores: ';   
                foreach ($match["_source"]['author'] as $autores) {
                    echo ''.$autores['person']['name'].', ';
                }
                if (isset($match["_source"]["doi"])){
                    $doc["doc"]["bdpi"]["doi_bdpi"] = $match["_source"]["doi"];
                } else {
                    
                }
                echo '</p>';
            }
            echo '</div>';            

            $doc["doc"]["bdpi"]["existe"] = "Sim";
            $doc["doc_as_upsert"] = true;
            //print_r($doc);
            $result_elastic = elasticsearch::elastic_update($sha256,"trabalhos",$doc);
        }
        return $data;
    }

    static function query_bdpi_index($query_title,$query_year) {        
        $query = '
        {
            "min_score": 50,
            "query":{
                "bool": {
                    "should": [	
                        {
                            "multi_match" : {
                                "query":      "'.str_replace('"','',$query_title).'",
                                "type":       "cross_fields",
                                "fields":     [ "name" ],
                                "minimum_should_match": "90%" 
                             }
                        },	    
                        {
                            "multi_match" : {
                                "query":      "'.$query_year.'",
                                "type":       "best_fields",
                                "fields":     [ "datePublished" ],
                                "minimum_should_match": "75%" 
                            }
                        }
                    ],
                    "minimum_should_match" : 1               
                }
            }
        }
        ';

        $ch = curl_init();
        $method = "POST";
        $url = "http://172.31.0.90/sibi/producao/_search";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, 9200);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);
        $facet_bdpi = [];
        if ($data["hits"]["total"] > 0){
            $facet_bdpi["existe"] = "Sim";
            foreach ($data["hits"]["hits"] as $match){
                if (isset($match["_source"]["doi"])){
                    $facet_bdpi["doi_bdpi"] = $match["_source"]["doi"];
                }
            }
        } else {
            $facet_bdpi["existe"] = "Não";
        }
        return $facet_bdpi;
    }    

    static function coleta_json_lattes($id_lattes) {

        $ch = curl_init();
        $method = "GET";
        $url = "http://buscacv.cnpq.br/buscacv/rest/espelhocurriculo/$id_lattes";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        $tentativas = 0;
        while($tentativas < 3){        
            $result = curl_exec($ch);
            $info = curl_getinfo($ch);
            if ($info["http_code"] == 200) {
                var_dump($info);
                $data = json_decode($result, TRUE);
                curl_close($ch);
                return $data;
            } else {
                $tentativas++;
            }
        }


        echo '<br/><br/><br/><h2>Erro '.$info["http_code"].' ao obter o arquivo da Base do Lattes, favor tentar novamente. <a href="index.php">Clique aqui para voltar a página inicial</a></h2>';
        //var_dump($info);
        curl_close($ch);
    }

    static function coleta_json_download_lattes($id_lattes) {

        $result = file_get_contents($id_lattes);
        $data = json_decode($result, TRUE);
        return $data;

    }

    static function query_doi($doi,$tag) {
	    global $client; 
        global $index;
        $url = "https://api.crossref.org/v1/works/http://dx.doi.org/$doi";
        $json = file_get_contents($url);
        $data = json_decode($json, TRUE);

        $sha256 = hash('sha256', ''.$doi.'');
	
	    print_r($data);

        $doc_obra_array["doc"]["source"] = "Base DOI - CrossRef";
        $doc_obra_array["doc"]["source_id"] = $doi;
        $doc_obra_array["doc"]["doi"] = $doi;     
        $doc_obra_array["doc"]["tag"][] = $tag;
        
        if ($data["message"]["type"] == "journal-article") {
		$doc_obra_array["doc"]["tipo"] = "Artigo publicado";
	    
		if(isset($data["message"]["container-title"][0])){
		    $doc_obra_array["doc"]["artigoPublicado"]["tituloDoPeriodicoOuRevista"] = $data["message"]["container-title"][0];
		}
		if(isset($data["message"]["ISSN"][0])){
		    $doc_obra_array["doc"]["artigoPublicado"]["issn"] = $data["message"]["ISSN"][0];
		}      
		if(isset($data["message"]["volume"])){
		    $doc_obra_array["doc"]["artigoPublicado"]["volume"] = $data["message"]["volume"];
		}         
		if(isset($data["message"]["issue"])){
		    $doc_obra_array["doc"]["artigoPublicado"]["fasciculo"] = $data["message"]["issue"];
		}      
		if(isset($data["message"]["page"])){
		    $doc_obra_array["doc"]["artigoPublicado"]["paginaInicial"] = $data["message"]["page"];
		}      
		if(isset($data["message"]["publisher"])){
		    $doc_obra_array["doc"]["artigoPublicado"]["nomeDaEditora"] = $data["message"]["publisher"];
		}
		if(isset($data["message"]["cited-count"])){
		    $doc_obra_array["doc"]["citacoes_recebidas"] = $data["message"]["cited-count"];
		}   	    
	    } elseif ($data["message"]["type"] == "book") {    
		$doc_obra_array["doc"]["tipo"] = "Livros publicados ou organizados";
		if(isset($data["message"]["publisher"])){
		    $doc_obra_array["doc"]["detalhamentoDoLivro"]["nomeDaEditora"] = $data["message"]["publisher"];
		}
	    
        } else {
            $doc_obra_array["doc"]["tipo"] = $data["message"]["type"];
        }
        
        /* Título */
        $doc_obra_array["doc"]["titulo"] = str_replace('"','',trim($data["message"]["title"][0]));
        $doc_obra_array["doc"]["titulo"] = str_replace('\'',' ',trim($data["message"]["title"][0]));
        $doc_obra_array["doc"]["titulo"] = str_replace("\n","",$doc_obra_array["doc"]["titulo"]);
        
        if(isset($data["message"]["subtitle"][0])){
            $doc_obra_array["doc"]["subtitulo"] = $data["message"]["subtitle"][0];
        }    
        if(isset($data["message"]["published-online"]["date-parts"][0][0])){
            $doc_obra_array["doc"]["ano"] = $data["message"]["published-online"]["date-parts"][0][0];
        }     
        if(isset($data["message"]["URL"])){
            $doc_obra_array["doc"]["url"] = $data["message"]["URL"];
        }
        $doc_obra_array["doc"]["doi"] = $doi;

  
        if(isset($data["message"]["subject"])){
            foreach ($data["message"]["subject"]  as $assunto) {	
            $doc_obra_array["doc"]["palavras_chave"][] = $assunto;
            }
        }

        $i = 0;
        foreach ($data["message"]["author"]  as $autores) {
            $doc_obra_array["doc"]["autores"][$i]["nomeCompletoDoAutor"] = $autores["given"]." ".$autores["family"];
            $doc_obra_array["doc"]["autores"][$i]["nomeParaCitacao"] = $autores["family"].", ".$autores["given"];
	    if (isset($autores["ORCID"])) {
		    $doc_obra_array["doc"]["autores"][$i]["id_orcid"] = $autores["ORCID"];
	    }
            $i++;
        }

        $doc_obra_array["doc"]["concluido"] = "Não";
        $doc_obra_array["doc_as_upsert"] = true;

        // Retorna resultado
	    echo '<br/><br/><br/>';
	    print_r($doc_obra_array);

        $body = json_encode($doc_obra_array, JSON_UNESCAPED_UNICODE); 

        $resultado_crossref = elasticsearch::store_record($sha256,"trabalhos",$body);
        print_r($resultado_crossref);
    }    
    
}

/**
 * Classe que processa dados do JSON obtido da Base Lattes
 */
class processaLattes {
    
    static function processaFormacaoAcaddemica($dados,$nivel,$campos,$autor,$id_lattes) {  
        $i = 0;
        foreach ($dados as $curso) {
            foreach ($campos as $nivel_campos) {
                if (!empty($curso[$nivel_campos])) {
                    $doc_curriculo_array["doc"]["formacao_academica_titulacao_$nivel"][$i][$nivel_campos] = $curso[$nivel_campos];                   
                }                    
            }                      
        $i++;
        }        
        foreach ($dados as $curso) {
            if ($curso["statusDoCurso"] == "CONCLUIDO"){
                foreach ($campos as $nivel_campos) {
                    if (!empty($curso[$nivel_campos])) {
                        $doc_tese["doc"]["tese"][$nivel_campos] = $curso[$nivel_campos];                   
                    }                    
                }
                $doc_tese["doc"]["tese"]["nivel"] = $nivel;
                $doc_tese["doc"]["tese"]["autor"] = $autor;
                $doc_tese["doc"]["tese"]["id_lattes"] = $id_lattes;
                $doc_tese["doc_as_upsert"] = true;
                $sha256_tese = hash('sha256', ''.$id_lattes.$curso["sequenciaFormacao"].'');
                $doc_tese_json = json_encode($doc_tese, JSON_UNESCAPED_UNICODE);
                elasticsearch::elastic_update($sha256_tese,"teses",$doc_tese_json);                
            }
        }

        
        return $doc_curriculo_array;
    }    

    static function processaObra($obra,$tipo_de_obra,$tag,$id_lattes,$unidadeUSP,$codpes) {
        switch ($tipo_de_obra) {

            case "trabalhoEmEventos":
                $tipo_de_obra_nome = "Trabalhos em eventos";
                $campos_dadosBasicosDoTrabalho = ["natureza","tituloDoTrabalho","anoDoTrabalho","paisDoEvento","idioma","meioDeDivulgacao","homePageDoTrabalho","flagRelevancia","flagDivulgacaoCientifica"];
                $campos_detalhamentoDoTrabalho = ["classificacaoDoEvento","nomeDoEvento","cidadeDoEvento","anoDeRealizacao","tituloDosAnaisOuProceedings","paginaInicial","paginaFinal","doi","isbn","nomeDaEditora","cidadeDaEditora","volumeDosAnais","fasciculoDosAnais","serieDosAnais"];
                $resultado_comparador_local = compararRegistros::lattesEventos($obra["dadosBasicosDoTrabalho"]["anoDoTrabalho"],str_replace('"','',$obra["dadosBasicosDoTrabalho"]["tituloDoTrabalho"]),str_replace('"','',$obra["detalhamentoDoTrabalho"]["nomeDoEvento"]),"Trabalhos em eventos");
                $dadosBasicosNomeCampo = "dadosBasicosDoTrabalho";
                $detalhamentoNomeCampo = "detalhamentoDoTrabalho";
                $campos_sha256 = ["natureza","tituloDoTrabalho","anoDoTrabalho","paisDoEvento","nomeDoEvento","paginaInicial","homePageDoTrabalho"];

                break;

            case "artigoPublicado":
                $tipo_de_obra_nome = "Artigo publicado";
                $campos_dadosBasicosDoTrabalho = ["natureza","tituloDoArtigo","anoDoArtigo","idioma","meioDeDivulgacao","homePageDoTrabalho","flagRelevancia","doi","tituloDoArtigoIngles","flagDivulgacaoCientifica"];
                $campos_detalhamentoDoTrabalho = ["tituloDoPeriodicoOuRevista","issn","volume","serie","paginaInicial","paginaFinal","localDePublicacao"];            
                $dadosBasicosNomeCampo = "dadosBasicosDoArtigo";
                $detalhamentoNomeCampo = "detalhamentoDoArtigo";
                if (isset($obra["dadosBasicosDoArtigo"]["doi"])){
                    $campos_sha256 = ["doi"];
                    $resultado_comparador_local = compararRegistros::lattesArtigos($obra["dadosBasicosDoArtigo"]["anoDoArtigo"],str_replace('"','',$obra["dadosBasicosDoArtigo"]["tituloDoArtigo"]),str_replace('"','',$obra["detalhamentoDoArtigo"]["tituloDoPeriodicoOuRevista"]),$obra["dadosBasicosDoArtigo"]["doi"],"ARTIGO-PUBLICADO");
                } else {
                    $campos_sha256 = ["natureza","tituloDoArtigo","anoDoArtigo","tituloDoPeriodicoOuRevista","nomeDoEvento","paginaInicial","homePageDoTrabalho"];
                    $resultado_comparador_local = compararRegistros::lattesArtigos($obra["dadosBasicosDoArtigo"]["anoDoArtigo"],str_replace('"','',$obra["dadosBasicosDoArtigo"]["tituloDoArtigo"]),str_replace('"','',$obra["detalhamentoDoArtigo"]["tituloDoPeriodicoOuRevista"]),NULL,"Artigo publicado");
                }
                break;

            case "livrosPublicadosOuOrganizado":       
                $tipo_de_obra_nome = "Livros publicados ou organizados";
                $campos_dadosBasicosDoTrabalho = ["natureza","tituloDoLivro","ano","paisDePublicacao","idioma","meioDeDivulgacao","homePageDoTrabalho","flagRelevancia","flagDivulgacaoCientifica"];
                $campos_detalhamentoDoTrabalho = ["numeroDeVolumes","numeroDePaginas","isbn","numeroDaEdicaoRevisao","cidadeDaEditora","nomeDaEditora"];            
                $dadosBasicosNomeCampo = "dadosBasicosDoLivro";
                $detalhamentoNomeCampo = "detalhamentoDoLivro";
                if (isset($obra["dadosBasicosDoLivro"]["isbn"])){
                    $campos_sha256 = ["isbn"];
                    $resultado_comparador_local = compararRegistros::lattesLivros(str_replace('"','',$obra["dadosBasicosDoLivro"]["tituloDoLivro"]),str_replace('"','',$obra["detalhamentoDoLivro"]["isbn"]),"LIVRO-PUBLICADO");
                } else {
                    $campos_sha256 = ["natureza","tituloDoLivro"];
                    $resultado_comparador_local = compararRegistros::lattesLivros(str_replace('"','',$obra["dadosBasicosDoLivro"]["tituloDoLivro"]),NULL,"Livros publicados ou organizados");
                }
                break;

            case "capituloDeLivroPublicado":       
                $tipo_de_obra_nome = "Capítulo de livro publicado";
                $campos_dadosBasicosDoTrabalho = ["tituloDoCapituloDoLivro","ano","paisDePublicacao","idioma","meioDeDivulgacao","homePageDoTrabalho","flagRelevancia","tituloDoCapituloDoLivroIngles","flagDivulgacaoCientifica"];
                $campos_detalhamentoDoTrabalho = ["tituloDoLivro","paginaInicial","paginaFinal","isbn","organizadores","numeroDaEdicaoRevisao","cidadeDaEditora","nomeDaEditora"];            
                $dadosBasicosNomeCampo = "dadosBasicosDoCapitulo";
                $detalhamentoNomeCampo = "detalhamentoDoCapitulo";
                $campos_sha256 = ["natureza","tituloDoCapituloDoLivro","isbn"];
                $resultado_comparador_local = compararRegistros::lattesCapitulos(str_replace('"','',$obra["dadosBasicosDoCapitulo"]["tituloDoCapituloDoLivro"]),str_replace('"','',$obra["detalhamentoDoCapitulo"]["tituloDoLivro"]),"Capítulo de livro publicado");
                break;

            case "textoEmJornalOuRevista":       
                $tipo_de_obra_nome = "Textos em jornais de notícias/revistas";
                $campos_dadosBasicosDoTrabalho = ["natureza","tituloDoTexto","anoDoTexto","paisDePublicacao","idioma","meioDeDivulgacao","flagRelevancia","flagDivulgacaoCientifica"];
                $campos_detalhamentoDoTrabalho = ["tituloDoJornalOuRevista","formatoDataDePublicacao","dataDePublicacao","dataPublicacaoFormatoSimples"];            
                $dadosBasicosNomeCampo = "dadosBasicosDoTexto";
                $detalhamentoNomeCampo = "detalhamentoDoTexto";
                $campos_sha256 = ["natureza","tituloDoTexto","tituloDoJornalOuRevista"];                
                break;                

            case "midiaSocialWebsiteBlog":       
                $tipo_de_obra_nome = "Mídia Social ou Website ou Blog";
                $campos_dadosBasicosDoTrabalho = ["natureza","titulo","ano","pais","idioma","homePage","flagRelevancia","flagDivulgacaoCientifica"];
                $campos_detalhamentoDoTrabalho = ["tema"];            
                $dadosBasicosNomeCampo = "dadosBasicosDaMidiaSocialWebsiteBlog";
                $detalhamentoNomeCampo = "detalhamentoDaMidiaSocialWebsiteBlog";
                $campos_sha256 = ["natureza","titulo","homePage"];
                $resultado_comparador_local = compararRegistros::lattesMidiaSocial(str_replace('"','',$obra["dadosBasicosDaMidiaSocialWebsiteBlog"]["titulo"]),$obra["dadosBasicosDaMidiaSocialWebsiteBlog"]["homePage"],"Mídia Social ou Website ou Blog");
                break; 
                
            case "outraProducaoArtisticaCultural":       
                $tipo_de_obra_nome = "Outra produção Artística Cultural";
                $campos_dadosBasicosDoTrabalho = ["natureza","titulo","ano","pais","idioma","meioDeDivulgacao","homePage","flagRelevancia","flagDivulgacaoCientifica"];
                $campos_detalhamentoDoTrabalho = ["instituicaoPromotoraDoEvento","localDoEvento","cidade"];            
                $dadosBasicosNomeCampo = "dadosBasicosDeOutraProducaoArtisticaCultural";
                $detalhamentoNomeCampo = "detalhamentoDeOutraProducaoArtisticaCultural";
                $campos_sha256 = ["natureza","titulo","homePage"];
                $resultado_comparador_local = compararRegistros::lattesMidiaSocial(str_replace('"','',$obra["dadosBasicosDeOutraProducaoArtisticaCultural"]["titulo"]),$obra["dadosBasicosDeOutraProducaoArtisticaCultural"]["homePage"],"Outra produção Artística Cultural");
                $doc_obra_array["doc"]["informacoesAdicionais"]["descricaoInformacoesAdicionais"] = str_replace('"','',$obra["informacoesAdicionais"]["descricaoInformacoesAdicionais"]);
                break;
                
            case "artesVisuais":       
                $tipo_de_obra_nome = "Artes visuais";
                $campos_dadosBasicosDoTrabalho = ["natureza","titulo","ano","pais","idioma","meioDeDivulgacao","homePage","flagRelevancia","flagDivulgacaoCientifica"];
                $campos_detalhamentoDoTrabalho = ["instituicaoPromotoraDoEvento","localDoEvento","cidade"];            
                $dadosBasicosNomeCampo = "dadosBasicosDeArtesVisuais";
                $detalhamentoNomeCampo = "detalhamentoDeArtesVisuais";
                $campos_sha256 = ["natureza","titulo","homePage"];
                print_r(stripslashes($obra["dadosBasicosDeArtesVisuais"]["titulo"]));
                $resultado_comparador_local = compararRegistros::lattesMidiaSocial(stripslashes(str_replace('"','',$obra["dadosBasicosDeArtesVisuais"]["titulo"])),$obra["dadosBasicosDeArtesVisuais"]["homePage"],"Artes visuais");
                $doc_obra_array["doc"]["informacoesAdicionais"]["descricaoInformacoesAdicionais"] = str_replace('"','',$obra["informacoesAdicionais"]["descricaoInformacoesAdicionais"]);
                break;                     

        }

        $doc_obra_array["doc"]["tipo"] = $tipo_de_obra_nome;
        $doc_obra_array["doc"]["source"] = "Base Lattes";
        $doc_obra_array["doc"]["lattes_ids"][] = $id_lattes;
        $doc_obra_array["doc"]["tag"][] = $tag;
        $doc_obra_array["doc"]["unidadeUSP"][] = $unidadeUSP;
        $doc_obra_array["doc"]["codpes"] = $codpes;       

        $titulos_array = ["tituloDoTrabalho","tituloDoArtigo","tituloDoLivro","tituloDoCapituloDoLivro","tituloDoTexto"];
        $ano_array = ["anoDoTrabalho","anoDoArtigo","anoDoTexto"];
        foreach ($campos_dadosBasicosDoTrabalho as $dados_basicos) {
            if (isset($obra[$dadosBasicosNomeCampo][$dados_basicos])) {
                $doc_obra_array["doc"][$dados_basicos] = $obra[$dadosBasicosNomeCampo][$dados_basicos];
            }
            if (in_array($dados_basicos,$titulos_array)) {
                $doc_obra_array["doc"]["titulo"] = $obra[$dadosBasicosNomeCampo][$dados_basicos];
            }
            if (in_array($dados_basicos,$ano_array)) {
                $doc_obra_array["doc"]["ano"] = $obra[$dadosBasicosNomeCampo][$dados_basicos];
            }        
        }

        foreach ($campos_detalhamentoDoTrabalho as $detalhamento) {
            if (isset($obra[$detalhamentoNomeCampo][$detalhamento])){
                $doc_obra_array["doc"][$tipo_de_obra][$detalhamento] = $obra[$detalhamentoNomeCampo][$detalhamento];
            }

        }
        
        if (isset($obra["autores"])){
            $array_result = self::processaAutoresLattes ($obra["autores"]);    
            $doc_obra_array = array_merge_recursive($doc_obra_array,$array_result);            
        }


        if (isset($obra["palavrasChave"])){
            $array_result = self::processaPalavrasChaveLattes ($obra["palavrasChave"]);
            $doc_obra_array = array_merge_recursive($doc_obra_array,$array_result);
        }

        if (isset($obra["areasDoConhecimento"])){
            $array_result = self::processaAreaDoConhecimentoLattes ($obra["areasDoConhecimento"]);
            $doc_obra_array = array_merge_recursive($doc_obra_array,$array_result);
        }

        // Constroi sha256
        $sha256 = self::constroi_sha256 ($obra,$campos_sha256,$dadosBasicosNomeCampo,$detalhamentoNomeCampo);     

        // Comparador Local
        $i = 0;
        if (!empty($resultado_comparador_local["hits"]["hits"]) ) {
            foreach ($resultado_comparador_local["hits"]["hits"] as $result1) {                
                if ($result1["_id"] != $sha256){
                    if (!empty($result1["_id"])){
                        $doc_obra_array["doc"]["ids_match"][$i]["id_match"] = $result1["_id"];
                    }
                    if (isset($result1["_score"])){
                        $doc_obra_array["doc"]["ids_match"][$i]["nota"] = $result1["_score"];
                    }
                }
                $i++;
            }
        }


        $doc_obra_array["doc"]["bdpi"] = dadosExternos::query_bdpi_index($doc_obra_array["doc"]["titulo"],$doc_obra_array["doc"]["ano"]);
        $doc_obra_array["doc"]["concluido"] = "Não";
        $doc_obra_array["doc_as_upsert"] = true;

        // Retorna resultado

        //$body = json_encode($doc_obra_array, JSON_UNESCAPED_UNICODE);
        $body = $doc_obra_array;  
        print_r($body);

        return compact ('body','sha256');
    }    
    
    static function processaAutoresLattes($autores_array) {
        $i = 0;
        foreach ($autores_array as $autor) {
            $autor_campos = ["nomeCompletoDoAutor","nomeParaCitacao","ordemDeAutoria","nroIdCnpq"];
            foreach ($autor_campos as $campos){
                if (isset($autor[$campos])){
                    $array_result["doc"]["autores"][$i][$campos] = $autor[$campos];
                }
                if (isset($autor["nroIdCnpq"])){
                    $array_result["doc"]["lattes_ids"][] = $autor["nroIdCnpq"];
                }              
            }        
            $i++;
        }
        return $array_result;
    }
    
    static function processaPalavrasChaveLattes($palavras_chave) {
        foreach (range(1, 6) as $number) {
            if (isset($palavras_chave["palavraChave$number"])){
                $array_result["doc"]["palavras_chave"][] = $palavras_chave["palavraChave$number"];
            }
        }
        return $array_result;
    }    
 
    static function processaAreaDoConhecimentoLattes($areas_do_conhecimento) {
        $campos = ["nomeGrandeAreaDoConhecimento","nomeDaAreaDoConhecimento","nomeDaSubAreaDoConhecimento","nomeDaEspecialidade"];
        $i = 0;
        foreach ($areas_do_conhecimento as $ac) {
            foreach ($campos as $c){
                if (isset($ac[$c])){
                    $array_result["doc"]["area_do_conhecimento"][$i][$c] = $ac[$c];
                }
            } 
            $i++;
        }
        return $array_result;     
    }    
    
    static function constroi_sha256 ($obra,$campos_sha256,$dadosBasicosNomeCampo,$detalhamentoNomeCampo) {
        $sha_array = [];

        foreach ($campos_sha256 as $campos){
            if (isset($obra[$dadosBasicosNomeCampo][$campos])){
                $sha_array[] = $obra[$dadosBasicosNomeCampo][$campos];
            } elseif (isset($obra[$detalhamentoNomeCampo][$campos])){
                $sha_array[] = $obra[$detalhamentoNomeCampo][$campos];
            }       
        }
        $sha256 = hash('sha256', ''.implode("",$sha_array).'');
        return $sha256;               
    }    
    
}

/**
 * Classe que processa dados obtidos por meio de servidores z39.50
 */
class z3950 {
    
    static function parse_usmarc_string($record){
        $ret = array();
        // there was a case where angle brackets interfered
        $record = str_replace(array("<", ">"), array("",""), $record);
        //$record = utf8_decode($record);
        // split the returned fields at their separation character (newline)
        $record = explode("\n",$record);
        //examine each line for wanted information (see USMARC spec for details)
        foreach($record as $category){
            // subfield indicators are preceded by a $ sign
            $parts = explode("$", $category);
            // remove leading and trailing spaces
            array_walk($parts, "z3950::custom_trim");
            // the first value holds the field id,
            // depending on the desired info a certain subfield value is retrieved
            switch(substr($parts[0],0,3)){
                case "008" : $ret["language"] = substr($parts[0],39,3); break;
                case "020" : $ret["isbn"] = z3950::get_subfield_value($parts,"a"); break;
                case "022" : $ret["issn"] = z3950::get_subfield_value($parts,"a"); break;
                case "100" : $ret["author"] = z3950::get_subfield_value($parts,"a"); break;
                case "245" : $ret["title"] = z3950::get_subfield_value($parts,"a");
                             $ret["subtitle"] = z3950::get_subfield_value($parts,"b"); break;
                case "250" : $ret["edition"] = z3950::get_subfield_value($parts,"a"); break;
                case "260" : $ret["pub_date"] = z3950::get_subfield_value($parts,"c");
                             $ret["pub_place"] = z3950::get_subfield_value($parts,"a");
                             $ret["publisher"] = z3950::get_subfield_value($parts,"b"); break;
                case "300" : $ret["extent"] = z3950::get_subfield_value($parts,"a");
                             $ext_b = z3950::get_subfield_value($parts,"b");
                             $ret["extent"] .= ($ext_b != "") ? (" : " . $ext_b) : "";
                             break;
                case "490" : $ret["series"] = z3950::get_subfield_value($parts,"a"); break;
                case "502" : $ret["diss_note"] = z3950::get_subfield_value($parts,"a"); break;
                case "700" : $ret["editor"] = z3950::get_subfield_value($parts,"a"); break;
            }
        }
        return $ret;
    }

    // fetches the value of a certain subfield given its label
    static function get_subfield_value($parts, $subfield_label){
        $ret = "";
        foreach ($parts as $subfield)
            if(substr($subfield,0,1) == $subfield_label)
                $ret = substr($subfield,2);
        return $ret;
    }

    // wrapper function for trim to pass it to array_walk
    static function custom_trim(& $value, & $key){
        $value = trim($value);
    }
    
    static function query_z3950($query,$host,$host_name,$type) {
        if ($type == "isbn") {
            $query_data='@attr 1=7 '.$query.'';
        } elseif ($type == "title") {            
            if ((!empty($query[0])) && (!empty($query[1])) && (!empty($query[2]))){
                $query_data = '@attrset gils @and @attr 1=4 @attr 2=3 '.$query[0].' @attr 1=1003 @attr 2=3 '.$query[1].' @attr 1=31 @attr 2=3 '.$query[2].'';
            } elseif ((!empty($query[0])) && (!empty($query[1]))) {
                $query_data='@attrset gils @and @attr 1=4 @attr 2=3 '.$query[0].' @attr 1=1003 @attr 2=3 '.$query[1].'';
            } elseif ((!empty($query[0])) && (!empty($query[2]))) {
                $query_data = '@attrset gils @and @attr 1=4 @attr 2=3 '.$query[0].' @attr 1=31 @attr 2=3 '.$query[2].'';
            } elseif ((!empty($query[1])) && (!empty($query[2])) && (empty($query[0])) ) {
                $query_data = '@attrset gils @and @attr 1=1003 @attr 2=3 '.$query[1].' @attr 1=31 @attr 2=3 '.$query[2].'';     
            } else {
                $query_data='@attrset gils @attr 1=4 '.$query[0].'';
            }
            //print_r($query_data);

            
        } elseif ($type == "sysno") {
            $query_data='@attr 1=12 '.$query.'';
        }
            
        $id = yaz_connect($host);
        yaz_range($id, 1, 10);
        yaz_syntax($id, "usmarc");        
        yaz_search($id, "rpn", $query_data);    
        yaz_wait();
        
        $error = yaz_error($id);

        if (!empty($error)) {
            echo "$host_name error: $error";
        } else {
            $hits = yaz_hits($id);
            echo "<p>$host_name - $hits resultado(s) </p>";

            if ($hits >= 1){

       


                for ($p = 1; $p <= $hits; $p++) {

                    echo '<ul class="uk-subnav uk-subnav-pill" uk-switcher>
                    <li><a href="#">Resumo</a></li>
                    <li><a href="#">Registro completo</a></li>
                    </ul>         
                    <ul class="uk-switcher uk-margin">';

                    echo '<li><table class="uk-table">    
                        <thead>
                            <tr>
                                <th>Fonte</th>    
                                <th>ISBN</th>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Editora</th>
                                <th>Local</th>
                                <th>Ano</th>
                                <th>Edição</th>
                                <th>Descrição física</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>    
                    ';        



                    $rec = yaz_record($id, $p, "string");
                    //print_r($rec);
                    $result_record = z3950::parse_usmarc_string($rec);
                    //print_r($result_record);
                    $rec_download = yaz_record($id, $p, "raw");
                    $rec_download = str_replace('"','',$rec_download);            
                    echo '<tr>';
                    echo '<th>'.$host_name.'</th>';
                    if (isset($result_record["isbn"])) {
                        echo '<td>'.$result_record["isbn"].'</td>';
                    } else {
                        echo '<td></td>';
                    }                    
                    echo '<td>'.$result_record["title"].'</td>';
                    
                    if (!empty($result_record["author"])) {
                        echo '<td>'.$result_record["author"].'</td>';
                    } else {
                        echo '<td>Sem autor cadastrado</td>';
                    }
                    
                    if (!empty($result_record["publisher"])) {
                        echo '<td>'.$result_record["publisher"].'</td>';
                    } else {
                        echo '<td>Sem editora cadastrada</td>';
                    }
                    
                    if (!empty($result_record["pub_place"])) {
                        echo '<td>'.$result_record["pub_place"].'</td>';
                    } else {
                        echo '<td>Sem local</td>';
                    }                    

                    if (!empty($result_record["pub_date"])) {
                        echo '<td>'.$result_record["pub_date"].'</td>';
                    } else {
                        echo '<td>Sem data</td>';
                    }                      

                    if (isset($result_record["edition"])){
                        echo '<td>'.$result_record["edition"].'</td>';
                    } else {
                        echo '<td></td>';
                    }

                    if (isset($result_record["extent"])) {
                        echo '<td>'.$result_record["extent"].'</td>';
                    } else {
                        echo '<td></td>';
                    }                    
                    echo '<td><button onclick="SaveAsFile(\''.addslashes($rec_download).'\',\'record.mrc\',\'text/plain;charset=CP1252\')">Baixar MARC</button></td>';
                    echo '</tr>';
                    echo '</tbody>
                    </table></li>';  
    
                    echo '<li>'.nl2br($rec).'</li>';
                    
                    echo '</ul>';  
                    
                    flush();
                }        



            }
        }

    }      
    
}

class testadores {
    public static function existe($variavel){
        $resultado_teste = ((isset($variavel) && $variavel)? $variavel : '');
        return $resultado_teste;
    }
}

?>
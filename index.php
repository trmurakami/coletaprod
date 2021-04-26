<?php
if ($_SERVER["REQUEST_URI"] == "/") {
    header("Location: http://unifesp.br/prodmais/index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">

<head>
    <?php
    require 'inc/config.php';
    require 'inc/meta-header.php';
    require 'inc/functions.php';
    ?>
    <title><?php echo $branch ?></title>
    <!-- Facebook Tags - START -->
    <meta property="og:locale" content="pt_BR">
    <meta property="og:url" content="<?php echo $url_base ?>">
    <meta property="og:title" content="<?php echo $branch ?> - Página Principal">
    <meta property="og:site_name" content="<?php echo $branch ?>">
    <meta property="og:description" content="<?php echo $branch_description ?>">
    <meta property="og:image" content="<?php echo $facebook_image ?>">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="800">
    <meta property="og:image:height" content="600">
    <meta property="og:type" content="website">
    <!-- Facebook Tags - END -->

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .jumbotron {
            background-image: url("<?php echo $background_1 ?>");
            background-size: 100%;
            background-repeat: no-repeat;
        }
    </style>

</head>

<body>



    <!-- NAV -->
    <?php require 'inc/navbar.php'; ?>
    <!-- /NAV -->


    <div class="jumbotron">
        <br />
        <div class="container bg-light rounded p-5 mt-5 mb-5">
            <img src="inc/images/Prod_mais_2.png" class="rounded mx-auto d-block">
            <?php isset($error_connection_message) ? print_r($error_connection_message) : "" ?>
            <br /><br />
            <div class="alert alert-warning" role="alert">
                <p>
                    O Prod+ é uma ferramenta de busca da produção docente e discente (pós-graduação) desenvolvida pela UNIFESP.
                    Ela agrega informações do Currículo Lattes (Docentes após a data de ingresso na UNIFESP e Discentes que ingressaram após 2014),
                    sendo possível buscá-las por meio de palavras, pesquisadores e Programas de Pós-Graduação, com a utilização de filtros bem como de termos conjugados.
                    Aqui se acede à informação na forma de artigos, livros (e capítulos), além de trabalhos apresentados em eventos.
                    Como se tratam de informações não processadas, duplicações podem ocasionalmente aparecer.
                    <br />
                    Caso encontre algum erro, por favor <a href="https://docs.google.com/forms/d/e/1FAIpQLScmHGNgM_1z9sntKJo1uhIwIrxRt6qDdMZiPs0hvx8BMKuTmQ/viewform?usp=sf_link">use nosso formulário</a> para reportá-lo.
                </p>
            </div>
            <div id="app">
                <form class="mt-3" action="result.php" v-if="searchPage == 'simple'">
                    <label for="searchQuery">Pesquisa por palavras - <a href="result.php">Navegar por todos</a></label>
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" id="searchQuery" aria-describedby="searchHelp" placeholder="Pesquise por termo, autor ou ID do Lattes (16 dígitos)">
                    </div>
                    <div class="input-group-append mt-3">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </form>
                <form class="mt-3" action="result.php" v-if="searchPage == 'advanced'">
                    <label for="searchQuery">Pesquisa por palavras - <a href="result.php">Navegar por todos</a></label>
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" id="searchQuery" aria-describedby="searchHelp" placeholder="Pesquise por termo, autor ou ID do Lattes (16 dígitos)">
                        <label>Filtrar por Nome do Programa de Pós-Graduação (Opcional):</label>
                        <?php paginaInicial::filter_select("vinculo.ppg_nome"); ?>
                    </div>
                    <label for="authorsDataList" class="form-label">Autores (ID Lattes)</label>
                    <input class="form-control" list="datalistOptions" id="authorsDataList" placeholder="Digite parte do nome do autor..." name="filter[]" v-model="query" @input="searchCV()">
                    <datalist id="datalistOptions">
                        <option v-for="author in authors" :key="author._id" :value="'vinculo.lattes_id:' + author._id">{{author._source.nome_completo}}</option>
                    </datalist>
                    <label>Filtrar por data (Opcional):</label>
                    <div class="input-group">
                        <div class="form-group">
                            <label for="initialYear">Ano inicial</label>
                            <input type="text" class="form-control" id="initialYear" name="initialYear" pattern="\d{4}" placeholder="Ex. 2010" value="">
                        </div>
                        <div class="form-group">
                            <label for="finalYear">Ano final</label>
                            <input type="text" class="form-control" id="finalYear" name="finalYear" pattern="\d{4}" placeholder="Ex. 2020" value="">
                        </div>
                    </div>
                    <div class="input-group-append mt-3">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                    <small id="searchHelp" class="form-text text-muted">Dica: Use * para busca por radical. Ex: biblio*.</small><br />
                    <small id="searchHelp" class="form-text text-muted">Dica 2: Para buscas exatas, coloque entre "". Ex: "Direito civil"</small><br />
                    <small id="searchHelp" class="form-text text-muted">Dica 3: Por padrão, o sistema utiliza o operador booleano OR. Caso necessite deixar a busca mais específica, utilize o operador AND (em maiúscula)</small>
                </form>
                <div class="mt-3">
                    <button @click="searchPage = 'simple'" class="btn btn-primary">Busca simples</button>
                    <button @click="searchPage = 'advanced'" class="btn btn-primary">Busca avançada</button>
                </div>
            </div>
        </div>
        <br />
    </div>

    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="accordion" id="accordionPPGs">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                Nome do Programa de Pós-Graduação
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionPPGs">
                            <div class="accordion-body">
                                <ul class="list-group">
                                    <?php paginaInicial::unidade_inicio("vinculo.ppg_nome"); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Tipo de vínculo
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <ul class="list-group">
                                    <?php paginaInicial::unidade_inicio("vinculo.tipvin"); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Tipo de material
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <ul class="list-group">
                                    <?php paginaInicial::tipo_inicio(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <h2 class="uk-h3">Fonte</h2>
                <ul class="list-group">
                    <?php paginaInicial::fonte_inicio(); ?>
                </ul>
            </div>
            <div class="col-md-3">
                <h2 class="uk-h3">Estatísticas</h2>
                <ul class="list-group">
                    <li class="list-group-item"><?php echo paginaInicial::contar_registros_indice($index); ?> registros</li>
                    <li class="list-group-item"><?php echo paginaInicial::contar_registros_indice($index_cv);; ?> currículos</li>
                    <!--
                    <li class="list-group-item">< ?php echo paginaInicial::contar_registros_indice($index_source); ?> registros no Repositório Institucional</li>
                    <li class="list-group-item">< ?php echo paginaInicial::possui_lattes(); ?>% sem ID no Lattes</li>
                    -->
                </ul>
            </div>
        </div>
    </div>


    <?php include('inc/footer.php'); ?>
    <script>
        var app = new Vue({
            el: '#app',

            data: {
                searchPage: 'simple',
                query: "",
                message: "Teste",
                authors: []
            },
            mounted() {
                this.searchCV();
            },
            methods: {
                searchCV() {
                    axios.get(
                            'tools/proxy_autocomplete_cv.php?query=' + this.query
                        ).then((response) => {
                            this.authors = response.data.hits.hits;
                        })
                        .catch((error) => {
                            console.log(error);
                            console.error(error);
                            this.errored = true;
                        })
                        .finally(() => (this.loading = false));
                }
            }
        })
    </script>


</body>

</html>
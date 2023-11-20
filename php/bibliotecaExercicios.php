<?php
include "conexao.php";
include "queriesSql.php";

$result = $conn->query(QUERY_SELECT_TODOS_EXERCICIOS);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["pesquisa"])) {
        try {
            $busca = $_GET["pesquisa"];

            $stmt = mysqli_prepare($conn, QUERY_SELECT_BUSCA);

            // Adicione o '%' antes e depois do termo de pesquisa
            $termo_pesquisa = "%" . $busca . "%";

            mysqli_stmt_bind_param($stmt, "s", $termo_pesquisa);

            if (isset($_GET["filtro"])) {
                $filtro = $_GET["filtro"];
                $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE (nome_exercicio LIKE ? AND grupo_muscular = ?)");
                mysqli_stmt_bind_param($stmt, "ss", $termo_pesquisa, $filtro);
                if ($filtro === "musculacao" || $filtro === "exercicios-em-casa") {
                    $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE (nome_exercicio LIKE ? AND modalidade = ?)");
                    mysqli_stmt_bind_param($stmt, "ss", $termo_pesquisa, $filtro);
                }
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } catch (Exception $e) {
            echo $e->getMessage();
            mysqli_rollback($conn);
        }
    } else {
        try {
            if (isset($_GET["filtro"]) || isset($_GET["ordenacao"])) {
                $filtro = $_GET["filtro"];
                $ordenacao = $_GET["ordenacao"];

                if ($filtro && $ordenacao) {
                    $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE grupo_muscular = ? ORDER BY ?");
                    mysqli_stmt_bind_param($stmt, "ss", $filtro, $ordenacao);
                    if ($filtro === "musculacao" || $filtro === "exercicios-em-casa") {
                        $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE modalidade = ?");
                        mysqli_stmt_bind_param($stmt, "ss", $filtro, $ordenacao);
                    }
                }
                
                if (!isset($ordenacao)) {
                    $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE grupo_muscular = ?");
                    mysqli_stmt_bind_param($stmt, "s", $filtro);
                    if ($filtro === "musculacao" || $filtro === "exercicios-em-casa") {
                        $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE modalidade = ?");
                        mysqli_stmt_bind_param($stmt, "s", $filtro);
                    }
                } else if (!isset($filtro)) {
                    $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio ORDER BY ?");
                    mysqli_stmt_bind_param($stmt, "s", $ordenacao);
                }

                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            mysqli_rollback($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca de Exercícios | Consulado Fitness</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/bibliotecaExercicios.css">
</head>

<body>
    <!-- menu de navegação  -->
    <nav class="navegacao">
        <div class="logo">
            <img src="../imagens/logo-mobile.png" alt="logo-mobile">
        </div>

        <div class="nav_desktop">
            <ul>
                <li class="nav"><a href="#" class="link">Treino</a></li>
                <li class="nav"><a href="#" class="link">Biblioteca</a></li>
                <li class="nav"><a href="#" class="link">Contato</a></li>
                <li class="nav"><a href="#" class="link">Perfil</a></li>
            </ul>
        </div>

        <div class="icon_menu">
            <button onclick="menuResponsivo()"><img class="icon_botao" src="../imagens/menu02.png" alt="menu"></button>
        </div>
    </nav>

    <div class="mobile">
        <ul>
            <li class="nav"><a href="#" class="link">Treino</a></li>
            <li class="nav"><a href="#" class="link">Biblioteca</a></li>
            <li class="nav"><a href="#" class="link">Contato</a></li>
            <li class="nav"><a href="#" class="link">Perfil</a></li>
        </ul>
    </div>

    <script src="../js/global.js"></script>
    <!-- fim do menu navegacao  -->

    <main>
        <!-- Pesquisa, Filtragem e Ordenação -->
        <form class="pesquisa_form" action="" method="get">
            <div class="select_container">
                <label for="filtro">Filtrar por:</label>
                <select class="select_campo" id="filtro" name="filtro" onchange="this.form.submit">
                    <optgroup label="Grupos musculares">
                        <option value=""></option>
                        <option value="abdomen">Abdomen</option>
                        <option value="biceps">Biceps</option>
                        <option value="triceps">Triceps</option>
                        <option value="costas">Costas</option>
                        <option value="peito">Peito</option>
                        <option value="quadriceps">Quadriceps</option>
                        <option value="posterior de coxa">Posterior de coxa</option>
                        <option value="gluteos">Gluteos</option>
                        <option value="panturrilha">Panturrilha</option>
                        <option value="ombro anterior">Ombro Anterior</option>
                        <option value="ombro lateral">Ombro Lateral</option>
                        <option value="ombro posterior">Ombro Posterior</option>
                    </optgroup>
                    <optgroup label="Modalidade">
                        <option value="musculacao">Musculação</option>
                        <option value="exercicios-em-casa">Exercícios em casa</option>
                    </optgroup>
                </select>
            </div>
            <div class="select_container">
                <label for="ordenacao">Ordenar por:</label>
                <select class="select_campo" id="ordenacao" name="ordenacao">
                    <option value="asc">A-Z</option>
                    <option value="desc">Z-A</option>
                </select>
            </div>
            <div class="pesquisa_container">
                <input type="text" id="campo_pesquisa" name="pesquisa" placeholder="Digite sua pesquisa...">
                <button id="pesquisa_btn">
                    <img src="../imagens/icone_pesquisa.svg" alt="Botao de pesquisa">
                </button>
            </div>
        </form>

        <section>
            <ul class="card_container">
                <?php
                foreach ($result as $exercicio) {
                    echo '<li>';
                    echo '    <div class="card" onclick="abrirModal(\'' . $exercicio["nome_exercicio"] . '\', \'' . $exercicio["urlVideo"] . '\')">';
                    echo '        <h2 class="titulo_card">' . $exercicio["nome_exercicio"] . '</h2>';
                    echo '        <img class="imagem_card" src="" alt="Imagem do exercício">';
                    echo '        <p class="link_card">Mais informações +</p>';
                    echo '    </div>';
                    echo '</li>';
                }
                ?>
            </ul>
        </section>

        <div id="myModal" class="modal" onclick="fecharModal()">
            <div class="modal-content" onclick="event.stopPropagation();">
                <span class="close" onclick="fecharModal()">&times;</span>
                <h2 id="modalTitle"></h2>
                <p id="modalDescricao"></p>
            </div>
        </div>
    </main>

    <script>
        // Funções do modal
        function abrirModal(titulo, descricao) {
            document.getElementById('modalTitle').innerText = titulo;
            document.getElementById('modalDescricao').innerText = descricao;
            document.getElementById('myModal').style.display = 'flex';
        }

        function fecharModal() {
            document.getElementById('myModal').style.display = 'none';
        }
    </script>
</body>

</html>
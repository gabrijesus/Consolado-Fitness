<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
// Redirecionar para a página de login ou fazer qualquer outra ação necessária
    header("Location: login.php");
    exit();
}

include "../utils/conexao.php";
include "../utils/queriesSql.php";

$result = $conn->query(QUERY_SELECT_TODOS_EXERCICIOS . " ORDER BY nome_exercicio ASC");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["pesquisa"]) && $_GET["pesquisa"] !== "") {
        try {
            $busca = $_GET["pesquisa"];

            $stmt = mysqli_prepare($conn, QUERY_SELECT_BUSCA);

            // Adicione o '%' antes e depois do termo de pesquisa
            $termo_pesquisa = "%" . $busca . "%";

            if (isset($_GET["filtro"]) && $_GET["filtro"] !== "") {
                $filtro = $_GET["filtro"];
                if ($filtro === "musculacao" || $filtro === "exercicios-em-casa") {
                    $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE (nome_exercicio LIKE ? AND modalidade = ?)");
                    mysqli_stmt_bind_param($stmt, "ss", $termo_pesquisa, $filtro);
                } else {
                    $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE (nome_exercicio LIKE ? AND grupo_muscular = ?)");
                    mysqli_stmt_bind_param($stmt, "ss", $termo_pesquisa, $filtro);
                }
            } else {
                mysqli_stmt_bind_param($stmt, "s", $termo_pesquisa);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } catch (Exception $e) {
            echo $e->getMessage();
            mysqli_rollback($conn);
        }
    } else {
        try {
            if ((isset($_GET["filtro"]) && $_GET["filtro"] !== "") || (isset($_GET["ordenacao"]) && $_GET["ordenacao"] !== "")) {
                $filtro = $_GET["filtro"];
                $ordenacao = $_GET["ordenacao"];

                if (isset($filtro) && $filtro !== "") {
                    if ($filtro === "musculacao" || $filtro === "exercicios-em-casa") {
                        $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE modalidade = ?" . " ORDER BY nome_exercicio " . $ordenacao);
                        mysqli_stmt_bind_param($stmt, "s", $filtro);
                    } else {
                        $stmt = mysqli_prepare($conn, "SELECT * FROM exercicio WHERE grupo_muscular = ?" . " ORDER BY nome_exercicio " . $ordenacao);
                        mysqli_stmt_bind_param($stmt, "s", $filtro);
                    }
                } else if (isset($ordenacao) && $ordenacao !== "") {
                    $result = $conn->query(QUERY_SELECT_TODOS_EXERCICIOS . " ORDER BY nome_exercicio " . $ordenacao);
                } else {
                    if ($filtro === "musculacao" || $filtro === "exercicios-em-casa") {
                        $result = $conn->query(QUERY_SELECT_TODOS_EXERCICIOS . " WHERE modalidade = " . $filtro . " ORDER BY nome_exercicio " . $ordenacao);
                    } else {
                        $result = $conn->query(QUERY_SELECT_TODOS_EXERCICIOS . " WHERE grupo_muscular = " . $filtro . " ORDER BY nome_exercicio " . $ordenacao);
                    }
                }

                if (isset($stmt)) {
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                }
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
    <link rel="shortcut icon" href="../imagens/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php 
        include_once "../utils/cabecalho.php";
    ?>

    <main>
        <!-- Pesquisa, Filtragem e Ordenação -->
        <form class="pesquisa_form" action="" method="get">
            <div class="select_container">
                <label for="filtro">Filtrar por:</label>
                <select class="select_campo" id="filtro" name="filtro" onchange="this.form.submit()">
                    <optgroup label="Grupos musculares">
                        <option value="">Todos</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'abdomen') ? 'selected' : ''; ?> value="abdomen">Abdomen</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'biceps') ? 'selected' : ''; ?> value="biceps">Biceps</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'triceps') ? 'selected' : ''; ?> value="triceps">Triceps</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'costas') ? 'selected' : ''; ?> value="costas">Costas</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'peito') ? 'selected' : ''; ?> value="peito">Peito</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'quadriceps') ? 'selected' : ''; ?> value="quadriceps">Quadriceps</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'posterior de coxa') ? 'selected' : ''; ?> value="posterior de coxa">Posterior de coxa</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'gluteos') ? 'selected' : ''; ?> value="gluteos">Gluteos</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'panturrilha') ? 'selected' : ''; ?> value="panturrilha">Panturrilha</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'ombro anterior') ? 'selected' : ''; ?> value="ombro anterior">Ombro Anterior</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'ombro lateral') ? 'selected' : ''; ?> value="ombro lateral">Ombro Lateral</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'ombro posterior') ? 'selected' : ''; ?> value="ombro posterior">Ombro Posterior</option>
                    </optgroup>
                    <optgroup label="Modalidade">
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'musculacao') ? 'selected' : ''; ?> value="musculacao">Musculação</option>
                        <option <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'exercicios-em-casa') ? 'selected' : ''; ?> value="exercicios-em-casa">Exercícios em casa</option>
                    </optgroup>
                </select>
            </div>
            <div class="select_container">
                <label for="ordenacao">Ordenar por:</label>
                <select class="select_campo" id="ordenacao" name="ordenacao" onchange="this.form.submit()">
                    <option <?php echo (isset($_GET['ordenacao']) && $_GET['ordenacao'] == 'ASC') ? 'selected' : ''; ?> value="ASC">A-Z</option>
                    <option <?php echo (isset($_GET['ordenacao']) && $_GET['ordenacao'] == 'DESC') ? 'selected' : ''; ?> value="DESC">Z-A</option>
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
                    echo '    <div class="card" onclick="abrirModal(\'' . $exercicio["nome_exercicio"] . '\', \'' . $exercicio["grupo_muscular"] . '\', \'' . $exercicio["urlImagem"] . '\', \'' . $exercicio["urlVideo"] . '\')">';
                    echo '        <h2 class="titulo_card">' . $exercicio["nome_exercicio"] . '</h2>';
                    echo '        <img class="imagem_card" src=' . $exercicio["urlImagem"] . ' alt="Imagem do exercício">';
                    echo '        <p class="link_card">Mais informações +</p>';
                    echo '    </div>';
                    echo '</li>';
                }
                ?>
            </ul>
        </section>

        <div id="myModal" class="modal" onclick="fecharModal()">
            <div class="modal-content" onclick="event.stopPropagation();">
                <span class="modal-content-titulo">
                    <span class="close" onclick="fecharModal()">&times;</span>
                    <h2 id="modalTitle"></h2>
                </span>
                <div id="modal-info">
                    <img id="modalImagem" src="" alt="">
                    <h3 class="modalSubtitulo">Músculos Envolvidos:</h3>
                    <p id="modalMusculos"></p>
                    <h3 class="modalSubtitulo">Instruções:</h3>
                </div>
                <iframe width="350" height="250" id="modalVideo" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </div>

    </main>

    <script>
        // Funções do modal
        function abrirModal(titulo, musculos, urlImagem, urlVideo) {
            document.getElementById('modalTitle').innerText = titulo;
            document.getElementById('modalImagem').src = urlImagem;
            document.getElementById('modalMusculos').innerHTML = musculos;
            document.getElementById('modalVideo').src = urlVideo;
            document.getElementById('myModal').style.display = 'flex';
        }

        function fecharModal() {
            document.getElementById('myModal').style.display = 'none';
            // Também é uma boa prática reiniciar o src do iframe ao fechar o modal para interromper a reprodução do vídeo
            document.getElementById('modalVideo').src = '';
        }
    </script>
</body>

</html>
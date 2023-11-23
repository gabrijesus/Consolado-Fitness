<?php
include "conexao.php";
include "queriesSql.php";

function selecionar_exercicios($conn, $preferencia_treino, $grupos_musculares, $quantidades)
{
    $resultados = [];

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, QUERY_SELECT_EXERCICIOS_FILTRO);

        foreach ($grupos_musculares as $index => $grupo_muscular) {
            $quantidade = $quantidades[$index];

            if ($grupo_muscular === "Abdomen") {
                $preferencia_treino = "vazio";
            }

            mysqli_stmt_bind_param($stmt, "ssi", $preferencia_treino, $grupo_muscular, $quantidade);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                $row["serie"] = 3;
                $row["repeticao"] = 12;
                $resultados[] = $row;
            }

            mysqli_stmt_reset($stmt);
        }

        mysqli_stmt_close($stmt);
        mysqli_commit($conn);

        return $resultados;
    } catch (Exception $e) {
        echo $e->getMessage();
        mysqli_rollback($conn);
    }
}

function gerar_treino($conn, $preferencia_treino, $nivel_treino, $treinos_semana)
{
    $ficha_treino = [];

    if ($treinos_semana < 2) {
        echo "É necessário treinar ao menos 2 vezes por semana!";
        return;
    }

    switch ($treinos_semana) {
        case 2:
            $ficha_treino["divisao"]["A - Full Body"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Biceps", "Triceps", "Quadriceps", "Posterior de coxa", "Abdomen"], [1, 1, 1, 1, 1, 1, 1, 1]);
            break;
        case 3:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["divisao"]["A - Full Body"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Biceps", "Triceps", "Quadriceps", "Posterior de coxa", "Abdomen"], [1, 1, 1, 1, 1, 1, 1, 1]);
            } else {
                $ficha_treino["divisao"]["A - Peito, Ombros e Tríceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Ombro Anterior", "Ombro Lateral", "Triceps", "Abdomen"], [2, 1, 1, 2, 1]);
                $ficha_treino["divisao"]["B - Costas, Ombros e Bíceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Ombro Posterior", "Ombro Lateral", "Biceps"], [3, 1, 1, 2]);
                $ficha_treino["divisao"]["C - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de coxa", "Panturrilha"], [3, 3, 1]);
            }
            break;
        case 4:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["divisao"]["A - Superiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Ombro Lateral", "Biceps", "Triceps", "Abdomen"], [2, 2, 1, 1, 1, 1, 1]);
                $ficha_treino["divisao"]["B - Inferiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 2, 1]);
            } else {
                $ficha_treino["divisao"]["A - Peito e Tríceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Triceps"], [4, 3]);
                $ficha_treino["divisao"]["B - Costas e Bíceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Biceps"], [4, 3]);
                $ficha_treino["divisao"]["C - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 3, 1]);
                $ficha_treino["divisao"]["D - Ombros"] = selecionar_exercicios($conn, $preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior", "Abdômen"], [3, 3, 3, 1]);
            }
            break;
        case 5:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["divisao"]["A - Superiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Ombro Lateral", "Biceps", "Triceps", "Abdomen"], [2, 2, 1, 1, 1, 1, 1]);
                $ficha_treino["divisao"]["B - Inferiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 2, 1]);
            } else if ($nivel_treino === "intermediario") {
                $ficha_treino["divisao"]["A - Peito e Tríceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Triceps", "Abdomen"], [4, 3, 1]);
                $ficha_treino["divisao"]["B - Costas e Bíceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Biceps", "Abdomen"], [4, 3, 1]);
                $ficha_treino["divisao"]["C - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 3, 1]);
                $ficha_treino["divisao"]["D - Ombros"] = selecionar_exercicios($conn, $preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior"], [3, 3, 3]);
            } else {
                $ficha_treino["divisao"]["A - - Peito e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Abdomen"], [5, 2]);
                $ficha_treino["divisao"]["B - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 3, 2]);
                $ficha_treino["divisao"]["C - Costas e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Abdomen"], [6, 2]);
                $ficha_treino["divisao"]["D - Ombros"] = selecionar_exercicios($conn, $preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior"], [3, 3, 3]);
                $ficha_treino["divisao"]["E - Braços"] = selecionar_exercicios($conn, $preferencia_treino, ["Biceps", "Triceps", "Panturrilha"], [4, 3, 2]);
            }
            break;
        case 6:
            if ($nivel_treino === "iniciante" || $nivel_treino === "intermediario") {
                $ficha_treino["divisao"]["A - Peito, Ombros e Tríceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Ombro Anterior", "Ombro Lateral", "Triceps", "Abdomen"], [2, 1, 1, 2, 1]);
                $ficha_treino["divisao"]["B - Costas, Ombros e Bíceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Ombro Posterior", "Ombro Lateral", "Biceps"], [3, 1, 1, 2]);
                $ficha_treino["divisao"]["C - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de coxa", "Panturrilha"], [3, 3, 1]);
            } else {
                $ficha_treino["divisao"]["A - Peito e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Abdomen"], [5, 2]);
                $ficha_treino["divisao"]["B - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 3, 2]);
                $ficha_treino["divisao"]["C - Costas e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Abdomen"], [6, 2]);
                $ficha_treino["divisao"]["D - Ombros"] = selecionar_exercicios($conn, $preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior"], [3, 3, 3]);
                $ficha_treino["divisao"]["E - Braços"] = selecionar_exercicios($conn, $preferencia_treino, ["Biceps", "Triceps", "Panturrilha"], [4, 3, 2]);
            }
            break;
    }

    return $ficha_treino;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $preferencia_treino = $_POST["prefTreino"];
    $nivel_treino = $_POST["nivelTreino"];
    $treinos_semana = $_POST["qtdTreinos"];

    $ficha_treino = gerar_treino($conn, $preferencia_treino, $nivel_treino, $treinos_semana);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de treino | Consulado Fitness</title>
    <link rel="stylesheet" href="../css/geraTreino.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="shortcut icon" href="../imagens/favicon.ico" type="image/x-icon">
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

    <main>
        <form action="" method="post">
            <div class="input-container">
                <div class="input-item">
                    <label for="qtdTreinos">Quantas vezes você treina por semana?</label>
                    <select name="qtdTreinos" id="qtdTreinos">
                        <option value="vazio" <?php echo (isset($_POST['qtdTreinos']) && $_POST['qtdTreinos'] == 'vazio') ? 'selected' : ''; ?>></option>
                        <option value="2" <?php echo (isset($_POST['qtdTreinos']) && $_POST['qtdTreinos'] == '2') ? 'selected' : ''; ?>>2</option>
                        <option value="3" <?php echo (isset($_POST['qtdTreinos']) && $_POST['qtdTreinos'] == '3') ? 'selected' : ''; ?>>3</option>
                        <option value="4" <?php echo (isset($_POST['qtdTreinos']) && $_POST['qtdTreinos'] == '4') ? 'selected' : ''; ?>>4</option>
                        <option value="5" <?php echo (isset($_POST['qtdTreinos']) && $_POST['qtdTreinos'] == '5') ? 'selected' : ''; ?>>5</option>
                        <option value="6" <?php echo (isset($_POST['qtdTreinos']) && $_POST['qtdTreinos'] == '6') ? 'selected' : ''; ?>>6</option>
                    </select>
                </div>
                <div class="input-item">
                    <label for="prefTreino">Preferência de treino:</label>
                    <select name="prefTreino" id="prefTreino">
                        <option value="vazio" <?php echo (isset($_POST['prefTreino']) && $_POST['prefTreino'] == 'vazio') ? 'selected' : ''; ?>></option>
                        <option value="Musculacao" <?php echo (isset($_POST['prefTreino']) && $_POST['prefTreino'] == 'Musculacao') ? 'selected' : ''; ?>>Musculação</option>
                        <option value="Exercicios-em-casa" <?php echo (isset($_POST['prefTreino']) && $_POST['prefTreino'] == 'Exercicios-em-casa') ? 'selected' : ''; ?>>Treino em casa</option>
                    </select>
                </div>
                <div class="input-item">
                    <label for="nivelTreino">Nível de treino:</label>
                    <select name="nivelTreino" id="nivelTreino">
                        <option value="vazio" <?php echo (isset($_POST['nivelTreino']) && $_POST['nivelTreino'] == 'vazio') ? 'selected' : ''; ?>></option>
                        <option value="iniciante" <?php echo (isset($_POST['nivelTreino']) && $_POST['nivelTreino'] == 'iniciante') ? 'selected' : ''; ?>>Iniciante</option>
                        <option value="intermediario" <?php echo (isset($_POST['nivelTreino']) && $_POST['nivelTreino'] == 'intermediario') ? 'selected' : ''; ?>>Intermediário</option>
                        <option value="avancado" <?php echo (isset($_POST['nivelTreino']) && $_POST['nivelTreino'] == 'avancado') ? 'selected' : ''; ?>>Avançado</option>
                    </select>
                </div>
                <div class="input-item">
                    <button class="submit-btn" type="submit">Gerar Treino</button>
                </div>
            </div>
        </form>

        <div class="table-container">
            <!-- Botão para exportar a tabela -->
            <form action="exportarPlanilha.php" method="post" class="export-form">
                <?php
                if (isset($ficha_treino) && is_array($ficha_treino)) {
                    echo "<input type='hidden' name='cabecalho_treino' value='" . htmlspecialchars(json_encode($ficha_treino["divisao"])) . "'>";
                    echo "<input type='hidden' name='ficha_treino' value='" . json_encode($ficha_treino) . "'>";
                    echo "<button class='export-btn' type='submit' name='exportar'>Baixar Treino</button>";
                }
                ?>
            </form>
            <table border="1">
                <thead>
                    <tr>
                        <?php
                        // Gera automaticamente as colunas com base nas divisões presentes
                        if (isset($ficha_treino) && is_array($ficha_treino)) {
                            $fichaLength = count($ficha_treino["divisao"]);
                            switch ($fichaLength) {
                                case 1:
                                    echo "<th colspan='3'>Treino A - Full Body</th>";
                                    break;
                                case 2:
                                    echo "<th colspan='3'>Treino A - Superiores</th>";
                                    echo "<th colspan='3'>Treino B - Inferiores</th>";
                                    break;
                                case 3:
                                    echo "<th colspan='3'>Treino A - Peito, Ombros e Tríceps</th>";
                                    echo "<th colspan='3'>Treino B - Costas, Ombros e Bíceps</th>";
                                    echo "<th colspan='3'>Treino C - Pernas</th>";
                                    break;
                                case 4:
                                    echo "<th colspan='3'>Treino A - Peito e Tríceps</th>";
                                    echo "<th colspan='3'>Treino B - Costas e Bíceps</th>";
                                    echo "<th colspan='3'>Treino C - Pernas</th>";
                                    echo "<th colspan='3'>Treino D - Ombros</th>";
                                    break;
                                case 5:
                                    echo "<th colspan='3'>Treino A - Peito e Abdomen</th>";
                                    echo "<th colspan='3'>Treino B - Pernas</th>";
                                    echo "<th colspan='3'>Treino C - Costas e Abdomen</th>";
                                    echo "<th colspan='3'>Treino D - Ombros</th>";
                                    echo "<th colspan='3'>Treino E - Braços</th>";
                                    break;
                                default:
                                    echo "<th colspan='3'></th>";
                                    break;
                            }
                        } else {
                            echo "<div class='mensagemInicial'>";
                            echo "<h2>Você ainda não possui nenhum treino, preencha as informações e bora treinar!!</h2>";
                            echo "</div>";
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        if (isset($ficha_treino) && is_array($ficha_treino)) {
                            // Obtemos o número total de divisões
                            $numDivisoes = count($ficha_treino["divisao"]);

                            // Repetir a estrutura do cabeçalho conforme o tamanho do array
                            for ($j = 0; $j < $numDivisoes; $j++) {
                                // Exibir as colunas do cabeçalho
                                echo "<th>Exercício</th>";
                                echo "<th>Série</th>";
                                echo "<th>Repetições</th>";
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($ficha_treino) && is_array($ficha_treino)) {
                        // Encontrar o número máximo de exercícios em uma única divisão
                        $maxExercicios = 0;

                        foreach ($ficha_treino["divisao"] as $divisao => $exercicios) {
                            $numExercicios = count($exercicios);
                            if ($numExercicios > $maxExercicios) {
                                $maxExercicios = $numExercicios;
                            }
                        }

                        // Iterar sobre o número máximo de exercícios
                        for ($i = 0; $i < $maxExercicios; $i++) {
                            echo "<tr align='center'>";
                            foreach ($ficha_treino["divisao"] as $divisao => $exercicios) {
                                // Verificar se existe um exercício para o índice atual
                                if (isset($exercicios[$i])) {
                                    echo "<td>" . $exercicios[$i]["nome_exercicio"] . "</td>";
                                    echo "<td>" . $exercicios[$i]["serie"] . "</td>";
                                    echo "<td>" . $exercicios[$i]["repeticao"] . "</td>";
                                } else {
                                    // Se não houver exercício, exibir células vazias
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                }
                            }

                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="../js/global.js"></script>
</body>

</html>
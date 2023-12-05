<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

include "../utils/conexao.php";
include "../utils/queriesSql.php";

$id_usuario = $_SESSION["id_usuario"];

$infoUsuario = $conn->query("SELECT preferencia_treino, nivel_treino, sexo FROM usuarios WHERE id_usuario = $id_usuario");

$usuarioInfo = $infoUsuario->fetch_assoc();
$nivelTreinoSelecionado = $usuarioInfo['nivel_treino'];
$prefTreinoSelecionada = $usuarioInfo['preferencia_treino'];
$sexoUsuario = $usuarioInfo['sexo'];

$ficha_treino_result = $conn->query("SELECT * FROM ficha_de_treino WHERE id_usuario = $id_usuario");

if (isset($ficha_treino_result) && $ficha_treino_result->num_rows > 0) {
    $ficha_treino_fetch = $ficha_treino_result->fetch_assoc();
    $exercicios_ficha_treino = json_decode($ficha_treino_fetch["exercicios"], true);

    $ficha_treino["nome"] = $ficha_treino_fetch["nome_ficha_treino"];

    foreach ($exercicios_ficha_treino as $key => $value) {
        $ficha_treino["divisao"][$key] = $value;
    }
}

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

function gerar_treino($conn, $preferencia_treino, $nivel_treino, $treinos_semana, $sexo): array
{
    $ficha_treino = [];

    if ($treinos_semana < 2) {
        echo "É necessário treinar ao menos 2 vezes por semana!";
        return [];
    }

    switch ($treinos_semana) {
        case 2:
            $ficha_treino["nome"] = "A - Full Body";
            $ficha_treino["divisao"]["A - Full Body"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Biceps", "Triceps", "Quadriceps", "Posterior de coxa", "Abdomen"], [1, 1, 1, 1, 1, 1, 1, 1]);
            break;
        case 3:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["nome"] = "A - Full Body";
                $ficha_treino["divisao"]["A - Full Body"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Biceps", "Triceps", "Quadriceps", "Posterior de coxa", "Abdomen"], [1, 1, 1, 1, 1, 1, 1, 1]);
            } else {
                $ficha_treino["nome"] = "ABC";
                if ($sexo === "masculino") {
                    $ficha_treino["divisao"]["A - Peito, Ombros e Tríceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Ombro Anterior", "Ombro Lateral", "Triceps", "Abdomen"], [2, 1, 1, 2, 1]);
                    $ficha_treino["divisao"]["B - Costas, Ombros e Bíceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Ombro Posterior", "Ombro Lateral", "Biceps"], [3, 1, 1, 2]);
                    $ficha_treino["divisao"]["C - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de coxa", "Panturrilha"], [3, 3, 1]);
                } else if ($sexo === "feminino") {
                    $ficha_treino["divisao"]["A - Quadriceps e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Panturrilha"], [4, 2]);
                    $ficha_treino["divisao"]["B - Superiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Ombro Lateral", "Biceps", "Triceps", "Abdomen"], [2, 2, 1, 1, 1, 1, 1]);
                    $ficha_treino["divisao"]["C - Posterior de coxa e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Posterior de coxa", "Panturrilha"], [4, 2]);
                } else {
                    return "Sexo inexistente";
                }
            }
            break;
        case 4:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["nome"] = "AB";
                $ficha_treino["divisao"]["A - Superiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Ombro Lateral", "Biceps", "Triceps", "Abdomen"], [2, 2, 1, 1, 1, 1, 1]);
                $ficha_treino["divisao"]["B - Inferiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 2, 2]);
            } else {
                $ficha_treino["nome"] = "ABCD";
                if ($sexo === "masculino") {
                    $ficha_treino["divisao"]["A - Peito e Tríceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Triceps", "Abdomen"], [4, 3, 1]);
                    $ficha_treino["divisao"]["B - Costas e Bíceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Biceps", "Abdomen"], [4, 3, 1]);
                    $ficha_treino["divisao"]["C - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 3, 1]);
                    $ficha_treino["divisao"]["D - Ombros"] = selecionar_exercicios($conn, $preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior"], [3, 3, 3]);
                } else if ($sexo === "feminino") {
                    $ficha_treino["divisao"]["A - Quadriceps e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Panturrilha"], [4, 2]);
                    $ficha_treino["divisao"]["B - Peito, Ombros e Triceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Ombro Anterior", "Ombro Lateral", "Triceps", "Abdomen"], [2, 1, 1, 2, 1]);
                    $ficha_treino["divisao"]["C - Posterior de coxa e Glúteos"] = selecionar_exercicios($conn, $preferencia_treino, ["Posterior de Coxa", "Gluteos", "Panturrilha"], [3, 2, 1]);
                    $ficha_treino["divisao"]["D - Costas, Bíceps e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Biceps", "Abdomen"], [3, 2, 2]);
                } else {
                    return "Sexo inexistente";
                }
            }
            break;
        case 5:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["nome"] = "AB";
                $ficha_treino["divisao"]["A - Superiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Ombro Lateral", "Biceps", "Triceps", "Abdomen"], [2, 2, 1, 1, 1, 1, 1]);
                $ficha_treino["divisao"]["B - Inferiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 2, 1]);
            } else if ($nivel_treino === "intermediario") {
                $ficha_treino["nome"] = "ABCD";
                if ($sexo === "masculino") {
                    $ficha_treino["divisao"]["A - Peito e Tríceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Triceps", "Abdomen"], [4, 3, 1]);
                    $ficha_treino["divisao"]["B - Costas e Bíceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Biceps", "Abdomen"], [4, 3, 1]);
                    $ficha_treino["divisao"]["C - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 3, 1]);
                    $ficha_treino["divisao"]["D - Ombros"] = selecionar_exercicios($conn, $preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior"], [3, 3, 3]);
                } else if ($sexo === "feminino") {
                    $ficha_treino["divisao"]["A - Quadriceps e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Panturrilhas"], [4, 2]);
                    $ficha_treino["divisao"]["B - Peito, Ombros e Triceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Ombro Anterior", "Ombro Lateral", "Triceps", "Abdomen"], [2, 1, 1, 2, 1]);
                    $ficha_treino["divisao"]["C - Posterior de coxa e Glúteos"] = selecionar_exercicios($conn, $preferencia_treino, ["Posterior de Coxa", "Gluteos", "Panturrilha"], [3, 2, 1]);
                    $ficha_treino["divisao"]["D - Costas, Bíceps e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Biceps", "Abdomen"], [3, 2, 2]);
                } else {
                    return "Sexo inexistente";
                }
            } else {
                $ficha_treino["nome"] = "ABCDE";
                if ($sexo === "masculino") {
                    $ficha_treino["divisao"]["A - Peito e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Abdomen"], [5, 2]);
                    $ficha_treino["divisao"]["B - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 3, 2]);
                    $ficha_treino["divisao"]["C - Costas e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Abdomen"], [6, 2]);
                    $ficha_treino["divisao"]["D - Ombros"] = selecionar_exercicios($conn, $preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior"], [3, 3, 3]);
                    $ficha_treino["divisao"]["E - Braços"] = selecionar_exercicios($conn, $preferencia_treino, ["Biceps", "Triceps", "Panturrilha"], [4, 3, 2]);
                } else if ($sexo === "feminino") {
                    $ficha_treino["divisao"]["A - Quadríceps e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Panturrilha"], [4, 2]);
                    $ficha_treino["divisao"]["B - Peito, Ombros e Biceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Ombro Anterior", "Ombro Lateral", "Biceps", "Abdômen"], [2, 1, 1, 2, 1]);
                    $ficha_treino["divisao"]["C - Posterior de coxa e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Posterior de coxa", "Panturrilha"], [4, 2]);
                    $ficha_treino["divisao"]["D - Costas, Tríceps, Abdômen"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Triceps", "Abdomen"], [3, 2, 1]);
                    $ficha_treino["divisao"]["E - Glúteos e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Gluteos", "Panturrilha"], [3, 2]);
                } else {
                    return "Sexo inexistente";
                }
            }
            break;
        case 6:
            if ($nivel_treino === "iniciante" || $nivel_treino === "intermediario") {
                $ficha_treino["nome"] = "ABC";
                if ($sexo === "masculino") {
                    $ficha_treino["divisao"]["A - Peito, Ombros e Tríceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Ombro Anterior", "Ombro Lateral", "Triceps", "Abdomen"], [2, 1, 1, 2, 1]);
                    $ficha_treino["divisao"]["B - Costas, Ombros e Bíceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Ombro Posterior", "Ombro Lateral", "Biceps"], [3, 1, 1, 2]);
                    $ficha_treino["divisao"]["C - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de coxa", "Panturrilha"], [3, 3, 1]);
                } else if ($sexo === "feminino") {
                    $ficha_treino["divisao"]["A - Quadriceps e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Panturrilhas"], [4, 2]);
                    $ficha_treino["divisao"]["B - Superiores"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Ombro Lateral", "Biceps", "Triceps", "Abdomen"], [2, 2, 1, 1, 1, 1, 1]);
                    $ficha_treino["divisao"]["C - Posterior de coxa, Glúteos e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Posterior de coxa", "Gluteos", "Panturrilha"], [3, 2, 2]);
                } else {
                    return "Sexo inexistente";
                }
            } else {
                $ficha_treino["nome"] = "ABCDE";
                if ($sexo === "masculino") {
                    $ficha_treino["divisao"]["A - Peito e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Abdomen"], [5, 2]);
                    $ficha_treino["divisao"]["B - Pernas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], [3, 3, 2]);
                    $ficha_treino["divisao"]["C - Costas e Abdomen"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Abdomen"], [6, 2]);
                    $ficha_treino["divisao"]["D - Ombros"] = selecionar_exercicios($conn, $preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior"], [3, 3, 3]);
                    $ficha_treino["divisao"]["E - Braços"] = selecionar_exercicios($conn, $preferencia_treino, ["Biceps", "Triceps", "Panturrilha"], [4, 3, 2]);
                } else if ($sexo === "feminino") {
                    $ficha_treino["divisao"]["A - Quadríceps e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Quadriceps", "Panturrilha"], [4, 2]);
                    $ficha_treino["divisao"]["B - Peito, Ombros e Biceps"] = selecionar_exercicios($conn, $preferencia_treino, ["Peito", "Ombro Anterior", "Ombro Lateral", "Biceps", "Abdômen"], [2, 1, 1, 2, 1]);
                    $ficha_treino["divisao"]["C - Posterior de coxa e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Posterior de coxa", "Panturrilha"], [4, 2]);
                    $ficha_treino["divisao"]["D - Costas, Tríceps, Abdômen"] = selecionar_exercicios($conn, $preferencia_treino, ["Costas", "Triceps", "Abdomen"], [3, 2, 1]);
                    $ficha_treino["divisao"]["E - Glúteos e Panturrilhas"] = selecionar_exercicios($conn, $preferencia_treino, ["Gluteos", "Panturrilha"], [3, 2]);
                } else {
                    return "Sexo inexistente";
                }
            }
            break;
    }

    return $ficha_treino;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $preferencia_treino = $_POST["prefTreino"];
    $nivel_treino = $_POST["nivelTreino"];
    $treinos_semana = $_POST["qtdTreinos"];

    $ficha_treino = gerar_treino($conn, $preferencia_treino, $nivel_treino, $treinos_semana, $sexoUsuario);

    $nome_ficha_treino = $ficha_treino["nome"];
    $divisao_ficha_treino = json_encode($ficha_treino["divisao"]);

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, QUERY_INSERT_FICHA_TREINO);
        mysqli_stmt_bind_param($stmt, "sis", $nome_ficha_treino, $id_usuario, $divisao_ficha_treino);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        $e->getMessage();
        mysqli_rollback($conn);
    }
    mysqli_commit($conn);
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
    <?php
    include_once "../utils/cabecalho.php";
    ?>

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
                        <option value='vazio' <?php echo ($prefTreinoSelecionada == 'vazio') ? 'selected' : ''; ?>></option>
                        <option value='Musculacao' <?php echo ($prefTreinoSelecionada == 'm usculação') ? 'selected' : ''; ?>>Musculação</option>
                        <option value='Exercicios-em-casa' <?php echo ($prefTreinoSelecionada == 'Exercicios-em-casa') ? 'selected' : ''; ?>>Treino em casa</option>
                    </select>
                </div>
                <div class="input-item">
                    <label for="nivelTreino">Nível de treino:</label>
                    <select name="nivelTreino" id="nivelTreino">
                        <option value='vazio' <?php echo ($nivelTreinoSelecionado == 'vazio') ? 'selected' : ''; ?>></option>
                        <option value='iniciante' <?php echo ($nivelTreinoSelecionado == 'iniciante') ? 'selected' : ''; ?>>Iniciante</option>
                        <option value='intermediario' <?php echo ($nivelTreinoSelecionado == 'intermediario') ? 'selected' : ''; ?>>Intermediário</option>
                        <option value='avancado' <?php echo ($nivelTreinoSelecionado == 'avancado') ? 'selected' : ''; ?>>Avançado</option>
                    </select>
                </div>
                <div class="input-item">
                    <button class="submit-btn" onclick="return confirmarGeracaoTreino();" type="submit">Gerar Treino</button>
                </div>
            </div>
        </form>

        <div class="table-container">
            <!-- Botão para exportar a tabela -->
            <form action="../utils/exportarPlanilha.php" method="post" class="export-form">
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
                                    if ($sexoUsuario === "masculino") {
                                        echo "<th colspan='3'>Treino A - Peito, Ombros e Tríceps</th>";
                                        echo "<th colspan='3'>Treino B - Costas, Ombros e Bíceps</th>";
                                        echo "<th colspan='3'>Treino C - Pernas</th>";
                                    } else if ($sexoUsuario === "feminino") {
                                        echo "<th colspan='3'>Treino A - Quadriceps e Panturrilhas</th>";
                                        echo "<th colspan='3'>Treino B - Superiores</th>";
                                        echo "<th colspan='3'>Treino C - Posterior de coxa, Glúteos e Panturrilhas</th>";
                                    }
                                    break;
                                case 4:
                                    if ($sexoUsuario === "masculino") {
                                        echo "<th colspan='3'>Treino A - Peito e Tríceps</th>";
                                        echo "<th colspan='3'>Treino B - Costas e Bíceps</th>";
                                        echo "<th colspan='3'>Treino C - Pernas</th>";
                                        echo "<th colspan='3'>Treino D - Ombros</th>";
                                    } else if ($sexoUsuario === "feminino") {
                                        echo "<th colspan='3'>Treino A - Quadriceps e Panturrilhas</th>";
                                        echo "<th colspan='3'>Treino B - Peito, Ombros e Triceps</th>";
                                        echo "<th colspan='3'>Treino C - Posterior de coxa e Glúteos</th>";
                                        echo "<th colspan='3'>Treino D - Costas, Bíceps e Abdomen</th>";
                                    }
                                    break;
                                case 5:
                                    if ($sexoUsuario === "masculino") {
                                        echo "<th colspan='3'>Treino A - Peito e Abdomen</th>";
                                        echo "<th colspan='3'>Treino B - Pernas</th>";
                                        echo "<th colspan='3'>Treino C - Costas e Abdomen</th>";
                                        echo "<th colspan='3'>Treino D - Ombros</th>";
                                        echo "<th colspan='3'>Treino E - Braços</th>";
                                    } else if ($sexoUsuario === "feminino") {
                                        echo "<th colspan='3'>Treino A - Quadriceps e Panturrilhas</th>";
                                        echo "<th colspan='3'>Treino B - Peito, Ombros e Biceps</th>";
                                        echo "<th colspan='3'>Treino C - Posterior de coxa e Panturrilhas</th>";
                                        echo "<th colspan='3'>Treino D - Costas, Tríceps, Abdômen</th>";
                                        echo "<th colspan='3'>Treino E - Glúteos e Abdômen</th>";
                                    }
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
    <script>
        function confirmarGeracaoTreino() {
            var confirmacao = confirm("Você está prestes a criar um novo treino. Essa ação só é recomendada caso você esteja a pelo menos 3 mêses com o treino anterior. Deseja continuar?");

            return confirmacao;
        }
    </script>
</body>

</html>
<?php
include "conexao.php";

function selecionar_exercicios($preferencia_treino, $grupos_musculares, $quantidades)
{
    global $exercicios;

    $exercicios_selecionados = [];

    // Para cada grupo muscular desejado, selecione a quantidade especificada
    foreach ($grupos_musculares as $grupo_muscular) {
        // Filtra exercícios com base na preferência de treino e grupo muscular
        $exercicios_filtrados = array_filter($exercicios, function ($exercicio) use ($preferencia_treino, $grupo_muscular) {
            return $exercicio["grupo_muscular"] === $grupo_muscular && $exercicio["tipo"] === $preferencia_treino;
        });

        // Limita a quantidade de exercícios com base na quantidade especificada
        $quantidade = $quantidades[$grupo_muscular];

        // Adiciona informações de série e repetição aos exercícios
        foreach ($exercicios_filtrados as &$exercicio) {
            $exercicio["serie"] = 3;  // Replace with the actual number of series
            $exercicio["repeticao"] = 12;  // Replace with the actual number of repetitions
        }

        // Use the "+" operator to preserve keys
        $exercicios_selecionados += array_slice($exercicios_filtrados, 0, $quantidade);
    }

    var_dump($exercicios_selecionados);
    return $exercicios_selecionados;
}

function gerar_treino($preferencia_treino, $nivel_treino, $treinos_semana)
{
    $ficha_treino = [];

    if ($treinos_semana < 2) {
        echo "É necessário treinar ao menos 2 vezes por semana!";
        return;
    }

    switch ($treinos_semana) {
        case 2:
            $ficha_treino["divisao"] = ["Full-body"];
            $ficha_treino["divisao"]["A"] = [];
            $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito", "Ombros", "Braços", "Pernas", "Costas", "Abdômen"], ["Pernas" => 1, "Peito" => 1, "Abdômen" => 1, "Ombros" => 1, "Braços" => 1, "Costas" => 1]);
            break;
        case 3:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["divisao"] = ["Full-body"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito", "Ombros", "Braços", "Pernas", "Costas", "Abdômen"], ["Pernas" => 1, "Peito" => 1, "Abdômen" => 1, "Ombros" => 1, "Braços" => 1, "Costas" => 1]);;
            } else {
                $ficha_treino["divisao"] = ["ABC"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito", "Ombros", "Braços", "Abdômen"], ["Peito" => 2, "Abdômen" => 1, "Ombros" => 2, "Braços" => 2]);
                $ficha_treino["divisao"]["B"] = selecionar_exercicios($preferencia_treino, ["Costas", "Ombros", "Braços"], ["Costas" => 2, "Abdômen" => 1, "Ombros" => 2, "Braços" => 2]);
                $ficha_treino["divisao"]["C"] = selecionar_exercicios($preferencia_treino, ["Pernas", "Abdômen"], ["Pernas" => 6, "Abdômen" => 1]);
            }
            break;
        case 4:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["divisao"] = ["AB"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Ombro Lateral", "Bíceps", "Tríceps", "Abdômen"], ["Peito" => 2, "Costas" => 2, "Ombro Anterior" => 1, "Ombro Lateral" => 1, "Bíceps" => 1, "Tríceps" => 1, "Abdômen" => 1]);
                $ficha_treino["divisao"]["B"] = selecionar_exercicios($preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], ["Quadriceps" => 3, "Posterior de Coxa" => 3, "Panturrilha" => 1]);
            } else {
                $ficha_treino["divisao"] = ["ABCD"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito", "Tríceps"], ["Peito" => 4, "Tríceps" => 3]);
                $ficha_treino["divisao"]["B"] = selecionar_exercicios($preferencia_treino, ["Costas", "Bíceps"], ["Costas" => 4, "Bíceps" => 3]);
                $ficha_treino["divisao"]["C"] = selecionar_exercicios($preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], ["Quadriceps" => 3, "Posterior de Coxa" => 3, "Panturrilha" => 1]);
                $ficha_treino["divisao"]["D"] = selecionar_exercicios($preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior", "Abdômen"], ["Ombro Anterior" => 3, "Ombro Lateral" => 3, "Ombro Posterior" => 3, "Abdômen" => 1]);
            }
            break;
        case 5:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["divisao"] = ["AB"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito", "Costas", "Ombro Anterior", "Ombro Lateral", "Bíceps", "Tríceps", "Abdômen"], ["Peito" => 2, "Costas" => 2, "Ombro Anterior" => 1, "Ombro Lateral" => 1, "Bíceps" => 1, "Tríceps" => 1, "Abdômen" => 1]);
                $ficha_treino["divisao"]["B"] = selecionar_exercicios($preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], ["Quadriceps" => 3, "Posterior de Coxa" => 3, "Panturrilha" => 1]);
            } else if ($nivel_treino === "intermediario") {
                $ficha_treino["divisao"] = ["ABCD"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito", "Tríceps"], ["Peito" => 4, "Tríceps" => 3]);
                $ficha_treino["divisao"]["B"] = selecionar_exercicios($preferencia_treino, ["Costas", "Bíceps"], ["Costas" => 4, "Bíceps" => 3]);
                $ficha_treino["divisao"]["C"] = selecionar_exercicios($preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], ["Quadriceps" => 3, "Posterior de Coxa" => 3, "Panturrilha" => 1]);
                $ficha_treino["divisao"]["D"] = selecionar_exercicios($preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior", "Abdômen"], ["Ombro Anterior" => 3, "Ombro Lateral" => 3, "Ombro Posterior" => 3, "Abdômen" => 1]);
            } else {
                $ficha_treino["divisao"] = ["ABCDE"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito"], ["Peito" => 5, "Abdômen" => 2]);
                $ficha_treino["divisao"]["B"] = selecionar_exercicios($preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], ["Quadriceps" => 3, "Posterior de Coxa" => 3, "Panturrilha" => 2]);
                $ficha_treino["divisao"]["C"] = selecionar_exercicios($preferencia_treino, ["Costas", "Abdômen"], ["Costas" => 6, "Abdômen" => 2]);
                $ficha_treino["divisao"]["D"] = selecionar_exercicios($preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior"], ["Ombro Anterior" => 3, "Ombro Lateral" => 3, "Ombro Posterior" => 3]);
                $ficha_treino["divisao"]["E"] = selecionar_exercicios($preferencia_treino, ["Bíceps", "Tríceps", "Panturrilha"], ["Bíceps" => 4, "Tríceps" => 3, "Panturrilha" => 2]);
            }
            break;
        case 6:
            if ($nivel_treino === "iniciante" || $nivel_treino === "intermediario") {
                $ficha_treino["divisao"] = ["ABC"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito", "Ombros", "Braços", "Abdômen"], ["Peito" => 2, "Abdômen" => 1, "Ombros" => 2, "Braços" => 2]);
                $ficha_treino["divisao"]["B"] = selecionar_exercicios($preferencia_treino, ["Costas", "Ombros", "Braços"], ["Costas" => 2, "Abdômen" => 1, "Ombros" => 2, "Braços" => 2]);
                $ficha_treino["divisao"]["C"] = selecionar_exercicios($preferencia_treino, ["Pernas", "Abdômen"], ["Pernas" => 6, "Abdômen" => 1]);
            } else {
                $ficha_treino["divisao"] = ["ABCDE"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito"], ["Peito" => 5, "Abdômen" => 2]);
                $ficha_treino["divisao"]["B"] = selecionar_exercicios($preferencia_treino, ["Quadriceps", "Posterior de Coxa", "Panturrilha"], ["Quadriceps" => 3, "Posterior de Coxa" => 3, "Panturrilha" => 2]);
                $ficha_treino["divisao"]["C"] = selecionar_exercicios($preferencia_treino, ["Costas", "Abdômen"], ["Costas" => 6, "Abdômen" => 2]);
                $ficha_treino["divisao"]["D"] = selecionar_exercicios($preferencia_treino, ["Ombro Anterior", "Ombro Lateral", "Ombro Posterior"], ["Ombro Anterior" => 3, "Ombro Lateral" => 3, "Ombro Posterior" => 3]);
                $ficha_treino["divisao"]["E"] = selecionar_exercicios($preferencia_treino, ["Bíceps", "Tríceps", "Panturrilha"], ["Bíceps" => 4, "Tríceps" => 3, "Panturrilha" => 2]);
            }
            break;
    }

    // echo "<pre>";
    // var_dump($ficha_treino["divisao"]);
    // echo "</pre>";
    return $ficha_treino;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $preferencia_treino = $_POST["prefTreino"];
    $nivel_treino = $_POST["nivelTreino"];
    $treinos_semana = $_POST["qtdTreinos"];

    $ficha_treino = gerar_treino($preferencia_treino, $nivel_treino, $treinos_semana);
    var_dump($ficha_treino);
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
                        <option value="vazio"></option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                </div>
                <div class="input-item">
                    <label for="prefTreino">Preferência de treino:</label>
                    <select name="prefTreino" id="prefTreino">
                        <option value="vazio"></option>
                        <option value="musculacao">Musculação</option>
                        <option value="treinoEmCasa">Treino em casa</option>
                    </select>
                </div>
                <div class="input-item">
                    <label for="nivelTreino">Nível de treino:</label>
                    <select name="nivelTreino" id="nivelTreino">
                        <option value="vazio"></option>
                        <option value="iniciante">Iniciante</option>
                        <option value="intermediario">Intermediário</option>
                        <option value="avancado">Avançado</option>
                    </select>
                </div>
                <div class="input-item">
                    <button class="submit-btn" type="submit">Gerar Treino</button>
                </div>
            </div>
        </form>

        <div class="table-container">
            <table border="1">
                <thead>
                    <tr>
                        <th colspan="3">A (Peito, Ombros e Tríceps)</th>
                        <th colspan="3">B (Costas, Ombros e Bíceps)</th>
                        <th colspan="3">C (Pernas)</th>
                    </tr>
                    <tr>
                        <th>Exercício</th>
                        <th>Série</th>
                        <th>Repetições</th>
                        <th>Exercício</th>
                        <th>Série</th>
                        <th>Repetições</th>
                        <th>Exercício</th>
                        <th>Série</th>
                        <th>Repetições</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Substitua os dados de exemplo pelos exercícios gerados dinamicamente
                    if (isset($ficha_treino) && is_array($ficha_treino)) {
                        foreach ($ficha_treino["divisao"]["A"] as $exercicio) {
                            echo "<tr align='center'>";
                            echo "<td>" . $exercicio["nome"] . "</td>";
                            echo "<td>" . $exercicio["serie"] . "</td>";
                            echo "<td>" . $exercicio["repeticao"] . "</td>";
                            // Repita o mesmo padrão para as outras colunas
                            // echo "<td></td>";
                            // echo "<td></td>";
                            // echo "<td></td>";
                            // echo "<td></td>";
                            // echo "<td></td>";
                            // echo "<td></td>";
                            // echo "</tr>";
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
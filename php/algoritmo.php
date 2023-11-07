<?php
$preferencia_treino = $_POST["prefTreino"];
$nivel_treino = $_POST["nivelTreino"];
$treinos_semana = $_POST["qtdTreinos"];

function selecionar_exercicios($preferencias_treino, $grupos_musculares, $quantidades) {
    global $exercicios;

    $exercicios_selecionados = [];

    // Para cada grupo muscular desejado, selecione a quantidade especificada
    foreach ($grupos_musculares as $grupo_muscular) {
        // Filtra exercícios com base nas preferências de treino e grupo muscular
        $exercicios_filtrados = array_filter($exercicios, function ($exercicio) use ($preferencias_treino, $grupo_muscular) {
            return in_array($exercicio["tipo"], $preferencias_treino) && $exercicio["grupo_muscular"] === $grupo_muscular;
        });

        // Limita a quantidade de exercícios com base na quantidade especificada
        $quantidade = $quantidades[$grupo_muscular];
        $exercicios_selecionados = array_merge($exercicios_selecionados, array_slice($exercicios_filtrados, 0, $quantidade));
    }

    return $exercicios_selecionados;
}

function gerar_tabela() {
    return;
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
                $ficha_treino["divisao"]["A"] = [];
            } else {
                $ficha_treino["divisao"] = ["ABC"];
                $ficha_treino["divisao"]["A"] = selecionar_exercicios($preferencia_treino, ["Peito", "Ombros", "Braços", "Abdômen"], [ "Peito" => 2, "Abdômen" => 1, "Ombros" => 2, "Braços" => 2]);
                $ficha_treino["divisao"]["B"] = selecionar_exercicios($preferencia_treino, ["Costas", "Ombros", "Braços"], [ "Costas" => 2, "Abdômen" => 1, "Ombros" => 2, "Braços" => 2]);
                $ficha_treino["divisao"]["C"] = selecionar_exercicios($preferencia_treino, ["Pernas", "Abdômen"], ["Pernas" => 6, "Abdômen" => 1]);
            }
            break;
        case 4:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["divisao"] = ["AB"];
                $ficha_treino["divisao"]["A"] = ["Superiores"];
                $ficha_treino["divisao"]["B"] = ["Inferiores"];
            } else {
                $ficha_treino["divisao"] = ["ABCD"];
                $ficha_treino["divisao"]["A"] = ["Peito e tríceps"];
                $ficha_treino["divisao"]["B"] = ["Costas e bíceps"];
                $ficha_treino["divisao"]["C"] = ["Pernas"];
                $ficha_treino["divisao"]["D"] = ["Ombros"];
            }
            break;
        case 5:
            if ($nivel_treino === "iniciante") {
                $ficha_treino["divisao"] = ["AB"];
                $ficha_treino["divisao"]["A"] = ["Superiores"];
                $ficha_treino["divisao"]["B"] = ["Inferiores"];
            } else if ($nivel_treino === "intermediario") {
                $ficha_treino["divisao"] = ["ABCD"];
                $ficha_treino["divisao"]["A"] = ["Peito e tríceps"];
                $ficha_treino["divisao"]["B"] = ["Costas e bíceps"];
                $ficha_treino["divisao"]["C"] = ["Pernas"];
                $ficha_treino["divisao"]["D"] = ["Ombros"];
            } else {
                $ficha_treino["divisao"] = ["ABCDE"];
                $ficha_treino["divisao"]["A"] = ["Peito"];
                $ficha_treino["divisao"]["B"] = ["Pernas"];
                $ficha_treino["divisao"]["C"] = ["Costas"];
                $ficha_treino["divisao"]["D"] = ["Ombros"];
                $ficha_treino["divisao"]["E"] = ["Braços"];
            }
            break;
        case 6:
            if ($nivel_treino === "iniciante" || $nivel_treino === "intermediario") {
                $ficha_treino["divisao"] = ["ABC"];
                $ficha_treino["divisao"]["A"] = ["Peito, ombro e tríceps"];
                $ficha_treino["divisao"]["B"] = ["Costas, ombro e bíceps"];
                $ficha_treino["divisao"]["C"] = ["Pernas"];
            } else {
                $ficha_treino["divisao"] = ["ABCDE"];
                $ficha_treino["divisao"]["A"] = ["Peito"];
                $ficha_treino["divisao"]["B"] = ["Pernas"];
                $ficha_treino["divisao"]["C"] = ["Costas"];
                $ficha_treino["divisao"]["D"] = ["Ombros"];
                $ficha_treino["divisao"]["E"] = ["Braços"];
            }
            break;
    }

    echo "<pre>";
    var_dump($ficha_treino["divisao"]);
    echo "</pre>";
    gerar_tabela($ficha_treino);
}

gerar_treino("Musculação", "avançado", 3);
?>
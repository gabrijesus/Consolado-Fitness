<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    // Redirecionar para a página de login ou fazer qualquer outra ação necessária
    header("Location: login.php");
    exit();
}

include "../utils/conexao.php";
include "../utils/queriesSql.php";

$id_usuario = $_SESSION["id_usuario"];

$infoUsuario = $conn->query("SELECT * FROM usuarios WHERE id_usuario = $id_usuario");

$usuarioInfo = $infoUsuario->fetch_assoc();
$nomeUsuario = $usuarioInfo['nome_usuario'];
$emailUsuario = $usuarioInfo['email'];
$nivelTreinoUsuario = $usuarioInfo['nivel_treino'];
$prefTreinoUsuario = $usuarioInfo['preferencia_treino'];
$dataNascUsuario = $usuarioInfo['data_nascimento'];
$sexoUsuario = $usuarioInfo['sexo'];

$data = date_create($dataNascUsuario);
$dataFormatada = date_format($data, 'd/m/Y');

$ficha_treino_result = $conn->query("SELECT * FROM ficha_de_treino WHERE id_usuario = $id_usuario");

if (isset($ficha_treino_result) && $ficha_treino_result->num_rows > 0) {
    $ficha_treino_fetch = $ficha_treino_result->fetch_assoc();
    $exercicios_ficha_treino = json_decode($ficha_treino_fetch["exercicios"], true);

    $ficha_treino["nome"] = $ficha_treino_fetch["nome_ficha_treino"];

    foreach ($exercicios_ficha_treino as $key => $value) {
        $ficha_treino["divisao"][$key] = $value;
    }
}

try {
    $stmt = mysqli_prepare($conn, QUERY_SELECT_USER);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION["user_id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} catch (Exception $e) {
    $e->getMessage();
    mysqli_rollback($conn);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | Consulado Fitness</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="shortcut icon" href="../imagens/favicon.ico" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <?php
    include_once "../utils/cabecalho.php";
    ?>

    <main>
        <div id="perfil">
            <h1 class="titulo">Meu Perfil</h1>
            <div class="info first">
                <div>
                    <h2 id="nomeUsuario" class="info_texto_negrito"><?php echo $nomeUsuario ?></h2>
                    <p id="emailUsuario" class="info_texto"><?php echo $emailUsuario ?></p>
                </div>
                <button class="editar_btn" onclick="habilitarEdicao()"><img class="icone_lapis" src="../imagens/icone_lapis.svg" alt="">Editar</button>
            </div>

            <div class="info">
                <span class="info_btn_container">
                    <h3 class="info_texto">Informações Pessoais</h3>
                    <button class="editar_btn" onclick="habilitarEdicao()"><img class="icone_lapis" src="../imagens/icone_lapis.svg" alt="">Editar</button>
                </span>
                <div class="info_container">
                    <div class="info_linha">
                        <div class="info_coluna">
                            <h2 class="info_texto_negrito">Nível de Treino</h2>
                            <p id="nivelTreino" class="info_texto"><?php echo $nivelTreinoUsuario ?></p>
                        </div>
                        <div class="info_coluna">
                            <h2 class="info_texto_negrito">Preferência de Treino</h2>
                            <p id="preferenciaTreino" class="info_texto"><?php echo $prefTreinoUsuario ?></p>
                        </div>
                    </div>
                    <div class="info_linha">
                        <div class="info_coluna">
                            <h2 class="info_texto_negrito">Data de Nascimento</h2>
                            <p id="dataNasc" class="info_texto"><?php echo $dataFormatada ?></p>
                        </div>
                        <span class="info_coluna_container">
                            <div class="info_coluna">
                                <h2 class="info_texto_negrito">Sexo</h2>
                                <p id="sexo" class="info_texto"><?php echo $sexoUsuario ?></p>
                            </div>
                            <div class="info_coluna">
                                <h2 class="info_texto_negrito">Senha</h2>
                                <p id="senha" class="info_texto">*******</p>
                            </div>
                        </span>
                    </div>
                </div>
            </div>

            <h2 class="titulo">Treino Atual</h2>
            <div class="table-container">
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
        </div>

        <!-- Seção de formulário de edição (inicialmente oculta) -->
        <div id="formEdicaoContainer" style="display: none;">
            <h2>Editar Informações</h2>
            <form id="formEdicao">
                <label for="novoNome">Novo Nome:</label>
                <input type="text" id="novoNome" name="novoNome">

                <label for="novoEmail">Novo Email:</label>
                <input type="email" id="novoEmail" name="novoEmail">

                <label for="novoNivelTreino">Nível de treino</label>
                <select name="novoNivelTreino" id="novoNivelTreino" required>
                    <option value="iniciante">Iniciante</option>
                    <option value="intermediario">Intermediario</option>
                    <option value="avancado">Avançado</option>
                </select>

                <label for="novaEscolhaTreino">Preferência de Treino</label>
                <select name="novaEscolhaTreino" id="novaEscolhaTreino" required>
                    <option value="musculação">Academia</option>
                    <option value="exercicios-em-casa">Em casa</option>
                </select>

                <label for="novoSexo">Sexo</label>
                <select name="novoSexo" id="novoSexo" required>
                    <option value="homem">Masculino</option>
                    <option value="mulher">Feminino</option>
                </select>

                <label for="novaDataNasc">Data de nascimento</label>
                <input type="date" name="novaDataNasc" id="novaDataNasc" class="date" required>

                <button type="button" onclick="salvarEdicao()">Salvar</button>
                <button type="button" onclick="cancelarEdicao()">Cancelar</button>
            </form>
        </div>
    </main>

    <script src="../js/perfil.js"></script>
    <script src="../js/global.js"></script>
</body>

</html>
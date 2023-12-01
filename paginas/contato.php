<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/contato.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="shortcut icon" href="../imagens/favicon.ico" type="image/x-icon">
    <title>Contato | Consulado Fitness</title>
</head>

<body>
    <?php
    include_once "../utils/cabecalho.php";
    ?>

    <div class="area_contato">

        <form action="https://formspree.io/f/mwkddnoo" method="post">

            <h1>Área de Contato</h1>

            <div class="caixa_mensagem">
                <label for="nome">Nome</label>
                <input type="text" name="name" id="nome">
            </div>

            <div class="caixa_mensagem">
                <label for="email">Email</label>
                <input type="text" name="email" id="email">
            </div>

            <div class="caixa_mensagem .area">

                <label for="mensagem">Caixa de mensagem (dúvidas,reclamações,etc)</label>
                <textarea name="message" id="mensagem" cols="30" rows="15"></textarea>
            </div>
            <input type="hidden" name="_language" value="pt-BR" />

            <button type="submit" class="enviar">Enviar</button>

        </form>

        <img src="../imagens/img_contato.png" alt="contato" class="img_desktop">
    </div>


</body>

</html>